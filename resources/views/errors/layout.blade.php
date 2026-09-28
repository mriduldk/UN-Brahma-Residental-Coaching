<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Director of Education, BTC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('layouts.partials.tailwind-config')
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="bg-slate-50 relative overflow-hidden min-h-screen flex items-center justify-center">

    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute top-[-10%] right-[-5%] w-[600px] h-[600px] bg-blue-100 rounded-full blur-[120px] opacity-60"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[500px] h-[500px] bg-indigo-100 rounded-full blur-[100px] opacity-60"></div>
    </div>

    <div class="relative z-10 w-full max-w-lg px-6">
        <div class="glass-card rounded-3xl shadow-2xl p-8 md:p-12 text-center border border-slate-100/50 relative overflow-hidden">
            <!-- decorative ring -->
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

            <div class="mb-8 flex justify-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center shadow-inner animate-float">
                    @yield('icon')
                </div>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-800 mb-2 tracking-tight">
                @yield('code')
            </h1>
            
            <h2 class="text-xl md:text-2xl font-bold text-slate-700 mb-4">
                @yield('message')
            </h2>
            
            <p class="text-slate-500 mb-8 leading-relaxed max-w-sm mx-auto">
                @yield('description')
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/30 hover:-translate-y-1">
                    <i data-lucide="home" size="18" class="mr-2"></i> Go Home
                </a>
                <button onclick="history.back()" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-base font-medium rounded-xl text-slate-700 bg-white hover:bg-slate-50 transition-all hover:-translate-y-1">
                    <i data-lucide="arrow-left" size="18" class="mr-2"></i> Go Back
                </button>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-400 font-medium tracking-wide uppercase">
                    Director of Education, BTC
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
