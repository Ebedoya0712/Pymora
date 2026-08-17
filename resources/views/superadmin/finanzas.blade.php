@extends('layouts.app')

@section('title', 'Finanzas Propias SaaS - Pymora Super Admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div x-data="{ showPaymentModal: false, selectedTenantId: '', selectedPlan: 'pro', selectedMonths: 1, amountUsd: 79, paymentMethod: 'pago_movil', referenceCode: '', notes: '', updateAmount() { const rates = { starter: 29, pro: 79, enterprise: 199 }; this.amountUsd = (rates[this.selectedPlan] || 79) * this.selectedMonths; } }" class="space-y-6">

    <!-- Flash Alert -->
    @if(session('success'))
    <div class="glass-card p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-300 text-xs flex items-center justify-between shadow-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
        <button @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
    </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    Control Financiero SaaS
                </span>
                <span class="text-slate-500">•</span>
                <span class="text-xs font-mono text-slate-400">Pymora Platform Revenue</span>
            </div>
            <h1 class="text-2xl font-bold text-white font-display mt-1">Finanzas Propias & Cobranza SaaS</h1>
            <p class="text-slate-400 text-sm">Rendimiento en tiempo real: ganancias por día, semana, mes y acumulado total.</p>
        </div>

        <button @click="showPaymentModal = true" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>+ Registrar Pago de Suscripción</span>
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
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-white font-display text-base">Evolución Diaria de Ganancias SaaS ($ USD)</h3>
                    <p class="text-slate-400 text-xs">Ingresos registrados en los últimos 7 días.</p>
                </div>
                <div class="flex items-center gap-2 text-xs font-mono text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-lg border border-emerald-500/20">
                    <span>Tendencia Positiva</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
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

    <!-- Main Content Grid: Transactions Table & Licence Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payments History Table (2 cols) -->
        <div class="lg:col-span-2 glass-card rounded-2xl border border-slate-800 overflow-hidden">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-white font-display text-base">Historial de Cobros & Pagos SaaS</h3>
                    <p class="text-slate-400 text-xs">Registro de transacciones por planes y licencias comerciales.</p>
                </div>
                <span class="bg-indigo-500/20 text-indigo-300 text-xs px-2.5 py-1 rounded-lg font-mono">
                    {{ $payments->count() }} Registro(s)
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 text-slate-400 font-semibold uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3">Empresa</th>
                            <th class="px-4 py-3">Plan</th>
                            <th class="px-4 py-3">Monto (USD)</th>
                            <th class="px-4 py-3">Método / Ref</th>
                            <th class="px-4 py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 font-semibold text-white">
                                    {{ $payment->tenant->name ?? 'Empresa Registrada' }}
                                    <div class="text-[10px] text-slate-400 font-mono">ID: #{{ $payment->tenant_id }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeColor = match($payment->plan_tier) {
                                            'starter' => 'bg-slate-800 text-slate-300 border-slate-700',
                                            'pro' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
                                            'enterprise' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                            default => 'bg-slate-800 text-slate-300',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded border text-[10px] font-semibold uppercase font-mono {{ $badgeColor }}">
                                        {{ $payment->plan_tier }} ({{ $payment->months_paid }}M)
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-extrabold text-emerald-400 font-mono text-sm">${{ number_format($payment->amount_usd, 2) }}</div>
                                    @if($payment->amount_ves)
                                        <div class="text-[10px] text-slate-400 font-mono">Bs. {{ number_format($payment->amount_ves, 2) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-200 uppercase text-[11px]">
                                        {{ str_replace('_', ' ', $payment->payment_method) }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono">Ref: <span class="text-slate-300 font-semibold">{{ $payment->reference_code }}</span></div>
                                </td>
                                <td class="px-4 py-3 text-slate-400 font-mono">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-500 text-sm">
                                    No hay pagos registrados aún en el sistema.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tenant Expiration & Renewal Quick Actions Card -->
        <div class="glass-card rounded-2xl border border-slate-800 p-5 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-white font-display text-base mb-1">Estado de Licencias</h3>
                <p class="text-slate-400 text-xs mb-4">Vencimiento y estado actual de suscripción de empresas.</p>

                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                    @foreach($tenants as $tenant)
                        @php
                            $daysLeft = $tenant->expires_at ? now()->diffInDays(\Carbon\Carbon::parse($tenant->expires_at), false) : 0;
                            $isExpired = $daysLeft <= 0;
                        @endphp
                        <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-slate-200 text-xs truncate">{{ $tenant->name }}</div>
                                <div class="flex items-center gap-2 mt-1 text-[11px]">
                                    <span class="font-mono text-slate-400 uppercase font-semibold text-[10px]">{{ $tenant->plan_tier }}</span>
                                    <span class="text-slate-600">•</span>
                                    @if($isExpired)
                                        <span class="text-rose-400 font-semibold">Vencida</span>
                                    @else
                                        <span class="{{ $daysLeft <= 15 ? 'text-amber-400 font-semibold' : 'text-emerald-400' }}">
                                            Vence en {{ (int)$daysLeft }} días
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <button @click="showPaymentModal = true; selectedTenantId = '{{ $tenant->id }}'; selectedPlan = '{{ $tenant->plan_tier }}'; updateAmount();" class="px-2.5 py-1 text-[11px] font-medium bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 rounded-lg hover:bg-indigo-600/40 transition-colors whitespace-nowrap">
                                + Renovar
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-800/80 text-xs text-slate-400 flex items-center justify-between">
                <span>Tasa BCV Oficial:</span>
                <span class="font-bold font-mono text-emerald-400">{{ number_format($bcvUsdRate, 2) }} VES/USD</span>
            </div>
        </div>
    </div>

    <!-- Registration Payment Modal -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="showPaymentModal = false" class="glass-card w-full max-w-lg rounded-2xl border border-slate-800 p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-white font-display">Registrar Pago de Suscripción SaaS</h3>
                    <p class="text-xs text-slate-400">Extiende la licencia y emite comprobante de cobranza.</p>
                </div>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('superadmin.payments.store') }}" method="POST" class="space-y-4">
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

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Plan Contratado</label>
                        <select name="plan_tier" x-model="selectedPlan" @change="updateAmount()" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none font-mono">
                            <option value="starter">STARTER ($29/mes)</option>
                            <option value="pro">PRO ($79/mes)</option>
                            <option value="enterprise">ENTERPRISE ($199/mes)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Duración (Meses)</label>
                        <select name="months_paid" x-model="selectedMonths" @change="updateAmount()" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none font-mono">
                            <option value="1">1 Mes</option>
                            <option value="3">3 Meses</option>
                            <option value="6">6 Meses</option>
                            <option value="12">12 Meses (1 Año)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Monto en USD ($)</label>
                        <input type="number" step="0.01" name="amount_usd" x-model="amountUsd" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-emerald-400 font-bold font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Método de Pago</label>
                        <select name="payment_method" x-model="paymentMethod" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                            <option value="pago_movil">Pago Móvil VES</option>
                            <option value="zelle">Zelle (USD)</option>
                            <option value="binance_usdt">Binance Pay (USDT)</option>
                            <option value="bank_transfer">Transferencia Bancaria</option>
                            <option value="cash_usd">Efectivo USD</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Número / Código de Referencia</label>
                        <input type="text" name="reference_code" required placeholder="Ej: 99887766" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Fecha del Pago</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Notas / Observaciones (Opcional)</label>
                    <textarea name="notes" rows="2" placeholder="Detalle adicional sobre el cobro..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-indigo-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="showPaymentModal = false" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white rounded-xl">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-600/20">Registrar Pago & Activar Licencia</button>
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
