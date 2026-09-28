<!DOCTYPE html>
@php
    $org = auth()->check() ? auth()->user()->currentOrganization() : null;
    $businessType = $org ? $org->business_type : 'School';

    $appName = 'School Manager';
    $appSuite = 'Director of Education, BTC';
    $topNavTitle = 'School Management';
    $roleTitle = 'School Manager';

    if ($businessType === 'COACHING') {
        $appName = 'Coaching Manager';
        $appSuite = 'Director of Education, BTC';
        $topNavTitle = 'Coaching Management';
        $roleTitle = 'Institute Manager';
    } elseif ($businessType === 'TUITION') {
        $appName = 'Tuition Manager';
        $appSuite = 'Director of Education, BTC';
        $topNavTitle = 'Tuition Management';
        $roleTitle = 'Tutor Manager';
    }
@endphp
<html lang="en">

<head>
    <x-google-analytics />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School Dashboard') - Director of Education, BTC</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('image/logo/logo.png') }}">
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
            background-color: #4f46e5;
            /* indigo-600 */
            color: #ffffff;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        .nav-inactive {
            color: #cbd5e1;
            /* slate-300 */
        }

        .nav-inactive:hover {
            background-color: #312e81;
            /* indigo-900 */
            color: #ffffff;
        }

        /* Toggle Switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #6366f1;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #6366f1;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Hide scrollbar for sidebar but keep functionality */
        .sidebar-no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .sidebar-no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-800">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

        <!-- Sidebar -->
        <aside
            class="bg-indigo-950 text-white w-64 flex-shrink-0 flex flex-col transition-transform duration-300 absolute inset-y-0 left-0 z-[60] md:relative md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Sidebar Header / Brand -->
            <div class="h-16 flex flex-col justify-center px-6 border-b border-indigo-900/50 shrink-0">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 flex-shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold shadow-sm">
                        <i data-lucide="graduation-cap" size="18"></i>
                    </div>
                    <div class="overflow-hidden">
                        <span
                            class="font-bold text-[15px] tracking-wide leading-none block uppercase truncate">{{ $appName }}</span>
                        <span
                            class="text-[9px] text-indigo-300 font-medium block tracking-wider uppercase mt-0.5 truncate">{{ $appSuite }}</span>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav id="school-sidebar-nav"
                class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5 custom-scrollbar sidebar-no-scrollbar">

                <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-3 ml-2 mt-4 first:mt-0">
                    Core Modules</div>

                <a href="{{ route('school.dashboard') }}"
                    class="{{ request()->routeIs('school.dashboard') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="layout-dashboard" size="18"
                        class="{{ request()->routeIs('school.dashboard') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Dashboard
                </a>

                <a href="{{ route('school.sessions.index') }}"
                    class="{{ request()->routeIs('school.sessions*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="calendar" size="18"
                        class="{{ request()->routeIs('school.sessions*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Academic Sessions
                </a>

                <a href="{{ route('school.classes.index') }}"
                    class="{{ request()->routeIs('school.classes*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="book-open" size="18"
                        class="{{ request()->routeIs('school.classes*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Classes & Sections
                </a>

                <a href="{{ route('school.enrollment.mapped') }}"
                    class="{{ request()->routeIs('school.enrollment.mapped') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="user-check" size="18"
                        class="{{ request()->routeIs('school.enrollment.mapped') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Student Management
                </a>

                <a href="{{ route('school.staff.index') }}"
                    class="{{ request()->routeIs('school.staff*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="users-2" size="18"
                        class="{{ request()->routeIs('school.staff*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Staff Management
                </a>

                <a href="{{ route('administration.subscription.billing') }}"
                    class="{{ request()->routeIs('administration.subscription*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="credit-card" size="18"
                        class="{{ request()->routeIs('administration.subscription*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Billing & Subscription
                </a>

                <a href="{{ route('school.fees.index') }}"
                    class="{{ request()->routeIs('school.fees*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="banknote" size="18"
                        class="{{ request()->routeIs('school.fees*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Monthly Fee Tracking
                </a>

                <a href="{{ route('school.feed.index') }}"
                    class="{{ request()->routeIs('school.feed*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="rss" size="18"
                        class="{{ request()->routeIs('school.feed*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Feed
                </a>

                <a href="{{ route('school.homework.index') }}"
                    class="{{ request()->routeIs('school.homework*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="book-check" size="18"
                        class="{{ request()->routeIs('school.homework*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Homework
                </a>

                @if($businessType !== 'TUITION')
                    <a href="{{ route('school.notifications.index') }}"
                        class="{{ request()->routeIs('school.notifications*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                        <i data-lucide="bell" size="18"
                            class="{{ request()->routeIs('school.notifications*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                        Notifications
                    </a>
                @endif

                <a href="{{ route('administration.exams.index') }}"
                    class="{{ request()->routeIs('administration.exams*') || request()->routeIs('administration.question-banks*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="file-text" size="18"
                        class="{{ request()->routeIs('administration.exams*') || request()->routeIs('administration.question-banks*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Exams Management
                </a>

                @if(auth()->user()->role === 'administration' && $businessType !== 'TUITION')
                    <a href="{{ route('school.leaves.index') }}"
                        class="{{ request()->routeIs('school.leaves.index') || request()->routeIs('school.leaves.create') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                        <i data-lucide="calendar-off" size="18"
                            class="{{ request()->routeIs('school.leaves.index') || request()->routeIs('school.leaves.create') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                        Leave Management
                    </a>
                @endif

                @if(auth()->user()->role === 'student' && $businessType !== 'TUITION')
                    <a href="{{ route('school.leaves.personal.index') }}"
                        class="{{ request()->routeIs('school.leaves.personal*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                        <i data-lucide="user-minus" size="18"
                            class="{{ request()->routeIs('school.leaves.personal*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                        My Leaves
                    </a>
                @endif



                <a href="{{ route('school.live-classes.index') }}"
                    class="{{ request()->routeIs('school.live-classes*') ? 'bg-indigo-600 text-white shadow-md active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                    <i data-lucide="video" size="18"
                        class="{{ request()->routeIs('school.live-classes*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                    Live Classes
                </a>

                @if($businessType !== 'TUITION')
                    <div x-data="{ open: {{ request()->routeIs('school.timetable*') ? 'true' : 'false' }} }" class="pt-1">
                        <button @click="open = !open"
                            class="w-full {{ request()->routeIs('school.timetable*') ? 'bg-white/5 text-white active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                            <div class="flex items-center gap-3">
                                <i data-lucide="calendar-range" size="18"
                                    class="{{ request()->routeIs('school.timetable*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                                Timetable
                            </div>
                            <i data-lucide="chevron-down" size="14" class="transition-transform duration-200 opacity-70"
                                :class="{'rotate-180': open}"></i>
                        </button>

                        <!-- Submenu -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2" class="mt-1 space-y-1"
                            style="display: {{ request()->routeIs('school.timetable*') ? 'block' : 'none' }};">
                            <a href="{{ route('school.timetable.index') }}"
                                class="{{ request()->routeIs('school.timetable.index') ? 'text-white font-bold active-nav-link' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                                <div
                                    class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('school.timetable.index') ? 'bg-indigo-400' : 'bg-slate-600 group-hover:bg-indigo-400 transition-colors' }}">
                                </div>
                                Manage Schedule
                            </a>
                            <a href="{{ route('school.timetable.view') }}"
                                class="{{ request()->routeIs('school.timetable.view') ? 'text-white font-bold active-nav-link' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                                <div
                                    class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('school.timetable.view') ? 'bg-indigo-400' : 'bg-slate-600 group-hover:bg-indigo-400 transition-colors' }}">
                                </div>
                                Live View
                            </a>
                        </div>
                    </div>
                @endif

                <div x-data="{ open: {{ (request()->routeIs('school.enrollment*') && !request()->routeIs('school.enrollment.mapped')) || request()->routeIs('school.students*') ? 'true' : 'false' }} }"
                    class="pt-1">
                    <button @click="open = !open"
                        class="w-full {{ (request()->routeIs('school.enrollment*') && !request()->routeIs('school.enrollment.mapped')) || request()->routeIs('school.students*') ? 'bg-white/5 text-white active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                        <div class="flex items-center gap-3">
                            <i data-lucide="user-plus" size="18"
                                class="{{ (request()->routeIs('school.enrollment*') && !request()->routeIs('school.enrollment.mapped')) || request()->routeIs('school.students*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                            Enrollment
                        </div>
                        <i data-lucide="chevron-down" size="14" class="transition-transform duration-200 opacity-70"
                            :class="{'rotate-180': open}"></i>
                    </button>

                    <!-- Submenu -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="mt-1 space-y-1"
                        style="display: {{ request()->routeIs('school.enrollment*') || request()->routeIs('school.students*') ? 'block' : 'none' }};">
                        <a href="{{ route('school.enrollment.index') }}"
                            class="{{ request()->routeIs('school.enrollment.index') ? 'text-white font-bold active-nav-link' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                            <div
                                class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('school.enrollment.index') ? 'bg-indigo-400' : 'bg-slate-600 group-hover:bg-indigo-400 transition-colors' }}">
                            </div>
                            Enrollment Mapping
                        </a>
                        <a href="{{ route('school.students.create') }}"
                            class="{{ request()->routeIs('school.students.create') ? 'text-white font-bold active-nav-link' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                            <div
                                class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('school.students.create') ? 'bg-indigo-400' : 'bg-slate-600 group-hover:bg-indigo-400 transition-colors' }}">
                            </div>
                            Add New Student
                        </a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->routeIs('school.attendance*') ? 'true' : 'false' }} }" class="pt-1">
                    <button @click="open = !open"
                        class="w-full {{ request()->routeIs('school.attendance*') ? 'bg-white/5 text-white active-nav-link' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition-all group">
                        <div class="flex items-center gap-3">
                            <i data-lucide="calendar-check" size="18"
                                class="{{ request()->routeIs('school.attendance*') ? 'text-white' : 'text-indigo-400 group-hover:text-white transition-colors' }}"></i>
                            Attendance
                        </div>
                        <i data-lucide="chevron-down" size="14" class="transition-transform duration-200 opacity-70"
                            :class="{'rotate-180': open}"></i>
                    </button>

                    <!-- Submenu -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2" class="mt-1 space-y-1"
                        style="display: {{ request()->routeIs('school.attendance*') ? 'block' : 'none' }};">
                        <a href="{{ route('school.attendance.students') }}"
                            class="{{ request()->routeIs('school.attendance.students') ? 'text-white font-bold active-nav-link' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                            <div
                                class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('school.attendance.students') ? 'bg-indigo-400' : 'bg-slate-600 group-hover:bg-indigo-400 transition-colors' }}">
                            </div>
                            Student Attendance
                        </a>
                        @if($businessType !== 'TUITION')
                            <a href="{{ route('school.attendance.staff') }}"
                                class="{{ request()->routeIs('school.attendance.staff') ? 'text-white font-bold active-nav-link' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                                <div
                                    class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('school.attendance.staff') ? 'bg-indigo-400' : 'bg-slate-600 group-hover:bg-indigo-400 transition-colors' }}">
                                </div>
                                Staff Attendance
                            </a>
                        @endif

                        <div class="my-1.5 mx-3 h-px bg-white/10"></div>

                        <a href="{{ route('school.attendance.report.students') }}"
                            class="{{ request()->routeIs('school.attendance.report.students') ? 'text-white font-bold active-nav-link' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                            <div
                                class="w-1 h-1 rounded-full {{ request()->routeIs('school.attendance.report.students') ? 'bg-indigo-400' : 'bg-transparent border border-slate-500 group-hover:border-indigo-400 transition-colors' }}">
                            </div>
                            Student Report
                        </a>
                        <a href="{{ route('school.attendance.report.percentage') }}"
                            class="{{ request()->routeIs('school.attendance.report.percentage') ? 'text-white font-bold active-nav-link' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                            <div
                                class="w-1 h-1 rounded-full {{ request()->routeIs('school.attendance.report.percentage') ? 'bg-indigo-400' : 'bg-transparent border border-slate-500 group-hover:border-indigo-400 transition-colors' }}">
                            </div>
                            Student Percentage Report
                        </a>
                        @if($businessType !== 'TUITION')
                            <a href="{{ route('school.attendance.report.staff') }}"
                                class="{{ request()->routeIs('school.attendance.report.staff') ? 'text-white font-bold active-nav-link' : 'text-slate-400 font-medium hover:text-white hover:translate-x-1' }} flex items-center gap-3 pl-11 pr-3 py-2 rounded-lg text-sm transition-all group">
                                <div
                                    class="w-1 h-1 rounded-full {{ request()->routeIs('school.attendance.report.staff') ? 'bg-indigo-400' : 'bg-transparent border border-slate-500 group-hover:border-indigo-400 transition-colors' }}">
                                </div>
                                Staff Report
                            </a>
                        @endif
                    </div>
                </div>

            </nav>

            <!-- Sidebar Footer -->
            @if($businessType !== 'TUITION')
                <div class="p-4 border-t border-indigo-900/50 shrink-0">
                    <a href="{{ route('administration.dashboard') }}"
                        class="flex items-center justify-center gap-2 w-full py-2.5 bg-indigo-900 hover:bg-indigo-800 text-indigo-100 hover:text-white text-sm font-bold rounded-xl transition-colors border border-indigo-800 shadow-sm">
                        <i data-lucide="arrow-left-right" size="16"></i>
                        Return to Admin
                    </a>
                </div>
            @endif
        </aside>

        <!-- Overlay for mobile styling -->
        <div x-show="sidebarOpen" x-transition.opacity
            class="fixed inset-0 bg-slate-900/50 z-50 md:hidden backdrop-blur-sm" @click="sidebarOpen = false"
            style="display: none;"></div>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative">

            <!-- Top Header (For mobile toggle and Profile) -->
            <header
                class="h-16 bg-white border-b border-slate-200 shadow-sm flex items-center justify-between px-4 sm:px-6 lg:px-8 z-30 shrink-0 relative flex-shrink-0">

                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button @click="sidebarOpen = true"
                        class="md:hidden text-slate-500 hover:text-indigo-600 bg-slate-50 p-2 rounded-xl border border-slate-200 transition-colors">
                        <i data-lucide="menu" size="20"></i>
                    </button>
                    <!-- Small Breadcrumb/Brand for Desktop -->
                    <div class="hidden md:flex items-center gap-2 text-sm font-bold text-slate-400">
                        <i data-lucide="layout-grid" size="16" class="text-indigo-500"></i>
                        <span class="text-slate-600">{{ $topNavTitle }} <span class="text-slate-300 mx-1">|</span> <span
                                class="text-indigo-600">{{ auth()->user()->currentOrganization()->organization_name ?? '' }}</span></span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Profile & Actions -->
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-700 leading-none">
                                {{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mt-1">
                                {{ $roleTitle }}</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&background=6366f1&color=fff&bold=true"
                            class="w-9 h-9 rounded-xl border-2 border-slate-100 shadow-sm object-cover bg-white">
                    </div>

                    <div class="w-px h-6 bg-slate-200"></div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit"
                            class="w-9 h-9 flex items-center justify-center rounded-xl text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors border border-transparent hover:border-red-100"
                            title="Logout">
                            <i data-lucide="log-out" size="18"></i>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Scrollable Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 custom-scrollbar p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    <!-- Impersonation Alert -->
                    @if(session()->has('impersonator_id'))
                        <div class="rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 text-white flex items-center justify-between shadow-xl shadow-blue-900/10 border border-blue-400/20">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-md">
                                    <i data-lucide="user-cog" size="20"></i>
                                </div>
                                <div class="text-sm">
                                    <p class="font-bold">Impersonation Active</p>
                                    <p class="opacity-80 text-xs">You are currently logged in as {!! auth()->user()->name !!}</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('impersonate.leave') }}" class="m-0">
                                @csrf
                                <button type="submit" class="bg-white text-blue-700 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-50 transition-colors shadow-lg">
                                    Leave Profile
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Session Feedback -->
                    @if(session('success'))
                        <div
                            class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300 shadow-sm shadow-emerald-100/50">
                            <i data-lucide="check-circle-2" size="20"></i>
                            <span class="font-bold text-sm">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div
                            class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300 shadow-sm shadow-rose-100/50">
                            <i data-lucide="alert-circle" size="20"></i>
                            <span class="font-bold text-sm">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        lucide.createIcons();

        // Auto-scroll sidebar to active item
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(() => {
                const activeLink = document.querySelector('.active-nav-link');
                const sidebarNav = document.getElementById('school-sidebar-nav');

                if (activeLink && sidebarNav) {
                    activeLink.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }, 100);
        });
    </script>
    @stack('scripts')
</body>

</html>