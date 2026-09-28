@extends('layouts.school')

@section('content')
<div class="max-w-4xl mx-auto" x-data="{ userType: 'staff', selectedId: '', loading: false }">
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-2 text-sm font-bold text-slate-400 mb-6">
        <a href="{{ route('school.leaves.index') }}" class="hover:text-indigo-600 transition-colors">Leave Management</a>
        <i data-lucide="chevron-right" size="14"></i>
        <span class="text-slate-600">Add New Record</span>
    </div>

    <!-- Header Area -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm mb-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i data-lucide="calendar-plus" size="28"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Add Leave Record</h1>
                <p class="text-slate-500 text-sm">Create a new leave entry for a staff member or student.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('school.leaves.store') }}" method="POST" @submit="loading = true" class="space-y-6">
        @csrf
        
        <!-- Type Selection -->
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- User Type -->
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-slate-700 tracking-wide">Applying For</label>
                    <div class="flex p-1 bg-slate-100 rounded-xl">
                        <button type="button" @click="userType = 'staff'; selectedId = ''" 
                                :class="userType === 'staff' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                class="flex-1 py-2 text-sm font-bold rounded-lg transition-all"
                                :disabled="loading">
                            Staff Member
                        </button>
                        <button type="button" @click="userType = 'student'; selectedId = ''" 
                                :class="userType === 'student' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                                class="flex-1 py-2 text-sm font-bold rounded-lg transition-all"
                                :disabled="loading">
                            Student
                        </button>
                    </div>
                    <input type="hidden" name="leavable_type" :value="userType">
                </div>

                <!-- User Selection -->
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-slate-700 tracking-wide" x-text="userType === 'staff' ? 'Select Staff' : 'Select Student'"></label>
                    <div class="relative">
                        <select name="leavable_id" x-model="selectedId" required class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" :disabled="loading">
                            <option value="">Choose an option...</option>
                            
                            <!-- Staff List -->
                            <template x-if="userType === 'staff'">
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->full_name }} ({{ $staff->employee_id ?? 'No ID' }})</option>
                                @endforeach
                            </template>

                            <!-- Student List -->
                            <template x-if="userType === 'student'">
                                @foreach($students as $enrollment)
                                    <option value="{{ $enrollment->id }}">{{ $enrollment->student->first_name }} {{ $enrollment->student->last_name }} ({{ $enrollment->schoolClass->class_name }} - {{ $enrollment->section->section_name ?? 'N/A' }})</option>
                                @endforeach
                            </template>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <i data-lucide="chevron-down" size="18"></i>
                        </div>
                    </div>
                </div>

                <!-- Leave Type -->
                <div class="space-y-3">
                    <label class="block text-sm font-bold text-slate-700 tracking-wide">Leave Category</label>
                    <div class="relative">
                        <select name="leave_type" required class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" :disabled="loading">
                            <option value="Casual">Casual Leave</option>
                            <option value="Sick">Sick Leave</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Annual">Annual / Vacation</option>
                            <option value="Maternity">Maternity Leave</option>
                            <option value="Paternity">Paternity Leave</option>
                            <option value="Others">Others</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <i data-lucide="chevron-down" size="18"></i>
                        </div>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <label class="block text-sm font-bold text-slate-700 tracking-wide">From Date</label>
                        <input type="date" name="start_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" :readonly="loading">
                    </div>
                    <div class="space-y-3">
                        <label class="block text-sm font-bold text-slate-700 tracking-wide">To Date</label>
                        <input type="date" name="end_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" :readonly="loading">
                    </div>
                </div>
            </div>

            <!-- Reason -->
            <div class="space-y-3">
                <label class="block text-sm font-bold text-slate-700 tracking-wide">Reason for Leave</label>
                <textarea name="reason" required rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" placeholder="Briefly explain the reason for this leave request..." :readonly="loading"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-4">
                <a href="{{ route('school.leaves.index') }}" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors" x-show="!loading">Cancel</a>
                <button type="submit" 
                        class="flex items-center gap-2 px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-100 disabled:opacity-70 disabled:cursor-not-allowed"
                        :disabled="loading">
                    <template x-if="!loading">
                        <div class="flex items-center gap-2">
                            <i data-lucide="save" size="18"></i>
                            <span>Save Leave Record</span>
                        </div>
                    </template>
                    <template x-if="loading">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Saving...</span>
                        </div>
                    </template>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
