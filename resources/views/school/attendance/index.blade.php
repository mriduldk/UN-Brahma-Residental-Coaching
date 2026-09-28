@extends('layouts.school')

@section('title', 'Attendance Management')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Attendance Management</h1>
            <p class="text-slate-500 text-sm mt-1">Track and manage daily attendance for students and staff.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
        <!-- Student Attendance Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden group hover:border-indigo-300 transition-all">
            <div class="p-8">
                <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                    <i data-lucide="users" size="32"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight mb-2">Student Attendance</h2>
                <p class="text-slate-500 mb-8 leading-relaxed">
                    Mark daily attendance for students by selecting an active academic session, class, and section.
                </p>
                <a href="{{ route('school.attendance.students') }}" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200/50 transition-all flex items-center justify-center gap-2">
                    Manage Students <i data-lucide="arrow-right" size="18"></i>
                </a>
            </div>
            <div class="bg-slate-50/50 px-8 py-4 border-t border-slate-100 flex items-center gap-3 text-sm text-slate-500 font-medium">
                <i data-lucide="info" size="16" class="text-indigo-400"></i>
                Requires setting up classes & active sessions first.
            </div>
        </div>

        <!-- Staff Attendance Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden group hover:border-purple-300 transition-all">
            <div class="p-8">
                <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 mb-6 group-hover:scale-110 transition-transform">
                    <i data-lucide="user-check" size="32"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight mb-2">Staff Attendance</h2>
                <p class="text-slate-500 mb-8 leading-relaxed">
                    Track the daily attendance of all teachers and administrative staff within your organization.
                </p>
                <a href="{{ route('school.attendance.staff') }}" class="w-full py-3.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-lg shadow-purple-200/50 transition-all flex items-center justify-center gap-2">
                    Manage Staff <i data-lucide="arrow-right" size="18"></i>
                </a>
            </div>
            <div class="bg-slate-50/50 px-8 py-4 border-t border-slate-100 flex items-center gap-3 text-sm text-slate-500 font-medium">
                <i data-lucide="info" size="16" class="text-purple-400"></i>
                Mark present, absent, half-days, or on leave.
            </div>
        </div>
    </div>
</div>
@endsection
