<x-filament-panels::page class="IAKN-dashboard">
    <!-- Tailwind CSS CDN and custom styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            950: '#090b10',
                            900: '#0d0f17',
                            850: '#111420',
                            800: '#151926',
                            750: '#1a1f30',
                            700: '#22283d',
                            600: '#2f3752',
                        },
                        aura: {
                            blue: '#2563eb',
                            cyan: '#06b6d4',
                            amber: '#f59e0b',
                            purple: '#a855f7',
                            emerald: '#10b981',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .aura-dashboard {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #090b10 !important;
            color: #e2e8f0 !important;
        }
        .aura-card {
            background: linear-gradient(180deg, rgba(21, 25, 38, 0.9) 0%, rgba(17, 20, 32, 0.98) 100%);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }
        .aura-glow-blue {
            background: radial-gradient(circle at 80% 20%, rgba(37, 99, 235, 0.18) 0%, rgba(17, 20, 32, 0) 70%);
        }
        .aura-glow-amber {
            background: radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.18) 0%, rgba(17, 20, 32, 0) 70%);
        }
        .aura-glow-purple {
            background: radial-gradient(circle at 80% 20%, rgba(168, 85, 247, 0.18) 0%, rgba(17, 20, 32, 0) 70%);
        }
        .aura-glow-emerald {
            background: radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.18) 0%, rgba(17, 20, 32, 0) 70%);
        }
        .fi-page-header {
            display: none !important;
        }
    </style>

    <div class="space-y-6 -mt-6">
        <!-- TOP ACTION BAR -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pb-2 border-b border-white/[0.04]">
            <!-- Search Box -->
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" placeholder="Search" class="w-full bg-[#131622] text-slate-300 text-xs rounded-xl pl-10 pr-4 py-2 border border-white/[0.06] focus:outline-none focus:border-blue-500/50 transition-colors placeholder:text-slate-500">
            </div>

            <!-- Right Controls -->
            <div class="flex items-center gap-3 self-end sm:self-auto">
                <button type="button" class="text-xs text-slate-400 hover:text-white font-medium px-3 py-1.5 rounded-lg hover:bg-white/[0.04] transition-colors">
                    Notifications
                </button>

                <!-- Bell with Notification Badge -->
                <button type="button" class="relative p-2 text-slate-400 hover:text-white rounded-xl bg-[#131622] border border-white/[0.06] hover:bg-white/[0.05] transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border-2 border-[#090b10]">5</span>
                </button>

                <!-- Add New Product Button -->
                <button type="button" class="flex items-center gap-2 bg-[#2563eb] hover:bg-blue-600 active:scale-[0.98] text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-lg shadow-blue-600/30 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Add New Product</span>
                </button>
            </div>
        </div>

        <!-- ROW 1: 4 TOP STAT CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Sales -->
            <div class="aura-card aura-glow-blue rounded-2xl p-5 relative overflow-hidden flex flex-col justify-between h-36">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-400">Total Sales</span>
                    <span class="text-[11px] font-semibold text-blue-400 bg-blue-500/15 border border-blue-500/20 px-2 py-0.5 rounded-full">+15%</span>
                </div>
                <div class="flex items-end justify-between mt-2">
                    <div>
                        <div class="text-2xl font-bold text-white tracking-tight">$125,480</div>
                        <div class="text-[11px] font-medium text-emerald-400 mt-1 flex items-center gap-1">
                            <span>▲</span> +15.2%
                        </div>
                    </div>
                    <!-- Glowing Wave Sparkline -->
                    <div class="w-28 h-12">
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
                    <span class="text-xs font-medium text-slate-400">Total Orders</span>
                    <span class="text-[11px] font-semibold text-amber-400 bg-amber-500/15 border border-amber-500/20 px-2 py-0.5 rounded-full">-8.1%</span>
                </div>
                <div class="flex items-end justify-between mt-2">
                    <div>
                        <div class="text-2xl font-bold text-white tracking-tight">2,345</div>
                        <div class="text-[11px] font-medium text-amber-400 mt-1 flex items-center gap-1">
                            <span>▲</span> +8.1%
                        </div>
                    </div>
                    <!-- Amber Wave Sparkline -->
                    <div class="w-28 h-12">
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
                    <span class="text-xs font-medium text-slate-400">New Customers</span>
                    <span class="text-[11px] font-semibold text-purple-400 bg-purple-500/15 border border-purple-500/20 px-2 py-0.5 rounded-full">+3.5%</span>
                </div>
                <div class="flex items-end justify-between mt-2">
                    <div>
                        <div class="text-2xl font-bold text-white tracking-tight">452</div>
                        <div class="text-[11px] font-medium text-purple-400 mt-1 flex items-center gap-1">
                            <span>▲</span> +3.5%
                        </div>
                    </div>
                    <!-- Purple Wave Sparkline -->
                    <div class="w-28 h-12">
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
                    <span class="text-xs font-medium text-slate-400">Average Order Value</span>
                    <span class="text-[11px] font-semibold text-emerald-400 bg-emerald-500/15 border border-emerald-500/20 px-2 py-0.5 rounded-full">+2.1%</span>
                </div>
                <div class="flex items-end justify-between mt-2">
                    <div>
                        <div class="text-2xl font-bold text-white tracking-tight">$53.50</div>
                        <div class="text-[11px] font-medium text-emerald-400 mt-1 flex items-center gap-1">
                            <span>▲</span> +2.1%
                        </div>
                    </div>
                    <!-- Emerald Wave Sparkline -->
                    <div class="w-28 h-12">
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

        <!-- ROW 2: SALES OVERVIEW + RECENT ACTIVITY -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Sales Overview (Left, 8 Cols) -->
            <div class="lg:col-span-8 aura-card rounded-2xl p-6 flex flex-col justify-between">
                <!-- Card Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-white tracking-tight">Sales Overview</h2>
                        <div class="flex items-center gap-1 text-slate-400 text-xs mt-0.5 cursor-pointer hover:text-slate-200">
                            <span>Last 30 Days</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#141826] border border-white/[0.06] text-xs text-slate-300">
                            <span class="text-slate-500 font-medium">Date</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Oct 1 - Oct 31</span>
                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>

                        <button type="button" class="px-3 py-1.5 rounded-xl bg-cyan-500/15 text-cyan-400 border border-cyan-500/25 text-xs font-semibold flex items-center gap-1.5 hover:bg-cyan-500/20 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Date</span>
                        </button>
                    </div>
                </div>

                <!-- KPI Indicators -->
                <div class="flex items-center gap-8 mt-4 mb-2">
                    <div>
                        <div class="flex items-center gap-1.5 text-xs text-blue-400 font-medium">
                            <span class="w-2 h-2 rounded-full bg-blue-500 shadow-sm shadow-blue-500"></span>
                            <span>Revenue</span>
                        </div>
                        <div class="text-xl font-bold text-white mt-0.5">$150k</div>
                    </div>

                    <div>
                        <div class="flex items-center gap-1.5 text-xs text-orange-400 font-medium">
                            <span class="w-2 h-2 rounded-full bg-orange-500 shadow-sm shadow-orange-500"></span>
                            <span>Orders</span>
                        </div>
                        <div class="text-xl font-bold text-white mt-0.5">2.5k</div>
                    </div>
                </div>

                <!-- Main Area Spline Chart -->
                <div class="relative w-full h-56 mt-2">
                    <!-- Y-Axis Labels -->
                    <div class="absolute left-0 top-0 bottom-6 flex flex-col justify-between text-[11px] text-slate-500 select-none">
                        <span>2.50k</span>
                        <span>2.5k</span>
                        <span>$150k</span>
                    </div>

                    <!-- Interactive SVG Chart -->
                    <div class="ml-12 h-full flex flex-col justify-between">
                        <svg class="w-full h-44 overflow-visible" viewBox="0 0 700 160" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="chartAreaGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.38" />
                                    <stop offset="60%" stop-color="#2563eb" stop-opacity="0.12" />
                                    <stop offset="100%" stop-color="#1d4ed8" stop-opacity="0.0" />
                                </linearGradient>
                                <linearGradient id="chartAreaSecondary" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#06b6d4" stop-opacity="0.2" />
                                    <stop offset="100%" stop-color="#06b6d4" stop-opacity="0.0" />
                                </linearGradient>
                            </defs>

                            <!-- Horizontal Grid Guide Lines -->
                            <line x1="0" y1="20" x2="700" y2="20" stroke="rgba(255,255,255,0.04)" stroke-dasharray="4 4" />
                            <line x1="0" y1="80" x2="700" y2="80" stroke="rgba(255,255,255,0.04)" stroke-dasharray="4 4" />
                            <line x1="0" y1="140" x2="700" y2="140" stroke="rgba(255,255,255,0.04)" />

                            <!-- Secondary Cyan Flow Curve -->
                            <path d="M 0 130 C 50 140, 90 90, 140 100 C 190 110, 230 70, 280 85 C 330 100, 380 60, 430 75 C 480 90, 520 40, 570 55 C 620 70, 660 120, 700 115"
                                  fill="none" stroke="#0ea5e9" stroke-width="2" stroke-opacity="0.5" stroke-dasharray="3 3" />

                            <!-- Primary Blue Area Fill -->
                            <path d="M 0 120 C 45 130, 80 85, 130 90 C 180 95, 220 50, 270 58 C 320 65, 360 95, 410 88 C 460 80, 500 20, 550 25 C 600 30, 650 105, 700 95 L 700 160 L 0 160 Z"
                                  fill="url(#chartAreaGradient)" />

                            <!-- Primary Glowing Wave Line -->
                            <path d="M 0 120 C 45 130, 80 85, 130 90 C 180 95, 220 50, 270 58 C 320 65, 360 95, 410 88 C 460 80, 500 20, 550 25 C 600 30, 650 105, 700 95"
                                  fill="none" stroke="#3b82f6" stroke-width="3" stroke-linecap="round" />

                            <!-- Data Points on Peaks -->
                            <circle cx="270" cy="58" r="4.5" fill="#3b82f6" stroke="#ffffff" stroke-width="2" class="cursor-pointer" />
                            <circle cx="550" cy="25" r="5" fill="#60a5fa" stroke="#ffffff" stroke-width="2.5" class="cursor-pointer" />
                        </svg>

                        <!-- X-Axis Dates -->
                        <div class="flex justify-between text-[11px] text-slate-500 pt-2 select-none border-t border-white/[0.04]">
                            <span>07</span><span>05</span><span>09</span><span>10</span><span>12</span><span>13</span><span>15</span><span>16</span><span>17</span><span>18</span><span>19</span><span>20</span><span>21</span><span>22</span><span>31</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity (Right, 4 Cols) -->
            <div class="lg:col-span-4 aura-card rounded-2xl p-6 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-white tracking-tight">Recent Activity</h2>
                </div>

                <!-- Activity List -->
                <div class="space-y-3 my-auto">
                    <!-- Item 1: Blue Icon -->
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/[0.04] hover:bg-white/[0.04] transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-500/15 border border-blue-500/25 text-blue-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white leading-tight">New Order #9872</span>
                                <span class="text-[11px] text-slate-400 leading-tight mt-0.5 truncate max-w-[130px]">New Order #9872 - $135 Order ...</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-white">$135</span>
                    </div>

                    <!-- Item 2: Amber Icon -->
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/[0.04] hover:bg-white/[0.04] transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/25 text-amber-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white leading-tight">New Order #9872</span>
                                <span class="text-[11px] text-slate-400 leading-tight mt-0.5 truncate max-w-[130px]">New Order #9872 - $165 Order ...</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-white">$135</span>
                    </div>

                    <!-- Item 3: Orange/Red Icon -->
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/[0.04] hover:bg-white/[0.04] transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-500/15 border border-orange-500/25 text-orange-400 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white leading-tight">New Order #9872</span>
                                <span class="text-[11px] text-slate-400 leading-tight mt-0.5 truncate max-w-[130px]">New Order #9872 - Botificador ...</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-white">$135</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROW 3: ORDER MANAGEMENT TABLE + TOP SELLING PRODUCTS -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Order Management Table (Left, 7 Cols) -->
            <div class="lg:col-span-7 aura-card rounded-2xl p-6 overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-white tracking-tight">Order Management</h2>
                    <a href="#" class="text-xs text-slate-400 hover:text-white transition-colors">10 Order all</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/[0.06] text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="pb-3 font-medium">Order ID</th>
                                <th class="pb-3 font-medium">Customer</th>
                                <th class="pb-3 font-medium">Date</th>
                                <th class="pb-3 font-medium">Status</th>
                                <th class="pb-3 font-medium">Total</th>
                                <th class="pb-3 font-medium">Payment</th>
                                <th class="pb-3 font-medium text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.04] text-xs">
                            <!-- Row 1 -->
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3.5 font-bold text-white">#9872</td>
                                <td class="py-3.5 text-slate-300">Liam K.</td>
                                <td class="py-3.5 text-slate-400 text-[11px]">Oct 31, 10:45 AM</td>
                                <td class="py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">Shipped</span>
                                </td>
                                <td class="py-3.5 font-semibold text-white">$249.00</td>
                                <td class="py-3.5 text-slate-400">$55.50</td>
                                <td class="py-3.5 text-right">
                                    <a href="#" class="text-blue-400 hover:text-blue-300 font-medium text-[11px]">View/Manage</a>
                                </td>
                            </tr>
                            <!-- Row 2 -->
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3.5 font-bold text-white">#9863</td>
                                <td class="py-3.5 text-slate-300">Liam K.</td>
                                <td class="py-3.5 text-slate-400 text-[11px]">Oct 31, 10:45 AM</td>
                                <td class="py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-500/15 text-amber-400 border border-amber-500/20">Processing</span>
                                </td>
                                <td class="py-3.5 font-semibold text-white">$99.00</td>
                                <td class="py-3.5 text-slate-400">$88.00</td>
                                <td class="py-3.5 text-right">
                                    <a href="#" class="text-blue-400 hover:text-blue-300 font-medium text-[11px]">View/Manage</a>
                                </td>
                            </tr>
                            <!-- Row 3 -->
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3.5 font-bold text-white">#9863</td>
                                <td class="py-3.5 text-slate-300">Emma W.</td>
                                <td class="py-3.5 text-slate-400 text-[11px]">Oct 31, 10:45 AM</td>
                                <td class="py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-500/15 text-purple-400 border border-purple-500/20">Processing</span>
                                </td>
                                <td class="py-3.5 font-semibold text-white">$99.00</td>
                                <td class="py-3.5 text-slate-400">$45.00</td>
                                <td class="py-3.5 text-right">
                                    <a href="#" class="text-blue-400 hover:text-blue-300 font-medium text-[11px]">View/Manage</a>
                                </td>
                            </tr>
                            <!-- Row 4 -->
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3.5 font-bold text-white">#9863</td>
                                <td class="py-3.5 text-slate-300">Emma W.</td>
                                <td class="py-3.5 text-slate-400 text-[11px]">Oct 31, 10:45 AM</td>
                                <td class="py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-teal-500/15 text-teal-400 border border-teal-500/20">Delivered</span>
                                </td>
                                <td class="py-3.5 font-semibold text-white">$53.50</td>
                                <td class="py-3.5 text-slate-400">$45.50</td>
                                <td class="py-3.5 text-right">
                                    <a href="#" class="text-blue-400 hover:text-blue-300 font-medium text-[11px]">View/Manage</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Selling Products Table (Right, 5 Cols) -->
            <div class="lg:col-span-5 aura-card rounded-2xl p-6 overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-white tracking-tight">Top Selling Products</h2>
                    <a href="#" class="text-xs text-blue-400 hover:text-blue-300 transition-colors">View all</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/[0.06] text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="pb-3 font-medium">Product Name</th>
                                <th class="pb-3 font-medium">Category</th>
                                <th class="pb-3 font-medium">Price</th>
                                <th class="pb-3 font-medium">Stock</th>
                                <th class="pb-3 font-medium text-right">Sales</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.04] text-xs">
                            <!-- Product 1 -->
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-slate-800 border border-white/[0.08] flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z"/></svg>
                                        </div>
                                        <span class="font-semibold text-white truncate max-w-[130px]" title="Zenith Noise-Canceling Headphones">Zenith Noise-Canceling...</span>
                                    </div>
                                </td>
                                <td class="py-3.5 text-slate-400 text-[11px]">Image</td>
                                <td class="py-3.5 font-semibold text-white">$249.00</td>
                                <td class="py-3.5 text-slate-400 text-[11px]">65 left</td>
                                <td class="py-3.5 font-bold text-white text-right">987</td>
                            </tr>

                            <!-- Product 2 -->
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-slate-800 border border-white/[0.08] flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <span class="font-semibold text-white truncate max-w-[130px]" title="Nova Wireless Keyboard">Nova Wireless Keyboard</span>
                                    </div>
                                </td>
                                <td class="py-3.5 text-slate-400 text-[11px]">Category</td>
                                <td class="py-3.5 font-semibold text-white">$99.00</td>
                                <td class="py-3.5 text-slate-400 text-[11px]">102 left</td>
                                <td class="py-3.5 font-bold text-white text-right">645</td>
                            </tr>

                            <!-- Product 3 -->
                            <tr class="hover:bg-white/[0.02] transition-colors">
                                <td class="py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-slate-800 border border-white/[0.08] flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <span class="font-semibold text-white truncate max-w-[130px]" title="Aura Smartwatch Series 5">Aura Smartwatch Series 5</span>
                                    </div>
                                </td>
                                <td class="py-3.5 text-slate-400 text-[11px]">Aura Smart</td>
                                <td class="py-3.5 font-semibold text-white">$199.00</td>
                                <td class="py-3.5 text-slate-400 text-[11px]">48 left</td>
                                <td class="py-3.5 font-bold text-white text-right">521</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
