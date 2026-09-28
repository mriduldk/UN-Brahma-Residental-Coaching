@extends('layouts.student')

@section('title', 'My Leaves')

@section('content')
<div class="space-y-8">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-50 rounded-full opacity-30 blur-3xl"></div>
        <div class="relative flex items-center gap-6">
            <div class="w-20 h-20 rounded-3xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-200 ring-4 ring-indigo-50">
                <i data-lucide="user-minus" size="40"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-indigo-950 tracking-tight">Leave Management</h1>
                <p class="text-slate-500 font-medium tracking-tight uppercase text-xs">Track and apply for your leaves</p>
            </div>
        </div>
        <div class="relative">
            <a href="{{ route('school.leaves.personal.create') }}" class="flex items-center gap-2 px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all shadow-lg shadow-indigo-100 group">
                <i data-lucide="plus" size="20" class="group-hover:rotate-90 transition-transform"></i>
                Request New Leave
            </a>
        </div>
    </div>

    <!-- Main List Card -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden min-h-[500px]">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-black text-slate-800 flex items-center gap-2">
                <i data-lucide="clipboard-list" class="text-slate-400" size="20"></i>
                Leave History
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-50">
                    <tr>
                        <th class="px-8 py-4">Dates</th>
                        <th class="px-6 py-4">Duration</th>
                        <th class="px-6 py-4">Reason</th>
                        <th class="px-6 py-4">Applied On</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($leaves as $leave)
                    <tr class="group hover:bg-slate-50/50 transition-all">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800">{{ $leave->start_date->format('M d, Y') }} @if($leave->total_days > 1) - {{ $leave->end_date->format('M d, Y') }} @endif</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ $leave->leave_type ?? 'General' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 font-black text-slate-500 text-sm">
                            {{ $leave->total_days }} {{ Str::plural('Day', $leave->total_days) }}
                        </td>
                        <td class="px-6 py-6">
                            <p class="text-slate-600 text-sm max-w-xs truncate" title="{{ $leave->reason }}">{{ $leave->reason }}</p>
                            @if($leave->remarks)
                                <p class="text-[10px] text-indigo-500 font-bold mt-1 italic">Admin: {{ $leave->remarks }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-6 text-slate-400 text-xs font-medium">
                            {{ $leave->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-6">
                            @if($leave->status === 'pending')
                                <span class="px-4 py-1.5 bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-amber-100">Pending</span>
                            @elseif($leave->status === 'approved')
                                <span class="px-4 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-100">Approved</span>
                            @else
                                <span class="px-4 py-1.5 bg-red-50 text-red-700 text-[10px] font-black uppercase tracking-widest rounded-full border border-red-100">Rejected</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <a href="{{ route('school.leaves.personal.show', $leave->id) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors inline-flex items-center gap-2 font-bold text-xs uppercase tracking-widest" title="View Details">
                                <i data-lucide="eye" size="18"></i>
                                <span class="hidden md:inline">View</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4 opacity-30">
                                <i data-lucide="inbox" size="64"></i>
                                <p class="font-black text-slate-400 uppercase tracking-widest text-sm">No leave records found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
