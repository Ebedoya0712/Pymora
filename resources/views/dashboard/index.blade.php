@extends('layouts.app')

@section('title', 'CFO Dashboard & Resumen Ejecutivo - Pymora')

@section('content')
<div class="space-y-6">
    <!-- Header Title & Quick Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white font-display">Dashboard CFO & Resumen Ejecutivo</h2>
            <p class="text-xs text-slate-400">Control en tiempo real de finanzas, inventario y ventas multimoneda (USD / VES).</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-xs font-semibold rounded-lg shadow-lg shadow-emerald-500/20 flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva Venta POS
            </a>
            <a href="{{ route('quotes.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg border border-slate-700 flex items-center gap-2 transition-all">
                Cotizar Presupuesto
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: Ventas Hoy -->
        <div class="glass-card glass-card-hover p-4 rounded-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Ventas Totales Hoy</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">${{ number_format($salesTodayUsd, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">USD</span></div>
            <div class="text-xs font-mono text-emerald-400">Bs {{ number_format($salesTodayVes, 2) }} VES</div>
        </div>

        <!-- KPI 2: Margen Bruto Estimado -->
        <div class="glass-card glass-card-hover p-4 rounded-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Margen Bruto Estimado</span>
                <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">34.8%</div>
            <div class="text-xs text-slate-400">+2.4% vs mes anterior</div>
        </div>

        <!-- KPI 3: Productos en Inventario -->
        <div class="glass-card glass-card-hover p-4 rounded-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Productos & Lotes</span>
                <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">{{ $totalProductsCount }} <span class="text-xs font-normal text-slate-400 font-sans">SKUs</span></div>
            <div class="text-xs text-amber-400">2 productos con stock bajo</div>
        </div>

        <!-- KPI 4: Deudas Pendientes (CXC) -->
        <div class="glass-card glass-card-hover p-4 rounded-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Cuentas por Cobrar (CXC)</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">${{ number_format($totalDebtUsd, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">USD</span></div>
            <div class="text-xs text-slate-400">1 cliente con crédito disponible</div>
        </div>
    </div>

    <!-- Main Grid: Cash ledger & Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Recent Sales & Active Cash Session -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Active Cash Drawer Status -->
            <div class="glass-card p-5 rounded-xl space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></div>
                        <h3 class="font-bold text-white text-base">Estado de Caja en Turno</h3>
                    </div>
                    <span class="px-2.5 py-1 bg-emerald-500/20 text-emerald-400 text-xs font-mono rounded-md border border-emerald-500/30">ABIERTA</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="bg-slate-900/60 p-3 rounded-lg border border-slate-800">
                        <div class="text-slate-400">Monto Inicial en Turno</div>
                        <div class="text-sm font-bold text-slate-200 mt-1">$50.00 USD / Bs 1,000</div>
                    </div>
                    <div class="bg-slate-900/60 p-3 rounded-lg border border-slate-800">
                        <div class="text-slate-400">Ventas en Efectivo USD</div>
                        <div class="text-sm font-bold text-emerald-400 mt-1">$200.00 USD</div>
                    </div>
                    <div class="bg-slate-900/60 p-3 rounded-lg border border-slate-800">
                        <div class="text-slate-400">Total Esperado en Arqueo</div>
                        <div class="text-sm font-bold text-indigo-400 mt-1">$250.00 USD / Bs 5,000</div>
                    </div>
                </div>
            </div>

            <!-- Recent Sales Table -->
            <div class="glass-card p-5 rounded-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-white text-base">Últimas Transacciones de Venta</h3>
                    <a href="{{ route('pos.index') }}" class="text-xs text-indigo-400 hover:underline">Ver todas las ventas &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/80 text-slate-400 uppercase text-[10px]">
                            <tr>
                                <th class="p-3">Factura / Ticket</th>
                                <th class="p-3">Cliente</th>
                                <th class="p-3">Total USD</th>
                                <th class="p-3">Total VES</th>
                                <th class="p-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($recentSales as $s)
                            <tr class="hover:bg-slate-800/40">
                                <td class="p-3 font-mono font-bold text-indigo-400">{{ $s->sale_number }}</td>
                                <td class="p-3">{{ $s->customer->name ?? 'Cliente General' }}</td>
                                <td class="p-3 font-bold text-white">${{ number_format($s->total_usd, 2) }}</td>
                                <td class="p-3 font-mono text-emerald-400">Bs {{ number_format($s->total_ves, 2) }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">PAGADO</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500">No hay ventas registradas hoy.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Column 3: Multi-Account Ledger & Quick Alerts -->
        <div class="space-y-6">
            <!-- Bank Accounts Balance Card -->
            <div class="glass-card p-5 rounded-xl space-y-4">
                <h3 class="font-bold text-white text-base">Saldos en Cuentas & Bancos</h3>
                <div class="space-y-3">
                    @foreach($bankAccounts as $acc)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-900/60 border border-slate-800">
                        <div>
                            <div class="text-xs font-semibold text-slate-200">{{ $acc->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $acc->account_number }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold font-mono text-emerald-400">
                                {{ $acc->currency === 'USD' ? '$' : 'Bs ' }}{{ number_format($acc->balance, 2) }}
                            </div>
                            <div class="text-[10px] text-slate-500 uppercase">{{ $acc->currency }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Advanced Modules Indicators -->
            <div class="glass-card p-5 rounded-xl space-y-3">
                <h3 class="font-bold text-white text-base">Alertas & Módulos Avanzados</h3>
                <div class="space-y-2 text-xs">
                    <a href="{{ route('quotes.index') }}" class="flex items-center justify-between p-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 hover:bg-amber-500/20 transition-all">
                        <span>Cotizaciones Pendientes de Aprobación</span>
                        <span class="font-mono font-bold bg-amber-500/30 px-2 py-0.5 rounded text-amber-200">{{ $pendingQuotesCount }}</span>
                    </a>

                    <a href="{{ route('transfers.index') }}" class="flex items-center justify-between p-3 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 hover:bg-indigo-500/20 transition-all">
                        <span>Traslados en Tránsito entre Sucursales</span>
                        <span class="font-mono font-bold bg-indigo-500/30 px-2 py-0.5 rounded text-indigo-200">{{ $transfersInTransitCount }}</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
