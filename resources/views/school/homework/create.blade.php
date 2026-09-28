@extends('layouts.school')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <x-ui.page-header title="{{ isset($homework) ? 'Edit Homework' : 'Assign New Homework' }}"
            subtitle="Fill in the details below to assign homework to students." />

        <x-ui.card>
            <form
                action="{{ isset($homework) ? route('school.homework.update', $homework->id) : route('school.homework.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($homework)) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Class -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Class <span
                                class="text-danger">*</span></label>
                        <x-ui.select name="class_id" id="classSelect" searchable placeholder="Select Class" required
                            onchange="updateSections()">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (old('class_id', $homework->class_id ?? '')) == $class->id ? 'selected' : '' }} data-sections='{{ json_encode($class->sections) }}'>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </x-ui.select>
                        @error('class_id') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Section -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section</label>
                        <x-ui.select name="section_id" id="sectionSelect" searchable placeholder="All Sections">
                            <option value="">All Sections</option>
                        </x-ui.select>
                        @error('section_id') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Subject -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Subject <span
                                class="text-danger">*</span></label>
                        <x-ui.input type="text" name="subject_name" required
                            value="{{ old('subject_name', $homework->subject_name ?? '') }}" />
                        @error('subject_name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Assignment Type
                            <span class="text-danger">*</span></label>
                        <x-ui.select name="type" required searchable placeholder="Select Type">
                            <option value="homework" {{ (old('type', $homework->type ?? 'homework')) == 'homework' ? 'selected' : '' }}>Homework</option>
                            <option value="assignment" {{ (old('type', $homework->type ?? '')) == 'assignment' ? 'selected' : '' }}>Assignment</option>
                        </x-ui.select>
                        @error('type') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Title <span
                            class="text-danger">*</span></label>
                    <x-ui.input type="text" name="title" required value="{{ old('title', $homework->title ?? '') }}" />
                    @error('title') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Description <span
                            class="text-danger">*</span></label>
                    <x-ui.textarea name="description" rows="4"
                        required>{{ old('description', $homework->description ?? '') }}</x-ui.textarea>
                    @error('description') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Date -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Homework Date
                            <span class="text-danger">*</span></label>
                        <x-ui.input type="date" name="homework_date" required
                            value="{{ old('homework_date', isset($homework) ? $homework->homework_date->format('Y-m-d') : date('Y-m-d')) }}" />
                        @error('homework_date') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Due Date <span
                                class="text-danger">*</span></label>
                        <x-ui.input type="date" name="due_date" required
                            value="{{ old('due_date', isset($homework) ? $homework->due_date->format('Y-m-d') : '') }}" />
                        @error('due_date') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Attachment -->
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Attachment
                        (Optional)</label>
                    <x-ui.input type="file" name="attachment"
                        class="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" />
                    @if(isset($homework) && $homework->attachment)
                        <p class="text-sm text-muted mt-2">Current attachment: <a
                                href="{{ Storage::url($homework->attachment) }}" target="_blank"
                                class="text-primary hover:underline">View File</a></p>
                    @endif
                    @error('attachment') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-border">
                    <a href="{{ route('school.homework.index') }}">
                        <x-ui.button variant="outline" type="button">
                            Cancel
                        </x-ui.button>
                    </a>
                    <x-ui.button 
                        variant="primary" 
                        type="submit"
                        x-data="{ submitting: false }"
                        @click="if ($el.form.reportValidity()) { submitting = true; setTimeout(() => { submitting = false }, 8000); }"
                        ::class="{ 'opacity-70 pointer-events-none cursor-not-allowed': submitting }"
                    >
                        <span x-show="submitting" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Saving...</span>
                        </span>
                        <span x-show="!submitting">
                            {{ isset($homework) ? 'Update Homework' : 'Assign Homework' }}
                        </span>
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection

@push('scripts')
    <script>
        function updateSections() {
            const classSelect = document.getElementById('classSelect');
            const sectionSelect = document.getElementById('sectionSelect');
            if (!classSelect || !sectionSelect) return;

            const selectedOption = classSelect.options[classSelect.selectedIndex];

            // Clear existing sections
            sectionSelect.innerHTML = '<option value="">All Sections</option>';

            if (selectedOption && selectedOption.value && selectedOption.hasAttribute('data-sections')) {
                try {
                    const sections = JSON.parse(selectedOption.getAttribute('data-sections'));
                    const selectedSection = '{{ old('section_id', $homework->section_id ?? '') }}';

                    sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.name;
                        if (selectedSection == section.id) {
                            option.selected = true;
                        }
                        sectionSelect.appendChild(option);
                    });
                } catch (e) {
                    console.error('Error parsing sections json');
                }
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', updateSections);
    </script>
@endpush