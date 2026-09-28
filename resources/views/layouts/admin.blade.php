<!DOCTYPE html>
<html lang="en">
<head>
    <x-google-analytics />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Super Admin</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo/logo.png') }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="Director of Education, BTC">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    @include('layouts.partials.tailwind-config')
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Hide scrollbar for cleaner UI */
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
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <div class="min-h-screen flex flex-col">
        
        <!-- Top Navigation Bar -->
        <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    
                    <!-- Left: Brand -->
                    <div class="flex items-center gap-4">
                        <button class="md:hidden text-slate-400 hover:text-white transition-colors" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                            <i data-lucide="menu" size="24"></i>
                        </button>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-lg shadow-purple-900/50">
                                <span class="font-bold">A</span>
                            </div>
                            <div>
                                <span class="font-bold text-lg tracking-wide leading-none block">System</span>
                                <span class="text-[10px] text-purple-300 font-bold block tracking-wider uppercase">Super Admin</span>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i data-lucide="layout-dashboard" size="16"></i> Overview
                        </a>
                        <a href="{{ route('admin.organizations.index') }}" class="nav-item {{ request()->routeIs('admin.organizations.*') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i data-lucide="building-2" size="16"></i> Organizations
                        </a>
                        <a href="{{ route('admin.lead-tracking') }}" class="nav-item {{ request()->routeIs('admin.lead-tracking') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i data-lucide="phone-call" size="16"></i> Lead Tracking
                        </a>
                        <a href="{{ route('admin.impersonate.index') }}" class="nav-item {{ request()->routeIs('admin.impersonate.*') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2">
                            <i data-lucide="users" size="16"></i> Login As...
                        </a>
                        <a href="{{ route('log-viewer.index') }}" class="nav-item {{ request()->routeIs('log-viewer.*') ? 'nav-active' : 'nav-inactive' }} px-3 py-2 rounded-md text-sm font-medium transition-all flex items-center gap-2" target="_blank">
                            <i data-lucide="terminal" size="16"></i> Logs
                        </a>
                    </nav>

                    <!-- Right: Profile & Logout -->
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 pl-4 border-l border-slate-700 ml-2">
                            <div class="text-right hidden lg:block">
                                <p class="text-sm font-bold leading-none text-white">{{ auth()->user()->name ?? 'Administrator' }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Super Admin</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-slate-300 font-bold border-2 border-slate-600">
                                {{ substr(auth()->user()->name ?? 'AD', 0, 2) }}
                            </div>
                        </div>
                        
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-red-400 transition-colors" title="Logout">
                                <i data-lucide="log-out" size="20"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

             <!-- Mobile Menu -->
             <div id="mobile-menu" class="hidden md:hidden bg-slate-800 border-t border-slate-700 px-4 pt-2 pb-4 space-y-1 shadow-inner">
                <a href="{{ route('admin.dashboard') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">Overview</a>
                <a href="{{ route('admin.organizations.index') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.organizations.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">Organizations</a>
                <a href="{{ route('admin.lead-tracking') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.lead-tracking') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">Lead Tracking</a>
                <a href="{{ route('admin.impersonate.index') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.impersonate.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">Login As...</a>
                <a href="{{ route('log-viewer.index') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('log-viewer.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}" target="_blank">Logs</a>
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

        <!-- Main Content -->
        <main class="flex-1 w-full max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 relative">
            @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                });
            </script>
            @endif

            @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: "{{ session('error') }}",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                });
            </script>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
