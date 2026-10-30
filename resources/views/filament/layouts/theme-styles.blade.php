{{-- Global Theme & Layout Styles for IAKN Store Multi-Gerai Admin Panel --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false,
        },
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
<script>
    localStorage.setItem("_x_filament:sidebar:open", "false");
    localStorage.setItem("_x_filament:sidebar:desktop_open", "false");
    document.addEventListener("livewire:navigated", () => {
        if (window.Alpine && window.Alpine.store("sidebar")) {
            window.Alpine.store("sidebar").close();
        }
    });
</script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    /* =========================================================
       1. BASE BODY & TYPOGRAPHY
       ========================================================= */
    .fi-body {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        background-color: #090b10 !important;
        background-image: radial-gradient(ellipse at 50% 0%, rgba(30, 27, 75, 0.45) 0%, rgba(9, 11, 16, 0.95) 60%, #090b10 100%) !important;
        color: #e2e8f0 !important;
    }

    /* =========================================================
       2. TOPBAR & LOGO STYLING
       ========================================================= */
    .fi-topbar {
        background: rgba(13, 15, 23, 0.88) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.4) !important;
    }

    .fi-logo {
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
        font-size: 1.35rem !important;
        background: linear-gradient(135deg, #818cf8 0%, #38bdf8 50%, #c084fc 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* =========================================================
       3. HAMBURGER SIDEBAR DRAWER & OVERLAY
       ========================================================= */
    .fi-sidebar:not(.fi-sidebar-open) {
        transform: translateX(-100%) !important;
        translate: -100% 0 !important;
        width: 0 !important;
        min-width: 0 !important;
        max-width: 0 !important;
        overflow: hidden !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        border: none !important;
    }

    .fi-sidebar.fi-sidebar-open {
        position: fixed !important;
        top: var(--topbar-height, 4rem) !important;
        inset-inline-start: 0 !important;
        height: calc(100dvh - var(--topbar-height, 4rem)) !important;
        width: var(--sidebar-width, 18rem) !important;
        transform: translateX(0) !important;
        translate: 0 0 !important;
        visibility: visible !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        z-index: 30 !important;
        background: linear-gradient(180deg, #0f172a 0%, #090b10 100%) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.85) !important;
        transition: transform 0.25s ease, opacity 0.2s ease !important;
    }

    .fi-sidebar-close-overlay:not([style*="display: none"]) {
        display: block !important;
        z-index: 25 !important;
    }

    /* =========================================================
       4. UNIFIED CARDS, SECTIONS, TABLES & FORMS (FILAMENT)
       ========================================================= */
    .aura-card,
    .fi-section,
    .fi-ta-ctn {
        background: linear-gradient(180deg, rgba(21, 25, 38, 0.92) 0%, rgba(15, 18, 29, 0.98) 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.07) !important;
        border-radius: 1rem !important;
        box-shadow: 0 12px 32px -10px rgba(0, 0, 0, 0.55) !important;
    }

    .fi-section-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    }

    .fi-ta-header-ctn,
    .fi-ta-header-toolbar {
        background: rgba(255, 255, 255, 0.015) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    }

    .fi-ta-row:hover {
        background-color: rgba(99, 102, 241, 0.05) !important;
    }

    .fi-input-wrp,
    .fi-select-input {
        background-color: rgba(15, 23, 42, 0.75) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        border-radius: 0.75rem !important;
    }

    .fi-modal-window,
    .fi-dropdown-panel {
        background: #111524 !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 1rem !important;
    }

    /* Glow Utilities */
    .aura-glow-blue {
        background: radial-gradient(circle at 80% 20%, rgba(37, 99, 235, 0.18) 0%, rgba(17, 20, 32, 0) 70%) !important;
    }
    .aura-glow-amber {
        background: radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.18) 0%, rgba(17, 20, 32, 0) 70%) !important;
    }
    .aura-glow-purple {
        background: radial-gradient(circle at 80% 20%, rgba(168, 85, 247, 0.18) 0%, rgba(17, 20, 32, 0) 70%) !important;
    }
    .aura-glow-emerald {
        background: radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.18) 0%, rgba(17, 20, 32, 0) 70%) !important;
    }

    /* =========================================================
       5. LOGIN PAGE STYLING
       ========================================================= */
    .fi-simple-layout {
        background: radial-gradient(ellipse at 50% 0%, #1e1b4b 0%, #0f172a 65%, #020617 100%) !important;
        position: relative;
        min-height: 100vh;
    }
    .fi-simple-layout::before {
        content: "";
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(129, 140, 248, 0.12) 1px, transparent 1px);
        background-size: 28px 28px;
        pointer-events: none;
    }
    .fi-simple-main {
        background: rgba(15, 23, 42, 0.82) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 1.25rem !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(99, 102, 241, 0.15) !important;
        position: relative;
        z-index: 10;
    }
    .fi-simple-header-heading {
        font-weight: 700 !important;
        letter-spacing: -0.025em !important;
    }
</style>

