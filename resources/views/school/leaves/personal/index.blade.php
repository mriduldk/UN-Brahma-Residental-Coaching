@extends('layouts.school')

@section('content')
<div class="space-y-6">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i data-lucide="calendar-check" size="28"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">My Leaves</h1>
                <p class="text-slate-500 mt-1 text-sm">Track your leave applications and their approval status.</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('school.leaves.personal.create') }}" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold transition-all shadow-md shadow-indigo-200">
                <i data-lucide="plus" size="18"></i>
                Apply for Leave
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <i data-lucide="clock" size="24"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Pending</p>
                <p class="text-2xl font-bold text-slate-800">{{ $leaves->where('status', 'pending')->count() }}</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <i data-lucide="user-check" size="24"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Approved</p>
                <p class="text-2xl font-bold text-slate-800">{{ $leaves->where('status', 'approved')->count() }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i data-lucide="calendar-days" size="24"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Days</p>
                <p class="text-2xl font-bold text-slate-800">{{ $leaves->where('status', 'approved')->sum('total_days') }}</p>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800">Application History</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Date Range</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Reason</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Approver Info</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-700 font-bold">
                                    {{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $leave->total_days }} {{ Str::plural('Day', $leave->total_days) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider rounded-full">
                                    {{ $leave->leave_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600 max-w-xs truncate" title="{{ $leave->reason }}">
                                    {{ $leave->reason }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @if($leave->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-wider rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending
                                    </span>
                                @elseif($leave->status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Approved
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 text-[10px] font-bold uppercase tracking-wider rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($leave->status === 'approved' && $leave->approver)
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs font-bold text-slate-700">{{ $leave->approver->name }}</span>
                                        <span class="text-[10px] text-slate-400">Approved on {{ $leave->updated_at->format('M d') }}</span>
                                    </div>
                                @elseif($leave->status === 'rejected')
                                    <p class="text-xs text-red-500 font-medium italic">Reason: {{ $leave->rejection_reason ?? 'No comments' }}</p>
                                @else
                                    <span class="text-xs text-slate-400 italic">Waiting...</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 italic">
                                You haven't applied for any leaves yet.
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
</div>
@endsection
