@extends('layouts.app')

@section('title', 'Comprobantes de Pago - Pymora Super Admin')

@section('content')
<div x-data="{ 
    showPaymentModal: false, 
    viewReceiptModal: false,
    selectedReceipt: null,
    selectedTenantId: '', 
    selectedPlan: 'pro', 
    selectedMonths: 1, 
    amountUsd: {{ $plans['pro']['price'] ?? 79 }}, 
    paymentMethod: 'pago_movil', 
    referenceCode: '', 
    notes: '', 
    updateAmount() { 
        const rates = { 
            trial: {{ $plans['trial']['price'] ?? 0 }}, 
            starter: {{ $plans['starter']['price'] ?? 29 }}, 
            pro: {{ $plans['pro']['price'] ?? 79 }} 
        }; 
        this.amountUsd = (rates[this.selectedPlan] !== undefined ? rates[this.selectedPlan] : 79) * this.selectedMonths; 
    },
    openReceipt(payment) {
        this.selectedReceipt = payment;
        this.viewReceiptModal = true;
    }
}" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800/80 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    Cobranza SaaS & Licencias
                </span>
                <span class="text-slate-500">•</span>
                <span class="text-xs font-mono text-slate-400">Verificación de Comprobantes</span>
            </div>
            <h1 class="text-2xl font-bold text-white font-display mt-1">Comprobantes de Pago de Suscripción</h1>
            <p class="text-slate-400 text-sm">Listado y auditoría de comprobantes recibidos vía PayPal, Pago Móvil, Zinli y Binance.</p>
        </div>

        <button @click="showPaymentModal = true" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all transform hover:-translate-y-0.5 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Registrar Comprobante de Pago
        </button>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total USD -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="text-slate-400 text-xs font-medium">Total Recaudado SaaS</div>
            <div class="text-2xl font-bold text-emerald-400 font-display">${{ number_format($totalAmountUsd, 2) }}</div>
            <div class="text-[11px] text-slate-400 font-mono">Suscripciones cobradas</div>
        </div>

        <!-- Card 2: Pago Móvil -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-xs font-medium">Pago Móvil (VES)</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
            </div>
            <div class="text-2xl font-bold text-white font-display">{{ $countPagoMovil }} <span class="text-xs text-slate-400 font-normal">pagos</span></div>
            <div class="text-[11px] text-emerald-400">Canal Nacional Bolívares</div>
        </div>

        <!-- Card 3: Binance & Zinli -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-xs font-medium">Binance & Zinli</span>
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
            </div>
            <div class="text-2xl font-bold text-amber-300 font-display">{{ $countBinance + $countZinli }} <span class="text-xs text-slate-400 font-normal">pagos</span></div>
            <div class="text-[11px] text-amber-400 font-mono">USDT & Wallet Digital</div>
        </div>

        <!-- Card 4: PayPal -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-xs font-medium">PayPal (USD)</span>
                <span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span>
            </div>
            <div class="text-2xl font-bold text-sky-300 font-display">{{ $countPaypal }} <span class="text-xs text-slate-400 font-normal">pagos</span></div>
            <div class="text-[11px] text-sky-400">Pasarela Internacional</div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="glass-card p-4 rounded-xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Method Pills -->
        <div class="flex flex-wrap items-center gap-2 text-xs font-medium">
            <a href="{{ route('superadmin.comprobantes') }}" class="px-3 py-1.5 rounded-lg border transition-all {{ !request('method') || request('method') === 'all' ? 'bg-indigo-600/20 text-indigo-300 border-indigo-500/30 font-bold' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white' }}">
                Todos ({{ $payments->total() }})
            </a>
            <a href="{{ route('superadmin.comprobantes', ['method' => 'paypal']) }}" class="px-3 py-1.5 rounded-lg border transition-all flex items-center gap-1.5 {{ request('method') === 'paypal' ? 'bg-sky-500/20 text-sky-300 border-sky-500/40 font-bold' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white' }}">
                <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                PayPal
            </a>
            <a href="{{ route('superadmin.comprobantes', ['method' => 'pago_movil']) }}" class="px-3 py-1.5 rounded-lg border transition-all flex items-center gap-1.5 {{ request('method') === 'pago_movil' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 font-bold' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white' }}">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Pago Móvil
            </a>
            <a href="{{ route('superadmin.comprobantes', ['method' => 'zinli']) }}" class="px-3 py-1.5 rounded-lg border transition-all flex items-center gap-1.5 {{ request('method') === 'zinli' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40 font-bold' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white' }}">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                Zinli
            </a>
            <a href="{{ route('superadmin.comprobantes', ['method' => 'binance']) }}" class="px-3 py-1.5 rounded-lg border transition-all flex items-center gap-1.5 {{ request('method') === 'binance' ? 'bg-yellow-500/20 text-yellow-300 border-yellow-500/40 font-bold' : 'bg-slate-900/60 text-slate-400 border-slate-800 hover:text-white' }}">
                <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                Binance
            </a>
        </div>

        <!-- Search Input -->
        <form method="GET" action="{{ route('superadmin.comprobantes') }}" class="flex items-center gap-2">
            @if(request('method'))
                <input type="hidden" name="method" value="{{ request('method') }}">
            @endif
            <div class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por empresa o referencia..." class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-9 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none font-mono">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition-all">
                Filtrar
            </button>
        </form>
    </div>

    <!-- Main Vouchers Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-2xl">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-white font-display text-sm">Historial de Comprobantes de Pago Registrados</h3>
                <p class="text-[11px] text-slate-400">Comprobantes emitidos por empresas suscritos al SaaS Pymora.</p>
            </div>
            <span class="text-xs text-slate-400 font-mono bg-slate-900 px-3 py-1 rounded-lg border border-slate-800">
                Mostrando {{ $payments->count() }} de {{ $payments->total() }} comprobante(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 font-semibold uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5">Empresa Cliente</th>
                        <th class="px-4 py-3.5">Plan Contratado</th>
                        <th class="px-4 py-3.5">Monto (USD / VES)</th>
                        <th class="px-4 py-3.5">Método de Pago</th>
                        <th class="px-4 py-3.5">Nro. Comprobante / Ref</th>
                        <th class="px-4 py-3.5">Fecha de Emisión</th>
                        <th class="px-4 py-3.5 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-sans">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow">
                                        {{ strtoupper(substr($payment->tenant->name ?? 'E', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-white text-sm">{{ $payment->tenant->name ?? 'Empresa Registrada' }}</div>
                                        <div class="text-[10px] text-indigo-400 font-mono">{{ $payment->tenant->subdomain ?? 'subdominio' }}.pymora.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @php
                                    $planBadge = match($payment->plan_tier) {
                                        'trial' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                        'starter' => 'bg-slate-800 text-slate-300 border-slate-700',
                                        'pro' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
                                        default => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                    };
                                    $planName = match($payment->plan_tier) {
                                        'trial' => '1 Mes Gratis',
                                        'starter' => 'Plan Sencillo',
                                        'pro' => 'Plan Pro',
                                        default => 'Plan Enterprise',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-md border text-[10px] font-semibold font-mono uppercase {{ $planBadge }}">
                                    {{ $planName }} ({{ $payment->months_paid }}M)
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-extrabold text-emerald-400 font-mono text-sm">${{ number_format($payment->amount_usd, 2) }} <span class="text-[10px] text-slate-400 font-normal">USD</span></div>
                                @if($payment->amount_ves)
                                    <div class="text-[10px] text-slate-400 font-mono">Bs. {{ number_format($payment->amount_ves, 2) }} VES</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @php
                                    $method = $payment->payment_method;
                                    $methodStyle = match($method) {
                                        'paypal' => 'bg-sky-500/20 text-sky-300 border-sky-500/30',
                                        'pago_movil' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                        'zinli' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                        'binance', 'binance_usdt' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                        default => 'bg-slate-800 text-slate-300 border-slate-700',
                                    };
                                    $methodLabel = match($method) {
                                        'paypal' => 'PayPal (USD)',
                                        'pago_movil' => 'Pago Móvil (VES)',
                                        'zinli' => 'Zinli Wallet',
                                        'binance', 'binance_usdt' => 'Binance Pay / USDT',
                                        default => strtoupper(str_replace('_', ' ', $method)),
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-md border text-[10px] font-bold font-mono uppercase inline-flex items-center gap-1.5 {{ $methodStyle }}">
                                    @if($method === 'paypal')
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                                    @elseif($method === 'pago_movil')
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    @elseif($method === 'zinli')
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                    @else
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>
                                    @endif
                                    {{ $methodLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="bg-slate-900 border border-slate-700 text-indigo-300 font-mono text-[11px] px-2.5 py-1 rounded-lg">
                                    {{ $payment->reference_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-400 font-mono text-[11px]">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <button @click="openReceipt({{ json_encode($payment) }})" class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white border border-slate-700 text-[11px] font-semibold rounded-lg transition-all inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Ver Comprobante
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-500">
                                No se encontraron comprobantes de pago registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-900/50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Detail View of Payment Voucher -->
    <div x-show="viewReceiptModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="viewReceiptModal = false" class="glass-card w-full max-w-md rounded-2xl border border-slate-800 p-6 shadow-2xl space-y-5">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-base font-bold text-white font-display">Comprobante de Pago Oficial</h3>
                </div>
                <button @click="viewReceiptModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <template x-if="selectedReceipt">
                <div class="space-y-4 text-xs text-slate-300">
                    <!-- Voucher Digital Card -->
                    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 space-y-3 font-mono">
                        <div class="flex justify-between border-b border-slate-800 pb-2">
                            <span class="text-slate-400">Empresa:</span>
                            <span class="font-bold text-white font-sans" x-text="selectedReceipt.tenant ? selectedReceipt.tenant.name : 'Empresa'"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800 pb-2">
                            <span class="text-slate-400">Método de Pago:</span>
                            <span class="font-bold text-emerald-400 uppercase" x-text="selectedReceipt.payment_method"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800 pb-2">
                            <span class="text-slate-400">Nro. Referencia:</span>
                            <span class="font-bold text-indigo-400" x-text="selectedReceipt.reference_code"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800 pb-2">
                            <span class="text-slate-400">Monto USD:</span>
                            <span class="font-bold text-emerald-400 text-sm" x-text="'$' + parseFloat(selectedReceipt.amount_usd).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between border-b border-slate-800 pb-2" x-if="selectedReceipt.amount_ves">
                            <span class="text-slate-400">Monto VES (BCV):</span>
                            <span class="font-bold text-slate-200" x-text="'Bs. ' + parseFloat(selectedReceipt.amount_ves).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Fecha de Pago:</span>
                            <span class="text-slate-300" x-text="selectedReceipt.payment_date"></span>
                        </div>
                    </div>

                    <div class="bg-slate-900/50 p-3 rounded-lg border border-slate-800">
                        <div class="text-[11px] font-semibold text-slate-400 mb-1">Notas / Observaciones:</div>
                        <div class="text-xs text-slate-200 italic" x-text="selectedReceipt.notes || 'Sin observaciones adicionales.'"></div>
                    </div>
                </div>
            </template>

            <div class="pt-2 flex justify-end">
                <button @click="viewReceiptModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition-all">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Register New Payment Voucher -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="showPaymentModal = false" class="glass-card w-full max-w-lg rounded-2xl border border-slate-800 p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-white font-display">Registrar Comprobante de Pago</h3>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('superadmin.payments.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Empresa Cliente</label>
                    <select name="tenant_id" x-model="selectedTenantId" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none">
                        <option value="">-- Seleccionar Empresa --</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->subdomain }}.pymora.com)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Plan Contratado</label>
                        <select name="plan_tier" x-model="selectedPlan" @change="updateAmount()" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none font-mono">
                            <option value="trial">PLAN 1 MES GRATIS ($0/mes)</option>
                            <option value="starter">PLAN SENCILLO ($29/mes)</option>
                            <option value="pro">PLAN PRO ($79/mes)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Duración (Meses)</label>
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
                        <label class="block font-semibold text-slate-300 mb-1">Método de Pago</label>
                        <select name="payment_method" x-model="paymentMethod" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white focus:border-indigo-500 focus:outline-none font-mono">
                            <option value="paypal">PayPal (USD)</option>
                            <option value="pago_movil">Pago Móvil (VES)</option>
                            <option value="zinli">Zinli Wallet (USD)</option>
                            <option value="binance">Binance Pay / USDT</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Nro. Comprobante / Referencia</label>
                        <input type="text" name="reference_code" x-model="referenceCode" required placeholder="Ej: PP-99210 o PM-88310" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Monto Cobrado (USD)</label>
                        <input type="number" step="0.01" name="amount_usd" x-model="amountUsd" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-emerald-400 font-bold font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Fecha de Pago</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-300 mb-1">Notas / Observaciones</label>
                    <textarea name="notes" x-model="notes" rows="2" placeholder="Detalles adicionales del comprobante..." class="w-full bg-slate-900 border border-slate-700 rounded-xl p-2.5 text-xs text-white focus:border-indigo-500 focus:outline-none"></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-800">
                    <button type="button" @click="showPaymentModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-600/20 transition-all">
                        Guardar Comprobante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
