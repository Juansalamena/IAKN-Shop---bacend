<?php

use App\Http\Controllers\Api\AdminStoreController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PublicProductController;
use App\Http\Controllers\Api\StoreProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Multi-Gerai Backend E-Commerce
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. Modul Autentikasi Publik
// ==========================================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/google', [AuthController::class, 'googleLogin'])->name('api.auth.google');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
    });
});

// ==========================================
// 2. Modul Publik (Katalog Produk & Gerai)
// ==========================================
Route::get('/products', [PublicProductController::class, 'index'])->name('api.products.index');
Route::get('/products/{id}', [PublicProductController::class, 'show'])->name('api.products.show');
Route::get('/categories', [PublicProductController::class, 'categories'])->name('api.categories.index');
Route::get('/stores', [PublicProductController::class, 'stores'])->name('api.stores.index');

// ==========================================
// 3. Modul Customer (Orders & Checkout)
// ==========================================
Route::middleware(['auth:sanctum', 'role:customer,super_admin'])->group(function () {
    Route::post('/orders/checkout', [OrderController::class, 'checkout'])->name('api.orders.checkout');
    Route::get('/customer/orders', [OrderController::class, 'customerOrders'])->name('api.customer.orders');
    Route::get('/customer/orders/{id}', [OrderController::class, 'customerOrderDetail'])->name('api.customer.orders.show');
});

// ==========================================
// 4. Modul Admin Toko / Gerai (Produk & Pesanan Gerai)
// ==========================================
Route::middleware(['auth:sanctum', 'role:store_admin,super_admin', 'store.ownership'])->prefix('store')->group(function () {
    // Produk Gerai
    Route::get('/products', [StoreProductController::class, 'index'])->name('api.store.products.index');
    Route::post('/products', [StoreProductController::class, 'store'])->name('api.store.products.store');
    Route::get('/products/{id}', [StoreProductController::class, 'show'])->name('api.store.products.show');
    Route::put('/products/{id}', [StoreProductController::class, 'update'])->name('api.store.products.update');
    Route::delete('/products/{id}', [StoreProductController::class, 'destroy'])->name('api.store.products.destroy');

    // Pesanan Gerai
    Route::get('/orders', [OrderController::class, 'storeOrders'])->name('api.store.orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'storeOrderDetail'])->name('api.store.orders.show');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateOrderStatus'])->name('api.store.orders.update-status');
});

// ==========================================
// 5. Modul Super Admin (Manajemen Gerai & Pemantauan Global)
// ==========================================
Route::middleware(['auth:sanctum', 'role:super_admin'])->prefix('admin')->group(function () {
    // Manajemen Gerai
    Route::get('/stores', [AdminStoreController::class, 'index'])->name('api.admin.stores.index');
    Route::post('/stores', [AdminStoreController::class, 'store'])->name('api.admin.stores.store');
    Route::get('/stores/reports', [AdminStoreController::class, 'reports'])->name('api.admin.stores.reports');
    Route::get('/stores/{id}', [AdminStoreController::class, 'show'])->name('api.admin.stores.show');
    Route::put('/stores/{id}', [AdminStoreController::class, 'update'])->name('api.admin.stores.update');

    // Manajemen Akun Admin Toko
    Route::post('/store-admins', [AdminStoreController::class, 'createStoreAdmin'])->name('api.admin.store-admins.store');
    Route::get('/store-admins', [AdminStoreController::class, 'listStoreAdmins'])->name('api.admin.store-admins.index');

    // Pemantauan Pesanan Lintas Gerai
    Route::get('/all-orders', [OrderController::class, 'adminAllOrders'])->name('api.admin.all-orders');
    Route::get('/orders/{id}', [OrderController::class, 'adminOrderDetail'])->name('api.admin.orders.show');
});
