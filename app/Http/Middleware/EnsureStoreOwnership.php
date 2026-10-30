<?php

namespace App\Http\Middleware;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreOwnership
{
    /**
     * Handle an incoming request.
     * Prevents Store Admin A from accessing or mutating data of Store Admin B (anti-IDOR).
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        // Super Admin bypasses store ownership checks
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user || ! $user->isStoreAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized store access.',
            ], 403);
        }

        $adminStoreId = (int) $user->store_id;

        // If route or payload supplies a store_id parameter, enforce match
        if ($request->route('store') && (int) $request->route('store') !== $adminStoreId) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You cannot access or modify another store.',
            ], 403);
        }

        if ($request->has('store_id') && (int) $request->input('store_id') !== $adminStoreId) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: Payload store_id mismatch with assigned store.',
            ], 403);
        }

        // If product id is in route, check product ownership
        $productId = $request->route('product') ?? $request->route('id');
        if ($productId && $request->is('*products/*')) {
            $product = Product::find($productId);
            if ($product && (int) $product->store_id !== $adminStoreId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden: This product does not belong to your store.',
                ], 403);
            }
        }

        // If order id is in route, check order ownership
        $orderId = $request->route('order') ?? $request->route('id');
        if ($orderId && $request->is('*orders/*')) {
            $order = Order::find($orderId);
            if ($order && (int) $order->store_id !== $adminStoreId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden: This order does not belong to your store.',
                ], 403);
            }
        }

        return $next($request);
    }
}
