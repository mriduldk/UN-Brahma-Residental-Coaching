@extends('layouts.school')

@section('title', 'Student Attendance')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Student Attendance</h1>
            <p class="text-slate-500 text-sm mt-1">Mark daily attendance for students in a specific class and section.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('school.attendance.report.students') }}" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                <i data-lucide="bar-chart-2" size="16"></i>
                View Report
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300 shadow-sm">
            <i data-lucide="check-circle-2" size="20"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300 shadow-sm">
            <i data-lucide="alert-circle" size="20"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <form action="{{ route('school.attendance.students') }}" method="GET" class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Date <span class="text-red-500">*</span></label>
                <input type="date" name="date" value="{{ $date }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 font-medium focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm cursor-pointer" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Session <span class="text-red-500">*</span></label>
                <div class="relative group">
                    <select name="academic_year_id" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm" required>
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
                    <select name="class_id" id="classSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm" required onchange="updateSections()">
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

            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Section</label>
                    <div class="relative group">
                        <select name="section_id" id="sectionSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer shadow-sm">
                            <option value="">All / None</option>
                            <!-- Sections populated via JS if page loads with selection, otherwise it waits for user selection -->
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <i data-lucide="chevron-down" size="16"></i>
                        </div>
                    </div>
                </div>
                <button type="submit" class="h-[46px] px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center shrink-0 mt-6">
                    Load
                </button>
            </div>
        </div>
    </form>

    <!-- Attendance Table -->
    @if($academicYearId && $classId)
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden relative">
        <form action="{{ route('school.attendance.students.store') }}" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="academic_year_id" value="{{ $academicYearId }}">
            <input type="hidden" name="class_id" value="{{ $classId }}">
            <input type="hidden" name="section_id" value="{{ $sectionId }}">
            
            <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100">
                        <i data-lucide="users" size="20"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">{{ collect($classes)->firstWhere('id', $classId)->name ?? 'Class' }} <span class="text-slate-400 mx-1">|</span> {{ Date::parse($date)->format('d M, Y') }}</h2>
                        <p class="text-xs text-slate-500 font-medium">Found {{ count($students) }} enrolled students</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center justify-end gap-3 sm:gap-4 ml-auto">
                    <button type="button" onclick="document.querySelectorAll('input[value=Present]').forEach(e => e.click())" class="shrink-0 px-4 py-2.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold rounded-xl border border-emerald-200/50 shadow-sm transition-all focus:ring-4 focus:ring-emerald-50 text-sm flex items-center gap-2 group">
                        <i data-lucide="check-circle-2" size="18" class="text-emerald-500 group-hover:scale-110 transition-transform"></i>
                        Mark All Present
                    </button>
                    <button type="button" onclick="document.querySelectorAll('input[value=Holiday]').forEach(e => e.click())" class="shrink-0 px-4 py-2.5 bg-cyan-50 text-cyan-700 hover:bg-cyan-100 font-bold rounded-xl border border-cyan-200/50 shadow-sm transition-all focus:ring-4 focus:ring-cyan-50 text-sm flex items-center gap-2 group">
                        <i data-lucide="palmtree" size="18" class="text-cyan-500 group-hover:scale-110 transition-transform"></i>
                        Mark All Holiday
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-50 bg-white">
                            <th class="px-6 py-4 w-12 text-center">#</th>
                            <th class="px-6 py-4">Student Name</th>
                            <th class="px-6 py-4">Registration No.</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-50">
                        @forelse($students as $index => $student)
                        @php
                            $status = isset($attendances[$student->id]) ? $attendances[$student->id]->status : null;
                            $remarks = isset($attendances[$student->id]) ? $attendances[$student->id]->remarks : '';
                        @endphp
                        <tr class="hover:bg-indigo-50/20 transition-colors group">
                            <td class="px-6 py-4 text-center font-bold text-slate-400 text-xs">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $student->user->name }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $student->user->email ?? $student->user->mobile_number }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">{{ $student->registration_number ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="Present" class="peer sr-only" {{ $status === 'Present' ? 'checked' : '' }} required>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50">
                                            Present
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="Absent" class="peer sr-only" {{ $status === 'Absent' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-600 hover:bg-slate-50">
                                            Absent
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="Half Day" class="peer sr-only" {{ $status === 'Half Day' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-600 hover:bg-slate-50">
                                            Half
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="Late" class="peer sr-only" {{ $status === 'Late' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-600 hover:bg-slate-50">
                                            Late
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="Leave" class="peer sr-only" {{ $status === 'Leave' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-600 hover:bg-slate-50">
                                            Leave
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" name="attendance[{{ $student->id }}][status]" value="Holiday" class="peer sr-only" {{ $status === 'Holiday' ? 'checked' : '' }}>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold border border-slate-200 text-slate-500 transition-all peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:border-cyan-600 hover:bg-slate-50">
                                            Holiday
                                        </div>
                                    </label>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" name="attendance[{{ $student->id }}][remarks]" value="{{ $remarks }}" placeholder="Optional notes..." class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-700 focus:bg-white focus:ring-2 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center text-slate-400">
                                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                    <i data-lucide="user-x" size="24" class="text-slate-300"></i>
                                </div>
                                <p class="font-bold text-slate-600 text-base">No Students Found</p>
                                <p class="text-xs mt-1">There are no students mapped to this specific session and class combination.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($students) > 0)
            @php
                $hasAttendance = count($attendances) > 0;
                $lastModified = $hasAttendance ? $attendances->max('updated_at') : null;
            @endphp
            <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-4 sticky bottom-0 z-10 backdrop-blur-md">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-widest hidden sm:block">
                    @if($hasAttendance && $lastModified)
                        <span class="flex items-center gap-1.5"><i data-lucide="clock" size="14"></i> Last updated: {{ $lastModified->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</span>
                    @else
                        Date: {{ \Carbon\Carbon::parse($date)->format('d M, Y') }}
                    @endif
                </div>
                <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-not-allowed': isSubmitting}" class="px-8 py-3 {{ $hasAttendance ? 'bg-amber-500 hover:bg-amber-600 shadow-amber-200/50 text-white' : 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-200/50 text-white' }} font-bold rounded-xl shadow-lg transition-all flex items-center gap-2 w-full sm:w-auto justify-center">
                    <i data-lucide="{{ $hasAttendance ? 'refresh-cw' : 'save' }}" size="18" x-show="!isSubmitting"></i>
                    <i data-lucide="loader-2" size="18" class="animate-spin" x-show="isSubmitting" style="display: none;"></i>
                    <span x-text="isSubmitting ? 'Saving...' : '{{ $hasAttendance ? 'Update Attendance' : 'Save Attendance' }}'">{{ $hasAttendance ? 'Update Attendance' : 'Save Attendance' }}</span>
                </button>
            </div>
            @endif
        </form>
    </div>
    @else
    <div class="bg-indigo-50/50 border border-indigo-100 border-dashed rounded-3xl p-12 text-center mt-6">
        <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-indigo-50">
            <i data-lucide="filter" size="24" class="text-indigo-400"></i>
        </div>
        <h3 class="text-lg font-bold text-indigo-900 mb-1">Select Class & Session</h3>
        <p class="text-indigo-600/70 text-sm max-w-sm mx-auto">Use the filters above to load the student list for attendance tracking.</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Initialize sections if class is pre-selected on load
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
