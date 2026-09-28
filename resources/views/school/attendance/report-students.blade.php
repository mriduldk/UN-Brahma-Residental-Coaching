@extends('layouts.school')

@section('title', 'Student Attendance Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Student Attendance Report</h1>
            <p class="text-slate-500 text-sm mt-1">Monthly attendance overview for enrolled students.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('school.attendance.report.students.export', request()->all()) }}" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                <i data-lucide="file-spreadsheet" size="16"></i>
                Export to Excel
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                <i data-lucide="printer" size="16"></i>
                Print Report
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm print:hidden">
        <form action="{{ route('school.attendance.report.students') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 xl:grid-cols-6 gap-5 items-end">
            
            <div class="md:col-span-2 xl:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Session <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <select name="academic_year_id" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm" required>
                        <option value="">Select Session</option>
                        @foreach($academicYears as $acadYear)
                            <option value="{{ $acadYear->id }}" {{ $academicYearId == $acadYear->id ? 'selected' : ($acadYear->is_active && !$academicYearId ? 'selected' : '') }}>
                                {{ $acadYear->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i data-lucide="chevron-down" size="16"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Class <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <select name="class_id" id="classSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm" required onchange="updateSections()">
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" data-sections='{{ json_encode($c->sections) }}' {{ $classId == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i data-lucide="chevron-down" size="16"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Section</label>
                <div class="relative group">
                    <select name="section_id" id="sectionSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm">
                        <option value="">All / None</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i data-lucide="chevron-down" size="16"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Month</label>
                <div class="relative group">
                    <select name="month" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                            </option>
                        @endfor
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i data-lucide="chevron-down" size="16"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Year</label>
                <div class="relative group">
                    <select name="year" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm" required>
                        @for($y = date('Y') - 5; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i data-lucide="chevron-down" size="16"></i>
                    </div>
                </div>
            </div>

            <div >
                <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center shrink-0 w-full sm:w-auto">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    @if($academicYearId && $classId)
    <!-- Report Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden relative">
        <div class="p-6 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100">
                    <i data-lucide="users" size="20"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">
                        {{ collect($classes)->firstWhere('id', $classId)->name ?? 'Class' }} - Student Report
                    </h2>
                    <p class="text-xs text-slate-500 font-medium whitespace-nowrap">{{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}</p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-emerald-500"></div> Present (P)</span>
                <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-red-500"></div> Absent (A)</span>
                <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-amber-500"></div> Half Day (H)</span>
                <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-orange-500"></div> Late (L)</span>
                <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-blue-500"></div> Leave (V)</span>
                <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-cyan-500"></div> Holiday (O)</span>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-100 bg-white">
                        <th class="px-4 py-3 sticky left-0 z-20 bg-white shadow-[1px_0_0_0_#f1f5f9] w-64">Student Name</th>
                        @for($i = 1; $i <= $daysInMonth; $i++)
                            <th class="px-2 py-3 text-center border-l border-slate-50 w-8">{{ $i }}</th>
                        @endfor
                        <th class="px-4 py-3 text-center border-l bg-slate-50 border-slate-200 w-16">P</th>
                        <th class="px-4 py-3 text-center bg-slate-50 w-16">A</th>
                        <th class="px-4 py-3 text-center bg-slate-50 w-16">H</th>
                        <th class="px-4 py-3 text-center bg-slate-50 w-16">L</th>
                        <th class="px-4 py-3 text-center bg-slate-50 w-16">V</th>
                        <th class="px-4 py-3 text-center bg-slate-50 w-16">O</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-bold divide-y divide-slate-50">
                    @forelse($students as $student)
                    <tr class="hover:bg-indigo-50/20 transition-colors">
                        <td class="px-4 py-3 sticky left-0 z-10 bg-white shadow-[1px_0_0_0_#f1f5f9]">
                            <div class="text-slate-800">{{ $student->user->name ?? 'Unknown' }}</div>
                            <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $student->registration_number ?? 'NO REG' }}</div>
                        </td>
                        
                        @php
                            $studentRecords = isset($attendances[$student->id]) ? $attendances[$student->id]->keyBy(function($item) {
                                return \Carbon\Carbon::parse($item->date)->format('d');
                            }) : collect();
                            
                            $totals = ['Present' => 0, 'Absent' => 0, 'Half Day' => 0, 'Late' => 0, 'Leave' => 0, 'Holiday' => 0];
                        @endphp
                        
                        @for($i = 1; $i <= $daysInMonth; $i++)
                            @php
                                $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $status = isset($studentRecords[$d]) ? $studentRecords[$d]->status : null;
                                $display = '-';
                                $colorClass = 'text-slate-200 font-normal';
                                $bgClass = '';
                                
                                if ($status) {
                                    $totals[$status]++;
                                    switch($status) {
                                        case 'Present': $display = 'P'; $colorClass = 'text-emerald-700'; $bgClass = 'bg-emerald-50'; break;
                                        case 'Absent':  $display = 'A'; $colorClass = 'text-red-700'; $bgClass = 'bg-red-50'; break;
                                        case 'Half Day':$display = 'H'; $colorClass = 'text-amber-700'; $bgClass = 'bg-amber-50'; break;
                                        case 'Late':    $display = 'L'; $colorClass = 'text-orange-700'; $bgClass = 'bg-orange-50'; break;
                                        case 'Leave':   $display = 'V'; $colorClass = 'text-blue-700'; $bgClass = 'bg-blue-50'; break;
                                        case 'Holiday': $display = 'O'; $colorClass = 'text-cyan-700'; $bgClass = 'bg-cyan-50'; break;
                                    }
                                }
                            @endphp
                            <td class="px-2 py-3 text-center border-l border-slate-50">
                                @if($display !== '-')
                                    <div class="w-6 h-6 mx-auto rounded flex items-center justify-center {{$bgClass}} {{$colorClass}}" title="{{$status}}">{{ $display }}</div>
                                @else
                                    <span class="{{$colorClass}}">{{ $display }}</span>
                                @endif
                            </td>
                        @endfor
                        
                        <td class="px-4 py-3 text-center text-emerald-600 border-l border-slate-200 bg-slate-50">{{ $totals['Present'] }}</td>
                        <td class="px-4 py-3 text-center text-red-600 bg-slate-50">{{ $totals['Absent'] }}</td>
                        <td class="px-4 py-3 text-center text-amber-600 bg-slate-50">{{ $totals['Half Day'] }}</td>
                        <td class="px-4 py-3 text-center text-orange-600 bg-slate-50">{{ $totals['Late'] }}</td>
                        <td class="px-4 py-3 text-center text-blue-600 bg-slate-50">{{ $totals['Leave'] }}</td>
                        <td class="px-4 py-3 text-center text-cyan-600 bg-slate-50">{{ $totals['Holiday'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $daysInMonth + 6 }}" class="py-16 text-center text-slate-400">
                            No students found for this class.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-indigo-50/50 border border-indigo-100 border-dashed rounded-3xl p-12 text-center mt-6 print:hidden">
        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-indigo-50">
            <i data-lucide="filter" size="24" class="text-indigo-400"></i>
        </div>
        <h3 class="text-lg font-bold text-indigo-900 mb-1">Select Filters</h3>
        <p class="text-indigo-600/70 text-sm max-w-sm mx-auto">Use the filters above to generate the monthly student attendance report.</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classSelect = document.getElementById('classSelect');
        if (classSelect && classSelect.value) {
            updateSections('{{ $sectionId }}');
        }
    });

    function updateSections(preselectedSection = '') {
        const classSelect = document.getElementById('classSelect');
        const sectionSelect = document.getElementById('sectionSelect');
        
        if (!classSelect || !sectionSelect) return;
        
        const selectedOption = classSelect.options[classSelect.selectedIndex];
        
        sectionSelect.innerHTML = '<option value="">All / None</option>';
        
        if (selectedOption.value && selectedOption.hasAttribute('data-sections')) {
            try {
                const sections = JSON.parse(selectedOption.getAttribute('data-sections'));
                sections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.name;
                    if (preselectedSection && preselectedSection == section.id) {
                        option.selected = true;
                    }
                    sectionSelect.appendChild(option);
                });
            } catch (e) {
                console.error('Error parsing sections json', e);
            }
        }
    }
</script>
@endpush
@endsection
