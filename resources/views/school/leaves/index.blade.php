@extends('layouts.school')

@section('content')
    <div class="space-y-6" x-data="{ 
                                    showAddModal: false, 
                                    showStatusModal: false,
                                    selectedLeave: null,
                                    statusToUpdate: ''
                                }">
        <!-- Header Area -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Leave Management</h1>
                <p class="text-slate-500 mt-1 text-sm">Track and manage staff and student leave applications.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('school.leaves.create') }}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200">
                    <i data-lucide="plus" size="18"></i>
                    Add Leave Record
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                    <i data-lucide="clock-3" size="24"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Pending Requests</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $leaves->where('status', 'pending')->count() }}</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i data-lucide="check-circle" size="24"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Approved This Month</p>
                    <p class="text-2xl font-bold text-slate-800">
                        {{ $leaves->where('status', 'approved')->where('start_date', '>=', now()->startOfMonth())->count() }}
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i data-lucide="users" size="24"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total On Leave Today</p>
                    <p class="text-2xl font-bold text-slate-800">
                        {{ $leaves->where('status', 'approved')->where('start_date', '<=', now()->toDateString())->where('end_date', '>=', now()->toDateString())->count() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters & Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <form action="{{ route('school.leaves.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                    <select name="status"
                        class="bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>

                    <select name="applicant_type"
                        class="bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <option value="">All Applicants</option>
                        <option value="student" {{ request('applicant_type') == 'student' ? 'selected' : '' }}>Student
                        </option>
                        <option value="staff" {{ request('applicant_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>

                    <select name="type"
                        class="bg-slate-50 border border-slate-200 text-slate-700 text-sm rounded-xl px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        <option value="">All Leave Types</option>
                        <option value="Sick" {{ request('type') == 'Sick' ? 'selected' : '' }}>Sick</option>
                        <option value="Casual" {{ request('type') == 'Casual' ? 'selected' : '' }}>Casual</option>
                        <option value="Emergency" {{ request('type') == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                        <option value="Annual" {{ request('type') == 'Annual' ? 'selected' : '' }}>Annual</option>
                        <option value="Maternity" {{ request('type') == 'Maternity' ? 'selected' : '' }}>Maternity</option>
                        <option value="Paternity" {{ request('type') == 'Paternity' ? 'selected' : '' }}>Paternity</option>
                        <option value="Others" {{ request('type') == 'Others' ? 'selected' : '' }}>Others</option>
                    </select>

                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold text-sm transition-colors">
                        <i data-lucide="filter" size="16"></i>
                        Apply Filters
                    </button>
                    @if(request()->anyFilled(['status', 'type', 'applicant_type']))
                        <a href="{{ route('school.leaves.index') }}"
                            class="text-sm text-indigo-600 font-bold hover:underline">Clear</a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Applicant</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Type</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Leave Type</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Duration</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($leaves as $leave)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-sm uppercase shadow-sm">
                                            {{ substr($leave->leavable->full_name ?? ($leave->leavable->student->first_name ?? 'U'), 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="flex flex-col gap-0.5 mb-1.5">
                                                <div
                                                    class="flex items-center gap-1.5 text-[10px] text-slate-500 font-bold tracking-wide">
                                                    <i data-lucide="phone" class="w-3 h-3 opacity-60 text-indigo-400"></i>
                                                    <span class="student-phone">
                                                        {{ str_contains($leave->leavable_type, 'Staff') ? ($leave->leavable->phone_number ?? 'N/A') : ($leave->leavable->student->phone_number ?? 'N/A') }}
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center gap-1.5 text-[10px] text-slate-500 font-bold tracking-wide">
                                                    <i data-lucide="mail" class="w-3 h-3 opacity-60 text-indigo-400"></i>
                                                    <span class="student-email">
                                                        {{ str_contains($leave->leavable_type, 'Staff') ? ($leave->leavable->email ?? 'N/A') : ($leave->leavable->student->email ?? 'N/A') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="text-sm font-black text-slate-800 tracking-tight leading-tight">
                                                {{ $leave->leavable->full_name ?? ($leave->leavable->student->full_name) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 bg-slate-100 text-slate-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-slate-200 flex items-center w-fit gap-1.5">
                                        <i data-lucide="{{ str_contains($leave->leavable_type, 'Staff') ? 'user-cog' : 'graduation-cap' }}"
                                            size="10" class="opacity-50"></i>
                                        {{ str_contains($leave->leavable_type, 'Staff') ? 'Staff' : 'Student' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider rounded-full">
                                        {{ $leave->leave_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-slate-700 font-medium">
                                        {{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $leave->total_days }} {{ Str::plural('Day', $leave->total_days) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($leave->status === 'pending')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-wider rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Pending
                                        </span>
                                    @elseif($leave->status === 'approved')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Approved
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-[10px] font-bold uppercase tracking-wider rounded-full"
                                            title="{{ $leave->rejection_reason }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('school.leaves.show', $leave->id) }}"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="View Details">
                                            <i data-lucide="eye" size="18"></i>
                                        </a>

                                        @if($leave->status === 'pending')
                                            <button
                                                @click="selectedLeave = {{ json_encode($leave) }}; statusToUpdate = 'approved'; showStatusModal = true"
                                                class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                                title="Approve">
                                                <i data-lucide="check-circle" size="18"></i>
                                            </button>
                                            <button
                                                @click="selectedLeave = {{ json_encode($leave) }}; statusToUpdate = 'rejected'; showStatusModal = true"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Reject">
                                                <i data-lucide="x-circle" size="18"></i>
                                            </button>
                                        @endif

                                        <form action="{{ route('school.leaves.destroy', $leave->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this leave record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <i data-lucide="trash-2" size="18"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                            <i data-lucide="calendar-off" size="32"></i>
                                        </div>
                                        <p class="text-slate-500 font-medium">No leave records found</p>
                                        <a href="{{ route('school.leaves.create') }}"
                                            class="text-indigo-600 text-sm font-bold mt-2 hover:underline">Add first record</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100">
                {{ $leaves->links() }}
            </div>
        </div>

        <!-- Status Update Modal -->
        <template x-if="showStatusModal">
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
                    x-data="{ submitting: false }">
                    <div @click="!submitting ? showStatusModal = false : null"
                        class="fixed inset-0 bg-slate-900 bg-opacity-50 transition-opacity backdrop-blur-sm"
                        aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                        <form
                            :action="'{{ route('school.leaves.update-status', ['id' => '__ID__']) }}'.replace('__ID__', selectedLeave.id)"
                            method="POST" @submit="submitting = true">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" :value="statusToUpdate">

                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-bold text-slate-800"
                                        x-text="statusToUpdate === 'approved' ? 'Approve Leave' : 'Reject Leave'"></h3>
                                    <button type="button" @click="showStatusModal = false"
                                        class="text-slate-400 hover:text-slate-600" :disabled="submitting">
                                        <i data-lucide="x" size="20"></i>
                                    </button>
                                </div>

                                <p class="text-sm text-slate-500 mb-4">
                                    Are you sure you want to <span class="font-bold"
                                        :class="statusToUpdate === 'approved' ? 'text-emerald-600' : 'text-red-600'"
                                        x-text="statusToUpdate"></span>
                                    the leave request for <span class="font-bold text-slate-700"
                                        x-text="selectedLeave.leavable.full_name || 'the user'"></span>?
                                </p>

                                <template x-if="statusToUpdate === 'rejected'">
                                    <div class="space-y-2">
                                        <label class="text-sm font-bold text-slate-700">Rejection Reason</label>
                                        <textarea name="rejection_reason" required
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 outline-none transition-all h-24"
                                            placeholder="Enter reason for rejection..." :disabled="submitting"></textarea>
                                    </div>
                                </template>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 flex items-center justify-end gap-3">
                                <button type="button" @click="showStatusModal = false"
                                    class="px-4 py-2 text-sm font-bold text-slate-600 hover:text-slate-800"
                                    :disabled="submitting">Cancel</button>
                                <button type="submit" :disabled="submitting"
                                    :class="statusToUpdate === 'approved' ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200' : 'bg-red-600 hover:bg-red-700 shadow-red-200'"
                                    class="px-6 py-2 text-sm font-bold text-white rounded-xl transition-all shadow-md flex items-center gap-2">
                                    <template x-if="submitting">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </template>
                                    <span
                                        x-text="submitting ? 'Processing...' : 'Confirm ' + (statusToUpdate === 'approved' ? 'Approval' : 'Rejection')"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection