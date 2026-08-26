@extends('layouts.app')

@section('title', 'Mis Finanzas - Pymora Super Admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div x-data="{ 
    showPaymentModal: false, 
    editPlanModal: false,
    editPlanData: { id: '', name: '', price: 0, features: '' },
    selectedTenantId: '', 
    selectedPlan: 'pro', 
    selectedMonths: 1, 
    amountUsd: {{ $plans['pro']['price'] ?? 79 }}, 
    paymentMethod: 'pago_movil', 
    referenceCode: '', 
    notes: '', 
    openEditPlan(key, name, price, features) {
        this.editPlanData = { id: key, name: name, price: price, features: features };
        this.editPlanModal = true;
    },
    updateAmount() { 
        const rates = { trial: {{ $plans['trial']['price'] ?? 0 }}, starter: {{ $plans['starter']['price'] ?? 29 }}, pro: {{ $plans['pro']['price'] ?? 79 }} }; 
        this.amountUsd = (rates[this.selectedPlan] !== undefined ? rates[this.selectedPlan] : 79) * this.selectedMonths; 
    } 
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    Control Financiero Pymora
                </span>
                <span class="text-slate-500">•</span>
                <span class="text-xs font-mono text-slate-400">Recaudación & Ingresos Pymora</span>
            </div>
            <h1 class="text-2xl font-bold text-white font-display mt-1">Mis Finanzas</h1>
            <p class="text-slate-400 text-sm">Rendimiento en tiempo real: ganancias por día, semana, mes y acumulado total.</p>
        </div>

        <button @click="showPaymentModal = true" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>Registrar Pago de Suscripción</span>
        </button>
    </div>

    <!-- Top KPI Revenue Metric Cards Grid (Día, Semana, Mes, Acumulado) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Ganado Hoy (Día) -->
        <div class="glass-card p-5 rounded-2xl border border-slate-800 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                <span>Ganado Hoy (Día)</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-emerald-400 font-display">${{ number_format($todayRevenueUsd, 2) }} <span class="text-xs text-slate-400 font-normal">USD</span></div>
            <div class="text-xs text-slate-400 flex items-center justify-between mt-2 font-mono">
                <span>Bs. {{ number_format($todayRevenueVes, 2) }} VES</span>
                <span class="text-emerald-400 font-semibold">HOY</span>
            </div>
        </div>

        <!-- Ganado Esta Semana -->
        <div class="glass-card p-5 rounded-2xl border border-slate-800 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                <span>Ganado Esta Semana</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-indigo-400 font-display">${{ number_format($weekRevenueUsd, 2) }} <span class="text-xs text-slate-400 font-normal">USD</span></div>
            <div class="text-xs text-indigo-400 flex items-center gap-1 mt-2">
                <span>Últimos 7 días corridos</span>
            </div>
        </div>

        <!-- Ganado Este Mes (MRR) -->
        <div class="glass-card p-5 rounded-2xl border border-slate-800 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                <span>Ganado Este Mes</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-purple-400 font-display">${{ number_format($thisMonthRevenueUsd, 2) }} <span class="text-xs text-slate-400 font-normal">USD</span></div>
            <div class="text-xs text-purple-400 flex items-center gap-1 mt-2">
                <span>Mes de {{ now()->translatedFormat('F Y') }}</span>
            </div>
        </div>

        <!-- Total Acumulado SaaS -->
        <div class="glass-card p-5 rounded-2xl border border-slate-800 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-sky-500/10 rounded-full blur-2xl"></div>
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium mb-2">
                <span>Total Acumulado</span>
                <div class="w-8 h-8 rounded-lg bg-sky-500/10 flex items-center justify-center text-sky-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">${{ number_format($totalRevenueUsd, 2) }} <span class="text-xs text-slate-400 font-normal">USD</span></div>
            <div class="text-xs text-sky-400 flex items-center gap-1 mt-2">
                <span>Histórico general de la plataforma</span>
            </div>
        </div>
    </div>

    <!-- Charts Section: Line Trend & Payment Methods Doughnut -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: Tendencia de Ingresos SaaS (2 cols) -->
        <div class="lg:col-span-2 glass-card rounded-2xl border border-slate-800 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="font-bold text-white font-display text-base">{{ $chartTitle }}</h3>
                    <p class="text-slate-400 text-xs">{{ $chartSubtitle }}</p>
                </div>
                <!-- Period Filter Pills -->
                <div class="flex items-center gap-1.5 bg-slate-900/90 p-1 rounded-xl border border-slate-800 text-xs font-medium">
                    <a href="{{ route('superadmin.finanzas', ['period' => '7days']) }}" 
                       class="px-3 py-1 rounded-lg transition-all {{ $period === '7days' ? 'bg-emerald-600/20 text-emerald-300 font-bold border border-emerald-500/30' : 'text-slate-400 hover:text-white' }}">
                       7 Días
                    </a>
                    <a href="{{ route('superadmin.finanzas', ['period' => 'months']) }}" 
                       class="px-3 py-1 rounded-lg transition-all {{ $period === 'months' ? 'bg-emerald-600/20 text-emerald-300 font-bold border border-emerald-500/30' : 'text-slate-400 hover:text-white' }}">
                       Por Meses
                    </a>
                    <a href="{{ route('superadmin.finanzas', ['period' => 'years']) }}" 
                       class="px-3 py-1 rounded-lg transition-all {{ $period === 'years' ? 'bg-emerald-600/20 text-emerald-300 font-bold border border-emerald-500/30' : 'text-slate-400 hover:text-white' }}">
                       Por Años
                    </a>
                </div>
            </div>
            <div class="h-64 relative">
                <canvas id="dailyRevenueChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Distribución por Método de Pago (1 col) -->
        <div class="glass-card rounded-2xl border border-slate-800 p-5 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-white font-display text-base mb-1">Cobranza por Método</h3>
                <p class="text-slate-400 text-xs mb-4">Distribución porcentual por canal de pago.</p>
                <div class="h-52 relative flex items-center justify-center">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                <span>Canal más utilizado:</span>
                <span class="font-bold text-indigo-400 font-mono">Pago Móvil / Zelle</span>
            </div>
        </div>
    </div>

    <!-- Main Section: Subscriptions & Licenses Expiration Status -->
    <div class="glass-card rounded-2xl border border-slate-800 p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-white font-display text-base">Estado de Licencias & Suscripciones</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 font-mono">
                        {{ $expiringSoonCount }} A punto de vencer
                    </span>
                </div>
                <p class="text-slate-400 text-xs mt-0.5">Control de vencimientos y renovación de planes empresariales.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-mono text-slate-400 bg-slate-900 px-3 py-1.5 rounded-xl border border-slate-800">
                <span>Tasa BCV Oficial:</span>
                <span class="font-bold text-emerald-400">{{ number_format($bcvUsdRate, 2) }} VES/USD</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($tenants as $tenant)
                @php
                    $daysLeft = $tenant->expires_at ? now()->diffInDays(\Carbon\Carbon::parse($tenant->expires_at), false) : 0;
                    $isExpired = $daysLeft <= 0;
                    $isExpiringSoon = !$isExpired && $daysLeft <= 15;
                    
                    $cardBorder = $isExpired 
                        ? 'border-rose-500/40 bg-rose-950/20' 
                        : ($isExpiringSoon ? 'border-amber-500/40 bg-amber-950/20' : 'border-slate-800 bg-slate-900/60');
                    
                    $badgeStyle = match($tenant->plan_tier) {
                        'trial' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                        'starter' => 'bg-slate-800 text-slate-300 border-slate-700',
                        'pro' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
                        default => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                    };
                    $planName = match($tenant->plan_tier) {
                        'trial' => '1 Mes Gratis',
                        'starter' => 'Plan Sencillo',
                        'pro' => 'Plan Pro',
                        default => 'Enterprise',
                    };
                @endphp
                <div class="p-4 rounded-xl border {{ $cardBorder }} flex flex-col justify-between gap-3 transition-all hover:border-indigo-500/40">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="font-bold text-white text-sm font-display truncate">{{ $tenant->name }}</div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase font-mono border {{ $badgeStyle }}">
                                {{ $planName }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                            <span>Subdominio:</span>
                            <span class="text-indigo-400 font-semibold">{{ $tenant->subdomain }}.pymora.com</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between">
                        <div>
                            @if($isExpired)
                                <div class="text-xs font-bold text-rose-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Vencida</span>
                                </div>
                            @elseif($isExpiringSoon)
                                <div class="text-xs font-bold text-amber-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Vence en {{ (int)$daysLeft }} días</span>
                                </div>
                            @else
                                <div class="text-xs font-semibold text-emerald-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Vence en {{ (int)$daysLeft }} días</span>
                                </div>
                            @endif
                            <div class="text-[10px] text-slate-500 font-mono">
                                {{ $tenant->expires_at ? \Carbon\Carbon::parse($tenant->expires_at)->format('d/m/Y') : 'N/A' }}
                            </div>
                        </div>

                        <button @click="showPaymentModal = true; selectedTenantId = '{{ $tenant->id }}'; selectedPlan = '{{ $tenant->plan_tier }}'; updateAmount();" class="px-3 py-1.5 text-xs font-semibold bg-emerald-600/20 text-emerald-400 hover:bg-emerald-600 hover:text-white border border-emerald-500/30 rounded-xl transition-all shadow flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Renovar
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Registration Payment Modal -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="showPaymentModal = false" class="glass-card w-full max-w-lg rounded-2xl border border-slate-800 p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-white font-display">Registrar Pago de Suscripción Pymora</h3>
                    <p class="text-xs text-slate-400">Extiende la licencia y emite comprobante de cobranza.</p>
                </div>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('superadmin.payments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Empresa / Comercio</label>
                    <select name="tenant_id" x-model="selectedTenantId" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                        <option value="" disabled selected>-- Selecciona un comercio --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ strtoupper($t->plan_tier) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Plan de Suscripción</label>
                        <select name="plan_tier" x-model="selectedPlan" @change="updateAmount()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-indigo-500 focus:outline-none">
                            @foreach($plans as $pk => $pv)
                                <option value="{{ $pk }}">{{ $pv['name'] }} (${{ $pv['price'] }}/mes)</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Meses a Cancelar</label>
                        <select name="months_paid" x-model.number="selectedMonths" @change="updateAmount()" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-indigo-500 focus:outline-none">
                            <option value="1">1 Mes</option>
                            <option value="3">3 Meses</option>
                            <option value="6">6 Meses</option>
                            <option value="12">12 Meses (1 Año)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Monto de Suscripción ($ USD)</label>
                        <div class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-sm text-emerald-400 font-bold font-mono">
                            <span x-text="'$' + parseFloat(amountUsd).toFixed(2) + ' USD'"></span>
                        </div>
                        <input type="hidden" name="amount_usd" :value="amountUsd">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Método de Pago</label>
                        <select name="payment_method" x-model="paymentMethod" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-indigo-500 focus:outline-none">
                            <option value="pago_movil">Pago Móvil</option>
                            <option value="transferencia">Transferencia Bancaria VES</option>
                            <option value="zelle">Zelle USD</option>
                            <option value="efectivo_usd">Efectivo USD</option>
                            <option value="binance">Binance Pay USDT</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Código Referencia / Confirmación</label>
                        <input type="text" name="reference_code" required placeholder="Ej: 84920481" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs font-mono focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Fecha de Pago</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Adjuntar Captura / Comprobante (Opcional)</label>
                    <input type="file" name="proof_image" accept="image/*,.pdf" class="w-full bg-slate-900 border border-slate-700 text-slate-300 rounded-xl px-3 py-1.5 text-xs focus:border-indigo-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Notas Adicionales (Opcional)</label>
                    <input type="text" name="notes" placeholder="Ej: Pago verificado en cuenta Banesco" class="w-full bg-slate-900 border border-slate-700 text-white rounded-xl px-3 py-2 text-xs focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-800">
                    <button type="button" @click="showPaymentModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-xs">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs shadow-lg shadow-emerald-600/30">Guardar Pago & Renovar</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart 1: Daily Revenue Trend Line Chart
        const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        const dailyGradient = dailyCtx.createLinearGradient(0, 0, 0, 250);
        dailyGradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        dailyGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json($dailyLabels),
                datasets: [{
                    label: 'Ganancias USD ($)',
                    data: @json($dailyValues),
                    borderColor: '#10b981',
                    borderWidth: 3,
                    backgroundColor: dailyGradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#10b981',
                        borderColor: '#334155',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return ' Ganancias: $' + context.raw.toFixed(2) + ' USD';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#94a3b8', font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { 
                            color: '#94a3b8', 
                            font: { family: 'Inter', size: 11 },
                            callback: function(val) { return '$' + val; }
                        }
                    }
                }
            }
        });

        // Chart 2: Payment Methods Doughnut Chart
        const methodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        new Chart(methodCtx, {
            type: 'doughnut',
            data: {
                labels: @json($methodLabels),
                datasets: [{
                    data: @json($methodValues),
                    backgroundColor: [
                        '#10b981', // Emerald - Pago Movil
                        '#6366f1', // Indigo - Zelle
                        '#f59e0b', // Amber - Binance
                        '#06b6d4', // Cyan - Bank Transfer
                        '#8b5cf6'  // Purple - Cash
                    ],
                    borderWidth: 2,
                    borderColor: '#0f172a'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#cbd5e1',
                            font: { family: 'Inter', size: 10 },
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': $' + context.raw.toFixed(2) + ' USD';
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endsection
