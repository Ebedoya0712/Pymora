@extends('layouts.app')

@section('title', 'Panel de Administración - Pymora')

@section('content')
<div x-data="{ openTenantModal: false, activeTab: '{{ session('success') ? 'settings' : 'tenants' }}' }" class="space-y-6">

    <!-- Flash Alert Message -->
    @if(session('success'))
    <div class="glass-card p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 text-xs flex items-center justify-between shadow-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
        <button @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
    </div>
    @endif

    <!-- Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <h2 class="text-xl font-bold text-white font-display">Panel de Administración</h2>
            <p class="text-xs text-slate-400 mt-0.5">Gestión de empresas registradas, licencias y parámetros generales.</p>
        </div>
        
        <button @click="openTenantModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-md transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Registrar Empresa
        </button>
    </div>

    <!-- Global Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Tenants -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="text-slate-400 text-xs">Empresas Registradas</div>
            <div class="text-2xl font-bold text-white font-display">{{ $totalTenants }}</div>
            <div class="text-[11px] text-emerald-400">{{ $activeTenants }} Activas</div>
        </div>

        <!-- Card 2: Revenue -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="text-slate-400 text-xs">Ingresos Mensuales</div>
            <div class="text-2xl font-bold text-emerald-400 font-display">${{ number_format($totalMrrUsd, 2) }} <span class="text-xs font-normal text-slate-400">/ mes</span></div>
            <div class="text-[11px] text-slate-400">Suscripciones activas</div>
        </div>

        <!-- Card 3: Tasa BCV -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="text-slate-400 text-xs">Tasa Dólar BCV</div>
            <div class="text-2xl font-bold text-amber-300 font-display">{{ number_format($bcvUsdRate, 2) }} <span class="text-xs font-normal text-slate-400">VES</span></div>
            <div class="text-[11px] text-sky-400 flex items-center gap-1">
                <span>Euro: {{ number_format($bcvEurRate, 2) }} VES</span>
            </div>
        </div>

        <!-- Card 4: Server Status -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="text-slate-400 text-xs">Estado del Servidor</div>
            <div class="text-2xl font-bold text-indigo-400 font-display">100%</div>
            <div class="text-[11px] text-emerald-400">Servidores Activos</div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 text-xs font-medium">
        <button @click="activeTab = 'tenants'" :class="activeTab === 'tenants' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-2 border-b-2 rounded-t-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Empresas ({{ count($tenants) }})
        </button>
        <button @click="activeTab = 'plans'" :class="activeTab === 'plans' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-2 border-b-2 rounded-t-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Planes & Precios
        </button>
        <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-2 border-b-2 rounded-t-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Configuración General
        </button>
    </div>

    <!-- TAB 1: Tenants List Table -->
    <div x-show="activeTab === 'tenants'" class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-white text-sm">Empresas Registradas</h3>
                <p class="text-[11px] text-slate-400">Listado de comercios y su estado actual.</p>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" placeholder="Buscar empresa..." class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 w-52">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="p-3">Empresa / RIF</th>
                        <th class="p-3">Subdominio</th>
                        <th class="p-3">Plan</th>
                        <th class="p-3">Tasa BCV</th>
                        <th class="p-3">Vencimiento</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($tenants as $t)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3">
                            <div class="font-bold text-white">{{ $t->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $t->rif_tax_id }}</div>
                        </td>
                        <td class="p-3 font-mono text-indigo-400">
                            {{ $t->subdomain }}.pymora.com
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                {{ strtoupper($t->plan_tier) }}
                            </span>
                        </td>
                        <td class="p-3 font-mono text-emerald-400">{{ number_format($t->bcv_rate, 2) }} VES</td>
                        <td class="p-3 text-slate-400 font-mono">{{ $t->expires_at ? $t->expires_at->format('Y-m-d') : 'Activo' }}</td>
                        <td class="p-3">
                            @if($t->is_active ?? true)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">ACTIVO</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">SUSPENDIDO</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 text-[10px] rounded border border-slate-700">
                                    Editar
                                </button>
                                <a href="{{ route('dashboard') }}" class="px-2 py-1 bg-indigo-600/30 hover:bg-indigo-600 text-indigo-200 text-[10px] rounded font-semibold">
                                    Ingresar
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: Plans & Pricing -->
    <div x-show="activeTab === 'plans'" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Starter Plan -->
        <div class="glass-card rounded-xl p-5 border border-slate-800 space-y-3">
            <div class="text-xs font-bold text-slate-400 uppercase">Plan Starter</div>
            <div class="text-2xl font-bold text-white">$29 <span class="text-xs text-slate-400">/ mes</span></div>
            <ul class="text-xs text-slate-300 space-y-1.5 pt-2 border-t border-slate-800">
                <li>✓ 1 Sucursal</li>
                <li>✓ 1 Caja POS</li>
                <li>✓ 3 Usuarios</li>
            </ul>
        </div>

        <!-- Pro Plan -->
        <div class="glass-card rounded-xl p-5 border border-indigo-500/40 space-y-3 bg-indigo-950/20">
            <div class="text-xs font-bold text-indigo-400 uppercase">Plan Pro Multi-Sucursal</div>
            <div class="text-2xl font-bold text-white">$79 <span class="text-xs text-slate-400">/ mes</span></div>
            <ul class="text-xs text-slate-300 space-y-1.5 pt-2 border-t border-slate-800">
                <li>✓ Hasta 5 Sucursales</li>
                <li>✓ Cajas Ilimitadas</li>
                <li>✓ Usuarios Ilimitados</li>
                <li>✓ Cotizaciones & Traslados</li>
            </ul>
        </div>

        <!-- Enterprise Plan -->
        <div class="glass-card rounded-xl p-5 border border-slate-800 space-y-3">
            <div class="text-xs font-bold text-purple-400 uppercase">Plan Enterprise</div>
            <div class="text-2xl font-bold text-white">$199 <span class="text-xs text-slate-400">/ mes</span></div>
            <ul class="text-xs text-slate-300 space-y-1.5 pt-2 border-t border-slate-800">
                <li>✓ Sucursales Ilimitadas</li>
                <li>✓ Soporte Dedicado</li>
                <li>✓ Integraciones API</li>
            </ul>
        </div>
    </div>

    <!-- TAB 3: Global Settings -->
    <div x-show="activeTab === 'settings'" class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-900/60 p-4 rounded-xl border border-slate-800">
            <div>
                <h3 class="font-bold text-white text-sm">Configuración General del Sistema</h3>
                <p class="text-xs text-slate-400">Tasas oficiales BCV (Dólar y Euro), impuestos SENIAT y período de prueba.</p>
            </div>

            <!-- Sync DolarApi Direct Form -->
            <form action="{{ route('superadmin.sync-dolarapi') }}" method="POST">
                @csrf
                <button type="submit" class="px-3.5 py-2 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 text-xs font-semibold rounded-lg transition-all flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Sincronizar Tasas con DolarApi
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Section 1: Monedas BCV (Automático) -->
            <div class="glass-card rounded-xl p-5 border border-slate-800 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold">💱</div>
                    <div>
                        <h4 class="font-bold text-white text-xs uppercase tracking-wider">Tasas Oficiales BCV (DolarApi)</h4>
                        <p class="text-[11px] text-slate-400">Valores de referencia obtenidos en tiempo real.</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="bg-slate-900/80 p-3 rounded-lg border border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-slate-400 font-medium block">Dólar BCV Oficial (USD)</span>
                            <span class="text-xl font-bold text-emerald-400 font-mono">{{ number_format($bcvUsdRate, 4) }} VES</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            AUTOMÁTICO
                        </span>
                    </div>

                    <div class="bg-slate-900/80 p-3 rounded-lg border border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-slate-400 font-medium block">Euro BCV Oficial (EUR)</span>
                            <span class="text-xl font-bold text-sky-400 font-mono">{{ number_format($bcvEurRate, 4) }} VES</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-sky-500/20 text-sky-300 border border-sky-500/30 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                            AUTOMÁTICO
                        </span>
                    </div>

                    <p class="text-[11px] text-slate-400 italic">
                        💡 Las tasas monetarias no requieren edición manual ya que se actualizan de forma continua desde la fuente oficial.
                    </p>
                </div>
            </div>

            <!-- Section 2: Impuestos SENIAT & Parámetros SaaS -->
            <form action="{{ route('superadmin.settings.update') }}" method="POST" class="glass-card rounded-xl p-5 border border-slate-800 space-y-4">
                @csrf
                <div class="flex items-center gap-2 border-b border-slate-800 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">⚙️</div>
                    <div>
                        <h4 class="font-bold text-white text-xs uppercase tracking-wider">Parámetros del SaaS & Ley SENIAT</h4>
                        <p class="text-[11px] text-slate-400">Reglas comerciales y periodos de suscripción.</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-slate-300 font-medium mb-1">Impuesto IGTF Ley SENIAT (%)</label>
                        <div class="relative">
                            <input type="text" value="3.00%" disabled class="w-full bg-slate-900/60 border border-slate-800 rounded-lg p-2.5 text-slate-400 font-mono font-bold cursor-not-allowed">
                            <span class="absolute right-3 top-2.5 text-[10px] font-semibold bg-slate-800 text-slate-300 px-2 py-0.5 rounded border border-slate-700">FIJO POR LEY (3%)</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-slate-300 font-medium mb-1">Días de Prueba Gratis (1 Mes = 30 Días)</label>
                        <input type="number" name="trial_days" value="{{ $trialDays }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-indigo-300 font-mono font-bold focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-medium mb-1">Correo Electrónico de Soporte Técnic</label>
                        <input type="email" name="support_email" value="{{ $supportEmail }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-xl shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Guardar Parámetros SaaS
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal for registering new Tenant -->
    <div x-show="openTenantModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-card w-full max-w-lg rounded-xl p-5 space-y-3 border border-slate-700">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                <h3 class="font-bold text-white text-sm">Registrar Empresa</h3>
                <button @click="openTenantModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('superadmin.tenants.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1">Nombre Comercial</label>
                    <input type="text" name="name" required placeholder="Ej: Comercializadora Valera C.A." class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">RIF</label>
                        <input type="text" name="rif_tax_id" required placeholder="J-12345678-0" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Subdominio</label>
                        <input type="text" name="subdomain" required placeholder="valera" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 font-mono focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-400 mb-1">Plan</label>
                        <select name="plan_tier" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
                            <option value="starter">Starter ($29/mes)</option>
                            <option value="pro" selected>Pro Multi-Sucursal ($79/mes)</option>
                            <option value="enterprise">Enterprise ($199/mes)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">Correo Administrador</label>
                        <input type="email" name="email" required placeholder="admin@empresa.com" class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2 text-slate-200 focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="openTenantModal = false" class="px-3 py-1.5 bg-slate-800 text-slate-300 rounded-lg">Cancelar</button>
                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white font-semibold rounded-lg">Crear Empresa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
