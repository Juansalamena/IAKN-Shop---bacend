{{-- Partial: Top Products & Inventory Table --}}
<div class="lg:col-span-5 aura-card rounded-2xl p-6 overflow-hidden">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-sm font-bold text-white tracking-tight">Katalog Produk Gerai</h2>
            <p class="text-xs text-slate-400 mt-0.5">Produk aktif dengan isolasi data `store_id`</p>
        </div>
        <a href="{{ \App\Filament\Resources\ProductResource::getUrl('index') }}" class="text-[11px] font-semibold text-indigo-400 hover:text-indigo-300 bg-indigo-500/10 border border-indigo-500/20 px-2.5 py-1 rounded-lg transition-colors">
            Lihat &amp; Kelola Semua &rarr;
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/[0.06] text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="pb-3 font-medium">Nama Produk</th>
                    <th class="pb-3 font-medium">Gerai</th>
                    <th class="pb-3 font-medium">Stok</th>
                    <th class="pb-3 font-medium text-right">Harga</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[0.04] text-xs">
                @forelse($topProducts as $prod)
                <tr class="hover:bg-white/[0.02] transition-colors">
                    <td class="py-3.5">
                        <div class="flex items-center gap-2.5">
                            @if($prod->image)
                                <img src="{{ $prod->image }}" alt="{{ $prod->name }}" class="w-8 h-8 rounded-lg object-cover border border-white/[0.08]" onerror="this.src='https://placehold.co/100x100/151926/ffffff?text=Prod';">
                            @else
                                <div class="w-8 h-8 rounded-lg bg-slate-800 border border-white/[0.08] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </div>
                            @endif
                            <div class="truncate max-w-[140px]">
                                <a href="{{ \App\Filament\Resources\ProductResource::getUrl('edit', ['record' => $prod->id]) }}" class="font-semibold text-white hover:text-indigo-400 truncate block" title="{{ $prod->name }}">{{ $prod->name }}</a>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $prod->sku }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 text-slate-400 text-[11px]">
                        {{ $prod->store->name ?? '-' }}
                    </td>
                    <td class="py-3.5 text-[11px]">
                        <span class="px-2 py-0.5 rounded-full {{ $prod->stock > 10 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                            {{ $prod->stock }} unit
                        </span>
                    </td>
                    <td class="py-3.5 font-bold text-white text-right">
                        Rp {{ number_format($prod->price, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-8 text-center text-slate-500 text-xs">
                        Belum ada produk.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

