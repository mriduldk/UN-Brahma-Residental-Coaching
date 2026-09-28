@extends('layouts.school')

@section('title', 'Add New Staff')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Add New Staff</h1>
            <p class="text-slate-500 text-sm mt-1">Register a new teacher or administrative staff member.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('school.staff.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                <i data-lucide="arrow-left" size="18"></i>
                Back to Directory
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <i data-lucide="alert-circle" size="20"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-8">
        <form action="{{ route('school.staff.store') }}" method="POST" class="space-y-6" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Personal Details Section -->
                <div class="lg:col-span-3 pb-2 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Personal Details</h2>
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                        placeholder="e.g. Jane Doe">
                    @error('full_name') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone_number" value="{{ old('phone_number') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                        placeholder="e.g. 9876543210">
                    @error('phone_number') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                        placeholder="e.g. jane@example.com">
                    @error('email') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Gender</label>
                    <div class="relative group">
                        <select name="gender" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                            <i data-lucide="chevron-down" size="16"></i>
                        </div>
                    </div>
                </div>

                <!-- Employment Section -->
                <div class="lg:col-span-3 pt-4 pb-2 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Employment Details</h2>
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Employee ID</label>
                    <input type="text" name="employee_id" value="{{ old('employee_id') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                        placeholder="e.g. EMP-001">
                    <p class="text-[10px] text-slate-400 mt-1 font-medium tracking-wide">Must be unique within the organization.</p>
                    @error('employee_id') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                        placeholder="e.g. Senior Teacher">
                    @error('designation') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Department</label>
                    <input type="text" name="department" value="{{ old('department') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                        placeholder="e.g. Mathematics">
                    @error('department') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Joining Date</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm">
                    @error('joining_date') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-3 flex items-center mt-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-50 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 shadow-sm border border-slate-300 peer-checked:border-indigo-700"></div>
                        <span class="ml-3 text-sm font-bold text-slate-700">Active Staff Member</span>
                    </label>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-not-allowed': isSubmitting}" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200/50 transition-all flex items-center gap-2">
                    <i data-lucide="save" size="18" x-show="!isSubmitting"></i>
                    <i data-lucide="loader-2" size="18" class="animate-spin" x-show="isSubmitting" style="display: none;"></i>
                    <span x-text="isSubmitting ? 'Saving...' : 'Save Staff Profile'">Save Staff Profile</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
