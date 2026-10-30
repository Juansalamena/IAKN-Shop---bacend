<x-filament-panels::page class="aura-dashboard">
    <style>
        .fi-page-header {
            display: none !important;
        }
    </style>

    <div class="space-y-6">
        {{-- 1. Statistik Utama & Status Cabang Gerai --}}
        @include('filament.partials.stat-cards')

        {{-- 2. Tabel Pesanan Masuk Terbaru & Katalog Produk Gerai --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            @include('filament.partials.recent-orders-table')
            @include('filament.partials.top-products-table')
        </div>

        {{-- 3. Dokumentasi Endpoint RESTful API & Akun Uji --}}
        @include('filament.partials.api-guide')
    </div>
</x-filament-panels::page>
