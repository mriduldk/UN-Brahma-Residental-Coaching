@extends('layouts.school')

@section('title', 'Staff Attendance')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Staff Attendance</h1>
            <p class="text-slate-500 text-sm mt-1">Mark daily attendance for all active organization staff.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('school.attendance.report.staff') }}" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                <i data-lucide="bar-chart-2" size="16"></i>
                View Report
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300 shadow-sm">
            <i data-lucide="check-circle-2" size="20"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300 shadow-sm">
            <i data-lucide="alert-circle" size="20"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Date Selector -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-end gap-6 justify-between">
        <form action="{{ route('school.attendance.staff') }}" method="GET" class="flex-1 max-w-sm">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Select Date</label>
            <div class="flex items-center gap-3">
                <input type="date" name="date" value="{{ $date }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-700 font-medium focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm cursor-pointer" onchange="this.form.submit()">
                <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl shadow-sm transition-all">Load</button>
            </div>
        </form>
        <div class="text-right">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-1">Total Active Staff</p>
            <p class="text-2xl font-black text-indigo-900">{{ $staffs->count() }}</p>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden relative">
        <form action="{{ route('school.attendance.staff.store') }}" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-base font-bold text-slate-800">Attendance Sheet</h2>
                    <p class="text-xs text-slate-500 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</p>
                </div>
                <div class="flex flex-wrap items-center justify-end gap-3 sm:gap-4 ml-auto">
                    <button type="button" onclick="document.querySelectorAll('input[value=Present]').forEach(e => e.click())" class="shrink-0 px-4 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold rounded-xl border border-emerald-200/50 shadow-sm transition-all focus:ring-4 focus:ring-emerald-50 text-sm flex items-center gap-2 group">
                        <i data-lucide="check-circle-2" size="18" class="text-emerald-500 group-hover:scale-110 transition-transform"></i>
                        Mark All Present
                    </button>
                    <button type="button" onclick="document.querySelectorAll('input[value=Holiday]').forEach(e => e.click())" class="shrink-0 px-4 py-2.5 bg-cyan-50 text-cyan-700 hover:bg-cyan-100 font-bold rounded-xl border border-cyan-200/50 shadow-sm transition-all focus:ring-4 focus:ring-cyan-50 text-sm flex items-center gap-2 group">
                        <i data-lucide="palmtree" size="18" class="text-cyan-500 group-hover:scale-110 transition-transform"></i>
                        Mark All Holiday
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-50 bg-slate-50/50">
                            <th class="px-6 py-4">Staff Member</th>
                            <th class="px-6 py-4">Department & Role</th>
                            <th class="px-6 py-4">Attendance Status</th>
                            <th class="px-6 py-4">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-50">
                        @forelse($staffs as $staff)
                        @php
                            $status = isset($attendances[$staff->id]) ? $attendances[$staff->id]->status : null;
                            $remarks = isset($attendances[$staff->id]) ? $attendances[$staff->id]->remarks : '';
                        @endphp
                        <tr class="hover:bg-indigo-50/20 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-sm uppercase shrink-0">
                                        {{ substr($staff->full_name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $staff->full_name }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $staff->employee_id ?? 'No ID' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700">{{ $staff->designation ?? '-' }}</div>
                                <div class="text-[10px] text-slate-500 font-medium uppercase tracking-wider mt-0.5">{{ $staff->department ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $staff->id }}][status]" value="Present" class="peer sr-only" {{ $status === 'Present' ? 'checked' : '' }} required>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50">
                                            Present
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $staff->id }}][status]" value="Absent" class="peer sr-only" {{ $status === 'Absent' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-600 hover:bg-slate-50">
                                            Absent
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $staff->id }}][status]" value="Half Day" class="peer sr-only" {{ $status === 'Half Day' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-600 hover:bg-slate-50">
                                            Half Day
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $staff->id }}][status]" value="Late" class="peer sr-only" {{ $status === 'Late' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-600 hover:bg-slate-50">
                                            Late
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $staff->id }}][status]" value="Leave" class="peer sr-only" {{ $status === 'Leave' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-600 hover:bg-slate-50">
                                            Leave
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $staff->id }}][status]" value="Holiday" class="peer sr-only" {{ $status === 'Holiday' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:border-cyan-600 hover:bg-slate-50">
                                            Holiday
                                        </div>
                                    </label>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="attendance[{{ $staff->id }}][remarks]" value="{{ $remarks }}" placeholder="Optional notes..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-700 focus:bg-white focus:ring-2 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center text-slate-400">
                                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                    <i data-lucide="users" size="24" class="text-slate-300"></i>
                                </div>
                                <p class="font-bold text-slate-600 text-base">No Active Staff Found</p>
                                <p class="text-xs mt-1">Please add staff members in the directory first.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($staffs->count() > 0)
            @php
                $hasAttendance = count($attendances) > 0;
                $lastModified = $hasAttendance ? $attendances->max('updated_at') : null;
            @endphp
            <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4 sticky bottom-0 z-10 backdrop-blur-md">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:block">
                    @if($hasAttendance && $lastModified)
                        <span class="flex items-center gap-1.5"><i data-lucide="clock" size="14"></i> Last updated: {{ $lastModified->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</span>
                    @else
                        Date: {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}
                    @endif
                </div>
                <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-not-allowed': isSubmitting}" class="px-8 py-3 {{ $hasAttendance ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-200/50 text-white' : 'bg-purple-600 hover:bg-purple-700 shadow-purple-200/50 text-white' }} font-bold rounded-xl shadow-lg transition-all flex items-center gap-2 w-full sm:w-auto justify-center">
                    <i data-lucide="{{ $hasAttendance ? 'refresh-cw' : 'save' }}" size="18" x-show="!isSubmitting"></i>
                    <i data-lucide="loader-2" size="18" class="animate-spin" x-show="isSubmitting" style="display: none;"></i>
                    <span x-text="isSubmitting ? 'Saving...' : '{{ $hasAttendance ? 'Update Attendance' : 'Save Attendance' }}'">{{ $hasAttendance ? 'Update Attendance' : 'Save Attendance' }}</span>
                </button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
