@extends('layouts.app')

@section('title', 'Dashboard CFO - ' . $currentBusinessType['name'] . ' - Pymora')

@section('content')
<div class="space-y-6">

    <!-- Business Type Header Banner & 4 Core Pillars -->
    <div class="glass-card p-5 rounded-2xl border border-slate-800 bg-slate-900/80 space-y-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl border flex items-center justify-center text-2xl shadow-inner {{ $currentBusinessType['badge_color'] }}">
                {{ $currentBusinessType['icon'] }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-bold text-white font-display">{{ $currentBusinessType['name'] }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-mono border uppercase tracking-wider {{ $currentBusinessType['badge_color'] }}">
                        Rubro Activo
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">{{ $currentBusinessType['description'] }}</p>
            </div>
        </div>

        <!-- 4 Core Pillars Navigation Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5 pt-3 border-t border-slate-800/80">
            <!-- 1. Ventas y Caja -->
            <a href="{{ route('pos.index') }}" class="group p-2.5 rounded-xl bg-slate-950/60 hover:bg-slate-900 border border-slate-800/80 hover:border-emerald-500/50 transition-all duration-200 flex items-center gap-2.5 cursor-pointer shadow-sm hover:shadow-emerald-500/10 hover:-translate-y-0.5">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 group-hover:bg-emerald-500/30 group-hover:scale-110 transition-all flex items-center justify-center font-bold text-xs shrink-0 shadow-inner">1</div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-slate-200 group-hover:text-emerald-400 transition-colors flex items-center justify-between">
                        <span class="truncate">Ventas y Caja</span>
                        <svg class="w-3 h-3 text-emerald-400 opacity-0 group-hover:opacity-100 transition-all transform -translate-x-1 group-hover:translate-x-0 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <div class="text-[10px] text-slate-400 truncate group-hover:text-slate-300">
                        {{ match($selectedTypeKey) {
                            'restaurante' => 'Cobrar, Mesas y Comandero',
                            'carniceria_hortalizas' => 'Balanza Kilos y POS',
                            'licoreria' => 'Venta Botellas / Cajas POS',
                            'tecnologia_electro' => 'POS Seriales e IMEIs',
                            'servicios' => 'Agenda y Cobro de Citas',
                            'distribuidor' => 'Venta Mayorista y Despacho',
                            'fabricante' => 'Despacho de Producción',
                            'ropa' => 'Punto de Venta (Tallas)',
                            'repuestos' => 'Búsqueda y Venta de Piezas',
                            'articulos' => 'Punto de Venta Rápido',
                            default => 'Cobrar y Devoluciones'
                        } }}
                    </div>
                </div>
            </a>

            <!-- 2. Inventario -->
            <a href="{{ route('inventory.index') }}" class="group p-2.5 rounded-xl bg-slate-950/60 hover:bg-slate-900 border border-slate-800/80 hover:border-purple-500/50 transition-all duration-200 flex items-center gap-2.5 cursor-pointer shadow-sm hover:shadow-purple-500/10 hover:-translate-y-0.5">
                <div class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-400 group-hover:bg-purple-500/30 group-hover:scale-110 transition-all flex items-center justify-center font-bold text-xs shrink-0 shadow-inner">2</div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-slate-200 group-hover:text-purple-400 transition-colors flex items-center justify-between">
                        <span class="truncate">Inventario</span>
                        <svg class="w-3 h-3 text-purple-400 opacity-0 group-hover:opacity-100 transition-all transform -translate-x-1 group-hover:translate-x-0 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <div class="text-[10px] text-slate-400 truncate group-hover:text-slate-300">
                        {{ match($selectedTypeKey) {
                            'restaurante' => 'Recetas e Insumos Críticos',
                            'ropa' => 'Tallas, Colores y Marcas',
                            'tecnologia_electro' => 'Seriales, IMEIs y Garantías',
                            'licoreria' => 'Stock de Licores y Cajas',
                            'carniceria_hortalizas' => 'Kilos, Desposte y Mermas',
                            'fabricante' => 'Recetas BOM e Insumos',
                            'repuestos' => 'Catálogo Marca/Modelo/Año',
                            'distribuidor' => 'Stock Almacén Mayorista',
                            'servicios' => 'Repuestos y Mano de Obra',
                            'articulos' => 'Variedades, Combos y Packs',
                            default => 'Stock y Lotes con Vencimiento'
                        } }}
                    </div>
                </div>
            </a>

            <!-- 3. Gastos y Reportes -->
            <a href="{{ route('cxc.index') }}" class="group p-2.5 rounded-xl bg-slate-950/60 hover:bg-slate-900 border border-slate-800/80 hover:border-amber-500/50 transition-all duration-200 flex items-center gap-2.5 cursor-pointer shadow-sm hover:shadow-amber-500/10 hover:-translate-y-0.5">
                <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-400 group-hover:bg-amber-500/30 group-hover:scale-110 transition-all flex items-center justify-center font-bold text-xs shrink-0 shadow-inner">3</div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-slate-200 group-hover:text-amber-400 transition-colors flex items-center justify-between">
                        <span class="truncate">Gastos y Reportes</span>
                        <svg class="w-3 h-3 text-amber-400 opacity-0 group-hover:opacity-100 transition-all transform -translate-x-1 group-hover:translate-x-0 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <div class="text-[10px] text-slate-400 truncate group-hover:text-slate-300">
                        {{ match($selectedTypeKey) {
                            'licoreria' => 'Flujo Real, IGTF y Licores',
                            'distribuidor' => 'Flujo Real, Rutas y CXC',
                            'fabricante' => 'Costeo Fabril y CXP',
                            default => 'Flujo Real, CXC y CXP'
                        } }}
                    </div>
                </div>
            </a>

            <!-- 4. Configuración y Equipo -->
            <a href="{{ route('reports.index') }}" class="group p-2.5 rounded-xl bg-slate-950/60 hover:bg-slate-900 border border-slate-800/80 hover:border-indigo-500/50 transition-all duration-200 flex items-center gap-2.5 cursor-pointer shadow-sm hover:shadow-indigo-500/10 hover:-translate-y-0.5">
                <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 group-hover:bg-indigo-500/30 group-hover:scale-110 transition-all flex items-center justify-center font-bold text-xs shrink-0 shadow-inner">4</div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-bold text-slate-200 group-hover:text-indigo-400 transition-colors flex items-center justify-between">
                        <span class="truncate">Configuración y Equipo</span>
                        <svg class="w-3 h-3 text-indigo-400 opacity-0 group-hover:opacity-100 transition-all transform -translate-x-1 group-hover:translate-x-0 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <div class="text-[10px] text-slate-400 truncate group-hover:text-slate-300">
                        {{ match($selectedTypeKey) {
                            'restaurante' => 'Meseros, Cocina y Propinas',
                            'distribuidor' => 'Vendedores y Rutas',
                            'servicios' => 'Técnicos y Especialistas',
                            default => 'Usuarios y Comisiones'
                        } }}
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Main KPI Cards: 3 Key Financial & Inventory Pillars -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- Card 1: Ventas Totales Hoy (Interactive Currency Switcher: USD / BCV / EUR) -->
        <div x-data="{ currency: 'usd' }" class="glass-card glass-card-hover p-4 rounded-xl space-y-2.5 block relative">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <div class="flex items-center gap-1.5">
                    <span class="text-white font-semibold">Ventas Totales Hoy</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" title="Tasa Oficial BCV En Vivo"></span>
                </div>

                <!-- Currency Selector Pills (USD / BCV / EUR) -->
                <div class="flex items-center bg-slate-950 p-0.5 rounded-lg border border-slate-800 text-[10px] font-mono z-10" @click.stop>
                    <button type="button" 
                            @click="currency = 'usd'" 
                            :class="currency === 'usd' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200'"
                            class="px-2 py-0.5 rounded-md transition-all cursor-pointer">
                        USD
                    </button>
                    <button type="button" 
                            @click="currency = 'ves'" 
                            :class="currency === 'ves' ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200'"
                            class="px-2 py-0.5 rounded-md transition-all cursor-pointer">
                        BCV (Bs)
                    </button>
                    <button type="button" 
                            @click="currency = 'eur'" 
                            :class="currency === 'eur' ? 'bg-sky-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200'"
                            class="px-2 py-0.5 rounded-md transition-all cursor-pointer">
                        EUR (€)
                    </button>
                </div>
            </div>

            <!-- Dynamic View by Currency & Direct Link to POS -->
            <a href="{{ route('pos.index') }}" class="block space-y-2 group">
                <!-- 1. USD Selected View (Shows amount in USD & equivalence in Bs via BCV API) -->
                <div x-show="currency === 'usd'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-0.5" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="text-2xl font-extrabold text-white font-display">${{ number_format($salesUsdReal, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">USD</span></div>
                    <div class="flex items-center justify-between text-xs font-mono pt-1.5 border-t border-slate-800/60 mt-1.5">
                        <span class="text-emerald-400 font-semibold">Equivale a: Bs {{ number_format($salesUsdReal * $bcvUsdRate, 2) }} VES</span>
                        <span class="text-slate-400 text-[10px]">Tasa: {{ number_format($bcvUsdRate, 2) }}</span>
                    </div>
                </div>

                <!-- 2. VES (BCV) Selected View (Shows actual Bolivares earned) -->
                <div x-show="currency === 'ves'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-0.5" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="text-2xl font-extrabold text-emerald-400 font-display">Bs {{ number_format($salesVesReal, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">VES</span></div>
                </div>

                <!-- 3. EUR Selected View (Shows Euros earned & equivalence in Bs via Euro BCV API) -->
                <div x-show="currency === 'eur'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-0.5" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="text-2xl font-extrabold text-sky-400 font-display">€{{ number_format($salesEurReal, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">EUR</span></div>
                    <div class="flex items-center justify-between text-xs font-mono pt-1.5 border-t border-slate-800/60 mt-1.5">
                        <span class="text-sky-300 font-medium">
                            @if($salesEurReal > 0)
                                Equivale a: Bs {{ number_format($salesEurReal * $bcvEurRate, 2) }} VES
                            @else
                                Sin cobros en Euros registrados hoy
                            @endif
                        </span>
                        <span class="text-slate-400 text-[10px]">Tasa Euro: {{ number_format($bcvEurRate, 2) }}</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 2: Cantidad de Productos en Stock (Unified Clickable Inventory Card) -->
        <a href="{{ route('inventory.index') }}" class="glass-card glass-card-hover p-4 rounded-xl space-y-2 block cursor-pointer group">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span class="group-hover:text-white transition-colors">Cantidad de Productos en Stock</span>
                <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400 group-hover:bg-purple-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">{{ $totalProductsCount }} <span class="text-xs font-normal text-slate-400 font-sans">Productos</span></div>
            <div class="text-xs text-purple-400 flex items-center gap-1.5 pt-1 border-t border-slate-800/60">
                <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                <span>{{ number_format($totalStockUnits, 1) }} unidades totales en stock</span>
            </div>
        </a>

        <!-- Card 3: Cuentas por Cobrar (CXC - Interactive Currency Switcher) -->
        <div x-data="{ currency: 'usd' }" class="glass-card glass-card-hover p-4 rounded-xl space-y-2.5 block relative">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <div class="flex items-center gap-1.5">
                    <span class="text-white font-semibold">Cuentas por Cobrar (CXC)</span>
                </div>

                <!-- Currency Selector Pills (USD / BCV / EUR) -->
                <div class="flex items-center bg-slate-950 p-0.5 rounded-lg border border-slate-800 text-[10px] font-mono z-10" @click.stop>
                    <button type="button" 
                            @click="currency = 'usd'" 
                            :class="currency === 'usd' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200'"
                            class="px-2 py-0.5 rounded-md transition-all cursor-pointer">
                        USD
                    </button>
                    <button type="button" 
                            @click="currency = 'ves'" 
                            :class="currency === 'ves' ? 'bg-amber-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200'"
                            class="px-2 py-0.5 rounded-md transition-all cursor-pointer">
                        BCV (Bs)
                    </button>
                    <button type="button" 
                            @click="currency = 'eur'" 
                            :class="currency === 'eur' ? 'bg-sky-600 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-slate-200'"
                            class="px-2 py-0.5 rounded-md transition-all cursor-pointer">
                        EUR (€)
                    </button>
                </div>
            </div>

            <!-- Dynamic View by Currency & Direct Link to CXC -->
            <a href="{{ route('cxc.index') }}" class="block space-y-2 group">
                <!-- 1. USD View -->
                <div x-show="currency === 'usd'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-0.5" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="text-2xl font-extrabold text-white font-display">${{ number_format($totalDebtUsd, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">USD</span></div>
                    <div class="flex items-center justify-between text-xs font-mono pt-1.5 border-t border-slate-800/60 mt-1.5">
                        <span class="text-amber-400 font-semibold">Equivale a: Bs {{ number_format($totalDebtUsd * $bcvUsdRate, 2) }} VES</span>
                        <span class="text-slate-400 text-[10px]">Tasa: {{ number_format($bcvUsdRate, 2) }}</span>
                    </div>
                </div>

                <!-- 2. VES (BCV) View (Clean without footer row) -->
                <div x-show="currency === 'ves'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-0.5" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="text-2xl font-extrabold text-amber-400 font-display">Bs {{ number_format($totalDebtUsd * $bcvUsdRate, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">VES</span></div>
                </div>

                <!-- 3. EUR View -->
                <div x-show="currency === 'eur'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-0.5" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="text-2xl font-extrabold text-sky-400 font-display">€{{ number_format($bcvEurRate > 0 ? ($totalDebtUsd * $bcvUsdRate / $bcvEurRate) : ($totalDebtUsd * 0.92), 2) }} <span class="text-xs font-normal text-slate-400 font-sans">EUR</span></div>
                    <div class="flex items-center justify-between text-xs font-mono pt-1.5 border-t border-slate-800/60 mt-1.5">
                        <span class="text-sky-300 font-medium">Equivale a: Bs {{ number_format($totalDebtUsd * $bcvUsdRate, 2) }} VES</span>
                        <span class="text-slate-400 text-[10px]">Tasa Euro: {{ number_format($bcvEurRate, 2) }}</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Specialized Operational Dashboard Widget per Business Type -->
    <div class="glass-card p-5 rounded-2xl border border-slate-800 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="text-lg">{{ $currentBusinessType['icon'] }}</span>
                <h3 class="font-bold text-white font-display text-base">Operaciones Especializadas: {{ $currentBusinessType['name'] }}</h3>
            </div>
            <a href="{{ route('pos.index') }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl transition-all shadow flex items-center gap-1">
                + Ir al Punto de Venta (POS)
            </a>
        </div>

        <!-- Render Specific Sub-View Widgets for Each Business Type -->
        @if($selectedTypeKey === 'restaurante')
            <!-- RESTAURANTE: Visual Tables Layout & Kitchen Orders -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
                <!-- Mesas Grid (2 cols) -->
                <div class="lg:col-span-2 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-300">Plano de Mesas en Sala (Mesero / Comandero)</span>
                        <span class="text-emerald-400 font-mono text-[11px]">6 Libres / 6 Ocupadas</span>
                    </div>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        @for($m = 1; $m <= 12; $m++)
                            @php $isOcc = $m % 2 == 0; @endphp
                            <div class="p-3 rounded-xl border flex flex-col items-center justify-center gap-1.5 text-center transition-all cursor-pointer {{ $isOcc ? 'bg-rose-500/10 border-rose-500/30 text-rose-300' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-300' }}">
                                <div class="font-bold text-sm">Mesa #{{ $m }}</div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-mono uppercase font-bold {{ $isOcc ? 'bg-rose-500/20' : 'bg-emerald-500/20' }}">
                                    {{ $isOcc ? 'Ocupada ($' . ($m * 12.5) . ')' : 'Disponible' }}
                                </span>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Recetas & Insumos Críticos (1 col) -->
                <div class="space-y-3 bg-slate-900/60 p-4 rounded-xl border border-slate-800">
                    <div class="font-bold text-white">Ingredientes & Recetas Críticas</div>
                    <div class="space-y-2">
                        @foreach($businessWidgetsData['critical_ingredients'] as $ing)
                            <div class="p-2.5 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-slate-200">{{ $ing['name'] }}</div>
                                    <div class="text-[10px] text-slate-400">Min. Requerido: {{ $ing['min'] }} Kg</div>
                                </div>
                                <span class="px-2 py-1 rounded bg-amber-500/20 text-amber-300 font-mono font-bold text-xs">
                                    {{ $ing['stock'] }} Kg
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @elseif($selectedTypeKey === 'ropa')
            <!-- ROPA: Sizes & Colors Matrix -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Ventas por Talla</div>
                    <div class="space-y-1.5">
                        @foreach($businessWidgetsData['top_sizes'] as $size => $pct)
                            <div class="flex items-center justify-between text-slate-300">
                                <span>Talla {{ $size }}</span>
                                <span class="font-mono text-indigo-400 font-bold">{{ $pct }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Colores Más Demandados</div>
                    <div class="flex flex-wrap gap-2 pt-1">
                        @foreach($businessWidgetsData['top_colors'] as $color)
                            <span class="px-3 py-1 rounded-lg bg-slate-800 text-slate-200 border border-slate-700 font-medium">
                                🎨 {{ $color }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2 flex flex-col justify-between">
                    <div>
                        <div class="font-bold text-white">Etiquetas & Variantes</div>
                        <p class="text-slate-400 text-[11px] mt-1">Genera códigos de barra únicos por combinación Talla-Color.</p>
                    </div>
                    <button class="w-full py-2 bg-pink-600/20 text-pink-300 border border-pink-500/30 font-bold rounded-xl hover:bg-pink-600 hover:text-white transition-all">
                        Imprimir Matriz Tallas
                    </button>
                </div>
            </div>

        @elseif($selectedTypeKey === 'carniceria_hortalizas')
            <!-- CARNICERIA: Weight Scale & Waste Control -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Venta por Balanza / Báscula POS</div>
                    <div class="text-[11px] text-slate-400">Total Kilos Despachados Hoy:</div>
                    <div class="text-2xl font-extrabold text-emerald-400 font-mono">{{ $businessWidgetsData['total_kg_sold_today'] }} Kg</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Cámara Fría & Desposte</div>
                    <div class="text-[11px] text-slate-400">Canales de Res en Carga:</div>
                    <div class="text-2xl font-extrabold text-indigo-400 font-mono">{{ $businessWidgetsData['carcass_in_stock'] }} Canales</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Porcentaje de Merma</div>
                    <div class="text-[11px] text-slate-400">Mermas por hueso / graso / vegetal:</div>
                    <div class="text-2xl font-extrabold text-amber-400 font-mono">{{ $businessWidgetsData['waste_percentage'] }}</div>
                </div>
            </div>

        @elseif($selectedTypeKey === 'tecnologia_electro')
            <!-- TECNOLOGIA: IMEIs & Serial Numbers -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">IMEIs & Seriales Registrados</div>
                    <div class="text-[11px] text-slate-400">Captura automática en caja:</div>
                    <div class="text-2xl font-extrabold text-teal-400 font-mono">{{ $businessWidgetsData['imei_registered_today'] }} Equipos</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Garantías Vigentes Emitidas</div>
                    <div class="text-[11px] text-slate-400">Certificados digitales:</div>
                    <div class="text-2xl font-extrabold text-indigo-400 font-mono">{{ $businessWidgetsData['warranties_issued'] }} Garantías</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Taller de Servicio Técnico</div>
                    <div class="text-[11px] text-slate-400">Equipos en revisión:</div>
                    <div class="text-2xl font-extrabold text-purple-400 font-mono">{{ $businessWidgetsData['repairs_in_workshop'] }} Reparaciones</div>
                </div>
            </div>

        @elseif($selectedTypeKey === 'servicios')
            <!-- SERVICIOS: Work Orders & Appointments -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Agenda de Citas del Día</div>
                    <div class="text-[11px] text-slate-400">Citas confirmadas:</div>
                    <div class="text-2xl font-extrabold text-violet-400 font-mono">{{ $businessWidgetsData['appointments_today'] }} Citas</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Órdenes de Trabajo Abiertas</div>
                    <div class="text-[11px] text-slate-400">Proyectos / Servicios en marcha:</div>
                    <div class="text-2xl font-extrabold text-indigo-400 font-mono">{{ $businessWidgetsData['open_work_orders'] }} Órdenes</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Horas Facturables</div>
                    <div class="text-[11px] text-slate-400">Mano de obra acumulada:</div>
                    <div class="text-2xl font-extrabold text-emerald-400 font-mono">{{ $businessWidgetsData['billable_hours'] }}</div>
                </div>
            </div>

        @elseif($selectedTypeKey === 'distribuidor')
            <!-- DISTRIBUIDOR: Wholesale Rates & Despatch Routes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Rutas de Despacho Activas</div>
                    <div class="text-2xl font-extrabold text-indigo-400 font-mono">{{ $businessWidgetsData['routes_active'] }} Rutas</div>
                </div>
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Volumen Mayorista ($ USD)</div>
                    <div class="text-2xl font-extrabold text-emerald-400 font-mono">${{ number_format($businessWidgetsData['wholesale_volume_usd'], 2) }}</div>
                </div>
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Cobradores & Rutas</div>
                    <div class="text-2xl font-extrabold text-amber-400 font-mono">{{ $businessWidgetsData['collectors_pending'] }} En campo</div>
                </div>
            </div>

        @elseif($selectedTypeKey === 'licoreria')
            <!-- LICORERIA: Bottles vs Cases & Taxes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Ventas por Botella / Caja</div>
                    <div class="text-[11px] text-slate-400">Desglose de inventario:</div>
                    <div class="text-lg font-extrabold text-amber-400 font-mono">{{ $businessWidgetsData['bottles_sold_today'] }} Botellas / {{ $businessWidgetsData['cases_sold_today'] }} Cajas</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">IGTF & Licores Recaudado</div>
                    <div class="text-[11px] text-slate-400">Retención de impuestos:</div>
                    <div class="text-2xl font-extrabold text-emerald-400 font-mono">${{ number_format($businessWidgetsData['igtf_collected_usd'], 2) }} USD</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Licencia de Expendio</div>
                    <div class="text-[11px] text-slate-400">Vencimiento permiso sanitario:</div>
                    <div class="text-lg font-extrabold text-indigo-400 font-mono">{{ $businessWidgetsData['license_expiry'] }}</div>
                </div>
            </div>

        @elseif($selectedTypeKey === 'repuestos')
            <!-- REPUESTOS: Automotive Fitment Catalog -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Marcas Top Consultadas</div>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        @foreach($businessWidgetsData['top_brands'] as $brand)
                            <span class="px-2.5 py-1 rounded bg-slate-800 text-slate-200 border border-slate-700 font-semibold">🚗 {{ $brand }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Catálogo de Aplicación OEM</div>
                    <div class="text-2xl font-extrabold text-orange-400 font-mono">{{ $businessWidgetsData['oem_matches_count'] }} Piezas</div>
                </div>
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Garantías de Repuestos</div>
                    <div class="text-2xl font-extrabold text-indigo-400 font-mono">{{ $businessWidgetsData['warranties_active'] }} Activas</div>
                </div>
            </div>

        @elseif($selectedTypeKey === 'fabricante')
            <!-- FABRICANTE: Production Orders & BOM Recipes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Órdenes de Producción Activas</div>
                    <div class="text-[11px] text-slate-400">Lotes en proceso de fabricación:</div>
                    <div class="text-2xl font-extrabold text-purple-400 font-mono">{{ $businessWidgetsData['active_production_orders'] ?? 3 }} Órdenes</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Insumos & Recetas BOM</div>
                    <div class="text-[11px] text-slate-400">Materias primas vinculadas:</div>
                    <div class="text-2xl font-extrabold text-emerald-400 font-mono">{{ $businessWidgetsData['raw_materials_count'] ?? 28 }} Insumos</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Unidades Fabricadas (Semana)</div>
                    <div class="text-[11px] text-slate-400">Rendimiento de planta:</div>
                    <div class="text-2xl font-extrabold text-indigo-400 font-mono">{{ $businessWidgetsData['completed_this_week'] ?? 120 }} Unidades</div>
                </div>
            </div>

        @elseif($selectedTypeKey === 'articulos')
            <!-- ARTICULOS: Promos, Combos & Fast Movers -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Promociones Activas</div>
                    <div class="text-[11px] text-slate-400">Descuentos por categoría:</div>
                    <div class="text-2xl font-extrabold text-sky-400 font-mono">{{ $businessWidgetsData['promos_active'] ?? 3 }} Promos</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Combos y Packs Vendidos</div>
                    <div class="text-[11px] text-slate-400">Packs agrupados hoy:</div>
                    <div class="text-2xl font-extrabold text-emerald-400 font-mono">{{ $businessWidgetsData['bundles_sold'] ?? 12 }} Packs</div>
                </div>

                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Artículos Top Rotación</div>
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        @foreach($businessWidgetsData['fast_movers'] ?? ['Audífonos Bluetooth', 'Cargador Rápido', 'Protector'] as $item)
                            <span class="px-2.5 py-1 rounded bg-slate-800 text-slate-200 border border-slate-700 font-semibold">🛍️ {{ $item }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

        @else
            <!-- ABASTO / DEFAULT: Fast POS Metrics & Barcode Scanner Tool -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <!-- Interactive Barcode Scanner Card (Navigates to dedicated /scanner module) -->
                <a href="{{ route('scanner.index') }}" class="bg-slate-900/60 hover:bg-slate-800/80 p-4 rounded-xl border border-slate-800 hover:border-indigo-500/50 space-y-2 text-left transition-all group cursor-pointer w-full shadow-sm hover:shadow-indigo-500/10 block">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="font-bold text-white group-hover:text-indigo-400 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            Escáner de Código de Barras
                        </span>
                    </div>
                    <div class="text-2xl font-extrabold text-emerald-400 font-mono">
                        {{ $barcodeProductsCount }} <span class="text-xs font-normal text-slate-400 font-sans">Productos con Barcode</span>
                    </div>
                    <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Haz clic para escanear y verificar precios / stock</span>
                    </div>
                </a>

                <!-- Control de Vencimientos / Lotes Card (Navigates to /batches) -->
                <a href="{{ route('batches.index') }}" class="bg-slate-900/60 hover:bg-slate-800/80 p-4 rounded-xl border border-slate-800 hover:border-amber-500/50 space-y-2 text-left transition-all group cursor-pointer w-full shadow-sm hover:shadow-amber-500/10 block">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="font-bold text-white group-hover:text-amber-400 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Control de Vencimientos
                        </span>
                    </div>
                    <div class="text-2xl font-extrabold text-amber-400 font-mono">
                        {{ $batchAlertsCount }} <span class="text-xs font-normal text-slate-400 font-sans">Alertas de Lote</span>
                    </div>
                    <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $batchAlertsCount > 0 ? 'bg-amber-400 animate-pulse' : 'bg-emerald-400' }}"></span>
                        <span>Haz clic para ver y registrar lotes / caducidad</span>
                    </div>
                </a>

                <!-- Stock Bajo y Por Agotarse Card (Navigates to /inventory) -->
                <a href="{{ route('inventory.index') }}" class="bg-slate-900/60 hover:bg-slate-800/80 p-4 rounded-xl border border-slate-800 hover:border-rose-500/50 space-y-2 text-left transition-all group cursor-pointer w-full shadow-sm hover:shadow-rose-500/10 block">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="font-bold text-white group-hover:text-rose-400 transition-colors flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Stock Bajo y Por Agotarse
                        </span>
                    </div>
                    <div class="text-2xl font-extrabold {{ $lowStockProductsCount > 0 ? 'text-rose-400' : 'text-emerald-400' }} font-mono">
                        {{ $lowStockProductsCount }} <span class="text-xs font-normal text-slate-400 font-sans">Productos con Alerta</span>
                    </div>
                    <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $lowStockProductsCount > 0 ? 'bg-rose-400 animate-pulse' : 'bg-emerald-400' }}"></span>
                        <span>Haz clic para revisar y reponer stock</span>
                    </div>
                </a>
            </div>
        @endif
    </div>

    <!-- Main Shared Ledger Grid: Cash Turn & Bank Balances -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Recent Sales Ledger -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Recent Sales Ledger -->
            <div class="glass-card rounded-xl border border-slate-800 overflow-hidden">
                <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-white text-base">Últimas Ventas Registradas</h3>
                    <a href="{{ route('pos.index') }}" class="text-xs text-indigo-400 hover:underline">Ver todas &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900 text-slate-400 uppercase font-semibold text-[10px]">
                            <tr>
                                <th class="p-3">Comprobante</th>
                                <th class="p-3">Cliente</th>
                                <th class="p-3">Total (USD)</th>
                                <th class="p-3">Total (VES)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($recentSales as $sale)
                                <tr class="hover:bg-slate-900/40">
                                    <td class="p-3 font-mono font-bold text-white">{{ $sale->sale_number ?? 'VTA-001' }}</td>
                                    <td class="p-3">{{ $sale->customer->name ?? 'Cliente Detal' }}</td>
                                    <td class="p-3 text-emerald-400 font-bold font-mono">${{ number_format($sale->total_usd ?? 0, 2) }}</td>
                                    <td class="p-3 text-indigo-300 font-mono font-semibold">
                                        @php
                                            $vesAmount = ($sale->total_ves && $sale->total_ves > 0 && ($sale->total_ves / max(0.01, (float)($sale->total_usd ?? 1))) >= 100) 
                                                ? (float)$sale->total_ves 
                                                : (((float)($sale->total_usd ?? 0)) * $bcvUsdRate);
                                        @endphp
                                        Bs {{ number_format($vesAmount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-slate-500">No hay ventas registradas aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Column 3: Multi-Currency Accounts & Balances -->
        <div class="space-y-6">
            <div class="glass-card p-5 rounded-xl space-y-4">
                <h3 class="font-bold text-white text-base border-b border-slate-800 pb-2">Cuentas Bancarias & Cajas</h3>
                <div class="space-y-3 text-xs">
                    @foreach($bankAccounts as $acc)
                        <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-800 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-200">{{ $acc->name }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $acc->account_number }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold font-mono {{ $acc->currency === 'USD' ? 'text-emerald-400' : 'text-indigo-400' }}">
                                    {{ $acc->currency === 'USD' ? '$' : 'Bs' }} {{ number_format($acc->balance, 2) }}
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ $acc->currency }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
