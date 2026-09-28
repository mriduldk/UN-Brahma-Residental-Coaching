@extends('layouts.school')

@section('title', 'Fee Tracking')

@section('content')
<x-missing-session-popup />
<x-missing-class-popup />

<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Monthly Fee Tracking</h1>
            <p class="text-slate-500 text-sm mt-1">Monitor and record monthly tuition payments for students.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('school.fees.config') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-black rounded-xl shadow-sm hover:bg-slate-50 transition-all text-xs flex items-center gap-2 uppercase tracking-widest">
                <i data-lucide="settings" size="14"></i> Configure Fees
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <form action="{{ route('school.fees.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Class</label>
                <select name="class_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                    <option value="">Choose a Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Month</label>
                <select name="month" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Year</label>
                <select name="year" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                    @foreach(range(date('Y')-1, date('Y')+1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end pb-1">
                 @if($classId)
                 <a href="{{ route('school.fees.index') }}" class="text-[10px] font-black text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-widest flex items-center gap-1">
                    <i data-lucide="refresh-ccw" size="12"></i> Reset Filters
                 </a>
                 @endif
            </div>
        </form>
    </div>


    @if(!$classId)
        <div class="bg-indigo-50/50 border-2 border-dashed border-indigo-100 rounded-[3rem] p-16 text-center">
            <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-xl shadow-indigo-100/50">
                <i data-lucide="banknote" class="text-indigo-500" size="36"></i>
            </div>
            <h3 class="text-2xl font-black text-indigo-900 mb-3 tracking-tight">Select a Class to Track Payments</h3>
            <p class="text-slate-500 max-w-sm mx-auto font-medium leading-relaxed">Choose a class from the dropdown to see the payment status of all enrolled students for the selected month.</p>
        </div>
    @else
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Student Details</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Amount Paid</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($enrollments as $enrollment)
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($enrollment->student->full_name) }}&background=f1f5f9&color=64748b&bold=true" class="w-11 h-11 rounded-2xl border-2 border-white shadow-sm">
                                    <div>
                                        <p class="font-black text-slate-900 tracking-tight leading-none mb-1">{{ $enrollment->student->full_name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Roll: {{ $enrollment->roll_number ?? 'N/A' }} | {{ $enrollment->section->name ?? 'No Section' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($enrollment->payment && $enrollment->payment->status === 'paid')
                                    <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-100 flex items-center gap-1 justify-center">
                                        @if($enrollment->payment->payment_method === 'online')
                                            <i data-lucide="shield-check" size="12"></i>
                                        @endif
                                        Paid
                                    </span>
                                @else
                                    <span class="px-4 py-1.5 bg-rose-50 text-rose-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-rose-100">Unpaid</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="font-black text-slate-900 tracking-tight">
                                    @if($enrollment->payment)
                                        ₹{{ number_format($enrollment->payment->amount_paid, 2) }}
                                    @else
                                        ₹0.00
                                    @endif
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                @if($enrollment->payment && $enrollment->payment->status === 'paid')
                                    <a href="{{ route('school.fees.collect', ['enrollment' => $enrollment->id, 'month' => $month, 'year' => $year]) }}" class="inline-flex items-center justify-center w-10 h-10 bg-slate-50 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 rounded-xl transition-all shadow-sm">
                                        <i data-lucide="eye" size="18"></i>
                                    </a>
                                @else
                                    <a href="{{ route('school.fees.collect', ['enrollment' => $enrollment->id, 'month' => $month, 'year' => $year]) }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-xl shadow-lg shadow-indigo-100 transition-all text-[10px] uppercase tracking-widest inline-flex items-center gap-2">
                                        <i data-lucide="credit-card" size="14"></i> Collect Fee
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center">
                                <p class="text-slate-400 font-medium italic">No students found in this class.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
