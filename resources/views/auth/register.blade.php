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
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Outfit', sans-serif; }
        .glass-container {
            background: rgba(15, 23, 42, 0.75);
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
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-indigo-600/20 rounded-full blur-[130px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-[450px] h-[450px] bg-purple-600/15 rounded-full blur-[110px] pointer-events-none"></div>

    <div x-data="{ subdomain: '' }" class="w-full max-w-[550px] relative z-10 my-auto py-6">

        <!-- Unified Glass Container -->
        <div class="glass-container p-8 sm:p-10 rounded-3xl space-y-7">

            <!-- Detailed Pymora Brand Logo Header -->
            <div class="text-center space-y-3.5">
                <div class="inline-flex items-center justify-center relative group cursor-default">
                    <!-- Glow Backdrop -->
                    <div class="absolute -inset-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-400 rounded-2xl blur-md opacity-80 group-hover:opacity-100 transition duration-500"></div>
                    
                    <!-- Outer Glass Emblem Container -->
                    <div class="relative w-16 h-16 rounded-2xl bg-slate-900 border border-white/20 p-3 flex items-center justify-center shadow-2xl">
                        <!-- Custom Detailed Pymora Vector Emblem -->
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
                            <!-- Stylized "P" Emblem -->
                            <path d="M12 8C12 5.79086 13.7909 4 16 4H28C34.6274 4 40 9.37258 40 16C40 22.6274 34.6274 28 28 28H20V40C20 42.2091 18.2091 44 16 44C13.7909 44 12 42.2091 12 40V8Z" fill="url(#regLogoGradPrimary)"/>
                            <path d="M20 12H27.5C29.9853 12 32 14.0147 32 16.5C32 18.9853 29.9853 21 27.5 21H20V12Z" fill="#0F172A" />
                            <!-- Upward Financial Growth Arrow Accent -->
                            <path d="M26 32L32 26L36 30L42 24" stroke="url(#regLogoGradAccent)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M37 24H42V29" stroke="url(#regLogoGradAccent)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white font-display tracking-tight">Registra tu Empresa</h1>
                    <p class="text-xs text-slate-400 mt-1 font-normal">Sistema Administrativo & Financiero Multimoneda</p>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('register.post') }}" method="POST" class="space-y-6">
                @csrf

                @if($errors->any())
                    <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-xs flex items-center gap-2.5">
                        <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Section 1: Empresa -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-xs font-semibold text-indigo-400 uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        1. Datos de tu Empresa
                    </div>

                    <!-- Company Name Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Nombre Comercial de la Empresa</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                <!-- Heroicons Building Office -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5s0 0 0 0M13.5 6.75h1.5s0 0 0 0M9 10.5h1.5s0 0 0 0M13.5 10.5h1.5s0 0 0 0M9 14.25h1.5s0 0 0 0M13.5 14.25h1.5s0 0 0 0M9 18h1.5s0 0 0 0M13.5 18h1.5s0 0 0 0"/>
                                </svg>
                            </div>
                            <input type="text" name="company_name" required placeholder="Ej: Comercializadora Valera C.A." class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- RIF / Tax ID Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">RIF / Cédula Fiscal</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <!-- Heroicons Identification -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zM6.75 7.5h.008v.008H6.75V7.5zm0 3.75h.008v.008H6.75v-.008zm0 3.75h.008v.008H6.75v-.008z"/>
                                    </svg>
                                </div>
                                <input type="text" name="rif_tax_id" required placeholder="J-12345678-0" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>

                        <!-- Subdomain Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Subdominio deseado</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <!-- Heroicons Globe Alt -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9s2.015-9 4.5-9m0 0a9.004 9.004 0 018.716 6.747M12 3a9.004 9.004 0 00-8.716 6.747M3.75 9h16.5m-16.5 6h16.5"/>
                                    </svg>
                                </div>
                                <input x-model="subdomain" type="text" name="subdomain" required placeholder="valera" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-24 py-3 text-sm text-slate-100 font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                                <span class="absolute right-3 top-3.5 text-[11px] text-slate-500 font-mono font-semibold">.pymora.com</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Administrador -->
                <div class="space-y-4 pt-4 border-t border-slate-800/80">
                    <div class="flex items-center gap-2 text-xs font-semibold text-indigo-400 uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        2. Datos del Administrador (Owner)
                    </div>

                    <!-- Owner Name Input -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Nombre Completo del Dueño</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                <!-- Heroicons User -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
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
                                    <!-- Heroicons Envelope -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                    </svg>
                                </div>
                                <input type="email" name="email" required placeholder="carlos@empresa.com" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>

                        <!-- Phone Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Teléfono (WhatsApp)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <!-- Heroicons Phone -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.162-5.18-3.518-6.342-6.342l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                                    </svg>
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
                                    <!-- Heroicons Lock Closed -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                    </svg>
                                </div>
                                <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>

                        <!-- Password Confirmation Input -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Confirmar Contraseña</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                                    <!-- Heroicons Shield Check -->
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0112 2.714z"/>
                                    </svg>
                                </div>
                                <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 glow-effect font-display tracking-wider uppercase flex items-center justify-center gap-2 mt-2">
                    <!-- Heroicons Rocket Launch -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                    </svg>
                    <span>Crear mi Empresa</span>
                </button>
            </form>

            <!-- Registration Footer -->
            <div class="pt-2 text-center text-xs text-slate-400 border-t border-slate-800/80 flex items-center justify-center gap-1.5">
                <span>¿Ya tienes una cuenta registrada en Pymora?</span>
                <a href="{{ route('login') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1">
                    <!-- Heroicons Arrow Left On Rectangle -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    <span>Inicia sesión aquí</span>
                </a>
            </div>

        </div>
    </div>
</body>
</html>
