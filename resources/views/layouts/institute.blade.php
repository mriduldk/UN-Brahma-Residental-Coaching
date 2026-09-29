<!DOCTYPE html>
<html lang="en">
<head>
    <x-google-analytics />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Institute Dashboard') - Director of Education, BTC</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo/logo-em.png') }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="Director of Education, BTC">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    @include('layouts.partials.tailwind-config')
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js for Reports -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Active Nav Item Style */
        .nav-active {
            background-color: #2563eb; /* blue-600 */
            color: #ffffff;
        }
        .nav-inactive {
            color: #cbd5e1; /* slate-300 */
        }
        .nav-inactive:hover {
            background-color: #1e293b; /* slate-800 */
            color: #ffffff;
        }

        /* Toggle Switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #3b82f6;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #3b82f6;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <div class="min-h-screen flex flex-col">
        
        <!-- Top Navigation Bar -->
        <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    
                    <!-- Left: Brand & Mobile Toggle -->
                    <div class="flex items-center gap-4">
                        <!-- Mobile Menu Button -->
                        <button class="md:hidden text-slate-400 hover:text-white transition-colors" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                            <i data-lucide="menu" size="24"></i>
                        </button>
                        
                        <!-- Brand -->
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-900/50">
                                <i data-lucide="building" size="20"></i>
                            </div>
                            <div>
                                <span class="font-bold text-lg tracking-wide leading-none block">Institute Portal</span>
                                <span class="text-[10px] text-slate-400 font-medium block tracking-wider uppercase">Director of Education, BTC</span>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('institute.dashboard') }}" class="nav-item {{ request()->routeIs('institute.dashboard') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i data-lucide="layout-dashboard" size="16"></i> Dashboard
                        </a>
                        <a href="{{ route('institute.institutions.index') }}" class="nav-item {{ request()->routeIs('institute.institutions.*', 'institute.admission-configs.*') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i data-lucide="building-2" size="16"></i> Institutions
                        </a>
                        <a href="{{ route('institute.forms.index') }}" class="nav-item {{ request()->routeIs('institute.forms.*') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i data-lucide="file-text" size="16"></i> Forms
                        </a>
                        <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                            <a href="{{ route('institute.admissions.index') }}" class="nav-item {{ request()->routeIs('institute.admissions.*') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                                <i data-lucide="graduation-cap" size="16"></i> Admissions
                                <i data-lucide="chevron-down" size="14" class="ml-1 opacity-50"></i>
                            </a>
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute left-0 mt-0 w-48 bg-slate-900 border border-slate-700 rounded-md shadow-xl z-[60]">
                                <div class="py-1">
                                    <a href="{{ route('institute.admissions.index') }}" class="block px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors flex items-center gap-2">
                                        <i data-lucide="layout-dashboard" size="14"></i> Admission Dashboard
                                    </a>
                                    <a href="{{ route('institute.admissions.enrolled') }}" class="block px-4 py-2 text-xs font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors flex items-center gap-2">
                                        <i data-lucide="user-check" size="14"></i> Enrolled Students
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('institute.finance') }}" class="nav-item {{ request()->routeIs('institute.finance*') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i data-lucide="indian-rupee" size="16"></i> Payments
                        </a>
                    </nav>

                    <!-- Right: Search & Profile -->
                    <div class="flex items-center gap-4">

                        <a href="#">
                            <!-- Profile -->
                            <div class="flex items-center gap-3 pl-4 border-l border-slate-700 ml-2 cursor-pointer">
                                <div class="text-right hidden lg:block">
                                    <p class="text-sm font-bold leading-none text-white">{{ auth()->user()->name ?? 'Institute User' }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Institute Manager</p>
                                </div>
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Institute User') }}&background=6366f1&color=fff&bold=true" class="w-8 h-8 rounded-full border-2 border-slate-700 shadow-sm cursor-pointer hover:border-indigo-500 transition-colors">
                            </div>
                        </a>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-white transition-colors" title="Logout">
                                <i data-lucide="log-out" size="20"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="mobile-menu" class="hidden md:hidden bg-slate-800 border-t border-slate-700 px-4 pt-2 pb-4 space-y-1 shadow-inner">
                <a href="{{ route('institute.dashboard') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('institute.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">Dashboard</a>
                
                <div x-data="{ open: {{ request()->routeIs('institute.admissions.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('institute.admissions.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        <span>Admissions</span>
                        <i data-lucide="chevron-down" size="18" :class="{'rotate-180': open}"></i>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1 mt-1">
                        <a href="{{ route('institute.admissions.index') }}" class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('institute.admissions.index') ? 'text-white bg-slate-700' : 'text-slate-400 hover:text-white' }}">Admission Dashboard</a>
                        <a href="{{ route('institute.admissions.enrolled') }}" class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('institute.admissions.enrolled') ? 'text-white bg-slate-700' : 'text-slate-400 hover:text-white' }}">Enrolled Students</a>
                    </div>
                </div>
                
                <div class="border-t border-slate-700 pt-4 mt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center text-red-400 hover:text-red-300 w-full px-3 py-2">
                            <i data-lucide="log-out" size="18" class="mr-2"></i> Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
            @if(session()->has('impersonator_id'))
                <div class="mb-6 rounded-xl bg-indigo-600 px-4 py-3 text-white sm:flex sm:items-center sm:justify-between shadow-lg">
                    <p class="text-sm font-medium">
                        You are currently impersonating <strong>{{ auth()->user()->name }}</strong>.
                    </p>
                    <form method="POST" action="{{ route('impersonate.leave') }}" class="mt-4 sm:mt-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-sm font-bold text-indigo-600 shadow-sm hover:bg-indigo-50 transition-colors">
                            <i data-lucide="log-out" size="16"></i> Return to Admin
                        </button>
                    </form>
                </div>
            @endif

            <x-email-verification-alert />
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Initialize Icons
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
