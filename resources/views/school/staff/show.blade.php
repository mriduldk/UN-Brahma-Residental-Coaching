@extends('layouts.school')

@section('title', 'Staff Profile')

@section('content')
<div class="space-y-6 lg:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Staff Profile</h1>
            <p class="text-slate-500 text-sm mt-1">View staff details and manage their academic assignments.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('school.staff.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                <i data-lucide="arrow-left" size="16"></i>
                Directory
            </a>
            <a href="{{ route('school.staff.edit', $staff->id) }}" class="px-4 py-2 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                <i data-lucide="edit-3" size="16"></i>
                Edit Profile
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Left Column: Staff Details -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 relative">
                @if(!$staff->is_active)
                    <div class="absolute top-4 right-4 bg-red-50 text-red-600 px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider uppercase border border-red-100">Inactive</div>
                @else
                    <div class="absolute top-4 right-4 bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-md text-[10px] font-bold tracking-wider uppercase border border-emerald-100">Active</div>
                @endif
                
                <div class="flex flex-col items-center justify-center pt-4 pb-6 border-b border-slate-100">
                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center text-indigo-600 font-black text-3xl uppercase shadow-inner mb-4 ring-4 ring-white">
                        {{ substr($staff->full_name, 0, 2) }}
                    </div>
                    <h2 class="text-xl font-bold text-slate-800 text-center leading-tight">{{ $staff->full_name }}</h2>
                    <p class="text-sm font-bold text-indigo-600 mt-1 text-center">{{ $staff->designation ?? 'No Designation' }}</p>
                    <p class="text-xs text-slate-400 font-medium uppercase tracking-wider mt-1 text-center">{{ $staff->department ?? 'No Department' }}</p>
                </div>

                <div class="pt-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 shrink-0 border border-slate-100">
                            <i data-lucide="fingerprint" size="16"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Employee ID</p>
                            <p class="text-sm font-bold text-slate-700">{{ $staff->employee_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 shrink-0 border border-slate-100">
                            <i data-lucide="phone" size="16"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Phone</p>
                            <p class="text-sm font-bold text-slate-700">{{ $staff->phone_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 shrink-0 border border-slate-100">
                            <i data-lucide="mail" size="16"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Email</p>
                            <p class="text-sm font-bold text-slate-700">{{ $staff->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-red-50 rounded-3xl border border-red-100 shadow-sm overflow-hidden p-6 hidden">
                 <!-- Hidden delete functionality, could add later if needed -->
            </div>
        </div>

        <!-- Right Column: Mappings -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Assign Mapping Form -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 lg:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                        <i data-lucide="book-check" size="20"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Assign Academic Duties</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Map this staff member to a class or subject.</p>
                    </div>
                </div>

                <form action="{{ route('school.staff.map', $staff->id) }}" method="POST" class="space-y-5" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2">Academic Session <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <select name="academic_year_id" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm" required>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>{{ $year->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                    <i data-lucide="chevron-down" size="16"></i>
                                </div>
                            </div>
                            @error('academic_year_id') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2">Target Class <span class="text-red-500">*</span></label>
                            <div class="relative group">
                                <select name="class_id" id="classSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm" required onchange="updateSections()">
                                    <option value="" disabled selected>Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" data-sections='{{ json_encode($class->sections) }}'>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                    <i data-lucide="chevron-down" size="16"></i>
                                </div>
                            </div>
                            @error('class_id') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2">Target Section</label>
                            <div class="relative group">
                                <select name="section_id" id="sectionSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-2.5 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm">
                                    <option value="" disabled selected>Select Section</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                    <i data-lucide="chevron-down" size="16"></i>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest mb-2">Subject Name</label>
                            <input type="text" name="subject_name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-slate-700 font-medium focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm" placeholder="e.g. Mathematics, English">
                            <p class="text-[10px] text-slate-400 mt-1 font-medium tracking-wide">Leave blank if mapping as a purely Class Teacher/Administrator.</p>
                            @error('subject_name') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-not-allowed': isSubmitting}" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-lg transition-all flex items-center gap-2 text-sm">
                            <i data-lucide="plus-circle" size="16" x-show="!isSubmitting"></i>
                            <i data-lucide="loader-2" size="16" class="animate-spin" x-show="isSubmitting" style="display: none;"></i>
                            <span x-text="isSubmitting ? 'Assigning...' : 'Assign Class/Subject'">Assign Class/Subject</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Mappings List -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <i data-lucide="layers" size="16"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-800 tracking-tight">Current Assignments</h2>
                </div>
                
                @if($mappings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-50 bg-slate-50/50">
                                <th class="px-6 py-4">Session</th>
                                <th class="px-6 py-4">Assignment</th>
                                <th class="px-6 py-4">Subject</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-50">
                            @foreach($mappings as $mapping)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-bold tracking-wider border border-slate-200">{{ $mapping->academicYear->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $mapping->schoolClass->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ $mapping->section ? 'Section ' . $mapping->section->name : 'All Sections' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($mapping->subject_name)
                                        <span class="font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg text-xs">{{ $mapping->subject_name }}</span>
                                    @else
                                        <span class="text-xs text-slate-400 font-medium italic">Class Assignee</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('school.staff.mapping.destroy', $mapping->id) }}" method="POST" onsubmit="return confirm('Remove this assignment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-colors" title="Remove Assignment">
                                            <i data-lucide="trash-2" size="16"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-12 text-center text-slate-400">
                    <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="book-dashed" size="24" class="text-slate-300"></i>
                    </div>
                    <p class="font-bold text-slate-600 text-base">No Assignments Yet</p>
                    <p class="text-xs mt-1">Use the form above to assign subjects and classes.</p>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateSections() {
        const classSelect = document.getElementById('classSelect');
        const sectionSelect = document.getElementById('sectionSelect');
        const selectedOption = classSelect.options[classSelect.selectedIndex];
        
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        
        if (selectedOption.value && selectedOption.hasAttribute('data-sections')) {
            try {
                const sections = JSON.parse(selectedOption.getAttribute('data-sections'));
                sections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.name;
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
