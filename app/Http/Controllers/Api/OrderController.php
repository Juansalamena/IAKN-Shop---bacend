<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * POST /api/orders/checkout
     * Create new order with AUTOMATIC SPLIT ORDER by store
     * If cart contains items from multiple stores, separate Order records are created for each store.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        /** @var User $user */
        $user = $request->user();
        $customerId = $user->id;
        $itemsInput = collect($validated['items']);
        $productIds = $itemsInput->pluck('product_id')->unique();

        return DB::transaction(function () use ($itemsInput, $productIds, $validated, $customerId) {
            // Lock products for update to avoid race conditions on stock
            $products = Product::whereIn('id', $productIds)
                ->with('store')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Validate stock and active status
            foreach ($itemsInput as $item) {
                $product = $products->get($item['product_id']);

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => ["Product ID {$item['product_id']} is no longer available."],
                    ]);
                }

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock for product '{$product->name}'. Available: {$product->stock}, Requested: {$item['quantity']}."],
                    ]);
                }
            }

            // Group items by store_id for SPLIT ORDER
            $itemsByStore = $itemsInput->groupBy(function ($item) use ($products) {
                return $products->get($item['product_id'])->store_id;
            });

            $createdOrders = [];
            $grandTotal = 0;

            foreach ($itemsByStore as $storeId => $storeItems) {
                $orderTotal = 0;

                // 1. Create order header for this store
                $order = Order::create([
                    'customer_id' => $customerId,
                    'store_id' => $storeId,
                    'total_amount' => 0, // calculated below
                    'status' => Order::STATUS_PENDING,
                    'shipping_address' => $validated['shipping_address'],
                    'notes' => $validated['notes'] ?? null,
                ]);

                // 2. Create order items and decrement stock
                foreach ($storeItems as $item) {
                    $product = $products->get($item['product_id']);
                    $itemPrice = (float) $product->price;
                    $subtotal = $itemPrice * $item['quantity'];
                    $orderTotal += $subtotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price' => $itemPrice,
                        'quantity' => $item['quantity'],
                        'subtotal' => $subtotal,
                    ]);

                    // Deduct stock
                    $product->decrement('stock', $item['quantity']);
                }

                // 3. Update order total amount
                $order->update(['total_amount' => $orderTotal]);
                $grandTotal += $orderTotal;

                $createdOrders[] = $order->load(['store:id,name,phone', 'items']);
            }

            $splitCount = count($createdOrders);
            $message = $splitCount > 1
                ? "Checkout successful! Your order was automatically split into {$splitCount} separate store orders."
                : "Checkout successful! Order created.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'split_order' => $splitCount > 1,
                'total_orders_created' => $splitCount,
                'grand_total' => $grandTotal,
                'data' => $createdOrders,
            ], 201);
        });
    }

    /**
     * GET /api/customer/orders
     * List all orders of the logged in customer
     */
    public function customerOrders(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $orders = Order::with(['store:id,name,phone', 'items'])
            ->where('customer_id', $user->id)
            ->orderBy('id', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * GET /api/customer/orders/{id}
     * Detail of customer order
     */
    public function customerOrderDetail(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $order = Order::with(['store', 'items.product'])
            ->where('customer_id', $user->id)
            ->find($id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * GET /api/store/orders
     * List orders specific to the Store Admin's store
     */
    public function storeOrders(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $query = Order::with(['customer:id,name,email,phone', 'items'])
            ->where('store_id', $user->store_id);

        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->input('status')));
        }

        $orders = $query->orderBy('id', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'store_id' => $user->store_id,
            'data' => $orders,
        ]);
    }

    /**
     * GET /api/store/orders/{id}
     * Detail of store order with IDOR validation
     */
    public function storeOrderDetail(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $order = Order::with(['customer', 'items.product', 'store'])->find($id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Anti-IDOR validation
        if ($order->store_id !== $user->store_id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: This order belongs to another store.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * PUT /api/store/orders/{id}/status
     * Update order status by Store Admin
     */
    public function updateOrderStatus(Request $request, int $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $order = Order::find($id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Anti-IDOR validation
        if ((int) $order->store_id !== (int) $user->store_id && ! $user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You cannot modify an order of another store.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', Order::STATUSES),
        ]);

        $oldStatus = $order->status;
        $newStatus = strtoupper($validated['status']);

        // If transitioning to CANCELLED, restore product stocks
        if ($newStatus === Order::STATUS_CANCELLED && $oldStatus !== Order::STATUS_CANCELLED) {
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} status updated from {$oldStatus} to {$newStatus}.",
            'data' => $order->fresh(['items', 'customer']),
        ]);
    }

    /**
     * GET /api/admin/all-orders
     * Super Admin monitors all orders across all stores
     */
    public function adminAllOrders(Request $request): JsonResponse
    {
        $query = Order::with(['customer:id,name,email,phone', 'store:id,name,slug', 'items']);

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->input('store_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->input('status')));
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
        }

        $orders = $query->orderBy('id', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * GET /api/admin/orders/{id}
     * Super Admin view any order detail
     */
    public function adminOrderDetail(int $id): JsonResponse
    {
        $order = Order::with(['customer', 'store', 'items.product'])->find($id);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }
}
