<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pymora - Sistema Administrativo & Financiero')</title>
    <!-- Favicon Pymora Emblem Logo -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
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
        [x-cloak] { display: none !important; }
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

    <!-- Audit Mode Active Banner -->
    @if(session('is_impersonating'))
        <div class="bg-gradient-to-r from-amber-600 via-indigo-600 to-purple-600 text-white text-xs font-semibold px-4 py-2 flex items-center justify-between shadow-lg z-[60] relative">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
                <span>🔍 MODO AUDITORÍA EN VIVO SUPER ADMIN: Estás inspeccionando las ventas, ganancias, productos e inventario de <strong>{{ session('company_name') }}</strong></span>
            </div>
            <a href="{{ route('superadmin.stop-impersonating') }}" class="bg-slate-950 hover:bg-slate-900 text-amber-300 font-bold px-3.5 py-1.5 rounded-lg border border-amber-400/40 transition-colors shadow flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                <span>Finalizar Auditoría & Volver</span>
            </a>
        </div>
    @endif

    <!-- Top Header Ticker -->
    <header class="bg-slate-900/90 border-b border-slate-800 backdrop-blur px-4 py-2 text-xs flex items-center justify-between gap-3 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            @if(session('user_role') !== 'super_admin' || session('is_impersonating'))
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

        @php
            $ratesData = \App\Services\DolarApiService::getRates();
            $bcvUsdLive = number_format((float)\App\Models\GlobalSetting::get('bcv_usd_rate', $ratesData['bcv_usd']), 2);
            $bcvEurLive = number_format((float)\App\Models\GlobalSetting::get('bcv_eur_rate', $ratesData['bcv_eur']), 2);
        @endphp
        <div class="flex items-center gap-4 text-xs font-mono">
            <div class="flex items-center gap-2 bg-slate-800/90 px-3 py-1 rounded-md border border-indigo-500/30">
                <span class="text-indigo-400 font-sans font-medium">Dólar BCV:</span>
                <span class="font-bold text-emerald-400 flex items-center gap-1.5">
                    <span>{{ $bcvUsdLive }} VES</span>
                    <span title="BCV Oficial DolarApi En Vivo" class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                </span>
            </div>
            <div class="hidden md:flex items-center gap-2 bg-slate-800/90 px-3 py-1 rounded-md border border-slate-700">
                <span class="text-slate-400 font-sans">Euro BCV:</span>
                <span class="font-bold text-sky-400">{{ $bcvEurLive }} VES</span>
            </div>
            <div class="hidden md:flex items-center gap-2 bg-slate-800/90 px-3 py-1 rounded-md border border-slate-700" title="Impuesto a las Grandes Transacciones Financieras (SENIAT) sobre cobros en divisas / efectivo">
                <span class="text-slate-400 font-sans">IGTF:</span>
                <span class="font-bold text-slate-200">3.00%</span>
            </div>
        </div>
    </header>

    @if(session('superadmin_impersonating'))
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-amber-600 px-4 py-2 text-white text-xs font-semibold flex items-center justify-between shadow-lg">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-amber-300 animate-ping"></span>
            <span>🔍 MODO AUDITORÍA SUPER ADMIN: Estás previsualizando la vista de <strong>{{ session('tenant_name') }}</strong></span>
        </div>
        <form action="{{ route('superadmin.stop-impersonation') }}" method="POST">
            @csrf
            <button type="submit" class="px-3 py-1 bg-white/20 hover:bg-white text-white hover:text-slate-900 rounded font-bold transition-all text-[11px]">
                Salir de Modo Auditoría &rarr;
            </button>
        </form>
    </div>
    @endif

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
                        <div class="relative w-10 h-10 rounded-xl bg-slate-900 border border-white/20 p-2 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <svg class="w-full h-full" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="logoGradPrimarySidebar" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#818CF8" />
                                        <stop offset="50%" stop-color="#6366F1" />
                                        <stop offset="100%" stop-color="#A855F7" />
                                    </linearGradient>
                                    <linearGradient id="logoGradAccentSidebar" x1="0%" y1="100%" x2="100%" y2="0%">
                                        <stop offset="0%" stop-color="#38BDF8" />
                                        <stop offset="100%" stop-color="#34D399" />
                                    </linearGradient>
                                </defs>
                                <path d="M12 8C12 5.79086 13.7909 4 16 4H28C34.6274 4 40 9.37258 40 16C40 22.6274 34.6274 28 28 28H20V40C20 42.2091 18.2091 44 16 44C13.7909 44 12 42.2091 12 40V8Z" fill="url(#logoGradPrimarySidebar)"/>
                                <path d="M20 12H27.5C29.9853 12 32 14.0147 32 16.5C32 18.9853 29.9853 21 27.5 21H20V12Z" fill="#0F172A" />
                                <path d="M26 32L32 26L36 30L42 24" stroke="url(#logoGradAccentSidebar)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M37 24H42V29" stroke="url(#logoGradAccentSidebar)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
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
                    $isImpersonating = session('is_impersonating', false);
                    $showTenantModules = ($role !== 'super_admin') || $isImpersonating;
                @endphp
                <nav class="space-y-1 text-sm font-medium">
                    @if($role === 'super_admin' && !$isImpersonating)
                        <!-- Super Admin Specific Dedicated Menu -->
                        <div class="px-3 py-1.5 text-[10px] font-semibold text-indigo-400 uppercase tracking-wider">Gestión Global Pymora</div>

                        <!-- 1. Dashboard SuperAdmin -->
                        <a href="{{ route('superadmin.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('superadmin.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span>Dashboard</span>
                        </a>

                        <!-- 2. Módulo Empresas -->
                        <a href="{{ route('superadmin.empresas') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('superadmin.empresas') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>Empresas</span>
                        </a>

                        <!-- 3. Módulo Mis Finanzas -->
                        <div class="space-y-1">
                            <a href="{{ route('superadmin.finanzas') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('superadmin.finanzas') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Mis Finanzas</span>
                            </a>
                            <div class="pl-9 space-y-1 text-xs">
                                <a href="{{ route('superadmin.finanzas') }}" class="flex items-center gap-2 py-1 px-2 rounded hover:bg-slate-800 {{ request()->routeIs('superadmin.finanzas') && !request()->has('tab') ? 'text-emerald-400 font-semibold bg-emerald-500/10' : 'text-slate-300 hover:text-emerald-400' }} transition-colors group">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    <span>Ganancias</span>
                                </a>
                                <a href="{{ route('superadmin.comprobantes') }}" class="flex items-center gap-2 py-1 px-2 rounded hover:bg-slate-800 {{ request()->routeIs('superadmin.comprobantes') ? 'text-emerald-400 font-semibold bg-emerald-500/10' : 'text-slate-400 hover:text-emerald-400' }} transition-colors group">
                                    <svg class="w-3.5 h-3.5 {{ request()->routeIs('superadmin.comprobantes') ? 'text-emerald-400' : 'text-slate-400 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Comprobantes de pago</span>
                                </a>
                                <a href="{{ route('superadmin.planes') }}" class="flex items-center gap-2 py-1 px-2 rounded hover:bg-slate-800 {{ request()->routeIs('superadmin.planes') ? 'text-emerald-400 font-semibold bg-emerald-500/10' : 'text-slate-400 hover:text-emerald-400' }} transition-colors group">
                                    <svg class="w-3.5 h-3.5 {{ request()->routeIs('superadmin.planes') ? 'text-emerald-400' : 'text-slate-400 group-hover:text-emerald-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h10"/></svg>
                                    <span>Planes & Tarifas</span>
                                </a>
                            </div>
                        </div>

                        <!-- 4. Módulo Usuarios y Roles -->
                        <a href="{{ route('superadmin.users') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('superadmin.users') ? 'bg-purple-600/20 text-purple-400 border border-purple-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Usuarios y Roles</span>
                        </a>

                        <!-- 5. Módulo Configuración -->
                        <a href="{{ route('superadmin.configuracion') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('superadmin.configuracion') ? 'bg-amber-600/20 text-amber-400 border border-amber-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Configuración</span>
                        </a>
                    @endif

                    @if($showTenantModules)
                        @php
                            $currentTenantObj = \App\Models\Tenant::current();
                            $bTypeKey = session('business_type') ?? ($currentTenantObj->business_type ?? 'abasto');
                            $bMeta = \App\Models\Tenant::getBusinessTypes()[$bTypeKey] ?? \App\Models\Tenant::getBusinessTypes()['abasto'];

                            $posLabel = match($bTypeKey) {
                                'restaurante' => 'Mesas & Comandero (POS)',
                                'carniceria_hortalizas' => 'Punto de Venta (Báscula/Kg)',
                                'tecnologia_electro' => 'Punto de Venta (Seriales/IMEI)',
                                'servicios' => 'Agenda de Citas & POS',
                                default => 'Punto de Venta (POS)',
                            };

                            $invLabel = match($bTypeKey) {
                                'restaurante' => 'Inventario & Recetas',
                                'fabricante' => 'Insumos & Recetas BOM',
                                'ropa' => 'Inventario (Tallas & Colores)',
                                'carniceria_hortalizas' => 'Inventario (Kilos & Mermas)',
                                'tecnologia_electro' => 'Stock (Seriales & Garantías)',
                                'repuestos' => 'Catálogo Marca/Modelo/Año',
                                'servicios' => 'Órdenes de Trabajo & Servicios',
                                default => 'Inventario Inteligente',
                            };
                        @endphp

                        @if(in_array($role, ['super_admin', 'owner', 'tenant_admin']) || $isImpersonating)
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span>Dashboard {{ $bMeta['name'] }}</span>
                        </a>
                        @endif

                        @if(in_array($role, ['super_admin', 'owner', 'tenant_admin', 'cashier']) || $isImpersonating)
                        <a href="{{ route('pos.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('pos.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                <span>{{ $posLabel }}</span>
                            </span>
                            <span class="bg-emerald-500/20 text-emerald-300 text-[10px] px-1.5 py-0.5 rounded font-mono">{{ $bMeta['icon'] }}</span>
                        </a>
                        @endif

                        @if(in_array($role, ['super_admin', 'owner', 'tenant_admin', 'warehouse_manager']) || $isImpersonating)
                        <a href="{{ route('inventory.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('inventory.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span>{{ $invLabel }}</span>
                        </a>
                        @endif

                        @if(in_array($role, ['super_admin', 'owner', 'tenant_admin']) || $isImpersonating)
                        <a href="{{ route('cashbank.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('cashbank.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Caja, Devoluciones & Bancos</span>
                        </a>

                        <a href="{{ route('cxc.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('cxc.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Cuentas por Cobrar & Deudas</span>
                        </a>
                        @endif

                        @if(in_array($role, ['super_admin', 'owner', 'tenant_admin', 'cashier']) || $isImpersonating)
                        <a href="{{ route('quotes.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('quotes.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Cotizaciones & Canales</span>
                            </span>
                        </a>
                        @endif

                        @if(in_array($role, ['super_admin', 'owner', 'tenant_admin', 'warehouse_manager']) || $isImpersonating)
                        <a href="{{ route('transfers.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('transfers.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span>Traslados Multi-Sucursal</span>
                            </span>
                        </a>
                        @endif

                        @if(in_array($role, ['super_admin', 'owner', 'tenant_admin']) || $isImpersonating)
                        <a href="{{ route('reports.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('reports.index') ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Reportes & Comisiones Equipo</span>
                            </span>
                        </a>
                        @endif
                    @endif
                </nav>
            </div>

            <!-- User Info & Logout Footer -->
            <div class="p-4 border-t border-slate-800 flex items-center justify-between bg-slate-900/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center font-bold text-white text-xs">
                        {{ strtoupper(substr(session('user_name', 'Super Admin'), 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-200">{{ session('user_name', 'Super Admin Pymora') }}</div>
                        <div class="text-[10px] text-slate-400 font-medium capitalize">{{ str_replace('_', ' ', session('user_role', 'super_admin')) }}</div>
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
            <!-- Global Auto-Dismiss Notification Toast Banner -->
            @if(session('success') || session('error') || session('warning') || session('info'))
                @php
                    $alertType = session('error') ? 'error' : (session('warning') ? 'warning' : (session('info') ? 'info' : 'success'));
                    $alertMsg = session('success') ?? session('error') ?? session('warning') ?? session('info');
                    $alertBg = [
                        'success' => 'bg-emerald-500/15 border-emerald-500/40 text-emerald-300 shadow-emerald-950/40',
                        'error' => 'bg-rose-500/15 border-rose-500/40 text-rose-300 shadow-rose-950/40',
                        'warning' => 'bg-amber-500/15 border-amber-500/40 text-amber-300 shadow-amber-950/40',
                        'info' => 'bg-indigo-500/15 border-indigo-500/40 text-indigo-300 shadow-indigo-950/40',
                    ][$alertType];
                    $alertIconColor = [
                        'success' => 'text-emerald-400',
                        'error' => 'text-rose-400',
                        'warning' => 'text-amber-400',
                        'info' => 'text-indigo-400',
                    ][$alertType];
                    $barBg = [
                        'success' => 'bg-emerald-400',
                        'error' => 'bg-rose-400',
                        'warning' => 'bg-amber-400',
                        'info' => 'bg-indigo-400',
                    ][$alertType];
                @endphp
                <div x-data="{ show: true, progress: 100, timer: null }"
                     x-init="
                        timer = setInterval(() => {
                            progress -= 1.5;
                            if (progress <= 0) {
                                show = false;
                                clearInterval(timer);
                            }
                        }, 60);
                     "
                     x-show="show"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                     x-cloak
                     class="mb-5 relative overflow-hidden rounded-xl border p-4 text-xs font-semibold shadow-xl flex items-center justify-between gap-3 {{ $alertBg }}">
                    
                    <div class="flex items-center gap-3">
                        @if($alertType === 'success')
                            <svg class="w-5 h-5 {{ $alertIconColor }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($alertType === 'error')
                            <svg class="w-5 h-5 {{ $alertIconColor }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="w-5 h-5 {{ $alertIconColor }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                        <span class="text-sm font-medium leading-tight">{{ $alertMsg }}</span>
                    </div>

                    <button type="button" @click="show = false; clearInterval(timer)" title="Cerrar notificación" class="text-slate-400 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <!-- Auto-Dismiss Progress Bar -->
                    <div class="absolute bottom-0 left-0 h-1 {{ $barBg }} opacity-75 transition-all duration-75" :style="'width: ' + progress + '%'"></div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Global Page Navigation Loader Overlay -->
    <div id="page-loader" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/80 backdrop-blur-md opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="flex flex-col items-center gap-4 p-6 rounded-2xl glass-card border border-white/10 shadow-2xl">
            <!-- Animated Pymora Emblem with Pulse Ring -->
            <div class="relative w-14 h-14 flex items-center justify-center">
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-emerald-400 animate-spin blur-sm opacity-70"></div>
                <div class="relative w-12 h-12 rounded-xl bg-slate-900 border border-white/20 p-2 flex items-center justify-center shadow-xl">
                    <svg class="w-full h-full animate-pulse" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="loaderGradPrimary" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#818CF8" />
                                <stop offset="50%" stop-color="#6366F1" />
                                <stop offset="100%" stop-color="#A855F7" />
                            </linearGradient>
                            <linearGradient id="loaderGradAccent" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#38BDF8" />
                                <stop offset="100%" stop-color="#34D399" />
                            </linearGradient>
                        </defs>
                        <path d="M12 8C12 5.79086 13.7909 4 16 4H28C34.6274 4 40 9.37258 40 16C40 22.6274 34.6274 28 28 28H20V40C20 42.2091 18.2091 44 16 44C13.7909 44 12 42.2091 12 40V8Z" fill="url(#loaderGradPrimary)"/>
                        <path d="M20 12H27.5C29.9853 12 32 14.0147 32 16.5C32 18.9853 29.9853 21 27.5 21H20V12Z" fill="#0F172A" />
                        <path d="M26 32L32 26L36 30L42 24" stroke="url(#loaderGradAccent)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M37 24H42V29" stroke="url(#loaderGradAccent)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-200 font-display tracking-wider">Cargando</span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-bounce"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-bounce [animation-delay:0.2s]"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-bounce [animation-delay:0.4s]"></span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loader = document.getElementById('page-loader');

            function showLoader() {
                if (loader) {
                    loader.classList.remove('opacity-0', 'pointer-events-none');
                    loader.classList.add('opacity-100');
                }
            }

            function hideLoader() {
                if (loader) {
                    loader.classList.remove('opacity-100');
                    loader.classList.add('opacity-0', 'pointer-events-none');
                }
            }

            hideLoader();
            window.addEventListener('pageshow', hideLoader);
            window.addEventListener('load', hideLoader);

            document.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    const href = link.getAttribute('href');
                    const target = link.getAttribute('target');
                    if (e.defaultPrevented || !href || href === '#' || href.startsWith('#') || href.startsWith('javascript:') || target === '_blank' || link.hasAttribute('@click') || link.hasAttribute('x-on:click')) {
                        return;
                    }
                    showLoader();
                });
            });

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    if (form.checkValidity && !form.checkValidity()) {
                        hideLoader();
                        return;
                    }
                    if (!e.defaultPrevented) {
                        showLoader();
                    }
                });
            });
        });
    </script>
</body>
</html>
