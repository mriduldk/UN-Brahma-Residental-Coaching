@extends('layouts.school')

@section('title', 'Student Enrollment Mapping')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Student Enrollment Mapping</h1>
            <p class="text-slate-500 text-sm mt-1">Assign admitted students to their respective classes and sections.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('school.students.create') }}" class="px-4 py-2 bg-indigo-600 border border-transparent hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <i data-lucide="user-plus" size="14"></i>
                Add New Student
            </a>
            <a href="{{ route('school.enrollment.mapped') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <i data-lucide="users" size="14"></i>
                View Mapped Students
            </a>
            
            @if(!$activeYear)
                <div class="px-4 py-2 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-xs font-bold flex items-center gap-2">
                    <i data-lucide="alert-triangle" size="14"></i>
                    No Active Session Found
                </div>
            @else
                <div class="px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-xl text-indigo-700 text-xs font-bold flex items-center gap-2">
                    <i data-lucide="calendar" size="14"></i>
                    Active Session: {{ $activeYear->name }}
                </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <i data-lucide="check-circle" size="20"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('school.enrollment.map') }}" method="POST" id="enrollmentForm" onsubmit="return handleFormSubmit(this)">
        @csrf
        <div class="space-y-6">
            
            <!-- Filter & Selection Bar -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-end gap-6">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Academic Session</label>
                        <div class="relative group">
                            <select name="academic_year_id" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-2xl px-5 py-3 pr-12 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm" required>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>{{ $year->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                <i data-lucide="chevron-down" size="18"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Target Class</label>
                        <div class="relative group">
                            <select name="class_id" id="classSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-2xl px-5 py-3 pr-12 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm" required onchange="updateSections()">
                                <option value="" disabled selected>Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" data-sections='{{ json_encode($class->sections) }}'>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                <i data-lucide="chevron-down" size="18"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 px-1">Target Section (Optional)</label>
                        <div class="relative group">
                            <select name="section_id" id="sectionSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-2xl px-5 py-3 pr-12 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm">
                                <option value="" disabled selected>Select Section</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                <i data-lucide="chevron-down" size="18"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex-none lg:w-72">
                        <button type="submit" class="w-full h-[52px] bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2 group">
                            <i data-lucide="user-plus" size="20" class="transition-transform group-hover:scale-110"></i>
                            Map Selected Students
                        </button>
                    </div>
                </div>
            </div>

            <!-- Student List Table -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h3 class="font-bold text-slate-800">
                            {{ $source === 'enrolled' ? 'Previously Enrolled Students' : 'New Applications' }} ({{ $eligibleStudents->count() }})
                        </h3>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex bg-slate-100 p-1 rounded-xl">
                                <a href="{{ route('school.enrollment.index', ['source' => 'application']) }}" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $source === 'application' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                                    From Application
                                </a>
                                <a href="{{ route('school.enrollment.index', ['source' => 'enrolled']) }}" class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $source === 'enrolled' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                                    From Enrolled
                                </a>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 transition-colors">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" onclick="toggleAll(this)">
                                <span class="text-xs font-bold text-slate-600">Select All</span>
                            </label>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-50">
                                    <th class="px-6 py-4 w-12"></th>
                                    <th class="px-6 py-4">Student Name</th>
                                    <th class="px-6 py-4">Application Code</th>
                                    <th class="px-6 py-4">Trade/Department</th>
                                    <th class="px-6 py-4 text-center">Date</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-50">
                                @forelse($eligibleStudents as $admission)
                                <tr class="group hover:bg-indigo-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <input type="checkbox" name="student_ids[]" value="{{ $admission->student_id }}" class="student-checkbox w-5 h-5 rounded-lg border-slate-300 text-indigo-600 focus:ring-indigo-500 transition-all">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 font-bold text-xs uppercase">
                                                {{ substr($admission->student->full_name ?? '?', 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-800 leading-tight uppercase">{{ $admission->student->full_name }}</div>
                                                <div class="text-[10px] text-slate-400 font-medium">Mobile: {{ $admission->student->mobile }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold font-mono">{{ $admission->application_code ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-slate-600 uppercase">{{ $admission->application_form->course_name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-slate-400 text-xs">
                                        {{ $admission->created_at->format('d M, Y') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center text-slate-400">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i data-lucide="user-x" size="32"></i>
                                        </div>
                                        <p class="font-bold text-slate-600">No Eligible Students Found</p>
                                        <p class="text-xs mt-1">All admitted students have already been mapped or no admissions found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function updateSections() {
        const classSelect = document.getElementById('classSelect');
        const sectionSelect = document.getElementById('sectionSelect');
        const selectedOption = classSelect.options[classSelect.selectedIndex];
        
        // Clear existing sections
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        
        if (selectedOption.value) {
            const sections = JSON.parse(selectedOption.getAttribute('data-sections'));
            sections.forEach(section => {
                const option = document.createElement('option');
                option.value = section.id;
                option.textContent = section.name;
                sectionSelect.appendChild(option);
            });
        }
    }

    function toggleAll(source) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }

    function handleFormSubmit(form) {
        try {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn) return true;

            // Disable button
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            
            // Add spinner and text
            submitBtn.innerHTML = `
                <div class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-bold uppercase tracking-widest text-xs">Processing Mapping...</span>
                </div>
            `;
        } catch (e) {
            console.error('Loading state failed:', e);
        }
        
        return true;
    }
</script>
@endpush
@endsection
