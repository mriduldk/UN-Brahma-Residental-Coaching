@extends('layouts.school')

@section('title', 'Attendance Percentage Report')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Attendance Percentage</h1>
            <p class="text-slate-500 text-sm mt-1">Check individual student attendance percentages over a selected period.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('school.attendance.report.percentage.export', request()->all()) }}" class="px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
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
    <form action="{{ route('school.attendance.report.percentage') }}" method="GET" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm print:hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5 items-end">
            
            <div class="lg:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-700 font-medium focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm cursor-pointer" required>
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-700 font-medium focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm cursor-pointer" required>
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Session</label>
                <div class="relative group">
                    <select name="academic_year_id" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm" required>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $academicYearId == $year->id || (!$academicYearId && $year->is_active) ? 'selected' : '' }}>{{ $year->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i data-lucide="chevron-down" size="16"></i>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Class</label>
                <div class="relative group">
                    <select name="class_id" id="classSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm" required onchange="updateSections()">
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" data-sections='{{ json_encode($c->sections) }}' {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i data-lucide="chevron-down" size="16"></i>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Section</label>
                <div class="relative group">
                    <select name="section_id" id="sectionSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm">
                        <option value="">All Sections</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                        <i data-lucide="chevron-down" size="16"></i>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <button type="submit" class="w-full px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                    <i data-lucide="search" size="16"></i> Calculate
                </button>
            </div>
        </div>
    </form>

    <!-- Report Table -->
    @if($reportData->isNotEmpty())
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100">
                    <i data-lucide="percent" size="20"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Attendance Summary</h2>
                    <p class="text-xs text-slate-500 font-medium tracking-tight">Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d M') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d M, Y') }} (Holidays Excluded)</p>
                </div>
            </div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest hidden sm:block">Total Students: <span class="text-indigo-600">{{ $reportData->count() }}</span></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-50 bg-white">
                        <th class="px-6 py-4 w-12 text-center">#</th>
                        <th class="px-6 py-4">Student Name</th>
                        <th class="px-6 py-4">Registration</th>
                        <th class="px-6 py-4 text-center">Working Days</th>
                        <th class="px-6 py-4 text-center">Present</th>
                        <th class="px-6 py-4 text-center">Absent</th>
                        <th class="px-6 py-4">Percentage</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-50">
                    @foreach($reportData as $index => $data)
                    <tr class="hover:bg-indigo-50/20 transition-colors group">
                        <td class="px-6 py-4 text-center font-bold text-slate-400 text-xs">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $data['student']->user->name }}</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ $data['student']->email ?? $data['student']->user->mobile_number }}</div>
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-500 text-xs uppercase italic">{{ $data['registration_number'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded text-xs">{{ $data['total_days'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-emerald-600">{{ $data['present_days'] }}</td>
                        <td class="px-6 py-4 text-center font-bold text-red-500">{{ $data['absent_days'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden min-w-[120px]">
                                    <div class="h-full rounded-full shadow-[0_0_8px_rgba(0,0,0,0.1)] transition-all duration-1000 {{ $data['percentage'] >= 75 ? 'bg-emerald-500' : ($data['percentage'] >= 60 ? 'bg-amber-500' : 'bg-red-500') }}" 
                                         style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                                <span class="text-xs font-black min-w-[45px] {{ $data['percentage'] >= 75 ? 'text-emerald-700' : ($data['percentage'] >= 60 ? 'text-amber-700' : 'text-red-700') }}">
                                    {{ $data['percentage'] }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif($academicYearId && $classId)
    <div class="bg-indigo-50/50 border border-indigo-100 border-dashed rounded-3xl p-20 text-center">
        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-indigo-50">
            <i data-lucide="user-x" size="24" class="text-indigo-300"></i>
        </div>
        <h3 class="text-lg font-bold text-indigo-900 mb-1">No Data Found</h3>
        <p class="text-indigo-600/70 text-sm max-w-sm mx-auto">Either there are no students enrolled in this selection or no attendance has been marked for the chosen dates.</p>
    </div>
    @else
    <div class="bg-indigo-50/50 border border-indigo-100 border-dashed rounded-3xl p-20 text-center">
        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-indigo-50">
            <i data-lucide="filter" size="24" class="text-indigo-400"></i>
        </div>
        <h3 class="text-lg font-bold text-indigo-900 mb-1">Generate Statistics</h3>
        <p class="text-indigo-600/70 text-sm max-w-sm mx-auto">Select a date range and class to calculate individual attendance percentages.</p>
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
        sectionSelect.innerHTML = '<option value="">All Sections</option>';
        
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
                console.error('Error parsing sections json');
            }
        }
    }
</script>
@endpush
@endsection
