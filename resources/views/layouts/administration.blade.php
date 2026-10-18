<!DOCTYPE html>
<html lang="en">
<head>
    <x-google-analytics />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Director of Education, BTC</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo/logo-em.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('image/logo/logo-em.png') }}">
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <div class="flex h-screen overflow-hidden" x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('adminSidebarCollapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('adminSidebarCollapsed', this.sidebarCollapsed);
        }
    }">
        
        @php $currentOrg = auth()->user()->currentOrganization(); @endphp

        @if(!isset($hideSidebar) || !$hideSidebar)
        <!-- Sidebar -->
        <aside class="bg-slate-900 text-white flex-shrink-0 flex flex-col transition-all duration-300 absolute inset-y-0 left-0 z-[60] md:relative md:translate-x-0"
            :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full': !sidebarOpen && !sidebarCollapsed,
                'w-64': !sidebarCollapsed,
                'w-20': sidebarCollapsed
            }">
            
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center border-b border-slate-800 shrink-0 transition-all duration-300 overflow-hidden"
                :class="sidebarCollapsed ? 'justify-center' : 'px-6 justify-between'">
                
                <div class="flex items-center gap-3 transition-all duration-300">
                    <div class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-900/40">
                        <i data-lucide="shield-check" size="18"></i>
                    </div>
                    <div class="overflow-hidden transition-all duration-300" x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <span class="font-bold text-[15px] tracking-wide leading-none block uppercase truncate">Admin Portal</span>
                        <span class="text-[9px] text-slate-400 font-medium block tracking-wider uppercase mt-0.5 truncate italic">Director of Education, BTC</span>
                    </div>
                </div>

                <!-- Collapse Toggle (Desktop only) -->
                <button @click="toggleSidebar()" class="hidden md:flex items-center justify-center w-8 h-8 rounded-lg bg-slate-800 text-slate-400 hover:text-white transition-all shadow-inner border border-slate-700" x-show="!sidebarCollapsed">
                    <i data-lucide="chevron-left" size="16"></i>
                </button>
                <button @click="toggleSidebar()" class="hidden md:flex items-center justify-center w-10 h-10 rounded-full bg-blue-600/10 text-blue-400 hover:text-white transition-all absolute -right-5 top-8 z-50 border border-blue-500/20 backdrop-blur-md" x-show="sidebarCollapsed">
                    <i data-lucide="chevron-right" size="18"></i>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5 no-scrollbar transition-all duration-300">
                
                <div class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-3 ml-2 mt-4 first:mt-0 transition-opacity duration-300" 
                    :class="sidebarCollapsed ? 'opacity-0 h-0 my-0' : 'opacity-100'">
                    Core Administration
                </div>
                
                <a href="{{ route('administration.dashboard') }}" class="{{ request()->routeIs('administration.dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group overflow-hidden whitespace-nowrap"
                    :class="sidebarCollapsed ? 'justify-center mx-auto' : 'gap-3'">
                    <i data-lucide="layout-dashboard" size="18" class="{{ request()->routeIs('administration.dashboard') ? 'text-white' : 'text-blue-400 group-hover:text-white transition-colors' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">Dashboard</span>
                </a>

                <a href="{{ route('administration.institutions.index') }}" class="{{ request()->routeIs('administration.institutions*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group overflow-hidden whitespace-nowrap"
                    :class="sidebarCollapsed ? 'justify-center mx-auto' : 'gap-3'">
                    <i data-lucide="building" size="18" class="{{ request()->routeIs('administration.institutions*') ? 'text-white' : 'text-blue-400 group-hover:text-white transition-colors' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">Institutions</span>
                </a>

                <a href="{{ route('administration.forms') }}" class="{{ request()->routeIs('administration.forms*', 'administration.configuration*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group overflow-hidden whitespace-nowrap"
                    :class="sidebarCollapsed ? 'justify-center mx-auto' : 'gap-3'">
                    <i data-lucide="file-plus" size="18" class="{{ request()->routeIs('administration.forms*', 'administration.configuration*') ? 'text-white' : 'text-blue-400 group-hover:text-white transition-colors' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">Course Registration</span>
                </a>

                <a href="{{ route('administration.applications') }}" class="{{ request()->routeIs('administration.applications*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group overflow-hidden whitespace-nowrap"
                    :class="sidebarCollapsed ? 'justify-center mx-auto' : 'gap-3'">
                    <i data-lucide="files" size="18" class="{{ request()->routeIs('administration.applications*') ? 'text-white' : 'text-blue-400 group-hover:text-white transition-colors' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">All Applications</span>
                </a>

                <a href="{{ route('administration.admin-cards.index') }}" class="{{ request()->routeIs('administration.admin-cards*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group overflow-hidden whitespace-nowrap"
                    :class="sidebarCollapsed ? 'justify-center mx-auto' : 'gap-3'">
                    <i data-lucide="id-card" size="18" class="{{ request()->routeIs('administration.admin-cards*') ? 'text-white' : 'text-blue-400 group-hover:text-white transition-colors' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">Admit Card</span>
                </a>

                <a href="{{ route('administration.reports') }}" class="{{ request()->routeIs('administration.reports*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group overflow-hidden whitespace-nowrap"
                    :class="sidebarCollapsed ? 'justify-center mx-auto' : 'gap-3'">
                    <i data-lucide="pie-chart" size="18" class="{{ request()->routeIs('administration.reports*') ? 'text-white' : 'text-blue-400 group-hover:text-white transition-colors' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">Reports</span>
                </a>

                @if($currentOrg && optional($currentOrg->configuration)->is_admission_enabled)
                <div x-data="{ open: {{ request()->routeIs('administration.admissions*') ? 'true' : 'false' }} }" class="pt-1">
                    <button @click="if(sidebarCollapsed) { toggleSidebar() } else { open = !open }" class="w-full {{ request()->routeIs('administration.admissions*') ? 'bg-white/5 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center rounded-xl font-medium text-sm transition-all group overflow-hidden whitespace-nowrap"
                        :class="sidebarCollapsed ? 'justify-center mx-auto py-2.5 px-3' : 'px-3 py-2.5 justify-between'">
                        <div class="flex items-center" :class="sidebarCollapsed ? '' : 'gap-3'">
                            <i data-lucide="users" size="18" class="{{ request()->routeIs('administration.admissions*') ? 'text-white' : 'text-blue-400 group-hover:text-white transition-colors' }}"></i>
                            <span x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">Admissions</span>
                        </div>
                        <i data-lucide="chevron-down" size="14" class="transition-transform duration-200 opacity-70" :class="{'rotate-180': open}" x-show="!sidebarCollapsed"></i>
                    </button>
                    
                    <!-- Submenu (only if not collapsed) -->
                    <div x-show="open && !sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mt-1 space-y-1">
                        <a href="{{ route('administration.admissions.index') }}" class="{{ request()->routeIs('administration.admissions.index') ? 'text-white font-bold' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                            <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('administration.admissions.index') ? 'bg-blue-400' : 'bg-slate-600 group-hover:bg-blue-400 transition-colors' }}"></div>
                            Admission Dashboard
                        </a>
                        <a href="{{ route('administration.admissions.enrolled') }}" class="{{ request()->routeIs('administration.admissions.enrolled') ? 'text-white font-bold' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                            <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('administration.admissions.enrolled') ? 'bg-blue-400' : 'bg-slate-600 group-hover:bg-blue-400 transition-colors' }}"></div>
                            Enrolled Students
                        </a>
                    </div>
                </div>
                @endif



                <a href="{{ route('administration.notices.index') }}" class="{{ request()->routeIs('administration.notices*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group overflow-hidden whitespace-nowrap"
                    :class="sidebarCollapsed ? 'justify-center mx-auto' : 'gap-3'">
                    <i data-lucide="bell" size="18" class="{{ request()->routeIs('administration.notices*') ? 'text-white' : 'text-blue-400 group-hover:text-white transition-colors' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">Notice</span>
                </a>


            </nav>

            <!-- Sidebar Footer -->
            <!-- <div class="p-4 border-t border-slate-800 shrink-0 overflow-hidden">
                <a href="{{ route('school.dashboard') }}" class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-blue-900/20 border border-blue-500 whitespace-nowrap"
                    :class="sidebarCollapsed ? 'w-12 h-12 mx-auto sm:px-0' : 'gap-2 w-full py-2.5'">
                    <i data-lucide="graduation-cap" size="16"></i>
                    <span x-show="!sidebarCollapsed" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">Switch to {{ $currentOrg->business_type ?? 'School' }}</span>
                </a>
            </div> -->
        </aside>
        @endif

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative transition-all duration-300">
            
            @if(!isset($hideSidebar) || !$hideSidebar)
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-30 shrink-0 shadow-sm">
                
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button @click="sidebarOpen = true" class="md:hidden text-slate-500 hover:text-blue-600 bg-slate-50 p-2 rounded-xl border border-slate-200 transition-colors">
                        <i data-lucide="menu" size="20"></i>
                    </button>
                    <!-- Small Breadcrumb/Page Context -->
                    <div class="hidden md:flex items-center gap-2 text-sm font-bold text-slate-400">
                        <i data-lucide="shield-check" size="16" class="text-blue-500"></i>
                        <span class="text-slate-600 tracking-tight flex items-center gap-2">
                            <span>Administration</span>
                            <i data-lucide="chevron-right" size="12" class="opacity-30"></i>
                            <span class="text-blue-600">{{ $currentOrg->organization_name ?? 'Portal' }}</span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Profile -->
                    <a href="{{ route('administration.profile') }}" class="flex items-center gap-3 group">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-700 leading-none group-hover:text-blue-600 transition-colors">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mt-1 leading-none">Super Admin</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=3b82f6&color=fff&bold=true" class="w-9 h-9 rounded-xl border-2 border-slate-100 shadow-sm object-cover bg-white group-hover:border-blue-200 transition-all">
                    </a>
                    
                    <div class="w-px h-6 bg-slate-200"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:bg-red-50 hover:text-red-600 transition-all border border-transparent hover:border-red-100" title="Logout">
                            <i data-lucide="log-out" size="18"></i>
                        </button>
                    </form>
                </div>
            </header>
            @endif

            <!-- Scrollable Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8 custom-scrollbar relative">
                <div class="w-full">
                    <!-- Impersonation Alert -->
                    @if(session()->has('impersonator_id'))
                        <div class="mb-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 text-white flex items-center justify-between shadow-xl shadow-blue-900/10 border border-blue-400/20">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-md">
                                    <i data-lucide="user-cog" size="20"></i>
                                </div>
                                <div class="text-sm">
                                    <p class="font-bold">Impersonation Active</p>
                                    <p class="opacity-80 text-xs">You are currently logged in as {!! auth()->user()->name !!}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('impersonate.leave') }}">
                                @csrf
                                <button type="submit" class="bg-white text-blue-700 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-50 transition-colors shadow-lg">
                                    Leave Profile
                                </button>
                            </form>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 z-50 md:hidden backdrop-blur-sm" 
             @click="sidebarOpen = false" style="display: none;"></div>

    </div>

    <!-- Scripts -->
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
