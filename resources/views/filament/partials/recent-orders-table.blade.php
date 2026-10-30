{{-- Partial: Order Management Table & Status Pipeline --}}
<div class="lg:col-span-7 aura-card rounded-2xl p-6 overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-sm font-bold text-white tracking-tight">Pesanan Masuk Terbaru</h2>
            <p class="text-xs text-slate-400 mt-0.5">Daftar transaksi pesanan per-gerai (Single Database, Foreign Key `store_id`)</p>
        </div>
        <a href="{{ \App\Filament\Resources\OrderResource::getUrl('index') }}" class="text-[11px] font-semibold text-amber-400 hover:text-amber-300 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg transition-colors">
            Proses &amp; Lihat Semua &rarr;
        </a>
    </div>

    <!-- Order Status Pipeline Summary -->
    <div class="grid grid-cols-5 gap-2 mb-4 p-2.5 rounded-xl bg-white/[0.02] border border-white/[0.04] text-center">
        <div>
            <div class="text-xs font-bold text-amber-400">{{ $orderStatusCounts['PENDING'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400">Pending</div>
        </div>
        <div>
            <div class="text-xs font-bold text-blue-400">{{ $orderStatusCounts['PROCESSED'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400">Diproses</div>
        </div>
        <div>
            <div class="text-xs font-bold text-cyan-400">{{ $orderStatusCounts['SHIPPED'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400">Dikirim</div>
        </div>
        <div>
            <div class="text-xs font-bold text-emerald-400">{{ $orderStatusCounts['DELIVERED'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400">Selesai</div>
        </div>
        <div>
            <div class="text-xs font-bold text-rose-400">{{ $orderStatusCounts['CANCELLED'] ?? 0 }}</div>
            <div class="text-[10px] text-slate-400">Batal</div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/[0.06] text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="pb-3 font-medium">No. Order</th>
                    <th class="pb-3 font-medium">Customer</th>
                    <th class="pb-3 font-medium">Gerai</th>
                    <th class="pb-3 font-medium">Status</th>
                    <th class="pb-3 font-medium text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.04] text-xs">
                @forelse($recentOrders as $ord)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="py-3.5 font-bold text-white font-mono text-[11px]">
                        <a href="{{ \App\Filament\Resources\OrderResource::getUrl('edit', ['record' => $ord->id]) }}" class="hover:text-cyan-400 underline decoration-white/20">
                            {{ $ord->order_number }}
                        </a>
                    </td>
                    <td class="py-3.5 text-slate-300">
                        {{ $ord->customer->name ?? 'Customer' }}
                    </td>
                    <td class="py-3.5 text-slate-400 text-[11px]">
                        {{ $ord->store->name ?? '-' }}
                    </td>
                    <td class="py-3.5">
                        @php
                            $badgeClasses = match($ord->status) {
                                'PENDING' => 'bg-amber-500/15 text-amber-400 border-amber-500/20',
                                'PROCESSED' => 'bg-blue-500/15 text-blue-400 border-blue-500/20',
                                'SHIPPED' => 'bg-cyan-500/15 text-cyan-400 border-cyan-500/20',
                                'DELIVERED' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20',
                                'CANCELLED' => 'bg-rose-500/15 text-rose-400 border-rose-500/20',
                                default => 'bg-slate-500/15 text-slate-400 border-slate-500/20',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeClasses }}">
                            {{ $ord->status }}
                        </span>
                    </td>
                    <td class="py-3.5 font-bold text-white text-right">
                        Rp {{ number_format($ord->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-500 text-xs">
                        Belum ada data pesanan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

