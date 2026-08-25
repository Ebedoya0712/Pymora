@extends('layouts.app')

@section('title', 'Gestión de Empresas - Super Admin Pymora')

@section('content')
<div class="space-y-6" x-data="{ 
    showCreateModal: false, 
    showEditModal: false, 
    showRenewModal: false,
    selectedTenant: {} 
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-slate-900/60 p-6 rounded-2xl border border-slate-800 backdrop-blur-xl">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-2">
                🏬 Empresas & Licencias Registradas
            </h1>
            <p class="text-sm text-slate-400 mt-1">Crea, edita, renueva, suspende, elimina y audita todos los comercios activos en la plataforma.</p>
        </div>
        <button @click="showCreateModal = true" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Registrar Nueva Empresa</span>
        </button>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="glass-card p-4 rounded-xl space-y-1">
            <div class="text-xs text-slate-400 font-medium">Total Empresas Registradas</div>
            <div class="text-2xl font-black text-white">{{ $totalTenants }}</div>
            <div class="text-[11px] text-indigo-400 font-mono">Comercios en Base de Datos</div>
        </div>
        <div class="glass-card p-4 rounded-xl space-y-1">
            <div class="text-xs text-slate-400 font-medium">Empresas Activas en Pymora</div>
            <div class="text-2xl font-black text-emerald-400">{{ $activeTenants }}</div>
            <div class="text-[11px] text-emerald-400/80 font-mono">Acceso total habilitado</div>
        </div>
        <div class="glass-card p-4 rounded-xl space-y-1">
            <div class="text-xs text-slate-400 font-medium">Empresas Suspendidas / Vencidas</div>
            <div class="text-2xl font-black text-amber-400">{{ $suspendedTenants }}</div>
            <div class="text-[11px] text-amber-400/80 font-mono">Requieren renovación</div>
        </div>
    </div>

    <!-- Companies List Table -->
    <div class="glass-card p-6 rounded-2xl space-y-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <h3 class="font-bold text-white text-lg flex items-center gap-2">
                <span>Directorio de Empresas</span>
                <span class="bg-indigo-500/20 text-indigo-300 text-xs px-2.5 py-0.5 rounded-full font-mono">{{ $tenants->total() }}</span>
            </h3>
            <form action="{{ route('superadmin.empresas') }}" method="GET" class="w-full md:w-72 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por Nombre o RIF..." class="w-full bg-slate-900 border border-slate-700/80 text-white rounded-xl text-xs px-3.5 py-2 pl-9 focus:outline-none focus:border-indigo-500 transition-colors">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase font-mono text-[10px]">
                    <tr>
                        <th class="p-3.5">Empresa / RIF / Rubro</th>
                        <th class="p-3.5">Plan Contratado</th>
                        <th class="p-3.5">Vencimiento Licencia</th>
                        <th class="p-3.5">Estado</th>
                        <th class="p-3.5 text-right">Acciones Super Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 bg-slate-900/40">
                    @forelse($tenants as $t)
                    @php
                        $bMeta = $businessTypes[$t->business_type] ?? $businessTypes['abasto'];
                        $pMeta = $plans[$t->plan_tier] ?? $plans['starter'];
                        $isExpired = $t->expires_at && \Carbon\Carbon::parse($t->expires_at)->isPast();
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="p-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-indigo-600/20 border border-indigo-500/30 flex items-center justify-center text-lg shadow">
                                    {{ $bMeta['icon'] }}
                                </div>
                                <div>
                                    <div class="font-bold text-white text-sm">{{ $t->name }}</div>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $t->rif_tax_id ?: 'J-12345678-9' }} • <span class="text-indigo-300 font-semibold">{{ $bMeta['name'] }}</span></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3.5 whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-mono font-bold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 whitespace-nowrap">
                                {{ $t->plan_tier === 'trial' ? 'MES GRATIS ($0/MES)' : strtoupper($t->plan_tier) . ' ($' . $pMeta['price'] . '/MES)' }}
                            </span>
                        </td>
                        <td class="p-3.5 font-mono whitespace-nowrap">
                            @if($t->expires_at)
                                <div class="{{ $isExpired ? 'text-rose-400 font-bold' : 'text-slate-300' }} whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($t->expires_at)->format('d/m/Y') }}
                                </div>
                                <div class="text-[10px] text-slate-500 whitespace-nowrap">
                                    {{ $isExpired ? 'Licencia Vencida' : \Carbon\Carbon::parse($t->expires_at)->locale('es')->diffForHumans() }}
                                </div>
                            @else
                                <span class="text-emerald-400 font-semibold whitespace-nowrap">Ilimitada / Gratis</span>
                            @endif
                        </td>
                        <td class="p-3.5 whitespace-nowrap">
                            @if($t->is_active && !$isExpired)
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-mono font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 whitespace-nowrap">● ACTIVO</span>
                            @else
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-mono font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30 whitespace-nowrap">● SUSPENDIDO</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-right whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5 align-middle">
                                <!-- Auditar -->
                                <a href="{{ route('superadmin.impersonate', $t->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600/30 hover:bg-indigo-600 text-indigo-200 text-xs font-bold rounded-lg border border-indigo-500/30 transition-colors shadow whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Auditar</span>
                                </a>

                                <!-- Renovar -->
                                <button @click="selectedTenant = {{ json_encode($t) }}; showRenewModal = true" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600/30 hover:bg-emerald-600 text-emerald-200 text-xs font-bold rounded-lg border border-emerald-500/30 transition-colors shadow whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    <span>Renovar</span>
                                </button>

                                <!-- Editar -->
                                <button @click="selectedTenant = {{ json_encode($t) }}; showEditModal = true" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-lg border border-slate-700 transition-colors shadow whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Editar</span>
                                </button>

                                <!-- Toggle Status -->
                                <form action="{{ route('superadmin.tenants.toggle', $t->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 {{ $t->is_active ? 'bg-amber-950/60 hover:bg-amber-900 text-amber-300 border-amber-800/60' : 'bg-emerald-950/60 hover:bg-emerald-900 text-emerald-300 border-emerald-800/60' }} text-xs font-bold rounded-lg border transition-colors shadow whitespace-nowrap">
                                        @if($t->is_active)
                                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Suspender</span>
                                        @else
                                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Reactivar</span>
                                        @endif
                                    </button>
                                </form>

                                <!-- Eliminar -->
                                <form action="{{ route('superadmin.tenants.delete', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás 100% seguro de ELIMINAR esta empresa y todos sus datos?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center p-2 bg-rose-950/60 hover:bg-rose-900 text-rose-300 border border-rose-800/60 rounded-lg transition-colors shadow whitespace-nowrap" title="Eliminar Empresa">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">
                            No hay empresas registradas aún. Haz clic en "Registrar Nueva Empresa".
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tenants->hasPages())
            <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL 1: Registrar Nueva Empresa -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-lg flex items-center gap-2">
                    <span>🏬 Registrar Nueva Empresa</span>
                </h3>
                <button @click="showCreateModal = false" class="text-slate-400 hover:text-white text-xl font-bold">×</button>
            </div>
            
            <form action="{{ route('superadmin.tenants.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Nombre Comercial</label>
                        <input type="text" name="name" required placeholder="Ej: Bodega El Sol C.A." class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">RIF / Identificación Fiscal</label>
                        <input type="text" name="rif_tax_id" required placeholder="Ej: J-12345678-9" class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Tipo de Negocio / Rubro</label>
                        <select name="business_type" class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                            @foreach($businessTypes as $key => $bt)
                            <option value="{{ $key }}">{{ $bt['icon'] }} {{ $bt['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Plan Inicial por Defecto</label>
                        <div class="w-full bg-slate-950 border border-slate-800 text-emerald-400 font-bold rounded-lg p-2.5 text-xs font-mono">
                            <span>1 Mes Gratis</span>
                        </div>
                        <input type="hidden" name="plan_tier" value="trial">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-300">Correo de la Empresa</label>
                    <input type="email" name="email" required placeholder="contacto@empresa.com" class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-lg">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow-lg">Crear Empresa</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Editar Empresa -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-lg flex items-center gap-2">
                    <span>✏️ Editar Datos de Empresa</span>
                </h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-white text-xl font-bold">×</button>
            </div>
            
            <form :action="'/superadmin/tenants/' + selectedTenant.id + '/update'" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Nombre Comercial</label>
                        <input type="text" name="name" :value="selectedTenant.name" required class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">RIF / Identificación Fiscal</label>
                        <input type="text" name="rif_tax_id" :value="selectedTenant.rif_tax_id" required class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Tipo de Negocio / Rubro</label>
                        <select name="business_type" x-model="selectedTenant.business_type" class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                            @foreach($businessTypes as $key => $bt)
                            <option value="{{ $key }}">{{ $bt['icon'] }} {{ $bt['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Plan Pymora</label>
                        <select name="plan_tier" x-model="selectedTenant.plan_tier" class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                            @foreach($plans as $pk => $pv)
                            <option value="{{ $pk }}">{{ $pv['name'] }} (${{ $pv['price'] }}/mes)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="font-semibold text-slate-300">Vencimiento Licencia</label>
                        <input type="date" name="expires_at" :value="selectedTenant.expires_at ? selectedTenant.expires_at.split('T')[0] : ''" class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-lg">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow-lg">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Renovar Suscripción -->
    <div x-show="showRenewModal" x-cloak class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-lg flex items-center gap-2">
                    <span>🔄 Renovar Licencia Comercial</span>
                </h3>
                <button @click="showRenewModal = false" class="text-slate-400 hover:text-white text-xl font-bold">×</button>
            </div>
            
            <form :action="'/superadmin/tenants/' + selectedTenant.id + '/renew'" method="POST" class="space-y-4 text-xs">
                @csrf
                <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-slate-300 space-y-1">
                    <div class="font-bold text-white" x-text="selectedTenant.name"></div>
                    <div class="text-[11px] text-indigo-400 font-mono">Plan Actual: <span class="uppercase font-bold" x-text="selectedTenant.plan_tier"></span></div>
                </div>

                <div class="space-y-1">
                    <label class="font-semibold text-slate-300">Extender Licencia Por:</label>
                    <select name="months" class="w-full bg-slate-950 border border-slate-800 text-white rounded-lg p-2.5 focus:border-indigo-500 focus:outline-none">
                        <option value="1">1 Mes (+ 30 días)</option>
                        <option value="3">3 Meses (+ 90 días)</option>
                        <option value="6">6 Meses (+ 180 días)</option>
                        <option value="12">12 Meses / 1 Año (+ 365 días)</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" @click="showRenewModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-lg">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg shadow-lg">Confirmar Renovación</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
