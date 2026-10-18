<!DOCTYPE html>
<html lang="en">

<head>
    <x-google-analytics />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Dashboard') - Director of Education, BTC</title>
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
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
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
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-800">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

        @if(!isset($hideNav) || !$hideNav)
            <!-- Sidebar -->
            <aside
                class="bg-slate-900 text-white flex-shrink-0 flex flex-col transition-all duration-300 absolute inset-y-0 left-0 z-50 md:relative md:translate-x-0 w-64"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

                <!-- Sidebar Header -->
                <div class="h-16 flex items-center px-6 border-b border-slate-800 shrink-0">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-blue-900/40">
                            <i data-lucide="shield-check" size="18"></i>
                        </div>
                        <div>
                            <span class="font-bold text-[15px] tracking-wide leading-none block uppercase">Student
                                Portal</span>
                            <span
                                class="text-[9px] text-slate-400 font-medium block tracking-wider uppercase mt-0.5 italic">Setu
                                Suite</span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Navigation -->
                <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5 no-scrollbar transition-all duration-300">
                    @php
                        $org = auth()->user()->currentOrganization();
                        $isTuition = $org && strtolower($org->business_type) === 'tuition';
                    @endphp

                    <div class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-3 ml-2 mt-4 first:mt-0">
                        Academics
                    </div>

                    <x-student-nav-link href="{{ route('student.dashboard') }}"
                        :active="request()->routeIs('student.dashboard')"
                        icon="layout-dashboard">Dashboard</x-student-nav-link>
                    {{-- 
                    <x-student-nav-link href="{{ route('student.exams.index') }}"
                        :active="request()->routeIs('student.exams*')" icon="file-text">Online Exams</x-student-nav-link>
                    <x-student-nav-link href="{{ route('school.live-classes.index') }}"
                        :active="request()->routeIs('school.live-classes*')" icon="video">Live Classes</x-student-nav-link>
                    @if(!$isTuition)
                        <x-student-nav-link href="{{ route('student.timetable.index') }}"
                            :active="request()->routeIs('student.timetable*')" icon="calendar">Class
                            Timetable</x-student-nav-link>
                        <x-student-nav-link href="{{ route('school.leaves.personal.index') }}"
                            :active="request()->routeIs('school.leaves.personal*')" icon="user-minus">My
                            Leaves</x-student-nav-link>
                    @endif
                    
                    <x-student-nav-link href="{{ route('student.homework.index') }}"
                        :active="request()->routeIs('student.homework*')" icon="book-check">Homework &
                        Assignments</x-student-nav-link>
                    <x-student-nav-link href="{{ route('student.fees.index') }}"
                        :active="request()->routeIs('student.fees.*')" icon="banknote">Academic Fees</x-student-nav-link>
                    <x-student-nav-link href="{{ route('student.feed.index') }}"
                        :active="request()->routeIs('student.feed.*')" icon="rss">Feed</x-student-nav-link>
                    @if(!$isTuition)
                        <x-student-nav-link href="{{ route('student.notifications.index') }}"
                            :active="request()->routeIs('student.notifications*')"
                            icon="bell">Notifications</x-student-nav-link>
                    @endif

                    <div class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-3 ml-2 mt-4">
                        Administrative
                    </div>
                    --}}

                    <x-student-nav-link href="{{ route('student.applications.history') }}"
                        :active="request()->routeIs('student.applications.history') || request()->routeIs('student.application.show') || request()->routeIs('student.application.payment') || request()->routeIs('student.application.form') || request()->routeIs('student.application.preview*')" icon="folder-open">My
                        Applications</x-student-nav-link>

                    <x-student-nav-link href="{{ route('student.admin-cards.index') }}"
                        :active="request()->routeIs('student.admin-cards.*')" icon="id-card">Admin Card</x-student-nav-link>


                </nav>

                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-slate-800 shrink-0">
                    <div class="flex items-center gap-3 p-2 rounded-xl select-none">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Student') }}&background=3b82f6&color=fff&bold=true"
                            class="w-8 h-8 rounded-lg shadow-sm border border-slate-700">
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Student' }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-medium truncate">Registered Student</p>
                        </div>
                    </div>
                </div>
            </aside>
        @endif

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative overflow-hidden transition-all duration-300">

            @if(!isset($hideNav) || !$hideNav)
                <!-- Top Header -->
                <header
                    class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-30 shrink-0 shadow-sm">

                    <div class="flex items-center gap-3">
                        <!-- Mobile Hamburger -->
                        <button @click="sidebarOpen = true"
                            class="md:hidden text-slate-500 hover:text-blue-600 bg-slate-50 p-2 rounded-xl border border-slate-200 transition-colors">
                            <i data-lucide="menu" size="20"></i>
                        </button>
                        <!-- Small Breadcrumb/Page Context -->
                        <div class="hidden md:flex items-center gap-2 text-sm font-bold text-slate-400">
                            <i data-lucide="user" size="16" class="text-blue-500"></i>
                            <span class="text-slate-600 tracking-tight flex items-center gap-2">
                                <span>Candidate Portal</span>
                                <i data-lucide="chevron-right" size="12" class="opacity-30"></i>
                                <span class="text-blue-600">Dashboard</span>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Profile (Mobile & Desktop) -->
                        <div class="flex items-center gap-3 select-none">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-slate-700 leading-none">
                                    {{ auth()->user()->name ?? 'Student' }}
                                </p>
                                <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mt-1 leading-none">
                                    Registered Student</p>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Student') }}&background=3b82f6&color=fff&bold=true"
                                class="w-9 h-9 rounded-xl border-2 border-slate-100 shadow-sm object-cover bg-white">
                        </div>

                        <div class="w-px h-6 bg-slate-200"></div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:bg-red-50 hover:text-red-600 transition-all border border-transparent hover:border-red-100"
                                title="Logout">
                                <i data-lucide="log-out" size="18"></i>
                            </button>
                        </form>
                    </div>
                </header>
            @endif

            <!-- Scrollable Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 sm:p-6 lg:p-8 relative">
                <div class="w-full">
                    <!-- Impersonation Alert -->
                    @if(session()->has('impersonator_id'))
                        <div
                            class="mb-6 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 text-white flex items-center justify-between shadow-xl shadow-blue-900/10 border border-blue-400/20">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-md">
                                    <i data-lucide="user-cog" size="20"></i>
                                </div>
                                <div class="text-sm">
                                    <p class="font-bold">Impersonation Active</p>
                                    <p class="opacity-80 text-xs">You are currently logged in as
                                        {!! auth()->user()->name !!}
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('impersonate.leave') }}">
                                @csrf
                                <button type="submit"
                                    class="bg-white text-blue-700 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-50 transition-colors shadow-lg">
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
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 z-40 md:hidden backdrop-blur-sm"
            @click="sidebarOpen = false" style="display: none;"></div>

    </div>

    <!-- Scripts -->
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>

</html>