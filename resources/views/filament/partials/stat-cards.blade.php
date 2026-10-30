{{-- Partial: Store Status Pills & Top 4 KPI Stat Cards --}}
@if(!$isStoreAdmin && count($stores) > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
    @foreach($stores as $st)
    <a href="{{ \App\Filament\Resources\StoreResource::getUrl('edit', ['record' => $st->id]) }}" class="aura-card p-3.5 rounded-xl flex items-center justify-between border border-white/[0.05] hover:border-indigo-500/30 transition-all">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-500/15 border border-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xs">
                {{ substr($st->name, 0, 2) }}
            </div>
            <div>
                <div class="text-xs font-bold text-white">{{ $st->name }}</div>
                <div class="text-[11px] text-slate-400">{{ $st->phone ?? 'Aktif' }}</div>
            </div>
        </div>
        <div class="flex items-center gap-3 text-right">
            <div>
                <div class="text-xs font-bold text-white">{{ $st->products_count }}</div>
                <div class="text-[10px] text-slate-500">Produk</div>
            </div>
            <div>
                <div class="text-xs font-bold text-cyan-400">{{ $st->orders_count }}</div>
                <div class="text-[10px] text-slate-500">Order</div>
            </div>
        </div>
    </a>
    @endforeach
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Card 1: Total Sales -->
    <div class="aura-card aura-glow-blue rounded-2xl p-5 relative overflow-hidden flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-400">Total Penjualan</span>
            <span class="text-[11px] font-semibold text-blue-400 bg-blue-500/15 border border-blue-500/20 px-2 py-0.5 rounded-full">Aktif</span>
        </div>
        <div class="flex items-end justify-between mt-2">
            <div>
                <div class="text-2xl font-bold text-white tracking-tight">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                <div class="text-[11px] font-medium text-emerald-400 mt-1 flex items-center gap-1">
                    <span>▲</span> Real-time dari database
                </div>
            </div>
            <div class="w-24 h-10">
                <svg class="w-full h-full overflow-visible" viewBox="0 0 100 40">
                    <defs>
                        <linearGradient id="glowBlue" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.4" />
                            <stop offset="100%" stop-color="#38bdf8" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0 32 Q 25 36, 45 22 T 95 10 L 95 40 L 0 40 Z" fill="url(#glowBlue)" />
                    <path d="M 0 32 Q 25 36, 45 22 T 95 10" fill="none" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Orders -->
    <div class="aura-card aura-glow-amber rounded-2xl p-5 relative overflow-hidden flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-400">Total Pesanan Masuk</span>
            <span class="text-[11px] font-semibold text-amber-400 bg-amber-500/15 border border-amber-500/20 px-2 py-0.5 rounded-full">{{ $totalOrders }} Order</span>
        </div>
        <div class="flex items-end justify-between mt-2">
            <div>
                <div class="text-2xl font-bold text-white tracking-tight">{{ number_format($totalOrders) }}</div>
                <div class="text-[11px] font-medium text-amber-400 mt-1 flex items-center gap-1">
                    <span>●</span> Mendukung Split Order
                </div>
            </div>
            <div class="w-24 h-10">
                <svg class="w-full h-full overflow-visible" viewBox="0 0 100 40">
                    <defs>
                        <linearGradient id="glowAmber" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#fb923c" stop-opacity="0.4" />
                            <stop offset="100%" stop-color="#fb923c" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0 30 Q 25 38, 50 20 T 95 8 L 95 40 L 0 40 Z" fill="url(#glowAmber)" />
                    <path d="M 0 30 Q 25 38, 50 20 T 95 8" fill="none" stroke="#fb923c" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 3: New Customers -->
    <div class="aura-card aura-glow-purple rounded-2xl p-5 relative overflow-hidden flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-400">Pelanggan Terdaftar</span>
            <span class="text-[11px] font-semibold text-purple-400 bg-purple-500/15 border border-purple-500/20 px-2 py-0.5 rounded-full">Customer</span>
        </div>
        <div class="flex items-end justify-between mt-2">
            <div>
                <div class="text-2xl font-bold text-white tracking-tight">{{ number_format($newCustomers) }}</div>
                <div class="text-[11px] font-medium text-purple-400 mt-1 flex items-center gap-1">
                    <span>▲</span> Akun Customer Aktif
                </div>
            </div>
            <div class="w-24 h-10">
                <svg class="w-full h-full overflow-visible" viewBox="0 0 100 40">
                    <defs>
                        <linearGradient id="glowPurple" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#c084fc" stop-opacity="0.4" />
                            <stop offset="100%" stop-color="#c084fc" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0 34 Q 25 36, 50 22 T 95 12 L 95 40 L 0 40 Z" fill="url(#glowPurple)" />
                    <path d="M 0 34 Q 25 36, 50 22 T 95 12" fill="none" stroke="#c084fc" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 4: Average Order Value -->
    <div class="aura-card aura-glow-emerald rounded-2xl p-5 relative overflow-hidden flex flex-col justify-between h-36">
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-400">Rata-rata Nilai Order</span>
            <span class="text-[11px] font-semibold text-emerald-400 bg-emerald-500/15 border border-emerald-500/20 px-2 py-0.5 rounded-full">AOV</span>
        </div>
        <div class="flex items-end justify-between mt-2">
            <div>
                <div class="text-2xl font-bold text-white tracking-tight">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</div>
                <div class="text-[11px] font-medium text-emerald-400 mt-1 flex items-center gap-1">
                    <span>▲</span> Per transaksi gerai
                </div>
            </div>
            <div class="w-24 h-10">
                <svg class="w-full h-full overflow-visible" viewBox="0 0 100 40">
                    <defs>
                        <linearGradient id="glowEmerald" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#34d399" stop-opacity="0.4" />
                            <stop offset="100%" stop-color="#34d399" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <path d="M 0 32 Q 25 34, 50 18 T 95 14 L 95 40 L 0 40 Z" fill="url(#glowEmerald)" />
                    <path d="M 0 32 Q 25 34, 50 18 T 95 14" fill="none" stroke="#34d399" stroke-width="2.5" stroke-linecap="round" />
                </svg>
            </div>
        </div>
    </div>
</div>

