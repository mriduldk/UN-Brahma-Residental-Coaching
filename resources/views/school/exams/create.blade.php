@extends('layouts.school')

@section('title', 'Create New Exam')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-indigo-950 tracking-tight">Create New Exam</h1>
            <p class="text-slate-500 text-sm mt-1">Configure the main details for your upcoming examination.</p>
        </div>
        <div>
            <a href="{{ route('administration.exams.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition-all hover:shadow-md">
                <i data-lucide="arrow-left" size="18"></i>
                Back to List
            </a>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <form action="{{ route('administration.exams.store') }}" method="POST" class="p-10 space-y-8" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            
            <div class="grid grid-cols-1 gap-8">
                <!-- Basic Info -->
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Exam Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required autofocus
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all placeholder:text-slate-300"
                            placeholder="e.g. Mid-Term Mathematics 2024">
                        @error('title') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Description (Optional)</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all placeholder:text-slate-300"
                            placeholder="Instructions or details for the exam...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Targeting -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8" x-data="{ 
                    selectedClass: '{{ old('school_class_id') }}',
                    classes: {{ $classes->toJson() }},
                    get availableSections() {
                        if (!this.selectedClass) return [];
                        const cls = this.classes.find(c => c.id == this.selectedClass);
                        return cls ? cls.sections : [];
                    }
                }">
                    <div>
                        <label for="academic_year_id" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Academic Session</label>
                        <div class="relative">
                            <select name="academic_year_id" id="academic_year_id" required
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none cursor-pointer">
                                <option value="">Select Session</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="layers" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-indigo-400 pointer-events-none"></i>
                        </div>
                        @error('academic_year_id') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label for="school_class_id" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Class</label>
                        <div class="relative">
                            <select name="school_class_id" id="school_class_id" required x-model="selectedClass"
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none cursor-pointer">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            <i data-lucide="book-open" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-indigo-400 pointer-events-none"></i>
                        </div>
                        @error('school_class_id') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="school_section_id" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Section (Optional)</label>
                        <div class="relative">
                            <select name="school_section_id" id="school_section_id"
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none cursor-pointer">
                                <option value="">All Sections</option>
                                <template x-for="section in availableSections" :key="section.id">
                                    <option :value="section.id" x-text="section.name" :selected="section.id == '{{ old('school_section_id') }}'"></option>
                                </template>
                            </select>
                            <i data-lucide="hash" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-indigo-400 pointer-events-none"></i>
                        </div>
                        @error('school_section_id') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="h-px bg-slate-50"></div>

                <!-- Timing -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <!-- Start Time -->
                    <div class="space-y-4" x-data="{ 
                        date: '{{ old('start_time') ? date('Y-m-d', strtotime(old('start_time'))) : '' }}',
                        hour: '{{ old('start_time') ? date('h', strtotime(old('start_time'))) : '10' }}',
                        minute: '{{ old('start_time') ? date('i', strtotime(old('start_time'))) : '00' }}',
                        period: '{{ old('start_time') ? date('A', strtotime(old('start_time'))) : 'AM' }}',
                        get combined() {
                            if (!this.date) return '';
                            let h = parseInt(this.hour);
                            if (this.period === 'PM' && h < 12) h += 12;
                            if (this.period === 'AM' && h === 12) h = 0;
                            return `${this.date} ${h.toString().padStart(2, '0')}:${this.minute}:00`;
                        }
                    }">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Start Date & Time</label>
                        <input type="hidden" name="start_time" :value="combined">
                        
                        <div class="space-y-3">
                            <div class="relative">
                                <input type="date" x-model="date" required
                                    class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                                <i data-lucide="calendar" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-indigo-400 pointer-events-none"></i>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-3">
                                <select x-model="hour" class="px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none cursor-pointer text-center">
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                    @endfor
                                </select>
                                <select x-model="minute" class="px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none cursor-pointer text-center">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                                <select x-model="period" class="px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none cursor-pointer text-center">
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                        @error('start_time') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- End Time -->
                    <div class="space-y-4" x-data="{ 
                        date: '{{ old('end_time') ? date('Y-m-d', strtotime(old('end_time'))) : '' }}',
                        hour: '{{ old('end_time') ? date('h', strtotime(old('end_time'))) : '05' }}',
                        minute: '{{ old('end_time') ? date('i', strtotime(old('end_time'))) : '00' }}',
                        period: '{{ old('end_time') ? date('A', strtotime(old('end_time'))) : 'PM' }}',
                        get combined() {
                            if (!this.date) return '';
                            let h = parseInt(this.hour);
                            if (this.period === 'PM' && h < 12) h += 12;
                            if (this.period === 'AM' && h === 12) h = 0;
                            return `${this.date} ${h.toString().padStart(2, '0')}:${this.minute}:00`;
                        }
                    }">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">End Date & Time</label>
                        <input type="hidden" name="end_time" :value="combined">
                        
                        <div class="space-y-3">
                            <div class="relative">
                                <input type="date" x-model="date" required
                                    class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                                <i data-lucide="calendar-off" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-rose-400 pointer-events-none"></i>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-3">
                                <select x-model="hour" class="px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none cursor-pointer text-center">
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                    @endfor
                                </select>
                                <select x-model="minute" class="px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none cursor-pointer text-center">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                                <select x-model="period" class="px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none cursor-pointer text-center">
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                        @error('end_time') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="h-px bg-slate-50"></div>

                <!-- Metrics -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
                    <div>
                        <label for="duration_minutes" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Duration (Mins)</label>
                        <div class="relative">
                            <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}" required
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                            <i data-lucide="timer" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-amber-500"></i>
                        </div>
                        @error('duration_minutes') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="total_marks" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Total Marks</label>
                        <div class="relative">
                            <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', 100) }}" required
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                            <i data-lucide="award" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-indigo-500"></i>
                        </div>
                        @error('total_marks') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="passing_marks" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Passing Marks</label>
                        <div class="relative">
                            <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', 40) }}" required
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                            <i data-lucide="shield-check" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-emerald-500"></i>
                        </div>
                        @error('passing_marks') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="h-px bg-slate-50"></div>

                <!-- Logic -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label for="negative_marks_per_wrong" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Negative Marks (Optional)</label>
                        <div class="relative">
                            <input type="number" step="0.25" name="negative_marks_per_wrong" id="negative_marks_per_wrong" value="{{ old('negative_marks_per_wrong', 0) }}"
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                            <i data-lucide="minus-circle" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-rose-500"></i>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-tight ml-1">Use 0 for no negative marking</p>
                        @error('negative_marks_per_wrong') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="max_attempts" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Max Attempts Allowed</label>
                        <div class="relative">
                            <input type="number" name="max_attempts" id="max_attempts" value="{{ old('max_attempts', 1) }}" required
                                class="w-full pl-12 pr-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all">
                            <i data-lucide="refresh-cw" size="18" class="absolute left-5 top-1/2 -translate-y-1/2 text-sky-500"></i>
                        </div>
                        @error('max_attempts') <p class="text-rose-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="pt-8 mt-10 border-t border-slate-50 flex items-center justify-between">
                 <p class="text-slate-400 text-xs font-medium italic">Make sure to add questions after creating the exam.</p>
                 <div class="flex gap-4">
                    <a href="{{ route('administration.exams.index') }}" class="px-8 py-4 bg-slate-50 text-slate-600 font-black rounded-2xl hover:bg-slate-100 transition-all">Cancel</a>
                    <button type="submit" :disabled="loading" class="px-10 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 hover:shadow-indigo-600/40 disabled:opacity-70 disabled:cursor-not-allowed inline-flex items-center gap-2">
                        <span x-show="!loading">Create Exam</span>
                        <div x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Creating...</span>
                        </div>
                    </button>
                 </div>
            </div>
        </form>
    </div>
</div>
@endsection
