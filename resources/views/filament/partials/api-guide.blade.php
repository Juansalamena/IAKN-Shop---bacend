{{-- Partial: PRD Specification & Interactive API Guide --}}
<div id="api-spec" class="aura-card rounded-2xl p-6 border border-cyan-500/20">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-4 border-b border-white/[0.06]">
        <div>
            <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>
                <span>Panduan Endpoint RESTful API Sesuai PRD Multi-Gerai</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Sistem telah siap digunakan oleh frontend/mobile dengan autentikasi Token Bearer (Sanctum) &amp; RBAC.
            </p>
        </div>
        <div class="text-xs font-mono bg-[#141826] px-3 py-1.5 rounded-xl border border-white/[0.08] text-slate-300">
            Base URL: <span class="text-cyan-400 font-semibold">{{ url('/api') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
        <!-- Group 1: Auth & Public -->
        <div class="bg-white/[0.02] border border-white/[0.04] p-4 rounded-xl">
            <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">1. Autentikasi &amp; Publik</h3>
            <ul class="space-y-2 text-xs font-mono text-slate-300">
                <li class="flex items-center gap-2">
                    <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-1.5 py-0.5 rounded font-bold">POST</span>
                    <span>/api/auth/login</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-1.5 py-0.5 rounded font-bold">POST</span>
                    <span>/api/auth/register</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="bg-blue-500/20 text-blue-400 text-[10px] px-1.5 py-0.5 rounded font-bold">GET</span>
                    <span>/api/products</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="bg-blue-500/20 text-blue-400 text-[10px] px-1.5 py-0.5 rounded font-bold">GET</span>
                    <span>/api/stores</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="bg-blue-500/20 text-blue-400 text-[10px] px-1.5 py-0.5 rounded font-bold">GET</span>
                    <span>/api/categories</span>
                </li>
            </ul>
        </div>

        <!-- Group 2: Customer & Checkout -->
        <div class="bg-white/[0.02] border border-white/[0.04] p-4 rounded-xl">
            <h3 class="text-xs font-bold text-amber-400 uppercase tracking-wider mb-3">2. Customer (Split Order)</h3>
            <ul class="space-y-2 text-xs font-mono text-slate-300">
                <li class="flex items-center gap-2">
                    <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-1.5 py-0.5 rounded font-bold">POST</span>
                    <span class="text-amber-300 font-bold">/api/orders/checkout</span>
                </li>
                <li class="text-[11px] text-slate-400 font-sans pl-1">
                    *Otomatis split order jika keranjang berisi item dari &gt;1 gerai.
                </li>
                <li class="flex items-center gap-2 pt-1">
                    <span class="bg-blue-500/20 text-blue-400 text-[10px] px-1.5 py-0.5 rounded font-bold">GET</span>
                    <span>/api/customer/orders</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="bg-blue-500/20 text-blue-400 text-[10px] px-1.5 py-0.5 rounded font-bold">GET</span>
                    <span>/api/customer/orders/{id}</span>
                </li>
            </ul>
        </div>

        <!-- Group 3: Store Admin & Super Admin -->
        <div class="bg-white/[0.02] border border-white/[0.04] p-4 rounded-xl">
            <h3 class="text-xs font-bold text-cyan-400 uppercase tracking-wider mb-3">3. Admin Toko &amp; Super Admin</h3>
            <ul class="space-y-2 text-xs font-mono text-slate-300">
                <li class="flex items-center gap-2">
                    <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-1.5 py-0.5 rounded font-bold">POST</span>
                    <span>/api/store/products</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="bg-amber-500/20 text-amber-400 text-[10px] px-1.5 py-0.5 rounded font-bold">PUT</span>
                    <span>/api/store/orders/{id}/status</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-1.5 py-0.5 rounded font-bold">POST</span>
                    <span>/api/admin/stores</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="bg-emerald-500/20 text-emerald-400 text-[10px] px-1.5 py-0.5 rounded font-bold">POST</span>
                    <span>/api/admin/store-admins</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="bg-blue-500/20 text-blue-400 text-[10px] px-1.5 py-0.5 rounded font-bold">GET</span>
                    <span class="text-cyan-300 font-bold">/api/admin/stores/reports</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Testing Credentials Table -->
    <div class="mt-4 pt-3 border-t border-white/[0.06] flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
        <div class="flex flex-wrap items-center gap-4">
            <span class="font-bold text-slate-300">Akun Uji (Password: <code class="text-amber-400 font-mono">password</code>):</span>
            <span>👑 Super Admin: <code class="text-indigo-400 font-mono">superadmin@iakn.com</code></span>
            <span>🏪 Admin Ambon: <code class="text-emerald-400 font-mono">admin.ambon@iakn.com</code></span>
            <span>🏪 Admin Saparua: <code class="text-cyan-400 font-mono">admin.saparua@iakn.com</code></span>
            <span>👤 Customer: <code class="text-purple-400 font-mono">customer@iakn.com</code></span>
        </div>
    </div>
</div>

