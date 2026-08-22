@extends('layouts.app')

@section('title', 'Dashboard CFO - ' . $currentBusinessType['name'] . ' - Pymora')

@section('content')
<div class="space-y-6">

    <!-- Business Type Selector Banner & 4 Core Pillars -->
    <div class="glass-card p-5 rounded-2xl border border-slate-800 bg-slate-900/80 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
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

            <!-- Business Type Switcher for Live Demo -->
            <div class="flex items-center gap-2 bg-slate-950/80 p-1.5 rounded-xl border border-slate-800">
                <span class="text-[11px] text-slate-400 font-mono px-2">Ver Dashboard de:</span>
                <form action="{{ route('dashboard') }}" method="GET" class="flex items-center">
                    <select name="type" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-xs text-indigo-300 font-semibold rounded-lg px-3 py-1.5 focus:border-indigo-500 focus:outline-none">
                        @foreach($allBusinessTypes as $key => $bType)
                            <option value="{{ $key }}" {{ $selectedTypeKey === $key ? 'selected' : '' }}>
                                {{ $bType['icon'] }} {{ $bType['name'] }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- 4 Core Pillars Navigation Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 pt-3 border-t border-slate-800/80">
            <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs">1</div>
                <div>
                    <div class="text-xs font-bold text-slate-200">Ventas & Caja</div>
                    <div class="text-[10px] text-slate-400 truncate">
                        {{ $selectedTypeKey === 'restaurante' ? 'Cobrar, Mesas & Comandero' : ($selectedTypeKey === 'carniceria_hortalizas' ? 'Balanza Kilos & POS' : 'Cobrar & Devoluciones') }}
                    </div>
                </div>
            </div>

            <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-xs">2</div>
                <div>
                    <div class="text-xs font-bold text-slate-200">Inventario</div>
                    <div class="text-[10px] text-slate-400 truncate">
                        {{ $selectedTypeKey === 'restaurante' ? 'Recetas & Insumos' : ($selectedTypeKey === 'ropa' ? 'Tallas & Colores' : ($selectedTypeKey === 'tecnologia_electro' ? 'Seriales & IMEIs' : 'Stock & Lotes')) }}
                    </div>
                </div>
            </div>

            <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-xs">3</div>
                <div>
                    <div class="text-xs font-bold text-slate-200">Gastos & Reportes</div>
                    <div class="text-[10px] text-slate-400 truncate">Flujo Real, CXC & CXP</div>
                </div>
            </div>

            <div class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800/80 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs">4</div>
                <div>
                    <div class="text-xs font-bold text-slate-200">Configuración & Equipo</div>
                    <div class="text-[10px] text-slate-400 truncate">Usuarios & Comisiones</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Specialized KPI Cards customized per Business Type -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: Ventas Totales Hoy -->
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

        <!-- Card 2: Specialized Metric 1 -->
        <div class="glass-card glass-card-hover p-4 rounded-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                @if($selectedTypeKey === 'restaurante')
                    <span>Mesas en Atención</span>
                    <span class="p-1.5 rounded-lg bg-rose-500/10 text-rose-400">🍽️</span>
                @elseif($selectedTypeKey === 'ropa')
                    <span>Variantes de Talla & Color</span>
                    <span class="p-1.5 rounded-lg bg-pink-500/10 text-pink-400">👗</span>
                @elseif($selectedTypeKey === 'carniceria_hortalizas')
                    <span>Kilos Vendidos Hoy</span>
                    <span class="p-1.5 rounded-lg bg-red-500/10 text-red-400">🥩</span>
                @elseif($selectedTypeKey === 'tecnologia_electro')
                    <span>Seriales / IMEIs Registrados</span>
                    <span class="p-1.5 rounded-lg bg-teal-500/10 text-teal-400">💻</span>
                @elseif($selectedTypeKey === 'servicios')
                    <span>Citas Agendadas Hoy</span>
                    <span class="p-1.5 rounded-lg bg-violet-500/10 text-violet-400">🛠️</span>
                @elseif($selectedTypeKey === 'distribuidor')
                    <span>Volumen Mayorista</span>
                    <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400">🚚</span>
                @elseif($selectedTypeKey === 'fabricante')
                    <span>Órdenes de Producción</span>
                    <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400">🏭</span>
                @elseif($selectedTypeKey === 'licoreria')
                    <span>Botellas & Cajas Vendidas</span>
                    <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400">🍾</span>
                @elseif($selectedTypeKey === 'repuestos')
                    <span>Búsquedas por Vehículo</span>
                    <span class="p-1.5 rounded-lg bg-orange-500/10 text-orange-400">🔧</span>
                @else
                    <span>Rotación de Inventario</span>
                    <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400">🛒</span>
                @endif
            </div>

            <div class="text-2xl font-extrabold text-white font-display">
                @if($selectedTypeKey === 'restaurante')
                    {{ $businessWidgetsData['tables_occupied'] }} / {{ $businessWidgetsData['tables_total'] }} <span class="text-xs font-normal text-slate-400 font-sans">Mesas</span>
                @elseif($selectedTypeKey === 'ropa')
                    {{ $businessWidgetsData['variants_count'] }} <span class="text-xs font-normal text-slate-400 font-sans">SKUs Talla/Color</span>
                @elseif($selectedTypeKey === 'carniceria_hortalizas')
                    {{ $businessWidgetsData['total_kg_sold_today'] }} <span class="text-xs font-normal text-slate-400 font-sans">Kg</span>
                @elseif($selectedTypeKey === 'tecnologia_electro')
                    {{ $businessWidgetsData['imei_registered_today'] }} <span class="text-xs font-normal text-slate-400 font-sans">Dispositivos</span>
                @elseif($selectedTypeKey === 'servicios')
                    {{ $businessWidgetsData['appointments_today'] }} <span class="text-xs font-normal text-slate-400 font-sans">Clientes</span>
                @elseif($selectedTypeKey === 'distribuidor')
                    ${{ number_format($businessWidgetsData['wholesale_volume_usd'], 2) }} <span class="text-xs font-normal text-slate-400 font-sans">USD</span>
                @elseif($selectedTypeKey === 'fabricante')
                    {{ $businessWidgetsData['active_production_orders'] }} <span class="text-xs font-normal text-slate-400 font-sans">Lotes</span>
                @elseif($selectedTypeKey === 'licoreria')
                    {{ $businessWidgetsData['bottles_sold_today'] }} <span class="text-xs font-normal text-slate-400 font-sans">Bot / {{ $businessWidgetsData['cases_sold_today'] }} Cajas</span>
                @elseif($selectedTypeKey === 'repuestos')
                    {{ $businessWidgetsData['oem_matches_count'] }} <span class="text-xs font-normal text-slate-400 font-sans">Códigos OEM</span>
                @else
                    {{ $businessWidgetsData['scans_per_minute'] ?? 14 }} <span class="text-xs font-normal text-slate-400 font-sans">items/min</span>
                @endif
            </div>

            <div class="text-xs text-indigo-400">
                @if($selectedTypeKey === 'restaurante')
                    {{ $businessWidgetsData['kitchen_orders'] }} comanda(s) en cocina
                @elseif($selectedTypeKey === 'ropa')
                    Talla 'M' es la más vendida (42%)
                @elseif($selectedTypeKey === 'carniceria_hortalizas')
                    Control de merma: {{ $businessWidgetsData['waste_percentage'] }}
                @elseif($selectedTypeKey === 'tecnologia_electro')
                    {{ $businessWidgetsData['warranties_issued'] }} garantías activas emitidas
                @elseif($selectedTypeKey === 'servicios')
                    {{ $businessWidgetsData['billable_hours'] }} facturables
                @else
                    Métricas ajustadas al rubro
                @endif
            </div>
        </div>

        <!-- Card 3: Productos / Insumos / Catalogo -->
        <div class="glass-card glass-card-hover p-4 rounded-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Catálogo & Stock Activo</span>
                <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">{{ $totalProductsCount }} <span class="text-xs font-normal text-slate-400 font-sans">Ítems</span></div>
            <div class="text-xs text-amber-400">Stock controlado en sucursal</div>
        </div>

        <!-- Card 4: Cuentas por Cobrar (CXC) -->
        <div class="glass-card glass-card-hover p-4 rounded-xl space-y-2">
            <div class="flex items-center justify-between text-slate-400 text-xs font-medium">
                <span>Cuentas por Cobrar (CXC)</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-extrabold text-white font-display">${{ number_format($totalDebtUsd, 2) }} <span class="text-xs font-normal text-slate-400 font-sans">USD</span></div>
            <div class="text-xs text-slate-400">Créditos de clientes vigentes</div>
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

        @else
            <!-- ABASTO / DEFAULT: Fast POS Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Escáner POS de Código de Barras</div>
                    <div class="text-2xl font-extrabold text-emerald-400 font-mono">{{ $businessWidgetsData['scans_per_minute'] ?? 14 }} escaneos/min</div>
                </div>
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Control de Vencimientos</div>
                    <div class="text-2xl font-extrabold text-amber-400 font-mono">{{ $businessWidgetsData['perishables_warning'] ?? 4 }} Alertas de Lote</div>
                </div>
                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-white">Doble Tasa Integrada (BCV)</div>
                    <div class="text-2xl font-extrabold text-indigo-400 font-mono">Activo (USD / VES)</div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Shared Ledger Grid: Cash Turn & Bank Balances -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1 & 2: Active Cash Session & Recent Sales -->
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
                                    <td class="p-3 text-slate-400 font-mono">Bs {{ number_format($sale->total_ves ?? 0, 2) }}</td>
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
