@php
    $user = \App\Models\User::current();
    $isStoreAdmin = $user !== null && $user->isStoreAdmin();
    $storeId = $user?->store_id;
    $userStore = $isStoreAdmin ? $user?->store : null;

    $orderQuery = \App\Models\Order::query();
    $productQuery = \App\Models\Product::query();

    if ($isStoreAdmin && $storeId) {
        $orderQuery->where('store_id', $storeId);
        $productQuery->where('store_id', $storeId);
    }

    $pendingOrdersCount = $user ? (clone $orderQuery)->where('status', \App\Models\Order::STATUS_PENDING)->count() : 0;
    $productsCount = $user ? (clone $productQuery)->count() : 0;
    $categoriesCount = $user ? \App\Models\Category::count() : 0;
    $storesCount = $user ? ($isStoreAdmin ? 1 : \App\Models\Store::count()) : 0;
    $customersCount = $user ? \App\Models\User::where('role', 'customer')->count() : 0;

    $currentPath = request()->path();
    $isDashboard = $currentPath === 'admin';
    $isProducts = str_starts_with($currentPath, 'admin/products');
    $isOrders = str_starts_with($currentPath, 'admin/orders');
    $isCategories = str_starts_with($currentPath, 'admin/categories');
    $isStores = str_starts_with($currentPath, 'admin/stores');
    $isUsers = str_starts_with($currentPath, 'admin/users');
@endphp

@if($user)
<div class="mb-6 pt-2">
    <!-- Top Identity & Quick Action Banner -->
    <div class="aura-card p-4 rounded-2xl mb-3 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/25 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-base font-extrabold text-white tracking-tight">IAKN Store Multi-Gerai</span>
                    @if($isStoreAdmin)
                        <span class="text-[11px] font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 px-2.5 py-0.5 rounded-full">
                            Gerai: {{ $userStore->name ?? 'Cabang' }}
                        </span>
                    @else
                        <span class="text-[11px] font-semibold bg-indigo-500/15 text-indigo-400 border border-indigo-500/30 px-2.5 py-0.5 rounded-full">
                            Super Admin (Multi-Cabang)
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
                    Sistem Manajemen E-Commerce Multi-Gerai Terpadu &bull; Login sebagai <span class="text-slate-200 font-semibold">{{ $user->name }}</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 self-end lg:self-auto">
            <a href="{{ \App\Filament\Resources\ProductResource::getUrl('create') }}" class="text-xs text-white font-semibold px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 transition-all flex items-center gap-1.5 shadow-lg shadow-indigo-500/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Produk</span>
            </a>
            <a href="{{ \App\Filament\Resources\OrderResource::getUrl('create') }}" class="text-xs text-amber-300 hover:text-amber-200 font-semibold px-3.5 py-2 rounded-xl bg-amber-500/15 border border-amber-500/30 hover:bg-amber-500/25 transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Input Order</span>
            </a>
        </div>
    </div>

    <!-- Unified Module Navigation Tabs -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
        <a href="{{ url('/admin') }}" class="p-3 rounded-xl border transition-all flex items-center justify-between {{ $isDashboard ? 'bg-indigo-600/20 border-indigo-500/50 text-white shadow-lg shadow-indigo-500/10' : 'aura-card border-white/[0.06] text-slate-300 hover:border-indigo-500/30 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg {{ $isDashboard ? 'bg-indigo-500 text-white' : 'bg-indigo-500/15 text-indigo-400' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold">Dashboard</div>
                    <div class="text-[10px] text-slate-400">Ringkasan</div>
                </div>
            </div>
        </a>

        <a href="{{ \App\Filament\Resources\ProductResource::getUrl('index') }}" class="p-3 rounded-xl border transition-all flex items-center justify-between {{ $isProducts ? 'bg-emerald-600/20 border-emerald-500/50 text-white shadow-lg shadow-emerald-500/10' : 'aura-card border-white/[0.06] text-slate-300 hover:border-emerald-500/30 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg {{ $isProducts ? 'bg-emerald-500 text-white' : 'bg-emerald-500/15 text-emerald-400' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold">Produk &amp; Stok</div>
                    <div class="text-[10px] text-slate-400">{{ $productsCount }} item</div>
                </div>
            </div>
        </a>

        <a href="{{ \App\Filament\Resources\OrderResource::getUrl('index') }}" class="p-3 rounded-xl border transition-all flex items-center justify-between {{ $isOrders ? 'bg-amber-600/20 border-amber-500/50 text-white shadow-lg shadow-amber-500/10' : 'aura-card border-white/[0.06] text-slate-300 hover:border-amber-500/30 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg {{ $isOrders ? 'bg-amber-500 text-white' : 'bg-amber-500/15 text-amber-400' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold">Pesanan</div>
                    <div class="text-[10px] {{ $pendingOrdersCount > 0 ? 'text-amber-400 font-semibold' : 'text-slate-400' }}">
                        {{ $pendingOrdersCount > 0 ? $pendingOrdersCount . ' Pending' : 'Transaksi' }}
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ \App\Filament\Resources\CategoryResource::getUrl('index') }}" class="p-3 rounded-xl border transition-all flex items-center justify-between {{ $isCategories ? 'bg-cyan-600/20 border-cyan-500/50 text-white shadow-lg shadow-cyan-500/10' : 'aura-card border-white/[0.06] text-slate-300 hover:border-cyan-500/30 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg {{ $isCategories ? 'bg-cyan-500 text-white' : 'bg-cyan-500/15 text-cyan-400' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold">Kategori</div>
                    <div class="text-[10px] text-slate-400">{{ $categoriesCount }} kategori</div>
                </div>
            </div>
        </a>

        <a href="{{ \App\Filament\Resources\StoreResource::getUrl('index') }}" class="p-3 rounded-xl border transition-all flex items-center justify-between {{ $isStores ? 'bg-blue-600/20 border-blue-500/50 text-white shadow-lg shadow-blue-500/10' : 'aura-card border-white/[0.06] text-slate-300 hover:border-blue-500/30 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg {{ $isStores ? 'bg-blue-500 text-white' : 'bg-blue-500/15 text-blue-400' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold">{{ $isStoreAdmin ? 'Profil Gerai' : 'Data Gerai' }}</div>
                    <div class="text-[10px] text-slate-400">{{ $storesCount }} cabang</div>
                </div>
            </div>
        </a>

        <a href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}" class="p-3 rounded-xl border transition-all flex items-center justify-between {{ $isUsers ? 'bg-purple-600/20 border-purple-500/50 text-white shadow-lg shadow-purple-500/10' : 'aura-card border-white/[0.06] text-slate-300 hover:border-purple-500/30 hover:text-white' }}">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg {{ $isUsers ? 'bg-purple-500 text-white' : 'bg-purple-500/15 text-purple-400' }} flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-bold">Pelanggan</div>
                    <div class="text-[10px] text-slate-400">{{ $customersCount }} customer</div>
                </div>
            </div>
        </a>
    </div>
</div>
@endif

