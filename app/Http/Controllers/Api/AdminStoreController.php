<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminStoreController extends Controller
{
    /**
     * GET /api/admin/stores
     * List all stores with summary metrics
     */
    public function index(): JsonResponse
    {
        $stores = Store::withCount(['products', 'orders', 'admins'])
            ->withSum('orders as total_revenue', 'total_amount')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stores,
        ]);
    }

    /**
     * POST /api/admin/stores
     * Register a new store (Super Admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stores,slug',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:25',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        }

        $store = Store::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Store registered successfully.',
            'data' => $store,
        ], 201);
    }

    /**
     * GET /api/admin/stores/{id}
     * Get details of a single store
     */
    public function show(int $id): JsonResponse
    {
        $store = Store::with(['admins'])->withCount(['products', 'orders'])->find($id);

        if (! $store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $store,
        ]);
    }

    /**
     * PUT /api/admin/stores/{id}
     * Update store details
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $store = Store::find($id);

        if (! $store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:stores,slug,' . $id,
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:25',
            'is_active' => 'nullable|boolean',
        ]);

        $store->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully.',
            'data' => $store,
        ]);
    }

    /**
     * POST /api/admin/store-admins
     * Create login credentials for a Store Admin mapped to a store_id
     */
    public function createStoreAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:25',
        ]);

        $store = Store::find($validated['store_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'store_admin',
            'store_id' => $validated['store_id'],
            'phone' => $validated['phone'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Store Admin created and successfully mapped to store: {$store->name}.",
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'store_id' => $user->store_id,
                'store' => [
                    'id' => $store->id,
                    'name' => $store->name,
                ],
            ],
        ], 201);
    }

    /**
     * GET /api/admin/store-admins
     * List all store admins and their mapped store
     */
    public function listStoreAdmins(): JsonResponse
    {
        $admins = User::with('store')
            ->where('role', 'store_admin')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $admins,
        ]);
    }

    /**
     * GET /api/admin/stores/reports
     * Transaction and sales reports aggregated across all stores
     */
    public function reports(): JsonResponse
    {
        $totalRevenue = (float) Order::where('status', '!=', Order::STATUS_CANCELLED)->sum('total_amount');
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', Order::STATUS_DELIVERED)->count();
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)->count();

        // Revenue & order count per store
        $storeStats = Store::withCount('orders')
            ->get()
            ->map(function ($store) {
                $revenue = (float) Order::where('store_id', $store->id)
                    ->where('status', '!=', Order::STATUS_CANCELLED)
                    ->sum('total_amount');

                return [
                    'store_id' => $store->id,
                    'store_name' => $store->name,
                    'total_orders' => $store->orders_count,
                    'total_revenue' => $revenue,
                    'pending_orders' => Order::where('store_id', $store->id)->where('status', Order::STATUS_PENDING)->count(),
                    'delivered_orders' => Order::where('store_id', $store->id)->where('status', Order::STATUS_DELIVERED)->count(),
                ];
            });

        // Status breakdown
        $statusBreakdown = Order::select('status', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_revenue' => $totalRevenue,
                    'total_orders' => $totalOrders,
                    'completed_orders' => $completedOrders,
                    'pending_orders' => $pendingOrders,
                ],
                'by_store' => $storeStats,
                'by_status' => $statusBreakdown,
            ],
        ]);
    }
}
