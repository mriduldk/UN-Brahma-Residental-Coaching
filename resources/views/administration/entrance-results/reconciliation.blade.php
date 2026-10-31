@extends('layouts.administration')

@section('title', 'Result Data Reconciliation')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-6">

    <!-- Clean Header with Direct Navigation Link Back to Entrance Results -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('administration.entrance-results.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <i data-lucide="arrow-left" size="14"></i>
                    <span>Back to Entrance Results</span>
                </a>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Result Data Reconciliation</h1>
            <p class="text-xs text-slate-500 mt-0.5">Cross-matching uploaded CSV result records against submitted database applications</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('administration.entrance-results.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm inline-flex items-center gap-1.5">
                <i data-lucide="table" size="14"></i>
                <span>View Results Table</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Uploaded Scores</div>
                <div class="text-xl font-bold text-slate-900 mt-0.5">{{ number_format($totalCount) }}</div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="file-spreadsheet" size="18"></i>
            </div>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Matched Applications in DB</div>
                <div class="text-xl font-bold text-emerald-700 mt-0.5">{{ number_format($linkedCount) }}</div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle" size="18"></i>
            </div>
        </div>

        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <div class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Unmatched (No Online App)</div>
                <div class="text-xl font-bold text-amber-700 mt-0.5">{{ number_format($unlinkedCount) }}</div>
            </div>
            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="help-circle" size="18"></i>
            </div>
        </div>
    </div>

    <!-- Stream Tabs & Search Filter -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3">
        <!-- Stream Tabs -->
        <div class="flex items-center gap-1 overflow-x-auto pb-1 border-b border-slate-100">
            @php
                $streams = ['ALL' => 'All Streams', 'APSC' => 'APSC', 'UPSC' => 'UPSC', 'NEET' => 'NEET', 'JEE' => 'JEE'];
            @endphp
            @foreach($streams as $key => $label)
            <a href="{{ route('administration.entrance-results.reconciliation', array_merge(request()->query(), ['stream' => $key, 'page' => 1])) }}"
               class="px-3 py-1.5 text-xs font-semibold rounded-lg transition whitespace-nowrap {{ strtoupper($stream) === $key ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('administration.entrance-results.reconciliation') }}" class="flex items-center gap-2">
            <input type="hidden" name="stream" value="{{ $stream }}">
            <div class="relative flex-1">
                <i data-lucide="search" size="14" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by Roll No, Candidate Name, or Phone..."
                       class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <button type="submit" class="py-2 px-4 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition">
                Search
            </button>
            @if($search)
            <a href="{{ route('administration.entrance-results.reconciliation', ['stream' => $stream]) }}" class="p-2 text-slate-500 hover:text-slate-700 border border-slate-300 rounded-lg bg-white" title="Clear search">
                <i data-lucide="x" size="14"></i>
            </a>
            @endif
        </form>
    </div>

    <!-- Reconciliation Table (Exact 6 Columns Requested) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-200 uppercase text-[11px] tracking-wider">
                        <!-- Col 1 -->
                        <th class="py-3 px-4 w-28">Roll No</th>
                        <!-- Col 2 -->
                        <th class="py-3 px-4">Result Uploaded Name &amp; Phone</th>
                        <!-- Col 3 -->
                        <th class="py-3 px-4">Result Uploaded District</th>
                        <!-- Col 4 -->
                        <th class="py-3 px-4">Database Name &amp; Phone</th>
                        <!-- Col 5 -->
                        <th class="py-3 px-4">Database Exam District</th>
                        <!-- Col 6 -->
                        <th class="py-3 px-4 text-center">Status (Match Details)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-normal text-slate-700">
                    @forelse($results as $res)
                    @php $rc = $res->recon; @endphp
                    <tr class="hover:bg-slate-50 transition">
                        
                        <!-- Col 1: Roll No -->
                        <td class="py-3 px-4 font-mono font-bold text-slate-900 align-top">
                            <div class="text-sm">{{ $res->roll_no }}</div>
                            <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold rounded bg-slate-100 text-slate-600 mt-0.5">
                                {{ $res->stream }}
                            </span>
                        </td>

                        <!-- Col 2: Result Uploaded Name & Phone Number -->
                        <td class="py-3 px-4 align-top">
                            <div class="font-bold text-slate-900 uppercase">{{ $res->candidate_name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                @if($res->phone_number)
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="phone" size="10" class="text-slate-400"></i>
                                        <span>{{ $res->phone_number }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">No phone in CSV</span>
                                @endif
                            </div>
                        </td>

                        <!-- Col 3: Result Uploaded District -->
                        <td class="py-3 px-4 align-top">
                            @if($res->district)
                                <span class="font-semibold text-slate-800 uppercase">{{ $res->district }}</span>
                            @else
                                <span class="text-slate-400 italic">No district in CSV</span>
                            @endif
                        </td>

                        <!-- Col 4: Database Name & Phone Number -->
                        <td class="py-3 px-4 align-top">
                            @if($rc->has_db && $rc->db_name)
                                <div class="font-bold text-slate-900 uppercase">{{ $rc->db_name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    @if($rc->db_phone)
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="phone" size="10" class="text-slate-400"></i>
                                            <span>{{ $rc->db_phone }}</span>
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">No phone in DB</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-slate-400 italic text-[11px]">No matching online app</span>
                            @endif
                        </td>

                        <!-- Col 5: Database Examination District -->
                        <td class="py-3 px-4 align-top">
                            @if($rc->has_db && $rc->db_district)
                                <span class="font-semibold text-slate-800 uppercase">{{ $rc->db_district }}</span>
                            @else
                                <span class="text-slate-400 italic text-[11px]">—</span>
                            @endif
                        </td>

                        <!-- Col 6: Status: Matched with name or not, phone or not, district or not -->
                        <td class="py-3 px-4 align-top">
                            @if(!$rc->has_db)
                                <div class="text-center">
                                    <span class="inline-block px-2 py-1 text-[10px] font-semibold rounded bg-slate-100 text-slate-500 border border-slate-200">
                                        No DB Application
                                    </span>
                                </div>
                            @else
                                <div class="flex flex-col gap-1 items-start sm:items-center">
                                    
                                    <!-- Name Match Status -->
                                    @if($rc->name_status === 'matched')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i data-lucide="check" size="10"></i> Name Matched
                                        </span>
                                    @elseif($rc->name_status === 'partial')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200">
                                            <i data-lucide="alert-circle" size="10"></i> Name Partial
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-rose-50 text-rose-700 border border-rose-200">
                                            <i data-lucide="x" size="10"></i> Name Mismatch
                                        </span>
                                    @endif

                                    <!-- Phone Match Status -->
                                    @if($rc->phone_status === 'matched')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i data-lucide="check" size="10"></i> Phone Matched
                                        </span>
                                    @elseif($rc->phone_status === 'mismatch')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-rose-50 text-rose-700 border border-rose-200">
                                            <i data-lucide="x" size="10"></i> Phone Mismatch
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded bg-slate-100 text-slate-500">
                                            Phone: N/A
                                        </span>
                                    @endif

                                    <!-- District Match Status -->
                                    @if($rc->district_status === 'matched')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i data-lucide="check" size="10"></i> District Matched
                                        </span>
                                    @elseif($rc->district_status === 'mismatch')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold rounded bg-rose-50 text-rose-700 border border-rose-200">
                                            <i data-lucide="x" size="10"></i> District Mismatch
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-medium rounded bg-slate-100 text-slate-500">
                                            District: N/A
                                        </span>
                                    @endif

                                </div>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                            No records found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($results->hasPages())
        <div class="p-3 bg-slate-50 border-t border-slate-200">
            {{ $results->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
