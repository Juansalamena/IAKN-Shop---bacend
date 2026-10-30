<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Enums\Width;

class AuraDashboard extends BaseDashboard
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Dashboard Multi-Gerai';

    protected static ?string $title = 'IAKN Multi-Gerai Shop';

    protected string $view = 'filament.pages.aura-dashboard';

    public function getMaxContentWidth(): Width | string | null
    {
        return Width::Full;
    }

    public function getHeading(): string
    {
        return '';
    }

    protected function getViewData(): array
    {
        $user = User::current();
        $isStoreAdmin = $user !== null && $user->isStoreAdmin();
        $storeId = $user?->store_id;

        // Query scopes
        $orderQuery = Order::query();
        $productQuery = Product::query();

        if ($isStoreAdmin && $storeId) {
            $orderQuery->where('store_id', $storeId);
            $productQuery->where('store_id', $storeId);
        }

        $totalSales = (float) (clone $orderQuery)->where('status', '!=', Order::STATUS_CANCELLED)->sum('total_amount');
        $totalOrders = (clone $orderQuery)->count();
        $newCustomers = User::where('role', 'customer')->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $recentOrders = (clone $orderQuery)->with(['customer', 'store', 'items'])
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        $topProducts = (clone $productQuery)->with(['store', 'category'])
            ->orderBy('stock', 'desc')
            ->limit(5)
            ->get();

        $stores = Store::withCount(['products', 'orders'])->get();

        $totalProducts = (clone $productQuery)->count();
        $lowStockCount = (clone $productQuery)->where('stock', '<=', 10)->count();
        $totalCategories = Category::count();

        $orderStatusCounts = [
            Order::STATUS_PENDING => (clone $orderQuery)->where('status', Order::STATUS_PENDING)->count(),
            Order::STATUS_PROCESSED => (clone $orderQuery)->where('status', Order::STATUS_PROCESSED)->count(),
            Order::STATUS_SHIPPED => (clone $orderQuery)->where('status', Order::STATUS_SHIPPED)->count(),
            Order::STATUS_DELIVERED => (clone $orderQuery)->where('status', Order::STATUS_DELIVERED)->count(),
            Order::STATUS_CANCELLED => (clone $orderQuery)->where('status', Order::STATUS_CANCELLED)->count(),
        ];

        return [
            'currentUser' => $user,
            'isStoreAdmin' => $isStoreAdmin,
            'userStore' => $isStoreAdmin ? $user->store : null,
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'newCustomers' => $newCustomers,
            'avgOrderValue' => $avgOrderValue,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'stores' => $stores,
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockCount,
            'totalCategories' => $totalCategories,
            'orderStatusCounts' => $orderStatusCounts,
        ];
    }
}
