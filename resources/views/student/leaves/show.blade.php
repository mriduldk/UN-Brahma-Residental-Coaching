@extends('layouts.student')

@section('content')
    <div class="space-y-6">
        <!-- Breadcrumbs & Back -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-400">
                <a href="{{ route('student.dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
                <i data-lucide="chevron-right" size="14"></i>
                <a href="{{ route('school.leaves.personal.index') }}" class="hover:text-indigo-600 transition-colors">My Leaves</a>
                <i data-lucide="chevron-right" size="14"></i>
                <span class="text-slate-600">Application Details</span>
            </div>

            <a href="{{ route('school.leaves.personal.index') }}"
                class="flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 rounded-xl font-bold text-xs uppercase tracking-widest transition-all border border-slate-200 shadow-sm">
                <i data-lucide="arrow-left" size="16"></i>
                Back to History
            </a>
        </div>

        <!-- Main Detail Card -->
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-50 rounded-full opacity-30 blur-3xl"></div>

                <div class="relative flex items-center gap-8">
                    <!-- Premium Avatar -->
                    <div class="relative group">
                        <div class="relative w-24 h-24 rounded-3xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-2xl border border-white shadow-xl overflow-hidden">
                            <i data-lucide="file-text" size="32"></i>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <h1 class="text-3xl font-black text-slate-800 tracking-tight leading-none mb-3">
                                Leave Application #L{{ str_pad($leave->id, 5, '0', STR_PAD_LEFT) }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-2.5">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-xl">
                                    <i data-lucide="tag" size="12" class="text-indigo-500"></i>
                                    <span class="text-[11px] font-black text-slate-600 tracking-wide uppercase">
                                        {{ $leave->leave_type }}
                                    </span>
                                </div>
                                <span class="px-3 py-1 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest rounded-lg">
                                    {{ str_contains($leave->leavable_type, 'Staff') ? 'Staff member' : 'Student' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="px-6 py-3 {{ $leave->status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : ($leave->status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-red-50 text-red-700 border-red-100') }} rounded-2xl font-black text-sm uppercase tracking-widest border flex items-center gap-2">
                        <i data-lucide="{{ $leave->status === 'approved' ? 'check-circle' : ($leave->status === 'pending' ? 'clock-3' : 'alert-circle') }}" size="18"></i>
                        {{ ucfirst($leave->status) }}
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="p-8 grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Leave Info -->
                <div class="lg:col-span-2 space-y-10">
                    <section>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Request Description</h3>
                        <div class="bg-slate-50/50 p-6 rounded-3xl border border-slate-100 italic text-slate-700 leading-relaxed font-medium whitespace-pre-wrap">{{ $leave->reason }}</div>
                    </section>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <section>
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Duration</h3>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                    <i data-lucide="calendar" size="24"></i>
                                </div>
                                <div>
                                    <p class="text-lg font-black text-slate-800">{{ $leave->total_days }} {{ Str::plural('Day', $leave->total_days) }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Leave Period</p>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Profile Reference</h3>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400">
                                    <i data-lucide="user" size="24"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-800">{{ $leave->leavable->full_name ?? ($leave->leavable->student->full_name) }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Applicant</p>
                                </div>
                            </div>
                        </section>
                    </div>

                    @if($leave->status === 'rejected' && $leave->rejection_reason)
                        <section>
                            <div class="bg-red-50 p-8 rounded-[2.5rem] border border-red-100 space-y-4">
                                <div class="flex items-center gap-3 text-red-600">
                                    <i data-lucide="message-square" size="20"></i>
                                    <h3 class="text-sm font-black uppercase tracking-widest">Rejection Remarks</h3>
                                </div>
                                <p class="text-red-700 leading-relaxed font-medium">
                                    {{ $leave->rejection_reason }}
                                </p>
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
                                <div class="absolute left-2.5 top-0 w-3.5 h-3.5 rounded-full bg-indigo-500 ring-4 ring-white"></div>
                                <p class="text-sm font-black text-slate-800 leading-none">Submitted</p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">{{ $leave->created_at->format('M d, Y - h:i A') }}</p>
                            </div>

                            <!-- Range -->
                            <div class="relative pl-10">
                                <div class="absolute left-2.5 top-0 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-4 ring-white"></div>
                                <p class="text-sm font-black text-slate-800 leading-none">Start Date</p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">{{ $leave->start_date->format('M d, Y') }}</p>
                            </div>

                            <div class="relative pl-10">
                                <div class="absolute left-2.5 top-0 w-3.5 h-3.5 rounded-full bg-slate-400 ring-4 ring-white"></div>
                                <p class="text-sm font-black text-slate-800 leading-none">End Date</p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">{{ $leave->end_date->format('M d, Y') }}</p>
                            </div>

                            @if($leave->status !== 'pending' && $leave->approver)
                                <div class="relative pl-10">
                                    <div class="absolute left-2.5 top-0 w-3.5 h-3.5 rounded-full {{ $leave->status === 'approved' ? 'bg-emerald-500' : 'bg-red-500' }} ring-4 ring-white"></div>
                                    <p class="text-sm font-black text-slate-800 leading-none">Processed</p>
                                    <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase">{{ $leave->updated_at->format('M d, Y - h:i A') }}</p>
                                    <span class="inline-block mt-2 px-2 py-0.5 {{ $leave->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} text-[8px] font-black uppercase rounded">
                                        {{ $leave->status }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($leave->status === 'pending')
                    <div class="p-6 bg-amber-50 rounded-3xl border border-amber-100 flex items-start gap-3">
                        <i data-lucide="info" size="20" class="text-amber-600 shrink-0"></i>
                        <p class="text-[11px] text-amber-800 font-medium leading-relaxed">
                            Your application is currently being reviewed by the administration. You will be notified once a decision is made.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
