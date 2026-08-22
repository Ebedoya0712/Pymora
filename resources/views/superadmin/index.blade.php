@extends('layouts.app')

@section('title', 'Panel de Administración - Pymora SaaS')

@section('content')
<div x-data="{ openTenantModal: false, editTenantModal: false, editPlanModal: false, editPlanData: { id: '', name: '', price: 0, features: '' }, activeTab: '{{ session('success') ? 'tenants' : 'tenants' }}' }" class="space-y-6">

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

    <!-- Header Title & Quick Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <h2 class="text-xl font-bold text-white font-display">Panel de Administración SaaS</h2>
            <p class="text-xs text-slate-400 mt-0.5">Gestión global de empresas registradas, licencias, cobranza y parámetros generales.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <button @click="openTenantModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Registrar Empresa
            </button>
        </div>
    </div>

    <!-- Global Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Card 1: Tenants -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="text-slate-400 text-xs font-medium">Empresas Registradas</div>
            <div class="text-2xl font-bold text-white font-display">{{ $totalTenants }}</div>
            <div class="text-[11px] text-emerald-400 font-semibold">{{ $activeTenants }} Activas en SaaS</div>
        </div>

        <!-- Card 2: Revenue -->
        <a href="{{ route('superadmin.finanzas') }}" class="glass-card p-4 rounded-xl border border-slate-800 space-y-1 hover:border-emerald-500/40 transition-all group">
            <div class="flex items-center justify-between">
                <div class="text-slate-400 text-xs">Ingresos Mensuales</div>
                <span class="text-[10px] text-emerald-400 group-hover:underline">Ver Finanzas &rarr;</span>
            </div>
            <div class="text-2xl font-bold text-emerald-400 font-display">${{ number_format($totalMrrUsd, 2) }} <span class="text-xs font-normal text-slate-400">/ mes</span></div>
            <div class="text-[11px] text-slate-400">Suscripciones activas</div>
        </a>

        <!-- Card 3: Tasa BCV -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="text-slate-400 text-xs font-medium">Tasa Oficial Dólar BCV</div>
            <div class="text-2xl font-bold text-amber-300 font-display">{{ number_format($bcvUsdRate, 2) }} <span class="text-xs font-normal text-slate-400">VES</span></div>
            <div class="text-[11px] text-sky-400 flex items-center gap-1 font-mono">
                <span>Euro: {{ number_format($bcvEurRate, 2) }} VES</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 text-xs font-medium">
        <button @click="activeTab = 'tenants'" :class="activeTab === 'tenants' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10 font-bold' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Empresas ({{ count($tenants) }})
        </button>
        <button @click="activeTab = 'plans'" :class="activeTab === 'plans' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10 font-bold' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Planes & Planes Tarifarios
        </button>
        <button @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'border-indigo-500 text-indigo-400 bg-indigo-500/10 font-bold' : 'border-transparent text-slate-400 hover:text-slate-200'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Parámetros & Configuración
        </button>
    </div>

    <!-- TAB 1: Tenants List Table -->
    <div x-show="activeTab === 'tenants'" class="glass-card rounded-xl overflow-hidden border border-slate-800">
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-white text-sm">Empresas Registradas</h3>
                <p class="text-[11px] text-slate-400">Listado de comercios, control de acceso e impersonación.</p>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" placeholder="Buscar por nombre o RIF..." class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200 focus:outline-none focus:border-indigo-500 w-56">
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
                        <th class="p-3 text-right">Acciones Super Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($tenants as $t)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3">
                            <div class="font-bold text-white text-sm">{{ $t->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $t->rif_tax_id }}</div>
                        </td>
                        <td class="p-3 font-mono text-indigo-400 font-semibold">
                            {{ $t->subdomain }}.pymora.com
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                {{ strtoupper($t->plan_tier) }}
                            </span>
                        </td>
                        <td class="p-3 font-mono text-emerald-400">{{ number_format($t->bcv_rate, 2) }} VES</td>
                        <td class="p-3 text-slate-400 font-mono">
                            @if(is_object($t->expires_at))
                                {{ $t->expires_at->format('d/m/Y') }}
                            @else
                                {{ $t->expires_at ? \Carbon\Carbon::parse($t->expires_at)->format('d/m/Y') : 'Activo' }}
                            @endif
                        </td>
                        <td class="p-3">
                            @if($t->is_active ?? true)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">ACTIVO</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/30">SUSPENDIDO</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Toggle Status Form -->
                                <form action="{{ route('superadmin.tenants.toggle', $t->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-2 py-1 text-[10px] font-semibold rounded border {{ ($t->is_active ?? true) ? 'bg-amber-500/10 text-amber-400 border-amber-500/30 hover:bg-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30 hover:bg-emerald-500/20' }}">
                                        {{ ($t->is_active ?? true) ? 'Suspender' : 'Activar' }}
                                    </button>
                                </form>

                                 <!-- Auditar Empresa Link -->
                                <a href="{{ route('superadmin.impersonate', $t->id) }}" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-bold rounded-lg transition-all shadow-md flex items-center gap-1.5 border border-indigo-400/30">
                                    <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Auditar Empresa</span>
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
        @foreach(['trial', 'starter', 'pro'] as $pKey)
            @php
                $plan = $plans[$pKey] ?? [
                    'id' => $pKey,
                    'name' => 'Plan ' . ucfirst($pKey),
                    'price' => 0,
                    'features' => ''
                ];
                $borderClass = $pKey === 'trial' ? 'border-emerald-500/40 bg-emerald-950/20' : ($pKey === 'pro' ? 'border-indigo-500/40 bg-indigo-950/20' : 'border-slate-800');
                $titleClass = $pKey === 'trial' ? 'text-emerald-400' : ($pKey === 'pro' ? 'text-indigo-400' : 'text-slate-300');
            @endphp
            <div class="glass-card rounded-xl p-5 border {{ $borderClass }} space-y-4 flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-bold {{ $titleClass }} uppercase tracking-wider">{{ $plan['name'] }}</div>
                        @if($pKey === 'trial')
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">1er MES GRATIS</span>
                        @elseif($pKey === 'pro')
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">MÁS COMPLETO</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">PLAN SENCILLO</span>
                        @endif
                    </div>
                    
                    <div class="text-3xl font-bold text-white font-display">
                        ${{ number_format($plan['price'], 2) }} <span class="text-xs font-normal text-slate-400">/ mes</span>
                    </div>

                    @if($pKey === 'trial')
                        <div class="text-[11px] text-emerald-400 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Asignado automáticamente por 30 días
                        </div>
                    @else
                        <div class="text-[11px] text-indigo-300 font-medium">
                            Opción disponible al finalizar el mes gratis
                        </div>
                    @endif

                    <ul class="text-xs text-slate-300 space-y-2 pt-3 border-t border-slate-800/80">
                        @foreach(explode("\n", str_replace("\r", "", $plan['features'])) as $feature)
                            @if(trim($feature))
                                <li class="flex items-start gap-1.5">
                                    <span class="text-emerald-400 font-bold">✓</span>
                                    <span>{{ preg_replace('/^[✓✔\s\-]+/', '', trim($feature)) }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                
                <div class="pt-3 border-t border-slate-800/80">
                    <button @click="editPlanData = { id: '{{ $plan['id'] }}', name: '{{ addslashes($plan['name']) }}', price: {{ $plan['price'] }}, features: `{{ addslashes($plan['features']) }}` }; editPlanModal = true" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 text-xs font-semibold rounded-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar Plan, Precio & Beneficios
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- TAB 3: Global Settings & DolarApi Sync -->
    <div x-show="activeTab === 'settings'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Settings Form -->
        <div class="glass-card rounded-xl p-5 border border-slate-800 space-y-4">
            <h3 class="font-bold text-white text-sm">Parámetros Globales SaaS</h3>
            
            <form action="{{ route('superadmin.settings.update') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-300 mb-1 font-semibold">Días de Prueba Gratuitos (Trial Period)</label>
                    <input type="number" name="trial_days" value="{{ $trialDays }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-slate-300 mb-1 font-semibold">Correo Electrónico de Soporte Global</label>
                    <input type="email" name="support_email" value="{{ $supportEmail }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white font-mono focus:border-indigo-500 focus:outline-none">
                </div>

                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow-md transition-all">
                    Guardar Configuración Global
                </button>
            </form>
        </div>

        <!-- DolarApi & Broadcast -->
        <div class="space-y-4">
            <!-- Sync Rates Form -->
            <div class="glass-card rounded-xl p-5 border border-slate-800 space-y-3 text-xs">
                <h3 class="font-bold text-white text-sm">Sincronización BCV en Tiempo Real</h3>
                <p class="text-slate-400">Pymora consulta la API oficial DolarApi.com para obtener las tasas vigentes del Banco Central de Venezuela.</p>

                <div class="p-3 bg-slate-900/60 rounded-lg border border-slate-800 flex items-center justify-between font-mono">
                    <span class="text-slate-300">Dólar BCV: <strong class="text-emerald-400">{{ number_format($bcvUsdRate, 2) }} VES</strong></span>
                    <span class="text-slate-300">Euro BCV: <strong class="text-sky-400">{{ number_format($bcvEurRate, 2) }} VES</strong></span>
                </div>

                <form action="{{ route('superadmin.sync-dolarapi') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Sincronizar Tasas BCV Ahora
                    </button>
                </form>
            </div>

            <!-- Global Broadcast Form -->
            <div class="glass-card rounded-xl p-5 border border-slate-800 space-y-3 text-xs">
                <h3 class="font-bold text-white text-sm">Aviso Broadcast Global</h3>
                
                <form action="{{ route('superadmin.broadcast.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <textarea name="broadcast_message" rows="2" placeholder="Escribe un mensaje de mantenimiento o aviso que verán todas las tiendas..." class="w-full bg-slate-900 border border-slate-700 rounded-lg p-2.5 text-xs text-white focus:border-indigo-500 focus:outline-none">{{ $broadcastMessage }}</textarea>
                    
                    <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-lg transition-all">
                        Publicar Aviso Broadcast
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div x-show="openTenantModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="openTenantModal = false" class="glass-card w-full max-w-lg rounded-2xl border border-slate-800 p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-white font-display">Registrar Empresa</h3>
                <button @click="openTenantModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('superadmin.tenants.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nombre Comercial de la Empresa</label>
                    <input type="text" name="name" required placeholder="Ej: Bodega & Abasto El Sol C.A." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">RIF / Tax ID</label>
                        <input type="text" name="rif_tax_id" required placeholder="J-12345678-0" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Subdominio Único</label>
                        <input type="text" name="subdomain" required placeholder="elsol" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-indigo-400 font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Plan Tarifario</label>
                        <select name="plan_tier" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none font-mono">
                            @foreach(['trial', 'starter', 'pro'] as $pKey)
                                @if(isset($plans[$pKey]))
                                    <option value="{{ $pKey }}" {{ $pKey === 'trial' ? 'selected' : '' }}>
                                        {{ strtoupper($plans[$pKey]['name']) }} (${{ number_format($plans[$pKey]['price'], 0) }}/mes)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Correo Administrador</label>
                        <input type="email" name="email" required placeholder="contacto@elsol.com" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Tipo de Negocio (Rubro Comercial)</label>
                    <select name="business_type" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-indigo-300 font-semibold focus:border-indigo-500 focus:outline-none">
                        @foreach($businessTypes as $bKey => $bMeta)
                            <option value="{{ $bKey }}">{{ $bMeta['icon'] }} {{ $bMeta['name'] }} — {{ $bMeta['description'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Teléfono de Contacto</label>
                    <input type="text" name="phone" placeholder="+58 412 1234567" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="openTenantModal = false" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg">Registrar & Generar Licencia</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Plan Modal -->
    <div x-show="editPlanModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="editPlanModal = false" class="glass-card w-full max-w-lg rounded-2xl border border-slate-800 p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-white font-display">Editar Plan de Suscripción</h3>
                <button @click="editPlanModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('superadmin.plans.update') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="plan_id" :value="editPlanData.id">

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Nombre del Plan</label>
                    <input type="text" name="name" x-model="editPlanData.name" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Precio Mensual (USD)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold">$</span>
                        <input type="number" step="0.01" name="price" x-model="editPlanData.price" required class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-7 pr-3 py-2 text-sm text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Beneficios y Características (Un beneficio por línea)</label>
                    <textarea name="features" x-model="editPlanData.features" rows="6" required placeholder="✓ 1 Sucursal&#10;✓ 1 Caja POS&#10;✓ 3 Usuarios" class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white font-mono focus:border-indigo-500 focus:outline-none leading-relaxed"></textarea>
                    <p class="text-[10px] text-slate-400 mt-1">Ingresa cada beneficio en una nueva línea.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="editPlanModal = false" class="px-4 py-2 text-xs text-slate-400 hover:text-white">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg">Guardar Cambios del Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
