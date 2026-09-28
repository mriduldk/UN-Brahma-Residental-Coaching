<!DOCTYPE html>
<html lang="en">
<head>
    <x-google-analytics />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Director of Education, BTC Demo' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900 overflow-x-hidden relative min-h-screen" x-data="{ sidebarOpen: false }">
    <!-- 1. Background Decoration (Ensuring Pointer Events are None) -->
    <div class="fixed inset-0 pointer-events-none -z-50 overflow-hidden" style="pointer-events: none !important;">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-100/10 rounded-full blur-[120px] -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-100/10 rounded-full blur-[100px] -ml-40 -mb-40"></div>
    </div>

    <!-- 2. Main Content Area -->
    <main class="pt-16 min-h-screen relative z-0">
        <div class="p-6 md:p-8 lg:p-12 max-w-7xl mx-auto">
            {{ $slot }}
        </div>
    </main>

    <!-- 3. Navigation Backdrop -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[9998]"
         style="display: none; pointer-events: auto;"
         x-cloak>
    </div>

    <!-- 4. Sidebar Drawer -->
    <aside class="fixed top-0 left-0 bottom-0 h-screen bg-white transition-all duration-300 z-[9999] overflow-hidden shadow-2xl w-72 border-r border-slate-100"
           x-show="sidebarOpen"
           style="display: none;"
           x-cloak>
        <div class="h-full flex flex-col p-8">

            <nav class="space-y-1.5 flex-1 italic mt-12">
                @if(request()->is('demo/admin*'))
                    <a href="{{ route('demo.admin.dashboard') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->routeIs('demo.admin.dashboard') ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50' }}">
                        <i data-lucide="bar-chart-3" size="18"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Dashboard</span>
                    </a>
                    <a href="{{ route('demo.admin.institutes') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->routeIs('demo.admin.institutes') ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <i data-lucide="building-2" size="18"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Institutes</span>
                    </a>
                    <a href="{{ route('demo.admin.forms') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->routeIs('demo.admin.forms') ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <i data-lucide="file-edit" size="18"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Form Builder</span>
                    </a>
                    <a href="{{ route('demo.admin.applications') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->routeIs('demo.admin.applications') ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <i data-lucide="users" size="18"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Applications</span>
                    </a>
                @else
                    <a href="{{ route('demo.student.dashboard') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->routeIs('demo.student.dashboard') ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:bg-slate-50' }}">
                        <i data-lucide="layout" size="18"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Portal Home</span>
                    </a>
                    <a href="{{ route('demo.student.application-form') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->routeIs('demo.student.application-form') ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <i data-lucide="clipboard-list" size="18"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Apply Now</span>
                    </a>
                    <a href="{{ route('demo.student.application-view') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->routeIs('demo.student.application-view') ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <i data-lucide="eye" size="18"></i>
                        <span class="text-xs font-black uppercase tracking-widest">My Status</span>
                    </a>
                    <a href="{{ route('demo.student.payment') }}" class="flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->routeIs('demo.student.payment') ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' }}">
                        <i data-lucide="wallet" size="18"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Payments</span>
                    </a>
                @endif
            </nav>
            
            <button @click="sidebarOpen = false" class="mt-auto w-full py-3 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest">Close</button>
        </div>
    </aside>

    <!-- 5. Fixed Header (Absolute Max Z-Index) -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-slate-200 shadow-md flex items-center justify-between px-6" 
            style="z-index: 999999 !important; pointer-events: auto !important;">
        <div class="flex items-center gap-4">
            <div @click="sidebarOpen = !sidebarOpen" 
                 class="bg-slate-900 p-2.5 rounded-xl cursor-pointer hover:bg-blue-600 transition-all text-white shadow-lg">
                <i data-lucide="menu" size="20"></i>
            </div>
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <div>
                    <p class="font-black text-xs uppercase tracking-[0.2em] leading-none text-slate-900 italic">Director of Education, BTC</p>
                    <p class="text-blue-600 text-[9px] font-black uppercase tracking-tighter opacity-80 italic">Enterprise Demo</p>
                </div>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('demo.admin.dashboard') }}" 
               class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request()->is('demo/admin*') ? 'bg-slate-900 text-white' : 'text-slate-400 hover:text-slate-900' }}">Admin</a>
            <a href="{{ route('demo.student.dashboard') }}" 
               class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request()->is('demo/student*') ? 'bg-slate-900 text-white' : 'text-slate-400 hover:text-slate-900' }}">Student</a>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>
