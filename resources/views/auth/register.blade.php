<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Empresa - Pymora</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Outfit', sans-serif; }
        .glass-container {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 60px 0 rgba(99, 102, 241, 0.15);
        }
        .glow-effect {
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.4);
        }
        .glow-effect:hover {
            box-shadow: 0 0 35px rgba(99, 102, 241, 0.65);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4 relative overflow-x-hidden antialiased">

    <!-- Ambient Glowing Orbs -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-600/20 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-purple-600/15 rounded-full blur-[120px] pointer-events-none"></div>

    <div x-data="{ 
        step: 1, 
        companyName: '', 
        rifTaxId: '', 
        subdomain: '', 
        selectedType: 'abasto', 
        selectedTypeName: 'Abasto & Supermercado',
        selectType(key, name) {
            this.selectedType = key;
            this.selectedTypeName = name;
        }
    }" class="w-full max-w-[650px] relative z-10 my-auto py-6">

        <!-- Unified Glass Container -->
        <div class="glass-container p-6 sm:p-10 rounded-3xl space-y-6">

            <!-- Detailed Pymora Brand Logo Header -->
            <div class="text-center space-y-3">
                <div class="inline-flex items-center justify-center relative group cursor-default">
                    <div class="absolute -inset-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-400 rounded-2xl blur-md opacity-80 group-hover:opacity-100 transition duration-500"></div>
                    <div class="relative w-14 h-14 rounded-2xl bg-slate-900 border border-white/20 p-2.5 flex items-center justify-center shadow-2xl">
                        <svg class="w-full h-full" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="regLogoGradPrimary" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#818CF8" />
                                    <stop offset="50%" stop-color="#6366F1" />
                                    <stop offset="100%" stop-color="#A855F7" />
                                </linearGradient>
                                <linearGradient id="regLogoGradAccent" x1="0%" y1="100%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#38BDF8" />
                                    <stop offset="100%" stop-color="#34D399" />
                                </linearGradient>
                            </defs>
                            <path d="M12 8C12 5.79086 13.7909 4 16 4H28C34.6274 4 40 9.37258 40 16C40 22.6274 34.6274 28 28 28H20V40C20 42.2091 18.2091 44 16 44C13.7909 44 12 42.2091 12 40V8Z" fill="url(#regLogoGradPrimary)"/>
                            <path d="M20 12H27.5C29.9853 12 32 14.0147 32 16.5C32 18.9853 29.9853 21 27.5 21H20V12Z" fill="#0F172A" />
                            <path d="M26 32L32 26L36 30L42 24" stroke="url(#regLogoGradAccent)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M37 24H42V29" stroke="url(#regLogoGradAccent)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <h1 class="text-2xl font-extrabold text-white font-display tracking-tight">Registra tu Empresa en Pymora</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Asistente de registro paso a paso (1 Mes de Prueba Gratis)</p>
                </div>
            </div>

            <!-- Step Wizard Navigation Indicator Header -->
            <div class="flex items-center justify-between gap-2 px-3 py-2.5 bg-slate-900/80 rounded-2xl border border-slate-800 text-xs">
                <!-- Step 1 Indicator -->
                <button type="button" @click="step = 1" class="flex items-center gap-2 transition-all focus:outline-none" :class="step >= 1 ? 'text-indigo-400 font-bold' : 'text-slate-500'">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-mono font-bold" :class="step >= 1 ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-800 text-slate-400'">1</span>
                    <span class="hidden sm:inline">1. Empresa</span>
                </button>
                <div class="h-0.5 flex-1 bg-slate-800 transition-colors" :class="step >= 2 ? 'bg-indigo-500' : ''"></div>

                <!-- Step 2 Indicator -->
                <button type="button" @click="if(companyName && subdomain) step = 2" class="flex items-center gap-2 transition-all focus:outline-none" :class="step >= 2 ? 'text-indigo-400 font-bold' : 'text-slate-500'">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-mono font-bold" :class="step >= 2 ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-800 text-slate-400'">2</span>
                    <span class="hidden sm:inline">2. Tipo de Negocio</span>
                </button>
                <div class="h-0.5 flex-1 bg-slate-800 transition-colors" :class="step >= 3 ? 'bg-indigo-500' : ''"></div>

                <!-- Step 3 Indicator -->
                <button type="button" @click="if(companyName && subdomain && selectedType) step = 3" class="flex items-center gap-2 transition-all focus:outline-none" :class="step >= 3 ? 'text-indigo-400 font-bold' : 'text-slate-500'">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-mono font-bold" :class="step >= 3 ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'bg-slate-800 text-slate-400'">3</span>
                    <span class="hidden sm:inline">3. Registro</span>
                </button>
            </div>

            <!-- Main Form -->
            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <input type="hidden" name="business_type" :value="selectedType">

                @if($errors->any())
                    <div class="mb-4 p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-xs flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- STEP 1: Información de la Empresa -->
                <div x-show="step === 1" x-cloak class="space-y-4">
                    <div class="flex items-center gap-2 text-xs font-semibold text-indigo-400 uppercase tracking-wider border-b border-slate-800 pb-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        Paso 1 de 3: Datos Generales de tu Empresa
                    </div>

                    <!-- Company Name Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Nombre Comercial de la Empresa</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5s0 0 0 0M13.5 6.75h1.5s0 0 0 0M9 10.5h1.5s0 0 0 0M13.5 10.5h1.5s0 0 0 0M9 14.25h1.5s0 0 0 0M13.5 14.25h1.5s0 0 0 0M9 18h1.5s0 0 0 0M13.5 18h1.5s0 0 0 0"/></svg>
                            </div>
                            <input type="text" name="company_name" x-model="companyName" required placeholder="Ej: Comercializadora Valera C.A." class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- RIF / Tax ID Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">RIF / Cédula Fiscal</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zM6.75 7.5h.008v.008H6.75V7.5zm0 3.75h.008v.008H6.75v-.008zm0 3.75h.008v.008H6.75v-.008z"/></svg>
                                </div>
                                <input type="text" name="rif_tax_id" x-model="rifTaxId" required placeholder="J-12345678-0" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>

                        <!-- Subdomain Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Subdominio deseado</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9s2.015-9 4.5-9m0 0a9.004 9.004 0 018.716 6.747M12 3a9.004 9.004 0 00-8.716 6.747M3.75 9h16.5m-16.5 6h16.5"/></svg>
                                </div>
                                <input x-model="subdomain" type="text" name="subdomain" required placeholder="valera" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-24 py-3 text-sm text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                                <span class="absolute right-3 top-3.5 text-[11px] text-slate-500 font-mono font-semibold">.pymora.com</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end">
                        <button type="button" @click="if(companyName && subdomain) { step = 2; } else { alert('Por favor ingresa el nombre de tu empresa y el subdominio para continuar.'); }" class="w-full sm:w-auto px-8 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <span>Siguiente</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Escoge el Tipo de tu Empresa (Visual Icon Grid) -->
                <div x-show="step === 2" x-cloak class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <div>
                            <h3 class="text-sm font-bold text-white font-display">Escoge el tipo de tu empresa</h3>
                            <p class="text-xs text-slate-400">Selecciona el rubro para personalizar tu Dashboard y vistas de operaciones.</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-300 font-mono text-xs font-bold border border-indigo-500/30" x-text="selectedTypeName"></span>
                    </div>

                    <!-- 11 Business Types Icon Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-[360px] overflow-y-auto pr-1">
                        @php
                            $typesList = isset($businessTypes) ? $businessTypes : App\Models\Tenant::getBusinessTypes();
                        @endphp
                        @foreach($typesList as $bKey => $bMeta)
                            <div @click="selectType('{{ $bKey }}', '{{ $bMeta['name'] }}')"
                                 class="p-3.5 rounded-2xl border transition-all cursor-pointer flex flex-col items-center justify-center text-center gap-2 group relative"
                                 :class="selectedType === '{{ $bKey }}' ? 'bg-indigo-600/20 border-indigo-500 shadow-xl ring-2 ring-indigo-500/40' : 'bg-slate-900/60 border-slate-800 hover:border-slate-700 hover:bg-slate-800/50'">
                                
                                <div class="w-11 h-11 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                    {{ $bMeta['icon'] }}
                                </div>
                                <div class="font-bold text-xs text-white leading-tight font-display">{{ $bMeta['name'] }}</div>
                                <div x-show="selectedType === '{{ $bKey }}'" class="absolute top-2 right-2 w-4 h-4 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center font-bold text-[10px]">✓</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 flex items-center justify-between gap-3 border-t border-slate-800">
                        <button type="button" @click="step = 1" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Volver</span>
                        </button>

                        <button type="button" @click="step = 3" class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center gap-2">
                            <span>Siguiente</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Datos del Administrador & Registro Completo -->
                <div x-show="step === 3" x-cloak class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <div class="flex items-center gap-2 text-xs font-semibold text-indigo-400 uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            Paso 3 de 3: Registro de Cuenta
                        </div>
                    </div>

                    <!-- Summary Badge -->
                    <div class="p-3.5 bg-slate-900/90 rounded-2xl border border-slate-800 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <div class="font-bold text-white text-sm" x-text="companyName || 'Tu Empresa'"></div>
                            <div class="text-indigo-400 font-mono text-[11px]" x-text="subdomain ? subdomain + '.pymora.com' : 'tu-subdominio.pymora.com'"></div>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl bg-indigo-600/20 text-indigo-300 font-bold text-xs border border-indigo-500/30" x-text="selectedTypeName"></span>
                    </div>

                    <!-- Owner Name Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Nombre Completo del Dueño</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            </div>
                            <input type="text" name="owner_name" required placeholder="Ej: Carlos Mendoza" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Email Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Correo Electrónico</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                                </div>
                                <input type="email" name="email" required placeholder="carlos@empresa.com" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>

                        <!-- Phone Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Teléfono (WhatsApp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.162-5.18-3.518-6.342-6.342l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                                </div>
                                <input type="text" name="phone" required placeholder="+584121234567" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Password Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Contraseña</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                                </div>
                                <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>

                        <!-- Password Confirmation Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Confirmar Contraseña</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0112 2.714z"/></svg>
                                </div>
                                <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between gap-3 border-t border-slate-800">
                        <button type="button" @click="step = 2" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Volver</span>
                        </button>

                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-emerald-600 via-teal-500 to-indigo-600 hover:from-emerald-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 glow-effect font-display tracking-wider uppercase flex items-center justify-center gap-2 shadow-2xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/></svg>
                            <span>Crear mi Empresa (30 Días Gratis)</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Registration Footer -->
            <div class="pt-2 text-center text-xs text-slate-400 border-t border-slate-800/80 flex items-center justify-center gap-1.5">
                <span>¿Ya tienes una cuenta registrada en Pymora?</span>
                <a href="{{ route('login') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                    <span>Inicia sesión aquí</span>
                </a>
            </div>

        </div>
    </div>
</body>
</html>
