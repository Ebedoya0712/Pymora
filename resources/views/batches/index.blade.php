@extends('layouts.app')

@section('title', 'Control de Lotes & Vencimiento - ' . $tenant->name . ' - Pymora')

@section('content')
<div x-data="batchManager()" class="space-y-6">

    <!-- Header Banner -->
    <div class="glass-card p-5 rounded-2xl border border-slate-800 bg-slate-900/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 border border-amber-500/30 flex items-center justify-center text-2xl shadow-inner text-amber-400">
                ⏳
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-white font-display">Control de Lotes y Fechas de Vencimiento</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase tracking-wider">
                        Especializado: Abasto y Supermercado
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Control preventivo de caducidad en anaqueles para rotación FIFO (Primero en Entrar, Primero en Salir) y cero pérdidas.</p>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center gap-2.5">
            <button type="button" @click="openModal = true" class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2 font-display cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Registrar Nuevo Lote</span>
            </button>
            <a href="{{ route('dashboard') }}" class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 transition-all flex items-center gap-1.5">
                <span>Volver al Dashboard</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- KPI Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Lotes -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span class="font-medium">Total Lotes Registrados</span>
                <span>📦</span>
            </div>
            <div class="text-2xl font-extrabold text-white font-mono">{{ $totalBatchesCount }}</div>
            <div class="text-[10px] text-slate-400">{{ $totalStockInBatches }} unidades registradas</div>
        </div>

        <!-- Alertas Próximas a Vencer (<= 30 días) -->
        <a href="{{ route('batches.index', ['filter' => 'expiring_soon']) }}" class="glass-card p-4 rounded-xl border {{ $alertBatchesCount > 0 ? 'border-amber-500/40 bg-amber-500/5 hover:bg-amber-500/10' : 'border-slate-800' }} space-y-1 transition-all cursor-pointer block">
            <div class="flex items-center justify-between text-amber-400 text-xs font-semibold">
                <span>Alertas por Vencer (≤ 30 días)</span>
                <span class="w-2 h-2 rounded-full {{ $alertBatchesCount > 0 ? 'bg-amber-400 animate-pulse' : 'bg-slate-600' }}"></span>
            </div>
            <div class="text-2xl font-extrabold text-amber-400 font-mono">{{ $alertBatchesCount }} Lotes</div>
            <div class="text-[10px] text-slate-400">Rotar en mostrador con prioridad</div>
        </a>

        <!-- Lotes Vencidos -->
        <a href="{{ route('batches.index', ['filter' => 'expired']) }}" class="glass-card p-4 rounded-xl border {{ $expiredBatchesCount > 0 ? 'border-rose-500/40 bg-rose-500/5 hover:bg-rose-500/10' : 'border-slate-800' }} space-y-1 transition-all cursor-pointer block">
            <div class="flex items-center justify-between text-rose-400 text-xs font-semibold">
                <span>Lotes Vencidos</span>
                <span class="text-xs">🚨</span>
            </div>
            <div class="text-2xl font-extrabold text-rose-400 font-mono">{{ $expiredBatchesCount }} Lotes</div>
            <div class="text-[10px] text-slate-400">Retirar de exhibición / anaquel</div>
        </a>

        <!-- Vigentes -->
        <a href="{{ route('batches.index', ['filter' => 'valid']) }}" class="glass-card p-4 rounded-xl border border-slate-800 hover:border-emerald-500/30 space-y-1 transition-all cursor-pointer block">
            <div class="flex items-center justify-between text-emerald-400 text-xs font-semibold">
                <span>Lotes Vigentes (> 30 días)</span>
                <span>✓</span>
            </div>
            <div class="text-2xl font-extrabold text-emerald-400 font-mono">{{ $totalBatchesCount - $alertBatchesCount - $expiredBatchesCount }}</div>
            <div class="text-[10px] text-slate-400">Fecha de consumo óptima</div>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-3">
        <form method="GET" action="{{ route('batches.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                <a href="{{ route('batches.index', ['filter' => 'all', 'search' => $search, 'branch_id' => $branchId]) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-all {{ $filter === 'all' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                    Todos ({{ $totalBatchesCount }})
                </a>
                <a href="{{ route('batches.index', ['filter' => 'expiring_soon', 'search' => $search, 'branch_id' => $branchId]) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-all flex items-center gap-1 {{ $filter === 'expiring_soon' ? 'bg-amber-500/30 text-amber-300 border border-amber-500/50 font-bold' : 'bg-slate-800 text-slate-400 hover:text-amber-300' }}">
                    <span>⚠️ Por Vencer ({{ $alertBatchesCount }})</span>
                </a>
                <a href="{{ route('batches.index', ['filter' => 'expired', 'search' => $search, 'branch_id' => $branchId]) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-all flex items-center gap-1 {{ $filter === 'expired' ? 'bg-rose-500/30 text-rose-300 border border-rose-500/50 font-bold' : 'bg-slate-800 text-slate-400 hover:text-rose-300' }}">
                    <span>🚨 Vencidos ({{ $expiredBatchesCount }})</span>
                </a>
                <a href="{{ route('batches.index', ['filter' => 'valid', 'search' => $search, 'branch_id' => $branchId]) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-all {{ $filter === 'valid' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                    Vigentes ({{ $totalBatchesCount - $alertBatchesCount - $expiredBatchesCount }})
                </a>
            </div>

            <!-- Search & Branch Filter -->
            <div class="flex items-center gap-2">
                <select name="branch_id" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-xs text-slate-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-amber-500">
                    <option value="all" {{ $branchId === 'all' ? 'selected' : '' }}>Todas las Sucursales</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>

                <div class="relative min-w-[220px]">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por Lote, Producto..." class="w-full bg-slate-900 border border-slate-700 rounded-lg pl-8 pr-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
                    <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </form>
    </div>

    <!-- Batches Table Card -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="p-3.5">Producto</th>
                        <th class="p-3.5">Lote #</th>
                        <th class="p-3.5">Sucursal / Ubicación</th>
                        <th class="p-3.5">Cantidad en Lote</th>
                        <th class="p-3.5">Fabricación</th>
                        <th class="p-3.5">Vencimiento</th>
                        <th class="p-3.5">Estado & Días Restantes</th>
                        <th class="p-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($batches as $b)
                        @php
                            $badge = $b->status_badge;
                        @endphp
                        <tr class="hover:bg-slate-900/50 transition-colors">
                            <td class="p-3.5">
                                <div class="font-bold text-white text-sm">{{ $b->product->name ?? 'Producto' }}</div>
                                <div class="flex items-center gap-2 text-[10px] font-mono text-slate-400 mt-0.5">
                                    <span>SKU: {{ $b->product->sku ?? 'N/A' }}</span>
                                    @if($b->product && $b->product->barcode)
                                        <span>•</span>
                                        <span class="text-indigo-400">Barcode: {{ $b->product->barcode }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-amber-300">
                                <span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700">{{ $b->batch_number }}</span>
                            </td>
                            <td class="p-3.5 text-slate-300">
                                {{ $b->branch->name ?? 'Altamira Principal' }}
                                @if($b->notes)
                                    <div class="text-[10px] text-slate-500 italic mt-0.5">{{ $b->notes }}</div>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-bold text-white text-sm">
                                {{ number_format($b->quantity, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">unid.</span>
                            </td>
                            <td class="p-3.5 text-slate-400 font-mono">
                                {{ $b->manufactured_date ? $b->manufactured_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="p-3.5 font-mono font-bold text-slate-200">
                                {{ $b->expiration_date ? $b->expiration_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold font-mono border inline-flex items-center gap-1.5 {{ $badge['color'] }}">
                                    <span>{{ $badge['icon'] }}</span>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                <form action="{{ route('batches.destroy', $b->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este lote?');" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 transition-colors" title="Eliminar Lote">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500 text-xs">
                                No se encontraron lotes registrados para los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: REGISTRAR NUEVO LOTE -->
    <div x-show="openModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="openModal = false" 
             class="glass-card w-full max-w-xl rounded-2xl border border-slate-700 bg-slate-900/95 shadow-2xl p-6 space-y-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-xl shadow-inner">
                        ⏳
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white font-display">Registrar Nuevo Lote de Producto</h3>
                        <p class="text-xs text-slate-400">Asocia fecha de caducidad y stock para control preventivo</p>
                    </div>
                </div>
                <button type="button" @click="openModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Form -->
            <form action="{{ route('batches.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div class="space-y-1">
                    <label class="font-bold text-slate-200">Seleccionar Producto *</label>
                    <select name="product_id" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-medium focus:border-amber-500 focus:outline-none">
                        <option value="">-- Elige un producto del inventario --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}">
                                {{ $prod->name }} (SKU: {{ $prod->sku }} | Barcode: {{ $prod->barcode ?? 'Sin código' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Número / Código de Lote *</label>
                        <input type="text" name="batch_number" required placeholder="Ej: LOTE-2026-0828" value="LOTE-{{ date('Y-md') }}" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono focus:border-amber-500 focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Cantidad / Unidades del Lote *</label>
                        <input type="number" step="0.01" min="0.01" name="quantity" required placeholder="Ej: 50" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Fecha de Vencimiento *</label>
                        <input type="date" x-model="expDate" name="expiration_date" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono focus:border-amber-500 focus:outline-none">
                        
                        <!-- Quick Date Presets -->
                        <div class="flex items-center gap-1 pt-1">
                            <button type="button" @click="setDays(15)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-[10px] text-amber-300 font-mono">+15d</button>
                            <button type="button" @click="setDays(30)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-[10px] text-amber-300 font-mono">+30d</button>
                            <button type="button" @click="setDays(90)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-[10px] text-slate-300 font-mono">+3 meses</button>
                            <button type="button" @click="setDays(180)" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-[10px] text-slate-300 font-mono">+6 meses</button>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Fecha de Fabricación (Opcional)</label>
                        <input type="date" name="manufactured_date" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-200">Sucursal de Almacenamiento</label>
                    <select name="branch_id" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white focus:border-amber-500 focus:outline-none">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-200">Notas / Ubicación en Anaquel</label>
                    <input type="text" name="notes" placeholder="Ej: Nevera 2, anaquel central, rotar primero..." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:border-amber-500 focus:outline-none">
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="openModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold rounded-xl shadow-lg shadow-amber-500/20 transition-all font-display">
                        ✓ Guardar Lote & Activar Alertas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function batchManager() {
    return {
        openModal: false,
        expDate: '',

        setDays(days) {
            const d = new Date();
            d.setDate(d.getDate() + days);
            this.expDate = d.toISOString().split('T')[0];
        }
    };
}
</script>
@endsection
