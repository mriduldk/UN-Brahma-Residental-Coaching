@extends('layouts.school')

@section('content')
    <div class="space-y-6" x-data="{ 
        showStatusModal: false,
        statusToUpdate: '',
        submitting: false 
    }">
        <!-- Breadcrumbs & Back -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-400">
                <a href="{{ route('school.leaves.index') }}" class="hover:text-indigo-600 transition-colors">Leave
                    Management</a>
                <i data-lucide="chevron-right" size="14"></i>
                <span class="text-slate-600">Leave Details</span>
            </div>

            <a href="{{ route('school.leaves.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-widest transition-all border border-slate-200 shadow-sm">
                <i data-lucide="arrow-left" size="16"></i>
                Back to List
            </a>
        </div>

        <!-- Main Detail Card -->
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <!-- Header -->
            <div
                class="p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-50 rounded-full opacity-30 blur-3xl">
                </div>

                <div class="relative flex items-center gap-8">
                    <!-- Premium Avatar -->
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-500 rounded-3xl blur opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="relative w-24 h-24 rounded-3xl bg-white flex items-center justify-center text-indigo-600 font-black text-2xl border border-slate-100 shadow-xl overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/50 to-transparent"></div>
                            <span class="relative z-10">{{ substr($leave->leavable->full_name ?? ($leave->leavable->student->full_name ?? 'U'), 0, 2) }}</span>
                        </div>
                    </div>

                    <div class="space-y-3">

                        <div>
                            <h1 class="text-3xl font-black text-slate-800 tracking-tight leading-none mb-3">
                                {{ $leave->leavable->full_name ?? ($leave->leavable->student->full_name) }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-2.5">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50/80 backdrop-blur-sm border border-slate-100 rounded-xl">
                                    <i data-lucide="phone" size="12" class="text-indigo-500"></i>
                                    <span class="text-[11px] font-black text-slate-600 tracking-wide">
                                        {{ str_contains($leave->leavable_type, 'Staff') ? ($leave->leavable->phone_number ?? 'N/A') : ($leave->leavable->student->phone_number ?? 'N/A') }}
                                    </span>
                                </div>
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50/80 backdrop-blur-sm border border-slate-100 rounded-xl">
                                    <i data-lucide="mail" size="12" class="text-indigo-500"></i>
                                    <span class="text-[11px] font-black text-slate-600 tracking-wide lowercase">
                                        {{ str_contains($leave->leavable_type, 'Staff') ? ($leave->leavable->email ?? 'N/A') : ($leave->leavable->student->email ?? 'N/A') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2.5 ml-1">
                                    <span class="px-3 py-1 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg shadow-md shadow-indigo-100">
                                        {{ str_contains($leave->leavable_type, 'Staff') ? 'Staff member' : 'Student' }}
                                    </span>
                                    <span class="text-slate-400 font-extrabold text-[10px] uppercase tracking-[0.2em] flex items-center gap-1.5">
                                        <i data-lucide="fingerprint" size="14" class="opacity-50"></i>
                                        ID: {{ $leave->leavable->employee_id ?? ($leave->leavable->roll_number ?? 'N/A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative flex items-center gap-3">
                    @if($leave->status === 'pending')
                        <button @click="statusToUpdate = 'approved'; showStatusModal = true"
                            class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-sm transition-all shadow-lg shadow-emerald-100 flex items-center gap-2">
                            <i data-lucide="check-circle" size="18"></i>
                            Approve
                        </button>
                        <button @click="statusToUpdate = 'rejected'; showStatusModal = true"
                            class="px-6 py-3 bg-red-50 text-red-600 hover:bg-red-100 rounded-2xl font-black text-sm transition-all flex items-center gap-2">
                            <i data-lucide="x-circle" size="18"></i>
                            Reject
                        </button>
                    @else
                        <div
                            class="px-6 py-3 {{ $leave->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }} rounded-2xl font-black text-sm uppercase tracking-widest border {{ $leave->status === 'approved' ? 'border-emerald-100' : 'border-red-100' }} flex items-center gap-2">
                            <i data-lucide="{{ $leave->status === 'approved' ? 'check-circle' : 'alert-circle' }}"
                                size="18"></i>
                            {{ ucfirst($leave->status) }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Content Grid -->
            <div class="p-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Leave Info -->
                <div class="lg:col-span-2 space-y-10">
                    <section>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">
                            {{ str_contains($leave->leavable_type, 'Staff') ? 'Professional Profile' : 'Academic Profile' }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if(str_contains($leave->leavable_type, 'Staff'))
                                <!-- Staff Details -->
                                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 shadow-sm">
                                        <i data-lucide="briefcase" size="20"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Designation</p>
                                        <p class="text-sm font-black text-slate-700">
                                            {{ $leave->leavable->designation ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 shadow-sm">
                                        <i data-lucide="building-2" size="20"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Department</p>
                                        <p class="text-sm font-black text-slate-700">{{ $leave->leavable->department ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <!-- Student Details -->
                                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 shadow-sm">
                                        <i data-lucide="hash" size="20"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Roll Number</p>
                                        <p class="text-sm font-black text-slate-700">
                                            {{ $leave->leavable->roll_number ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-slate-400 shadow-sm">
                                        <i data-lucide="layout-grid" size="20"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Class & Section
                                        </p>
                                        <p class="text-sm font-black text-slate-700">
                                            {{ $leave->leavable->schoolClass->class_name ?? 'N/A' }} -
                                            {{ $leave->leavable->section->section_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>

                    <section>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Request Description
                        </h3>
                        <div
                            class="bg-slate-50/50 p-6 rounded-3xl border border-slate-100 italic text-slate-700 leading-relaxed font-medium whitespace-pre-wrap">{{ $leave->reason }}</div>
                    </section>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <section>
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Leave Type</h3>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                    <i data-lucide="tag" size="24"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-black text-slate-800">{{ $leave->leave_type }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Category</p>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Duration</h3>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                    <i data-lucide="calendar" size="24"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-black text-slate-800">{{ $leave->total_days }}
                                        {{ Str::plural('Day', $leave->total_days) }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Leave
                                        periods</p>
                                </div>
                            </div>
                        </section>
                    </div>

                    @if($leave->status === 'rejected' && $leave->rejection_reason)
                        <section>
                            <h3 class="text-xs font-black text-red-400 uppercase tracking-[0.2em] mb-4">Rejection Remarks</h3>
                            <div
                                class="bg-red-50 p-6 rounded-3xl border border-red-100 text-red-700 leading-relaxed font-medium">
                                {{ $leave->rejection_reason }}
                            </div>
                        </section>
                    @endif
                </div>

                <!-- Timeline/Tracking -->
                <div class="space-y-8">
                    <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-100 space-y-6">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Application Timeline</h3>

                        <div class="space-y-6 relative">
                            <div class="absolute left-4 top-1 bottom-1 w-0.5 bg-slate-200"></div>

                            <!-- Applied -->
                            <div class="relative pl-10">
                                <div
                                    class="absolute left-2.5 top-0 w-3.5 h-3.5 rounded-full bg-indigo-500 ring-4 ring-white">
                                </div>
                                <p class="text-sm font-black text-slate-800 leading-none">Application Submitted</p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">
                                    {{ $leave->created_at->format('M d, Y - h:i A') }}</p>
                            </div>

                            <!-- Range Details -->
                            <div class="relative pl-10">
                                <div
                                    class="absolute left-2.5 top-0 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-4 ring-white">
                                </div>
                                <p class="text-sm font-black text-slate-800 leading-none">Leave Period Starts</p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">
                                    {{ $leave->start_date->format('M d, Y') }}</p>
                            </div>

                            <div class="relative pl-10">
                                <div
                                    class="absolute left-2.5 top-0 w-3.5 h-3.5 rounded-full bg-slate-400 ring-4 ring-white">
                                </div>
                                <p class="text-sm font-black text-slate-800 leading-none">Leave Period Ends</p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">
                                    {{ $leave->end_date->format('M d, Y') }}</p>
                            </div>

                            @if($leave->approver)
                                <div class="relative pl-10">
                                    <div
                                        class="absolute left-2.5 top-0 w-3.5 h-3.5 rounded-full {{ $leave->status === 'approved' ? 'bg-emerald-500' : 'bg-red-500' }} ring-4 ring-white">
                                    </div>
                                    <p class="text-sm font-black text-slate-800 leading-none">Processed By
                                        {{ $leave->approver->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">
                                        {{ $leave->updated_at->format('M d, Y - h:i A') }}</p>
                                    <p class="text-[10px] text-emerald-600 font-black mt-2 uppercase tracking-widest">
                                        {{ $leave->status }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('school.leaves.destroy', $leave->id) }}" method="POST"
                        onsubmit="return confirm('Permanently delete this record?')" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-6 py-4 text-red-500 hover:bg-red-50 rounded-2xl font-black text-xs uppercase tracking-widest transition-all text-center border border-transparent hover:border-red-100 flex items-center justify-center gap-2">
                            <i data-lucide="trash-2" size="16"></i>
                            Delete Leave Record
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Status Change Modal -->
        <template x-if="showStatusModal">
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div @click="!submitting ? showStatusModal = false : null"
                        class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity backdrop-blur-sm"
                        aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                        <form action="{{ route('school.leaves.update-status', $leave->id) }}" method="POST"
                            @submit="submitting = true">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" :value="statusToUpdate">

                            <div class="p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <h3 class="text-xl font-black text-slate-800 tracking-tight"
                                        x-text="statusToUpdate === 'approved' ? 'Approve Leave' : 'Reject Leave'"></h3>
                                    <button type="button" @click="showStatusModal = false"
                                        class="text-slate-400 hover:text-slate-600 transition-colors"
                                        :disabled="submitting">
                                        <i data-lucide="x" size="24"></i>
                                    </button>
                                </div>

                                <p class="text-slate-500 font-medium mb-8 leading-relaxed">
                                    You are about to <span class="font-black"
                                        :class="statusToUpdate === 'approved' ? 'text-emerald-600' : 'text-red-600'"
                                        x-text="statusToUpdate"></span>
                                    the leave request for <span
                                        class="font-black text-slate-800">{{ $leave->leavable->full_name ?? ($leave->leavable->student->first_name . ' ' . $leave->leavable->student->last_name) }}</span>.
                                    This action will be logged.
                                </p>

                                <template x-if="statusToUpdate === 'rejected'">
                                    <div class="space-y-3">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Rejection
                                            Reason</label>
                                        <textarea name="rejection_reason" required
                                            class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-red-100 outline-none transition-all h-32"
                                            placeholder="Specify why this request is being denied..."
                                            :disabled="submitting"></textarea>
                                    </div>
                                </template>
                            </div>

                            <div class="px-8 py-6 bg-slate-50 flex items-center justify-end gap-4">
                                <button type="button" @click="showStatusModal = false"
                                    class="text-sm font-black text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest"
                                    :disabled="submitting">Go Back</button>
                                <button type="submit" :disabled="submitting"
                                    :class="statusToUpdate === 'approved' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-xl shadow-emerald-100' : 'bg-red-600 hover:bg-red-700 shadow-xl shadow-red-100'"
                                    class="px-8 py-3 rounded-2xl text-sm font-black text-white transition-all flex items-center gap-3">
                                    <template x-if="submitting">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </template>
                                    <span
                                        x-text="submitting ? 'Processing...' : (statusToUpdate === 'approved' ? 'Confirm Approval' : 'Confirm Rejection')"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection