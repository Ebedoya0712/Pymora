@extends('layouts.app')

@section('title', 'Planes & Tarifas - Pymora Super Admin')

@section('content')
<div x-data="{ 
    editPlanModal: false,
    editPlanData: { id: '', name: '', price: 0, features: '' },
    openEditPlan(key, name, price, features) {
        this.editPlanData = { id: key, name: name, price: price, features: features };
        this.editPlanModal = true;
    }
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    Configuración Comercial SaaS
                </span>
                <span class="text-slate-500">•</span>
                <span class="text-xs font-mono text-slate-400">Pymora Subscription Tiers</span>
            </div>
            <h1 class="text-2xl font-bold text-white font-display mt-1">Planes & Tarifas Pymora</h1>
            <p class="text-slate-400 text-sm">Modifica las características, precios y beneficios de los planes de suscripción para los comercios.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs font-mono text-slate-300">
                Plan Actual Activo: <strong class="text-emerald-400">3 Planes Disponibles</strong>
            </span>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div x-data="{ showSuccess: true }" x-show="showSuccess" x-transition class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="showSuccess = false" title="Cerrar notificación" class="text-emerald-400/80 hover:text-white hover:bg-emerald-500/20 p-1.5 rounded-lg transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Visual Pricing Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
        @foreach($plans as $planKey => $plan)
        @php
            $isPro = $planKey === 'pro';
            $isTrial = $planKey === 'trial';
            $badgeBg = $isTrial ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : ($isPro ? 'bg-purple-500/10 text-purple-400 border-purple-500/20' : 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20');
            $borderCard = $isPro ? 'border-purple-500/40 shadow-xl shadow-purple-500/10' : ($isTrial ? 'border-emerald-500/30' : 'border-slate-800');
        @endphp
        <div class="relative bg-slate-900/90 rounded-2xl p-6 border {{ $borderCard }} transition-all hover:border-slate-700 flex flex-col justify-between group">
            @if($isPro)
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-[10px] font-extrabold uppercase px-3.5 py-0.5 rounded-full shadow-lg tracking-wider">
                    Recomendado / Más Popular
                </div>
            @endif

            <div>
                <!-- Plan Header -->
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-mono font-bold uppercase tracking-wider px-2.5 py-0.5 rounded border {{ $badgeBg }}">
                        {{ $isTrial ? '1 Mes Gratis' : strtoupper($planKey) }}
                    </span>
                    <div class="text-right">
                        <span class="text-3xl font-black text-white font-display">${{ number_format($plan['price'], 0) }}</span>
                        <span class="text-xs text-slate-400 font-mono">/mes</span>
                    </div>
                </div>

                <h4 class="text-lg font-bold text-white font-display mb-3">{{ $plan['name'] }}</h4>

                <!-- Features List -->
                <div class="space-y-2.5 border-t border-slate-800/80 pt-4 mb-6 text-xs text-slate-300">
                    @foreach(explode("\n", str_replace(["\r", "✓"], "", $plan['features'])) as $feature)
                        @if(trim($feature))
                            <div class="flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ trim($feature) }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Action to open Edit Modal -->
            <button type="button" @click='openEditPlan({{ json_encode($planKey) }}, {{ json_encode($plan["name"]) }}, {{ (float) $plan["price"] }}, {{ json_encode($plan["features"]) }})' class="w-full mt-2 py-2.5 px-4 rounded-xl text-xs font-semibold bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-500 transition-all flex items-center justify-center gap-2 group-hover:shadow-lg group-hover:shadow-indigo-600/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Editar Tarifa & Características</span>
            </button>
        </div>
        @endforeach
    </div>

    <!-- Modal Editar Tarifa & Plan -->
    <div x-show="editPlanModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="editPlanModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl max-w-md w-full p-6 space-y-5 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold text-white flex items-center gap-2 font-display">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Editar Plan de Suscripción</span>
                </h3>
                <button type="button" @click="editPlanModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
            </div>

            <form action="{{ route('superadmin.plans.update') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="plan_id" :value="editPlanData.id">

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Nombre Comercial del Plan</label>
                    <input type="text" name="name" x-model="editPlanData.name" required class="w-full bg-slate-950 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Precio Mensual ($ USD)</label>
                    <input type="number" step="1" name="price" x-model="editPlanData.price" required class="w-full bg-slate-950 border border-slate-700 text-emerald-400 font-bold font-mono rounded-xl px-3 py-2 text-xs focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-300">Lista de Características (1 por línea)</label>
                    <textarea name="features" rows="5" x-model="editPlanData.features" required class="w-full bg-slate-950 border border-slate-700 text-slate-300 rounded-xl px-3 py-2 text-xs font-mono leading-relaxed focus:border-indigo-500 focus:outline-none"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-800">
                    <button type="button" @click="editPlanModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-indigo-600/30">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
