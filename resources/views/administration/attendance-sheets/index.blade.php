@extends('layouts.administration')

@section('title', 'Examination Attendance Sheet')

@section('content')
<div class="space-y-6">

    <!-- Page Header & Action Buttons -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Attendance Sheet Generation
            </h1>
        </div>

        <!-- Export & Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Generate PDF Button -->
            <a href="{{ route('administration.attendance-sheets.pdf', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md shadow-blue-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i data-lucide="file-down" size="16"></i>
                <span>Generate PDF</span>
            </a>

            <!-- Generate Excel (.xls) Button -->
            <a href="{{ route('administration.attendance-sheets.excel', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md shadow-emerald-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i data-lucide="file-spreadsheet" size="16"></i>
                <span>Generate Excel</span>
            </a>
        </div>
    </div>

    <!-- Center Quick Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
        @foreach($centerStats as $centerName => $count)
            @php
                $isActive = strtolower($selectedCenter) === strtolower($centerName);
            @endphp
            <a href="{{ route('administration.attendance-sheets.index', array_merge(request()->except('examination_center', 'page'), ['examination_center' => $centerName])) }}"
               class="p-3.5 rounded-2xl border transition-all {{ $isActive ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-200 ring-2 ring-blue-400' : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700 shadow-xs' }}">
                <div class="text-[10px] font-black uppercase tracking-wider truncate {{ $isActive ? 'text-blue-100' : 'text-slate-400' }}">{{ $centerName }}</div>
                <div class="text-xl font-black mt-1">{{ number_format($count) }}</div>
                <div class="text-[11px] font-medium mt-0.5 truncate {{ $isActive ? 'text-blue-100' : 'text-slate-500' }}">Allotted</div>
            </a>
        @endforeach
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('administration.attendance-sheets.index') }}" class="space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- 1. Examination Center Select -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="map-pin" size="14" class="text-blue-600"></i>
                        <span>Examination Center</span>
                    </label>
                    <select name="examination_center" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                        @foreach($examCenters as $center)
                            @php
                                $cCount = $centerStats[$center] ?? 0;
                            @endphp
                            <option value="{{ $center }}" {{ strtolower($selectedCenter) === strtolower($center) ? 'selected' : '' }}>
                                {{ $center }} ({{ $cCount }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. Course / Stream (Applied For) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
                        <i data-lucide="graduation-cap" size="14" class="text-indigo-600"></i>
                        <span>Course / Stream</span>
                    </label>
                    <select name="applied_for" id="applied_for_select" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                        @foreach($appliedForOptions as $opt)
                            <option value="{{ $opt }}" {{ strtolower(request('applied_for', $appliedForOptions->first())) === strtolower($opt) ? 'selected' : '' }}>
                                {{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons Row -->
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                <div class="text-slate-500 font-medium">
                    Showing <span class="font-bold text-slate-900">{{ $applications->total() }}</span> candidate(s)
                </div>

                <div class="flex items-center gap-2">
                    @if(request()->anyFilled(['examination_center', 'applied_for', 'form_id']))
                        <a href="{{ route('administration.attendance-sheets.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition-all flex items-center gap-1.5">
                            <i data-lucide="rotate-ccw" size="14"></i> Reset
                        </a>
                    @endif
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-sm shadow-blue-200 flex items-center gap-1.5">
                        <i data-lucide="filter" size="14"></i> Apply Filters
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- Attendance Sheet Preview Card -->
    <div class="bg-white rounded-2xl border-2 border-slate-300/80 shadow-md overflow-hidden">
        
        <!-- Header Banner (Exact data requested) -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 text-center relative border-b-4 border-amber-500">
            <div class="max-w-3xl mx-auto space-y-2">
                <div class="text-[11px] font-black uppercase tracking-[0.25em] text-amber-400">
                    Department of Education &bull; Bodoland Territorial Council
                </div>
                
                <!-- Main Header Title -->
                <h2 class="text-xl sm:text-2xl md:text-3xl font-black uppercase tracking-wider text-white">
                    {{ $headerTitle }}
                </h2>

                <!-- Center Header -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs sm:text-sm font-extrabold uppercase tracking-widest text-amber-300 mt-1">
                    <i data-lucide="map-pin" size="15"></i>
                    <span>EXAMINATION CENTER : {{ $centerTitle }}</span>
                </div>

                <div class="text-xs text-slate-300 font-semibold tracking-wide pt-1 flex flex-wrap items-center justify-center gap-3">
                    <span>Date of Exam: <strong>Tuesday, 27th October, 2026</strong></span>
                    <span>&bull;</span>
                    <span>Time: <strong>{{ $examTime }}</strong></span>
                    <span>&bull;</span>
                    <span>Total Allotted: <strong class="text-white">{{ $applications->total() }} Candidates</strong></span>
                </div>
            </div>
        </div>

        <!-- Table Display -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b-2 border-slate-300 text-slate-800 text-[11px] font-black uppercase tracking-wider">
                        <th class="py-3.5 px-4 text-center border-r border-slate-200 w-16">SL NO</th>
                        <th class="py-3.5 px-4 text-center border-r border-slate-200 w-36">ROLL NO</th>
                        <th class="py-3.5 px-5 border-r border-slate-200">CANDIDATE NAME</th>
                        <th class="py-3.5 px-5 border-r border-slate-200">FATHER NAME</th>
                        <th class="py-3.5 px-6 text-center w-52 bg-slate-200/50">SIGNATURE (Blank)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse($applications as $index => $app)
                        @php
                            $student = $app->application_student;
                            $parent = $app->application_parent;
                            $candidateName = $student?->full_name ?? $app->student?->name ?? 'N/A';
                            $fatherName = $student?->father_name ?? $parent?->father_name ?? 'N/A';
                            $rollNo = $app->roll_number ?: $app->roll_no ?: $app->application_code;
                            $serial = ($applications->currentPage() - 1) * $applications->perPage() + $index + 1;
                        @endphp
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <!-- SL NO -->
                            <td class="py-4 px-4 text-center font-bold text-slate-500 border-r border-slate-200">
                                {{ $serial }}
                            </td>

                            <!-- ROLL NO -->
                            <td class="py-4 px-4 text-center border-r border-slate-200">
                                <span class="font-mono font-black text-slate-900 text-base tracking-wider bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                                    {{ $rollNo }}
                                </span>
                            </td>

                            <!-- CANDIDATE NAME -->
                            <td class="py-4 px-5 border-r border-slate-200">
                                <div class="font-bold text-slate-900 uppercase">
                                    {{ $candidateName }}
                                </div>
                                @if(!empty($student?->phone_number))
                                    <div class="text-[11px] text-slate-400 font-medium">Mob: {{ $student->phone_number }}</div>
                                @endif
                            </td>

                            <!-- FATHER NAME -->
                            <td class="py-4 px-5 border-r border-slate-200">
                                <div class="font-semibold text-slate-700 uppercase">
                                    {{ $fatherName }}
                                </div>
                            </td>

                            <!-- SIGNATURE (Blank Space for Physical Signature) -->
                            <td class="py-4 px-6 text-center bg-slate-50/50">
                                <div class="h-10 w-full rounded-md border border-dashed border-slate-300 flex items-center justify-center text-[10px] text-slate-300 uppercase font-bold tracking-wider">
                                    &nbsp;
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400 space-y-2">
                                    <i data-lucide="inbox" size="40" class="text-slate-300"></i>
                                    <p class="font-bold text-slate-600 text-base">No submitted candidates found</p>
                                    <p class="text-xs text-slate-400">Try adjusting your examination center or stream filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Sheet Footer Summary & Signatures Box -->
        <div class="p-6 bg-slate-50 border-t-2 border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <!-- Left: Attendance Stats Box -->
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-2xs space-y-2 text-xs">
                    <div class="font-bold uppercase tracking-wider text-slate-500">Attendance Summary</div>
                    <div class="grid grid-cols-3 gap-2 text-center pt-1">
                        <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <span class="block text-[10px] uppercase text-slate-400 font-semibold">Total</span>
                            <span class="text-sm font-black text-slate-800">{{ $applications->total() }}</span>
                        </div>
                        <div class="p-2 rounded-lg bg-emerald-50 border border-emerald-100">
                            <span class="block text-[10px] uppercase text-emerald-600 font-semibold">Present</span>
                            <span class="text-sm font-black text-emerald-700">_____</span>
                        </div>
                        <div class="p-2 rounded-lg bg-rose-50 border border-rose-100">
                            <span class="block text-[10px] uppercase text-rose-600 font-semibold">Absent</span>
                            <span class="text-sm font-black text-rose-700">_____</span>
                        </div>
                    </div>
                </div>

                <!-- Middle: Invigilator Signature -->
                <div class="text-center pb-2">
                    <div class="border-b border-dashed border-slate-400 w-48 mx-auto mb-2"></div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-700">Invigilator's Signature</p>
                    <p class="text-[10px] text-slate-400">Name: ______________________</p>
                </div>

                <!-- Right: Center Superintendent -->
                <div class="text-center pb-2">
                    <div class="border-b border-dashed border-slate-400 w-48 mx-auto mb-2"></div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-700">Center Superintendent Signature</p>
                    <p class="text-[10px] text-slate-400">With Official Seal</p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
        <div class="px-6 py-4 bg-white border-t border-slate-200">
            {{ $applications->links() }}
        </div>
        @endif

    </div>

</div>
@endsection
