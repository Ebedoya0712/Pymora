<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100 dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Pymora</title>
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

    <div class="w-full max-w-[450px] relative z-10 my-auto">

        <!-- Unified Glass Login Card -->
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
                                <linearGradient id="logoGradPrimary" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#818CF8" />
                                    <stop offset="50%" stop-color="#6366F1" />
                                    <stop offset="100%" stop-color="#A855F7" />
                                </linearGradient>
                                <linearGradient id="logoGradAccent" x1="0%" y1="100%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#38BDF8" />
                                    <stop offset="100%" stop-color="#34D399" />
                                </linearGradient>
                            </defs>
                            <!-- Stylized "P" Emblem -->
                            <path d="M12 8C12 5.79086 13.7909 4 16 4H28C34.6274 4 40 9.37258 40 16C40 22.6274 34.6274 28 28 28H20V40C20 42.2091 18.2091 44 16 44C13.7909 44 12 42.2091 12 40V8Z" fill="url(#logoGradPrimary)"/>
                            <path d="M20 12H27.5C29.9853 12 32 14.0147 32 16.5C32 18.9853 29.9853 21 27.5 21H20V12Z" fill="#0F172A" />
                            <!-- Upward Financial Growth Arrow Accent -->
                            <path d="M26 32L32 26L36 30L42 24" stroke="url(#logoGradAccent)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M37 24H42V29" stroke="url(#logoGradAccent)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white font-display tracking-tight">
                        Pymora
                    </h1>
                    <p class="text-xs text-slate-400 mt-1 font-normal">Sistema Administrativo & Financiero Multimoneda</p>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                @if($errors->any())
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 5000)" 
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-xs flex items-center justify-between gap-2.5 shadow-lg">
                        <div class="flex items-center gap-2.5">
                            <!-- Heroicons Exclamation Triangle -->
                            <svg class="w-5 h-5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                        <button type="button" @click="show = false" title="Cerrar" class="text-rose-400/80 hover:text-white hover:bg-rose-500/20 p-1 rounded-lg transition-all shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if(session('success'))
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-init="setTimeout(() => show = false, 4000)" 
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="p-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-xs flex items-center justify-between gap-2.5 shadow-lg">
                        <div class="flex items-center gap-2.5">
                            <!-- Heroicons Check Circle -->
                            <svg class="w-5 h-5 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" @click="show = false" title="Cerrar" class="text-emerald-400/80 hover:text-white hover:bg-emerald-500/20 p-1 rounded-lg transition-all shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                <!-- Email Input with Heroicon Envelope -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                            <!-- Heroicons Envelope -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                        </div>
                        <input type="email" name="email" required placeholder="tu@empresa.com" class="w-full bg-slate-900/90 border border-slate-800 rounded-xl pl-11 pr-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <!-- Password Input with Heroicon Lock Closed -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Contraseña</label>
                        <a href="#" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1">
                            <!-- Heroicons Key -->
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                            </svg>
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
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

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none text-xs text-slate-400 hover:text-slate-300 transition-colors">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-indigo-600 focus:ring-0 focus:ring-offset-0">
                        <span>Recordar mi sesión</span>
                    </label>
                </div>

                <!-- Submit Button with Heroicon Arrow Right On Rectangle -->
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm rounded-xl transition-all duration-200 transform hover:-translate-y-0.5 glow-effect font-display tracking-wider uppercase flex items-center justify-center gap-2">
                    <!-- Heroicons Arrow Right On Rectangle -->
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                    </svg>
                    <span>Iniciar Sesión</span>
                </button>
            </form>

            <!-- Registration Footer -->
            <div class="pt-2 text-center text-xs text-slate-400 border-t border-slate-800/80 flex items-center justify-center gap-1.5">
                <span>¿No tienes una cuenta?</span>
                <a href="{{ route('register') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1">
                    <!-- Heroicons User Plus -->
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
                    </svg>
                    <span>Registra tu empresa</span>
                </a>
            </div>

        </div>
    </div>
</body>
</html>
