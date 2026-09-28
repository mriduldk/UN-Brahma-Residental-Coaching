@extends('layouts.school')

@section('title', 'School Dashboard')

@section('content')
    <div class="space-y-8">
        <x-email-verification-alert />

        @if(isset($subscription) && $subscription->status !== 'trialing' && $isPaymentDue)
            <div class="{{ $isSubscriptionBlocked ? 'bg-red-50 border-red-200 text-red-800' : 'bg-amber-50 border-amber-200 text-amber-800' }} border px-6 py-4 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm relative overflow-hidden">
                <div class="flex items-start gap-4">
                    <div class="{{ $isSubscriptionBlocked ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }} w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="alert-triangle" size="24"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-lg {{ $isSubscriptionBlocked ? 'text-red-900' : 'text-amber-900' }}">
                            {{ $isSubscriptionBlocked ? 'Subscription Expired' : 'Payment Due' }}
                        </h4>
                        <p class="text-sm mt-1 {{ $isSubscriptionBlocked ? 'text-red-700' : 'text-amber-700' }}">
                            @if($isSubscriptionBlocked)
                                Your subscription has expired. Please clear your dues of ₹{{ number_format($amountDue, 2) }} to maintain uninterrupted access.
                            @else
                                You have a pending payment of ₹{{ number_format($amountDue, 2) }} for your subscription.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('administration.subscription.billing') }}" class="inline-flex items-center gap-2 px-5 py-2.5 {{ $isSubscriptionBlocked ? 'bg-red-600 hover:bg-red-700 text-white shadow-red-600/30' : 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/30' }} font-bold text-sm rounded-xl transition-all shadow-lg">
                        <i data-lucide="credit-card" size="16"></i>
                        Pay Now
                    </a>
                </div>
            </div>
        @endif
        <div class="mb-8 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-indigo-950 tracking-tight">Academic Overview</h1>
                <p class="text-slate-500 text-sm mt-1">Real-time metrics for your school's daily operations.</p>
            </div>
            <div class="flex items-center gap-3">
                @if($stats['active_year'])
                    <div class="px-5 py-2.5 bg-white border border-indigo-100 rounded-2xl shadow-sm flex items-center gap-3">
                        <span
                            class="w-3 h-3 bg-green-500 rounded-full shadow-[0_0_12px_rgba(34,197,94,0.6)] animate-pulse"></span>
                        <span
                            class="text-sm font-bold text-slate-700 uppercase tracking-widest">{{ $stats['active_year']->name }}</span>
                    </div>
                @endif
            </div>
        </div>

        @php
            $isSessionDone = $setupGuide->has_academic_session;
            $isClassDone = $setupGuide->has_classes;
            $isStudentDone = $setupGuide->has_students;
            $setupComplete = $isSessionDone && $isClassDone && $isStudentDone;

            $stepsCount = 3;
            $completedCount = ($isSessionDone ? 1 : 0) + ($isClassDone ? 1 : 0) + ($isStudentDone ? 1 : 0);
            $progressPercent = round(($completedCount / $stepsCount) * 100);
        @endphp

        @if(!$setupComplete)
            <!-- School Setup Journey -->
            <div
                class="bg-indigo-950 rounded-[2.5rem] p-8 border border-white/5 shadow-2xl shadow-indigo-900/40 relative overflow-hidden mb-8 group">
                <!-- Decorative Background Elements -->
                <div
                    class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-500 rounded-full opacity-10 blur-3xl transition-transform group-hover:scale-110 duration-700">
                </div>
                <div
                    class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-purple-500 rounded-full opacity-10 blur-3xl transition-transform group-hover:scale-110 duration-700">
                </div>
                <div
                    class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03] pointer-events-none">
                </div>

                <div class="relative">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-10">
                        <div>
                            <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center border border-white/10 backdrop-blur-md">
                                    <i data-lucide="sparkles" class="w-6 h-6 text-indigo-300"></i>
                                </div>
                                School Launch Journey
                            </h2>
                            <p class="text-indigo-200/60 text-sm font-medium mt-2">Follow the path to fully activate your
                                digital campus.</p>
                        </div>

                        <div
                            class="flex flex-col lg:items-end gap-3 bg-white/5 p-4 rounded-3xl border border-white/5 backdrop-blur-sm">
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.2em]">Platform
                                    Readiness</span>
                                <span class="text-lg font-black text-white">{{ $progressPercent }}%</span>
                            </div>
                            <div
                                class="w-48 lg:w-64 h-2.5 bg-indigo-950 rounded-full overflow-hidden border border-white/10 relative">
                                <div class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-500 transition-all duration-1000 ease-out shadow-[0_0_12px_rgba(52,211,153,0.3)]"
                                    style="width: {{ $progressPercent }}%"></div>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent w-20 animate-[shimmer_2s_infinite]">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                        <!-- Progress Line Connector (Desktop) -->
                        <div class="hidden md:block absolute top-[3rem] left-[15%] right-[15%] h-0.5 bg-white/5 -z-0">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-emerald-500/50 transition-all duration-1000"
                                style="width: {{ max(0, ($completedCount - 1) * 50) }}%"></div>
                        </div>

                        <!-- Step 1: Academic Session -->
                        <div class="relative z-10 flex flex-col group/step">
                            <div
                                class="flex flex-col items-center text-center p-8 rounded-[2rem] transition-all duration-500 border {{ $isSessionDone ? 'bg-white/5 border-white/5' : 'bg-white/10 border-white/20 shadow-xl' }} backdrop-blur-sm">
                                <div
                                    class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5 transition-all duration-500 {{ $isSessionDone ? 'bg-emerald-500 text-white shadow-[0_0_25px_rgba(16,185,129,0.3)]' : 'bg-indigo-600 text-white shadow-[0_0_25px_rgba(79,70,229,0.4)] animate-pulse' }}">
                                    <i data-lucide="{{ $isSessionDone ? 'check' : 'calendar-plus' }}" class="w-8 h-8"></i>
                                </div>
                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2">Step
                                    01</span>
                                <h3 class="text-sm font-black text-white mb-2 uppercase tracking-tight">Academic Session</h3>
                                <p class="text-xs text-indigo-200/40 mb-6 leading-relaxed">Establish the active year for all
                                    operations.</p>

                                @if(!$isSessionDone)
                                    <a href="{{ route('school.sessions.index') }}"
                                        class="w-full py-3 bg-white text-indigo-900 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all hover:bg-indigo-50 hover:scale-[1.02] shadow-xl">
                                        Setup Session
                                    </a>
                                @else
                                    <div
                                        class="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                        <i data-lucide="check" class="w-3 h-3 text-emerald-400"></i>
                                        <span
                                            class="text-[10px] text-emerald-400 font-black uppercase tracking-widest">Active</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Step 2: Classes & Sections -->
                        <div
                            class="relative z-10 flex flex-col group/step {{ !$isSessionDone ? 'opacity-50 grayscale-[0.5]' : '' }}">
                            <div
                                class="flex flex-col items-center text-center p-8 rounded-[2rem] transition-all duration-500 border {{ $isClassDone ? 'bg-white/5 border-white/5' : ($isSessionDone ? 'bg-white/10 border-white/20 shadow-xl' : 'bg-transparent border-white/5') }} backdrop-blur-sm">
                                <div
                                    class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5 transition-all duration-500 {{ $isClassDone ? 'bg-emerald-500 text-white shadow-[0_0_25px_rgba(16,185,129,0.3)]' : ($isSessionDone ? 'bg-indigo-600 text-white shadow-[0_0_25px_rgba(79,70,229,0.4)] animate-pulse' : 'bg-white/5 text-indigo-300 border border-white/10') }}">
                                    <i data-lucide="{{ $isClassDone ? 'check' : 'layers' }}" class="w-8 h-8"></i>
                                </div>
                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2">Step
                                    02</span>
                                <h3 class="text-sm font-black text-white mb-2 uppercase tracking-tight">Classes & Sections</h3>
                                <p class="text-xs text-indigo-200/40 mb-6 leading-relaxed">Define the academic structure and
                                    grades.</p>

                                @if(!$isClassDone)
                                    @if($isSessionDone)
                                        <a href="{{ route('school.classes.index') }}"
                                            class="w-full py-3 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all hover:bg-indigo-500 hover:scale-[1.02] shadow-xl shadow-indigo-600/20">
                                            Configure Now
                                        </a>
                                    @else
                                        <div class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/5 rounded-xl">
                                            <i data-lucide="lock" class="w-3 h-3 text-indigo-400"></i>
                                            <span class="text-[10px] text-indigo-400 font-bold uppercase">Locked</span>
                                        </div>
                                    @endif
                                @else
                                    <div
                                        class="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                        <i data-lucide="check" class="w-3 h-3 text-emerald-400"></i>
                                        <span
                                            class="text-[10px] text-emerald-400 font-black uppercase tracking-widest">Defined</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Step 3: Student Enrollment -->
                        <div
                            class="relative z-10 flex flex-col group/step {{ !$isClassDone ? 'opacity-50 grayscale-[0.5]' : '' }}">
                            <div
                                class="flex flex-col items-center text-center p-8 rounded-[2rem] transition-all duration-500 border {{ $isStudentDone ? 'bg-white/5 border-white/5' : ($isClassDone ? 'bg-white/10 border-white/20 shadow-xl' : 'bg-transparent border-white/5') }} backdrop-blur-sm">
                                <div
                                    class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5 transition-all duration-500 {{ $isStudentDone ? 'bg-emerald-500 text-white shadow-[0_0_25px_rgba(16,185,129,0.3)]' : ($isClassDone ? 'bg-indigo-600 text-white shadow-[0_0_25px_rgba(79,70,229,0.4)] animate-pulse' : 'bg-white/5 text-indigo-300 border border-white/10') }}">
                                    <i data-lucide="{{ $isStudentDone ? 'check' : 'user-plus' }}" class="w-8 h-8"></i>
                                </div>
                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-2">Step
                                    03</span>
                                <h3 class="text-sm font-black text-white mb-2 uppercase tracking-tight">Student Enrollment</h3>
                                <p class="text-xs text-indigo-200/40 mb-6 leading-relaxed">Map your students to their class
                                    roles.</p>

                                @if(!$isStudentDone)
                                    @if($isClassDone)
                                        <a href="{{ route('school.enrollment.index') }}"
                                            class="w-full py-3 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all hover:bg-indigo-500 hover:scale-[1.02] shadow-xl shadow-indigo-600/20">
                                            Enroll Students
                                        </a>
                                    @else
                                        <div class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/5 rounded-xl">
                                            <i data-lucide="lock" class="w-3 h-3 text-indigo-400"></i>
                                            <span class="text-[10px] text-indigo-400 font-bold uppercase">Locked</span>
                                        </div>
                                    @endif
                                @else
                                    <div
                                        class="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                        <i data-lucide="check" class="w-3 h-3 text-emerald-400"></i>
                                        <span
                                            class="text-[10px] text-emerald-400 font-black uppercase tracking-widest">Enrolled</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Style for Shimmer Animation -->
            <style>
                @keyframes shimmer {
                    0% {
                        transform: translateX(-100%);
                    }

                    100% {
                        transform: translateX(300%);
                    }
                }
            </style>
        @endif


        <!-- Academic Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Students -->
            <div
                class="relative group overflow-hidden bg-white p-6 rounded-[2rem] shadow-sm border border-indigo-50 transition-all hover:shadow-xl hover:-translate-y-1">
                <div
                    class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
                </div>
                <div class="relative">
                    <div
                        class="w-12 h-12 bg-indigo-600/10 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="users" size="24"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Enrolled Students</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-3xl font-black text-indigo-950">{{ number_format($stats['total_students']) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Total Staff -->
            <div
                class="relative group overflow-hidden bg-white p-6 rounded-[2rem] shadow-sm border border-indigo-50 transition-all hover:shadow-xl hover:-translate-y-1">
                <div
                    class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
                </div>
                <div class="relative">
                    <div
                        class="w-12 h-12 bg-purple-600/10 text-purple-600 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="user-round-cog" size="24"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Staff Members</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-3xl font-black text-purple-900">{{ number_format($stats['total_staff']) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Classes -->
            <div
                class="relative group overflow-hidden bg-white p-6 rounded-[2rem] shadow-sm border border-indigo-50 transition-all hover:shadow-xl hover:-translate-y-1">
                <div
                    class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-sky-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
                </div>
                <div class="relative">
                    <div class="w-12 h-12 bg-sky-600/10 text-sky-600 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="book-copy" size="24"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Classes</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-3xl font-black text-sky-900">{{ number_format($stats['active_classes']) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Attendance Today -->
            <div
                class="relative group overflow-hidden bg-white p-6 rounded-[2rem] shadow-sm border border-indigo-50 transition-all hover:shadow-xl hover:-translate-y-1">
                <div
                    class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
                </div>
                <div class="relative">
                    <div
                        class="w-12 h-12 bg-emerald-600/10 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                        <i data-lucide="calendar-days" size="24"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Today's Attendance</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <h3 class="text-3xl font-black text-emerald-600">{{ $stats['attendance_percentage'] }}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Attendance Breakdown & Class Strength -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Attendance Breakdown -->
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <h3 class="text-lg font-black text-indigo-900 mb-6 flex items-center gap-2">
                        <i data-lucide="bar-chart-3" size="20"></i> Today's Attendance Breakdown
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="p-4 bg-green-50 rounded-3xl border border-green-100">
                            <span
                                class="text-[10px] font-black text-green-700 uppercase tracking-widest block mb-1">Present</span>
                            <span
                                class="text-2xl font-black text-green-900">{{ $stats['student_attendance_summary']['Present'] ?? 0 }}</span>
                        </div>
                        <div class="p-4 bg-red-50 rounded-3xl border border-red-100">
                            <span
                                class="text-[10px] font-black text-red-700 uppercase tracking-widest block mb-1">Absent</span>
                            <span
                                class="text-2xl font-black text-red-900">{{ $stats['student_attendance_summary']['Absent'] ?? 0 }}</span>
                        </div>
                        <div class="p-4 bg-amber-50 rounded-3xl border border-amber-100">
                            <span
                                class="text-[10px] font-black text-amber-700 uppercase tracking-widest block mb-1">Late</span>
                            <span
                                class="text-2xl font-black text-amber-900">{{ $stats['student_attendance_summary']['Late'] ?? 0 }}</span>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-3xl border border-blue-100">
                            <span
                                class="text-[10px] font-black text-blue-700 uppercase tracking-widest block mb-1">Holiday/Leave</span>
                            <span
                                class="text-2xl font-black text-blue-900">{{ ($stats['student_attendance_summary']['Holiday'] ?? 0) + ($stats['student_attendance_summary']['Leave'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Class-wise Strength -->
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-black text-indigo-900 flex items-center gap-2">
                            <i data-lucide="layers" size="20"></i> Student Strength by Class
                        </h3>
                        <a href="{{ route('school.classes.index') }}"
                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">View All
                            Classes</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead
                                class="text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-50">
                                <tr>
                                    <th class="px-4 py-3">Class Name</th>
                                    <th class="px-4 py-3">Sections</th>
                                    <th class="px-4 py-3 text-right">Students</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($stats['class_strength'] as $class)
                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 py-4 font-bold text-slate-800">{{ $class->name }}</td>
                                        <td class="px-4 py-4 text-slate-500 font-medium">{{ $class->sections_count ?? 0 }}
                                            Sections</td>
                                        <td class="px-4 py-4 text-right font-black text-indigo-600">
                                            {{ number_format($class->student_count) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-slate-400 italic">No classes defined
                                            yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Side Actions & Recent Activity -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="bg-indigo-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-900/20">
                    <h3 class="font-black text-lg mb-6 flex items-center gap-2">
                        <i data-lucide="zap" class="text-amber-400" size="20"></i> Quick Actions
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('school.enrollment.mapped') }}"
                            class="flex flex-col items-center justify-center p-4 bg-white/10 hover:bg-white/20 rounded-3xl transition-all border border-white/10 group text-center">
                            <i data-lucide="user-plus" size="20"
                                class="mb-2 text-indigo-300 group-hover:scale-110 transition-transform"></i>
                            <span class="text-[10px] font-bold uppercase tracking-tight">Map Student</span>
                        </a>
                        <a href="{{ route('school.attendance.report.percentage') }}"
                            class="flex flex-col items-center justify-center p-4 bg-white/10 hover:bg-white/20 rounded-3xl transition-all border border-white/10 group text-center">
                            <i data-lucide="clipboard-check" size="20"
                                class="mb-2 text-indigo-300 group-hover:scale-110 transition-transform"></i>
                            <span class="text-[10px] font-bold uppercase tracking-tight">Percentage</span>
                        </a>
                        <a href="{{ route('school.staff.index') }}"
                            class="flex flex-col items-center justify-center p-4 bg-white/10 hover:bg-white/20 rounded-3xl transition-all border border-white/10 group text-center text-center">
                            <i data-lucide="user-cog" size="20"
                                class="mb-2 text-indigo-300 group-hover:scale-110 transition-transform"></i>
                            <span class="text-[10px] font-bold uppercase tracking-tight">Staff Mgmt</span>
                        </a>
                        <a href="{{ route('school.sessions.index') }}"
                            class="flex flex-col items-center justify-center p-4 bg-white/10 hover:bg-white/20 rounded-3xl transition-all border border-white/10 group text-center">
                            <i data-lucide="calendar" size="20"
                                class="mb-2 text-indigo-300 group-hover:scale-110 transition-transform"></i>
                            <span class="text-[10px] font-bold uppercase tracking-tight">Sessions</span>
                        </a>
                        <a href="{{ route('school.fees.index') }}"
                            class="flex flex-col items-center justify-center p-4 bg-amber-400 hover:bg-amber-500 rounded-3xl transition-all border border-amber-300 group text-center col-span-2">
                            <i data-lucide="banknote" size="20"
                                class="mb-1 text-amber-950 group-hover:scale-110 transition-transform"></i>
                            <span class="text-[10px] font-black text-amber-950 uppercase tracking-tight">Collect Fees</span>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity Exceptions -->
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden relative">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-black text-indigo-900 flex items-center gap-2">
                            <i data-lucide="bell-ring" class="text-indigo-500" size="20"></i> Attendance Alerts
                        </h3>
                        @if(count($stats['today_exceptions']) > 0)
                            <div class="flex gap-1 animate-in fade-in slide-in-from-right-4 duration-700">
                                @foreach(['Absent' => 'bg-red-500', 'Late' => 'bg-amber-500', 'Leave' => 'bg-blue-500', 'Half Day' => 'bg-orange-500'] as $status => $color)
                                    @if(isset($stats['today_exceptions'][$status]))
                                        <span
                                            class="flex items-center justify-center w-5 h-5 {{ $color }} text-white text-[8px] font-black rounded-full shadow-sm"
                                            title="{{ $status }} Today">
                                            {{ $stats['today_exceptions'][$status] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="relative">
                        <!-- Timeline Line -->
                        @if(count($stats['recent_activity']) > 0)
                            <div class="absolute left-[19px] top-2 bottom-2 w-0.5 bg-slate-100"></div>
                        @endif

                        <div class="space-y-8 relative">
                            @forelse($stats['recent_activity'] as $activity)
                                <div class="flex items-start gap-4 relative group">
                                    <!-- Timeline Dot/Icon -->
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center flex-shrink-0 z-10 transition-transform group-hover:scale-110
                                                        @if($activity->status === 'Leave') bg-blue-50 text-blue-600 border border-blue-100
                                                        @elseif($activity->status === 'Late') bg-amber-50 text-amber-600 border border-amber-100
                                                        @elseif($activity->status === 'Half Day') bg-orange-50 text-orange-600 border border-orange-100
                                                        @else bg-red-50 text-red-600 border border-red-100 @endif">
                                        <i data-lucide="@if($activity->status === 'Leave') plane-takeoff @elseif($activity->status === 'Late') clock @elseif($activity->status === 'Half Day') timer @else x @endif"
                                            size="18"></i>
                                    </div>

                                    <div class="flex-1 min-w-0 pt-0.5">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-black text-slate-800 truncate">
                                                {{ $activity->student->full_name }}
                                            </p>
                                            <span class="text-[9px] font-black whitespace-nowrap px-2 py-0.5 rounded-full
                                                                @if($activity->status === 'Leave') bg-blue-100 text-blue-700
                                                                @elseif($activity->status === 'Late') bg-amber-100 text-amber-700
                                                                @elseif($activity->status === 'Half Day') bg-orange-100 text-orange-700
                                                                @else bg-red-100 text-red-700 @endif">
                                                {{ $activity->status }}
                                            </span>
                                        </div>
                                        <p
                                            class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5 flex items-center gap-1">
                                            {{ $activity->schoolClass?->name ?? 'N/A' }}@if($activity->section)-{{ $activity->section->name }}@endif
                                            <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                            {{ $activity->date->isToday() ? 'Today' : $activity->date->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10">
                                    <div
                                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i data-lucide="check-circle-2" class="text-slate-300" size="30"></i>
                                    </div>
                                    <p class="text-sm text-slate-400 font-medium italic px-4">All systems normal. No recent
                                        attendance exceptions recorded.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if(count($stats['recent_activity']) > 0)
                        <div class="mt-8 pt-6 border-t border-slate-50">
                            <a href="{{ route('school.attendance.report.students') }}"
                                class="block text-center text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest transition-colors">
                                View Detailed Report
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick Help -->
                <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-indigo-600 radius-full blur-3xl opacity-20">
                    </div>
                    <div class="relative z-10 text-center">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="help-circle" size="24" class="text-indigo-400"></i>
                        </div>
                        <h4 class="font-bold mb-2">Need Guidance?</h4>
                        <p class="text-xs text-slate-400 mb-6">Manage your educational ecosystem efficiently with our guide.
                        </p>
                        <button
                            class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 rounded-3xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-600/20">
                            View Tutorial
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection