@extends('layouts.app')

@section('title', 'Inventario de Productos - ' . $tenant->name . ' - Pymora')

@section('content')
<div x-data="inventoryManager({{ $lowStockCount > 0 ? 'true' : 'false' }})" class="space-y-6">

    <!-- Header Banner -->
    <div class="glass-card p-5 rounded-2xl border border-slate-800 bg-slate-900/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center text-2xl shadow-inner text-indigo-400">
                📦
            </div>
            <div>
                <div>
                    <h2 class="text-xl font-bold text-white font-display">Inventario de Productos</h2>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">Gestión de existencias en tiempo real, alertas de stock crítico y valoración multimoneda (USD / VES).</p>
            </div>
        </div>

        <!-- Header Actions -->
        <div class="flex items-center flex-wrap gap-2.5">
            @if($lowStockCount > 0)
                <button type="button" @click="showLowStockAlertModal = true" class="px-3.5 py-2.5 bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 text-xs font-bold rounded-xl shadow-lg shadow-rose-500/10 transition-all flex items-center gap-2 animate-pulse cursor-pointer">
                    <span>🚨</span>
                    <span>Alerta Stock Crítico ({{ $lowStockCount }})</span>
                </button>
            @endif

            <button type="button" @click="openCreateModal = true" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2 font-display cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Agregar Producto</span>
            </button>
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
        <!-- Total Productos -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="flex items-center justify-between text-slate-400 text-xs">
                <span class="font-medium">Total Productos</span>
                <span>📋</span>
            </div>
            <div class="text-2xl font-extrabold text-white font-mono">{{ $totalProductsCount }}</div>
            <div class="text-[10px] text-slate-400">{{ number_format($totalStockUnits, 0) }} unidades totales</div>
        </div>

        <!-- Stock Bajo / Crítico Card -->
        <div @click="{{ $lowStockCount > 0 ? 'showLowStockAlertModal = true' : '' }}" 
             class="glass-card p-4 rounded-xl border {{ $lowStockCount > 0 ? 'border-rose-500/50 bg-rose-500/10 hover:bg-rose-500/15 cursor-pointer ring-1 ring-rose-500/30' : 'border-slate-800' }} space-y-1 transition-all">
            <div class="flex items-center justify-between {{ $lowStockCount > 0 ? 'text-rose-400' : 'text-slate-400' }} text-xs font-semibold">
                <span>Stock Bajo / Por Agotarse</span>
                <span class="w-2.5 h-2.5 rounded-full {{ $lowStockCount > 0 ? 'bg-rose-500 animate-ping' : 'bg-emerald-400' }}"></span>
            </div>
            <div class="text-2xl font-extrabold {{ $lowStockCount > 0 ? 'text-rose-400' : 'text-emerald-400' }} font-mono">
                {{ $lowStockCount }} <span class="text-xs font-normal font-sans text-slate-400">Productos</span>
            </div>
            <div class="text-[10px] {{ $lowStockCount > 0 ? 'text-rose-300 font-semibold' : 'text-slate-400' }}">
                {{ $lowStockCount > 0 ? '⚠️ Haz clic para ver el reporte de alerta' : '✓ Niveles de stock en rango normal' }}
            </div>
        </div>

        <!-- Valoración en USD -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="flex items-center justify-between text-emerald-400 text-xs font-semibold">
                <span>Valor del Inventario (USD)</span>
                <span>💵</span>
            </div>
            <div class="text-2xl font-extrabold text-emerald-400 font-mono">${{ number_format($totalInventoryValueUsd, 2) }}</div>
            <div class="text-[10px] text-slate-400">Calculado a precio de venta</div>
        </div>

        <!-- Valoración en Bolívares (BCV) -->
        <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-1">
            <div class="flex items-center justify-between text-indigo-400 text-xs font-semibold">
                <span>Valoración en Bolívares (BCV)</span>
                <span class="text-[10px] font-mono text-slate-400">Tasa: {{ number_format($bcvUsdRate, 2) }}</span>
            </div>
            <div class="text-2xl font-extrabold text-indigo-400 font-mono">Bs {{ number_format($totalInventoryValueVes, 2) }}</div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="glass-card p-4 rounded-xl border border-slate-800 space-y-3">
        <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                <a href="{{ route('inventory.index', ['filter' => 'all', 'category_id' => $categoryId, 'search' => $search]) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-all {{ $filter === 'all' ? 'bg-indigo-600 text-white font-bold' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                    Todos ({{ $totalProductsCount }})
                </a>
                <a href="{{ route('inventory.index', ['filter' => 'low_stock', 'category_id' => $categoryId, 'search' => $search]) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-all flex items-center gap-1.5 {{ $filter === 'low_stock' ? 'bg-rose-600 text-white font-bold' : 'bg-slate-800 text-slate-400 hover:text-rose-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $lowStockCount > 0 ? 'bg-rose-400 animate-pulse' : 'bg-slate-500' }}"></span>
                    <span>🚨 Stock Crítico / Bajo ({{ $lowStockCount }})</span>
                </a>
                <a href="{{ route('inventory.index', ['filter' => 'normal', 'category_id' => $categoryId, 'search' => $search]) }}" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-all {{ $filter === 'normal' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                    ✓ Stock Normal ({{ $totalProductsCount - $lowStockCount }})
                </a>
            </div>

            <!-- Search & Category Filter -->
            <div class="flex items-center gap-2">
                <select name="category_id" 
                        x-model="selectedCategory" 
                        class="bg-slate-900 border border-slate-700 text-xs text-slate-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-indigo-500 cursor-pointer">
                    <option value="all">Todas las Categorías</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <div class="relative min-w-[240px]">
                    <input type="text" 
                           name="search" 
                           x-model="searchQuery" 
                           @keydown.enter.prevent
                           placeholder="Buscar por Nombre, SKU, Barcode..." 
                           class="w-full bg-slate-900 border border-slate-700 rounded-lg pl-8 pr-7 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                    <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    
                    <!-- Botón para limpiar búsqueda rápida -->
                    <button type="button" 
                            x-show="searchQuery.length > 0" 
                            @click="searchQuery = ''" 
                            class="absolute right-2 top-2 text-slate-400 hover:text-white transition-colors"
                            title="Limpiar búsqueda">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Products Table Card -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/90 text-slate-400 uppercase font-semibold text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="p-3.5">Producto / SKU</th>
                        <th class="p-3.5">Categoría</th>
                        <th class="p-3.5">Costo USD</th>
                        <th class="p-3.5">Precio USD</th>
                        <th class="p-3.5">Precio BCV (VES)</th>
                        <th class="p-3.5">Stock Total</th>
                        <th class="p-3.5">Mínimo Alerta</th>
                        <th class="p-3.5">Estado de Stock</th>
                        <th class="p-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($products as $p)
                        <tr x-show="matchesSearch('{{ addslashes($p->name) }}', '{{ addslashes($p->sku ?? '') }}', '{{ addslashes($p->barcode ?? '') }}', '{{ $p->category_id ?? '' }}', '{{ addslashes($p->category->name ?? 'General') }}')" 
                            class="transition-colors {{ $p->is_low_stock ? 'bg-rose-950/20 border-l-4 border-rose-500 hover:bg-rose-900/30' : 'hover:bg-slate-900/50' }}">
                            <!-- Producto -->
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-slate-950 border border-slate-700/80 overflow-hidden shrink-0 shadow-md relative group">
                                        @if($p->image_url)
                                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-slate-900 text-indigo-400 font-extrabold text-xs font-mono border border-slate-800">
                                                📦
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-sm flex items-center gap-2">
                                            <span class="{{ $p->is_low_stock ? 'text-rose-200' : 'text-white' }}">{{ $p->name }}</span>
                                            @if($p->has_lots)
                                                <span class="bg-amber-500/20 text-amber-300 text-[9px] px-1.5 py-0.5 rounded font-mono font-bold">LOTE</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] font-mono text-slate-400 mt-0.5">
                                            <span class="text-indigo-400 font-bold">SKU: {{ $p->sku }}</span>
                                            @if($p->barcode)
                                                <span>•</span>
                                                <span class="text-slate-400">Barcode: {{ $p->barcode }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Categoría -->
                            <td class="p-3.5 text-slate-300">
                                <span class="px-2 py-0.5 rounded bg-slate-800/80 border border-slate-700 text-[11px]">
                                    {{ $p->category->name ?? 'General' }}
                                </span>
                            </td>

                            <!-- Costo USD -->
                            <td class="p-3.5 font-mono text-slate-400">
                                ${{ number_format($p->cost_usd, 2) }}
                            </td>

                            <!-- Precio USD -->
                            <td class="p-3.5 font-mono font-bold text-emerald-400 text-sm">
                                ${{ number_format($p->price_usd, 2) }}
                            </td>

                            <!-- Precio VES -->
                            <td class="p-3.5 font-mono font-bold text-indigo-300">
                                Bs {{ number_format($p->price_usd * $bcvUsdRate, 2) }}
                            </td>

                            <!-- Stock Total (RED IF LOW) -->
                            <td class="p-3.5 font-mono font-extrabold text-sm {{ $p->is_low_stock ? 'text-rose-400 font-black' : 'text-white' }}">
                                {{ (float)$p->total_stock == (int)$p->total_stock ? number_format($p->total_stock, 0) : rtrim(rtrim(number_format($p->total_stock, 2), '0'), '.') }} <span class="text-xs font-normal text-slate-400 font-sans">{{ $p->unit }}</span>
                            </td>

                            <!-- Mínimo Alerta -->
                            <td class="p-3.5 font-mono text-slate-400 text-xs">
                                {{ (float)$p->min_alert == (int)$p->min_alert ? number_format($p->min_alert, 0) : rtrim(rtrim(number_format($p->min_alert, 2), '0'), '.') }} {{ $p->unit }}
                            </td>

                            <!-- Estado Stock (RED BADGE IF LOW) -->
                            <td class="p-3.5">
                                @if($p->is_low_stock)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-rose-500/20 text-rose-300 border border-rose-500/40 inline-flex items-center gap-1.5 shadow-sm shadow-rose-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                                        <span>🚨 STOCK BAJO</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 inline-flex items-center gap-1">
                                        <span>✓</span>
                                        <span>NORMAL</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Acciones -->
                            <td class="p-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="openAdjustModal('{{ $p->id }}', '{{ addslashes($p->name) }}', '{{ $p->total_stock }}', '{{ $p->min_alert }}')" 
                                            class="px-2.5 py-1 rounded-lg {{ $p->is_low_stock ? 'bg-rose-500 hover:bg-rose-600 text-white font-bold shadow-md shadow-rose-500/20' : 'bg-slate-800 hover:bg-slate-700 text-slate-200' }} text-[11px] transition-all flex items-center gap-1">
                                        <span>+ Reponer</span>
                                    </button>

                                    <!-- Botón Editar Producto -->
                                    <button type="button" 
                                            @click="openEditProductModal({
                                                id: {{ $p->id }},
                                                name: '{{ addslashes($p->name) }}',
                                                category_id: '{{ $p->category_id }}',
                                                sku: '{{ addslashes($p->sku ?? '') }}',
                                                barcode: '{{ addslashes($p->barcode ?? '') }}',
                                                image_url: '{{ addslashes($p->image_url ?? '') }}',
                                                unit: '{{ addslashes($p->unit ?? 'Unidad') }}',
                                                cost_usd: '{{ $p->cost_usd }}',
                                                price_usd: '{{ $p->price_usd }}',
                                                min_stock_alert: '{{ $p->min_stock_alert ?? 10 }}',
                                                has_lots: {{ $p->has_lots ? 'true' : 'false' }},
                                                description: '{{ addslashes($p->description ?? '') }}'
                                            })" 
                                            class="p-1 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/25 text-indigo-400 hover:text-indigo-300 transition-colors" 
                                            title="Editar Información del Producto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- Botón Eliminar Producto (Abre Modal de Confirmación) -->
                                    <button type="button" 
                                            @click="openDeleteProductModal('{{ $p->id }}', '{{ addslashes($p->name) }}')" 
                                            class="p-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/25 text-rose-400 hover:text-rose-300 transition-colors" 
                                            title="Eliminar Producto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-500 text-xs">
                                No se encontraron productos registrados para los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse

                    <!-- Mensaje en vivo cuando la búsqueda no coincide con ningún producto -->
                    <tr x-show="visibleCount === 0 && productsList.length > 0" x-cloak>
                        <td colspan="9" class="p-8 text-center text-slate-400 text-xs space-y-1">
                            <div class="text-xl">🔍</div>
                            <div class="text-sm font-semibold text-white">No se encontraron productos que coincidan</div>
                            <div class="text-[11px] text-slate-400">No hay coincidencias para "<span class="text-indigo-400 font-semibold" x-text="searchQuery"></span>". Prueba con otro nombre, SKU o código de barras.</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- 🚨 MODAL ALERT: ALERTA DE PRODUCTOS CON POCO STOCK        -->
    <!-- ========================================================= -->
    <div x-show="showLowStockAlertModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="showLowStockAlertModal = false" 
             class="glass-card w-full max-w-2xl rounded-2xl border-2 border-rose-500/50 bg-slate-900/95 shadow-2xl shadow-rose-500/20 p-6 space-y-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <!-- Modal Alert Header -->
            <div class="flex items-center justify-between border-b border-rose-500/30 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center text-2xl shadow-inner animate-pulse">
                        🚨
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-white font-display">Alerta de Stock Crítico</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold font-mono bg-rose-500/30 text-rose-300 border border-rose-500/50 uppercase">
                                {{ $lowStockCount }} Por Agotarse
                            </span>
                        </div>
                        <p class="text-xs text-rose-200/80">Los siguientes productos han llegado o están por debajo del nivel mínimo de alerta.</p>
                    </div>
                </div>
                <button type="button" @click="showLowStockAlertModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Low Stock Products List -->
            <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                @forelse($lowStockProducts as $lowP)
                    <div class="p-3.5 rounded-xl bg-rose-950/30 border border-rose-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-white text-sm flex items-center gap-2">
                                <span>{{ $lowP->name }}</span>
                                <span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700 text-[10px] font-mono text-indigo-300">SKU: {{ $lowP->sku }}</span>
                            </div>
                            <div class="text-xs text-slate-300 mt-1 flex items-center gap-3">
                                <span>Existencia Actual: <strong class="text-rose-400 font-mono font-bold text-sm">{{ (float)$lowP->total_stock == (int)$lowP->total_stock ? number_format($lowP->total_stock, 0) : number_format($lowP->total_stock, 2) }} {{ $lowP->unit }}</strong></span>
                                <span>•</span>
                                <span>Alerta Mínima: <strong class="text-slate-200 font-mono">{{ (float)$lowP->min_alert == (int)$lowP->min_alert ? number_format($lowP->min_alert, 0) : number_format($lowP->min_alert, 2) }} {{ $lowP->unit }}</strong></span>
                            </div>
                        </div>

                        <!-- Quick Action Button -->
                        <button type="button" 
                                @click="showLowStockAlertModal = false; openAdjustModal('{{ $lowP->id }}', '{{ addslashes($lowP->name) }}', '{{ $lowP->total_stock }}', '{{ $lowP->min_alert }}')"
                                class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-lg shadow transition-all flex items-center justify-center gap-1.5 shrink-0">
                            <span>+ Reponer Stock</span>
                        </button>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-400 text-xs">
                        No hay productos con stock crítico en este momento.
                    </div>
                @endforelse
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                <div class="text-[11px] text-slate-400">
                    💡 Mantén tus anaqueles abastecidos para no perder ventas.
                </div>
                <button type="button" @click="showLowStockAlertModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl transition-colors">
                    Entendido / Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- 📦 MODAL: REGISTRAR NUEVO PRODUCTO                        -->
    <!-- ========================================================= -->
    <div x-show="openCreateModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="openCreateModal = false" 
             class="glass-card w-full max-w-xl rounded-2xl border border-slate-700 bg-slate-900/95 shadow-2xl p-6 space-y-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-xl shadow-inner">
                        📦
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white font-display">Registrar Nuevo Producto</h3>
                        <p class="text-xs text-slate-400">Agrega un artículo al catálogo con stock y niveles de alerta</p>
                    </div>
                </div>
                <button type="button" @click="openCreateModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('inventory.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Nombre del Producto *</label>
                        <input type="text" name="name" required placeholder="Ej: Harina de Maíz Juana 1kg" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">URL de Imagen (Opcional)</label>
                        <input type="text" name="image_url" placeholder="https://..." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono text-xs focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Categoría</label>
                        <select name="category_id" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white focus:border-indigo-500 focus:outline-none">
                            <option value="">-- Sin Categoría --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Código de Barras (Opcional)</label>
                        <input type="text" name="barcode" placeholder="Ej: 7591234567890" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Costo Compra USD *</label>
                        <input type="number" step="0.01" min="0" name="cost_usd" required placeholder="Ej: 1.20" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Precio Venta USD *</label>
                        <input type="number" step="0.01" min="0.01" name="price_usd" required placeholder="Ej: 1.80" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Stock Inicial (Unidades) *</label>
                        <input type="number" step="0.01" min="0" name="stock_quantity" value="20" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-rose-300">Alerta de Stock Mínimo (Rojo) *</label>
                        <input type="number" step="0.01" min="0" name="min_stock_alert" value="10" required class="w-full bg-slate-950 border border-rose-500/40 rounded-xl px-3 py-2.5 text-rose-200 font-mono focus:border-rose-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="openCreateModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all font-display">
                        ✓ Guardar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- 🔄 MODAL: REPONER / AJUSTAR STOCK                         -->
    <!-- ========================================================= -->
    <div x-show="openStockModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="openStockModal = false" 
             class="glass-card w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900/95 shadow-2xl p-6 space-y-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div>
                    <h3 class="text-base font-bold text-white font-display">Reponer / Ajustar Stock</h3>
                    <p class="text-xs text-slate-400 font-semibold" x-text="adjustProductName"></p>
                </div>
                <button type="button" @click="openStockModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('inventory.updateStock') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="product_id" :value="adjustProductId">

                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between">
                    <span class="text-slate-400">Stock Actual:</span>
                    <span class="text-sm font-extrabold font-mono text-white" x-text="adjustCurrentStock + ' unidades'"></span>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-200">Tipo de Ajuste</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-700 bg-slate-950 cursor-pointer">
                            <input type="radio" name="operation" value="add" checked class="text-indigo-600">
                            <span class="text-slate-200 font-semibold">+ Sumar Stock</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-700 bg-slate-950 cursor-pointer">
                            <input type="radio" name="operation" value="set" class="text-indigo-600">
                            <span class="text-slate-200 font-semibold">Fijar Nuevo Total</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-200">Cantidad *</label>
                    <input type="number" step="0.01" min="0.01" name="quantity" required placeholder="Ej: 50" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-white font-mono focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-rose-300">Alerta de Stock Mínimo (Rojo)</label>
                    <input type="number" step="0.01" min="0" name="min_stock_alert" :value="adjustMinAlert" class="w-full bg-slate-950 border border-rose-500/40 rounded-xl px-3 py-2.5 text-rose-200 font-mono focus:border-rose-500 focus:outline-none">
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="openStockModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all font-display">
                        ✓ Guardar Reposición
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- ✏️ MODAL: EDITAR INFORMACIÓN DE PRODUCTO                  -->
    <!-- ========================================================= -->
    <div x-show="openEditModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="openEditModal = false" 
             class="glass-card w-full max-w-2xl rounded-2xl border border-slate-700 bg-slate-900/95 shadow-2xl p-6 space-y-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 flex items-center justify-center text-lg shadow-inner">
                        ✏️
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white font-display">Editar Información del Producto</h3>
                        <p class="text-xs text-slate-400">Actualiza precios, códigos, categoría y parámetros de inventario</p>
                    </div>
                </div>
                <button type="button" @click="openEditModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'/inventory/' + editProductData.id + '/update'" method="POST" class="space-y-4 text-xs">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Nombre del Producto *</label>
                        <input type="text" name="name" x-model="editProductData.name" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white font-medium focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Categoría</label>
                        <select name="category_id" x-model="editProductData.category_id" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white focus:border-indigo-500 focus:outline-none">
                            <option value="">-- Sin categoría --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-200">URL de Imagen del Producto (Opcional)</label>
                    <input type="text" name="image_url" x-model="editProductData.image_url" placeholder="https://..." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white font-mono text-xs focus:border-indigo-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Código SKU</label>
                        <input type="text" name="sku" x-model="editProductData.sku" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Código de Barras (EAN/UPC)</label>
                        <input type="text" name="barcode" x-model="editProductData.barcode" placeholder="Ej: 7591001..." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Unidad de Medida</label>
                        <input type="text" name="unit" x-model="editProductData.unit" placeholder="Ej: Unidad, Kg, Litro..." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Costo ($ USD) *</label>
                        <input type="number" step="0.01" min="0" name="cost_usd" x-model="editProductData.cost_usd" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white font-mono focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-slate-200">Precio Venta ($ USD) *</label>
                        <input type="number" step="0.01" min="0.01" name="price_usd" x-model="editProductData.price_usd" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white font-mono focus:border-indigo-500 focus:outline-none">
                        <div class="text-[10px] text-emerald-400 font-mono" x-text="'Equivale a: Bs ' + ((parseFloat(editProductData.price_usd) || 0) * {{ $bcvUsdRate }}).toFixed(2) + ' VES'"></div>
                    </div>

                    <div class="space-y-1">
                        <label class="font-bold text-rose-300">Stock Mínimo Alerta *</label>
                        <input type="number" step="0.01" min="0" name="min_stock_alert" x-model="editProductData.min_stock_alert" required class="w-full bg-slate-950 border border-rose-500/40 rounded-xl px-3 py-2 text-rose-200 font-mono focus:border-rose-500 focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-200">Descripción / Detalles (Opcional)</label>
                    <textarea name="description" x-model="editProductData.description" rows="2" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-white focus:border-indigo-500 focus:outline-none"></textarea>
                </div>

                <div class="p-3 bg-slate-950/80 rounded-xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-slate-200">Manejo de Lotes y Vencimientos</div>
                        <div class="text-[10px] text-slate-400">Habilita trazabilidad por fecha de caducidad en anaquel</div>
                    </div>
                    <input type="checkbox" name="has_lots" value="1" x-model="editProductData.has_lots" class="rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0 w-4 h-4">
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/20 transition-all font-display">
                        ✓ Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- 🗑️ MODAL: CONFIRMAR ELIMINACIÓN DE PRODUCTO               -->
    <!-- ========================================================= -->
    <div x-show="openDeleteModal" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="openDeleteModal = false" 
             class="glass-card w-full max-w-md rounded-2xl border border-rose-500/40 bg-slate-900/95 shadow-2xl p-6 space-y-4 text-center"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <div class="w-14 h-14 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center text-2xl mx-auto shadow-inner">
                🚨
            </div>

            <div>
                <h3 class="text-base font-bold text-white font-display">¿Eliminar Producto del Inventario?</h3>
                <p class="text-xs text-slate-300 mt-2">
                    Estás a punto de eliminar el producto <strong class="text-white font-semibold" x-text="deleteProductName"></strong>.
                </p>
                <p class="text-[11px] text-rose-400/90 mt-1 font-medium bg-rose-500/10 border border-rose-500/20 rounded-lg p-2">
                    ⚠️ Esta acción borrará el registro de stock y no se puede deshacer.
                </p>
            </div>

            <form :action="'/inventory/' + deleteProductId + '/delete'" method="POST" class="flex items-center justify-center gap-3 pt-2">
                @csrf
                <button type="button" @click="openDeleteModal = false" class="w-1/2 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl transition-colors text-xs">
                    Cancelar
                </button>
                <button type="submit" class="w-1/2 py-2.5 bg-gradient-to-r from-rose-600 to-red-700 hover:from-rose-500 hover:to-red-600 text-white font-bold rounded-xl shadow-lg shadow-rose-600/30 transition-all text-xs font-display">
                    Sí, Eliminar
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function inventoryManager(hasLowStock) {
    return {
        showLowStockAlertModal: hasLowStock,
        openCreateModal: false,
        openStockModal: false,
        openEditModal: false,
        openDeleteModal: false,

        searchQuery: '{{ addslashes($search) }}',
        selectedCategory: '{{ $categoryId }}',

        productsList: {!! $productsJson !!},

        get visibleCount() {
            const q = (this.searchQuery || '').toLowerCase().trim();
            const cat = this.selectedCategory;
            return this.productsList.filter(p => {
                const matchCat = (cat === 'all' || cat === '' || cat === p.category_id);
                const matchText = !q || 
                    p.name.toLowerCase().includes(q) || 
                    p.sku.toLowerCase().includes(q) || 
                    p.barcode.toLowerCase().includes(q) || 
                    p.category_name.toLowerCase().includes(q);
                return matchCat && matchText;
            }).length;
        },

        matchesSearch(name, sku, barcode, categoryId, categoryName) {
            const cat = this.selectedCategory;
            if (cat !== 'all' && cat !== '' && cat != categoryId) {
                return false;
            }
            if (!this.searchQuery || this.searchQuery.trim() === '') {
                return true;
            }
            const q = this.searchQuery.toLowerCase().trim();
            return name.toLowerCase().includes(q) || 
                   sku.toLowerCase().includes(q) || 
                   barcode.toLowerCase().includes(q) || 
                   categoryName.toLowerCase().includes(q);
        },

        adjustProductId: null,
        adjustProductName: '',
        adjustCurrentStock: 0,
        adjustMinAlert: 10,

        deleteProductId: null,
        deleteProductName: '',

        editProductData: {
            id: null,
            name: '',
            category_id: '',
            sku: '',
            barcode: '',
            image_url: '',
            unit: 'Unidad',
            cost_usd: 0,
            price_usd: 0,
            min_stock_alert: 10,
            has_lots: false,
            description: ''
        },

        openAdjustModal(id, name, stock, minAlert) {
            this.adjustProductId = id;
            this.adjustProductName = name;
            this.adjustCurrentStock = stock;
            this.adjustMinAlert = minAlert;
            this.openStockModal = true;
        },

        openEditProductModal(prod) {
            this.editProductData = Object.assign({}, prod);
            this.openEditModal = true;
        },

        openDeleteProductModal(id, name) {
            this.deleteProductId = id;
            this.deleteProductName = name;
            this.openDeleteModal = true;
        }
    };
}
</script>
@endsection
