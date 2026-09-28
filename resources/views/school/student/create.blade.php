@extends('layouts.school')

@section('title', 'Add New Student')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Add New Student</h1>
            <p class="text-slate-500 text-sm mt-1">Create a new student profile and system user account.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('school.enrollment.index') }}" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                <i data-lucide="arrow-left" size="18"></i>
                Back to Enrollment
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <i data-lucide="alert-circle" size="20"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-8">
        <form action="{{ route('school.students.store') }}" method="POST" class="space-y-6" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Personal Details Section -->
                <div class="lg:col-span-3 pb-2 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Personal Details</h2>
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                        placeholder="e.g. John Doe">
                    @error('full_name') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone_number" value="{{ old('phone_number') }}" required 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                        placeholder="e.g. 9876543210">
                    <p class="text-[10px] text-slate-400 mt-1 font-medium tracking-wide">Must be unique (Used for their login and default password)</p>
                    @error('phone_number') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                        placeholder="e.g. john@example.com">
                    @error('email') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Academic Mapping Section -->
                <div class="lg:col-span-3 pt-4 pb-2 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Academic Mapping</h2>
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Academic Session <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <select name="academic_year_id" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm" required>
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

                <div class="lg:col-span-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Target Class <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <select name="class_id" id="classSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm" required onchange="updateSections()">
                            <option value="" disabled selected>Select Target Class</option>
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

                <div class="lg:col-span-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Target Section (Optional)</label>
                    <div class="relative group">
                        <select name="section_id" id="sectionSelect" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm">
                            <option value="" disabled selected>Select Section</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                            <i data-lucide="chevron-down" size="16"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" :disabled="isSubmitting" :class="{'opacity-75 cursor-not-allowed': isSubmitting}" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200/50 transition-all flex items-center gap-2">
                    <i data-lucide="save" size="18" x-show="!isSubmitting"></i>
                    <i data-lucide="loader-2" size="18" class="animate-spin" x-show="isSubmitting" style="display: none;"></i>
                    <span x-text="isSubmitting ? 'Creating...' : 'Create & Map Student'">Create & Map Student</span>
                </button>
            </div>
        </form>
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
