<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pymora - Sistema Administrativo & Financiero SaaS')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN for instant rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                        emeraldCustom: '#10b981',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Outfit', sans-serif; }
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card-hover:hover {
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex flex-col antialiased">

    <!-- Top Header Ticker -->
    <header class="bg-slate-900/90 border-b border-slate-800 backdrop-blur px-4 py-2 text-xs flex items-center justify-between gap-3 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            @if(session('user_role') === 'super_admin')
                <div class="flex items-center gap-2 text-slate-300 font-semibold text-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Panel de Control</span>
                </div>
            @else
                <div class="hidden sm:flex items-center gap-2 bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700">
                    <span class="text-slate-400">Empresa:</span>
                    <span class="font-semibold text-indigo-400">{{ session('company_name', 'Bodega & Abasto El Sol C.A.') }}</span>
                    <span class="bg-indigo-500/20 text-indigo-300 text-[10px] px-1.5 py-0.5 rounded font-mono font-semibold uppercase">PRO PLAN</span>
                </div>
                <div class="hidden lg:flex items-center gap-2 bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700">
                    <span class="text-slate-400">Sucursal:</span>
                    <span class="font-medium text-slate-200">Altamira Principal</span>
                </div>
            @endif
        </div>

        <div x-data="dolarRates()" x-init="fetchRates()" class="flex items-center gap-4 text-xs font-mono">
            <div class="flex items-center gap-2 bg-slate-800/90 px-3 py-1 rounded-md border border-indigo-500/30">
                <span class="text-indigo-400 font-sans font-medium">Dólar BCV:</span>
                <span class="font-bold text-emerald-400 flex items-center gap-1.5">
                    <span x-text="usdRate ? usdRate + ' VES' : 'Cargando...'">764.35 VES</span>
                    <span title="DolarApi en vivo" class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                </span>
            </div>
            <div class="hidden md:flex items-center gap-2 bg-slate-800/90 px-3 py-1 rounded-md border border-slate-700">
                <span class="text-slate-400 font-sans">Euro BCV:</span>
                <span class="font-bold text-sky-400" x-text="eurRate ? eurRate + ' VES' : '882.30 VES'">882.30 VES</span>
            </div>
            <div class="hidden md:flex items-center gap-2 bg-slate-800/90 px-3 py-1 rounded-md border border-slate-700">
                <span class="text-slate-400 font-sans">IGTF:</span>
                <span class="font-bold text-slate-200">3.00%</span>
            </div>
        </div>
    </header>

    <script>
        function dolarRates() {
            return {
                usdRate: null,
                eurRate: null,
                async fetchRates() {
                    try {
                        const [usdRes, eurRes] = await Promise.all([
                            fetch('https://ve.dolarapi.com/v1/dolares/oficial'),
                            fetch('https://ve.dolarapi.com/v1/euros/oficial')
                        ]);
                        const usdData = await usdRes.json();
                        const eurData = await eurRes.json();
                        
                        if (usdData && usdData.promedio) {
                            this.usdRate = Number(usdData.promedio).toFixed(2);
                            window.liveBcvRate = Number(usdData.promedio);
                        }
                        if (eurData && eurData.promedio) {
                            this.eurRate = Number(eurData.promedio).toFixed(2);
                            window.liveEurRate = Number(eurData.promedio);
                        }
                    } catch (e) {
                        console.log('Error conectando a DolarApi:', e);
                        this.usdRate = '764.35';
                        this.eurRate = '882.30';
                    }
                }
            }
        }
    </script>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between hidden md:flex">
            <div class="p-4 space-y-6">
                <!-- App Logo -->
                <div class="flex items-center justify-between px-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center font-bold text-white text-xl shadow-lg shadow-indigo-500/20 font-display">
                            P
                        </div>
                        <div>
                            <h1 class="font-bold text-lg text-white font-display leading-tight">Pymora</h1>
                            <p class="text-[11px] text-slate-400">Sistema Administrativo</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                @php
                    $role = session('user_role', 'owner');
                @endphp
                <nav class="space-y-1 text-sm font-medium">
                    @if($role === 'super_admin')
                        <!-- Super Admin Specific Menu -->
                        <div class="px-3 py-1.5 text-[10px] font-semibold text-indigo-400 uppercase tracking-wider">Gestión Global SaaS</div>

                        <a href="{{ route('superadmin.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('superadmin.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Inquilinos & Empresas
                            </span>
                            <span class="bg-indigo-500/20 text-indigo-300 text-[10px] px-1.5 py-0.5 rounded font-mono">CORE</span>
                        </a>

                        <div class="px-3 py-1.5 text-[10px] font-semibold text-slate-500 uppercase tracking-wider pt-3">Vista Previa Inquilino</div>
                    @endif

                    @if(in_array($role, ['super_admin', 'owner', 'tenant_admin']))
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Resumen CFO / Dashboard
                    </a>
                    @endif

                    @if(in_array($role, ['super_admin', 'owner', 'tenant_admin', 'cashier']))
                    <a href="{{ route('pos.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('pos.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                            Punto de Venta (POS)
                        </span>
                        <span class="bg-emerald-500/20 text-emerald-300 text-[10px] px-1.5 py-0.5 rounded font-mono">LIVE</span>
                    </a>
                    @endif

                    @if(in_array($role, ['super_admin', 'owner', 'tenant_admin', 'warehouse_manager']))
                    <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('inventory.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Inventario Inteligente
                    </a>
                    @endif

                    @if(in_array($role, ['super_admin', 'owner', 'tenant_admin']))
                    <a href="{{ route('cashbank.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('cashbank.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Caja & Bancos
                    </a>

                    <a href="{{ route('cxc.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('cxc.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Cuentas por Cobrar (CXC)
                    </a>
                    @endif

                    @if(in_array($role, ['super_admin', 'owner', 'tenant_admin', 'cashier']))
                    <a href="{{ route('quotes.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('quotes.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Cotizaciones Workflow
                        </span>
                    </a>
                    @endif

                    @if(in_array($role, ['super_admin', 'owner', 'tenant_admin', 'warehouse_manager']))
                    <a href="{{ route('transfers.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('transfers.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Traslados Multi-Sucursal
                        </span>
                    </a>
                    @endif

                    @if(in_array($role, ['super_admin', 'owner', 'tenant_admin']))
                    <a href="{{ route('reports.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('reports.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            SENIAT IVA / Comisiones
                        </span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- User Info & Logout Footer -->
            <div class="p-4 border-t border-slate-800 flex items-center justify-between bg-slate-900/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white text-xs">
                        {{ strtoupper(substr(session('user_name', 'Eliecer Admin'), 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-200">{{ session('user_name', 'Eliecer (Owner)') }}</div>
                        <div class="text-[10px] text-slate-400 font-mono uppercase">{{ session('user_role', 'owner') }}</div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Cerrar Sesión" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-slate-950 p-4 md:p-6">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
