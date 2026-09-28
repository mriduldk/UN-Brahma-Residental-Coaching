@extends('layouts.student')

@section('title', 'Request Leave')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest">
        <a href="{{ route('student.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
        <i data-lucide="chevron-right" size="14"></i>
        <a href="{{ route('school.leaves.personal.index') }}" class="hover:text-indigo-600 transition-colors">My Leaves</a>
        <i data-lucide="chevron-right" size="14"></i>
        <span class="text-indigo-600">Request Leave</span>
    </nav>

    <!-- Form Card -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden" x-data="{ loading: false }">
        <div class="p-8 border-b border-slate-50 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-indigo-50 rounded-full opacity-40 blur-2xl"></div>
            <div class="relative flex items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i data-lucide="send" size="28"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-indigo-950 tracking-tight">Leave Application</h1>
                    <p class="text-slate-500 font-medium text-sm">Submit your leave request for administrative approval</p>
                </div>
            </div>
        </div>

        <form action="{{ route('school.leaves.personal.store') }}" method="POST" @submit="loading = true" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Dates -->
                <div class="space-y-6">
                    <div>
                        <label for="start_date" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" required min="{{ date('Y-m-d') }}"
                               class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 transition-all">
                        @error('start_date') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">End Date</label>
                        <input type="date" name="end_date" id="end_date" required min="{{ date('Y-m-d') }}"
                               class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 transition-all">
                        <p class="text-[10px] text-slate-400 font-bold italic mt-2 ml-1">For single-day leave, use the same date as Start Date.</p>
                        @error('end_date') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Reason & Type -->
                <div class="space-y-6">
                    <div>
                        <label for="leave_type" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Leave Type</label>
                        <select name="leave_type" id="leave_type" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 transition-all">
                            <option value="Sick">Sick Leave</option>
                            <option value="Casual">Casual Leave</option>
                            <option value="Emergency">Emergency</option>
                            <option value="Annual">Annual / Vacation</option>
                            <option value="Maternity">Maternity Leave</option>
                            <option value="Paternity">Paternity Leave</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <div>
                        <label for="reason" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Reason for Leave</label>
                        <textarea name="reason" id="reason" rows="4" required placeholder="Explain why you need this leave..."
                                  class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 transition-all"></textarea>
                        @error('reason') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-12 pt-8 border-t border-slate-50 flex items-center justify-between">
                <a href="{{ route('school.leaves.personal.index') }}" class="text-xs font-black text-slate-400 hover:text-red-500 uppercase tracking-widest transition-colors flex items-center gap-2">
                    <i data-lucide="x-circle" size="18"></i> Cancel
                </a>
                
                <button type="submit" :disabled="loading"
                        class="flex items-center gap-3 px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black transition-all shadow-xl shadow-indigo-100 disabled:opacity-50 group">
                    <template x-if="!loading">
                        <span class="flex items-center gap-3">
                            Submit Request <i data-lucide="chevron-right" size="20" class="group-hover:translate-x-1 transition-transform"></i>
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Processing...
                        </span>
                    </template>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
