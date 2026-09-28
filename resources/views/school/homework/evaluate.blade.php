@extends('layouts.school')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <x-ui.page-header 
            title="Evaluate Submission" 
            subtitle="Student: {{ $submission->student->full_name }} | Subject: {{ $submission->homework->subject_name }}" 
            class="mb-0" 
        />
        <a href="{{ route('school.homework.show', $submission->homework_id) }}">
            <x-ui.button variant="outline" icon="arrow-left">
                Back to Details
            </x-ui.button>
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-success-light/20 border border-success/30 text-success px-4 py-3 rounded-2xl text-sm font-semibold" role="alert">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Side: Answer & Submission Details -->
        <div class="col-span-2 space-y-6">
            <x-ui.card>
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Student Submission Details</h3>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <span class="text-xs text-muted block mb-1">Homework/Assignment Title</span>
                        <span class="text-sm font-bold text-foreground block truncate" title="{{ $submission->homework->title }}">{{ $submission->homework->title }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-muted block mb-1">Submitted At</span>
                        <span class="text-sm font-bold text-foreground">
                            {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-border pt-4">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-3">Written Answer</span>
                    <div class="p-4 bg-slate-50/50 border border-border rounded-2xl text-sm text-muted leading-relaxed min-h-[180px] whitespace-pre-wrap">{!! e($submission->submission_text ?: 'No written answer provided.') !!}</div>
                </div>

                @if($submission->attachment)
                <div class="mt-6 p-4 bg-primary/5 border border-primary/10 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="paperclip" class="text-primary" size="20"></i>
                        <div>
                            <span class="text-sm font-bold text-foreground block">Attachment Uploaded</span>
                            <span class="text-xs text-muted">Click to view/download student's file</span>
                        </div>
                    </div>
                    <a href="{{ Storage::url($submission->attachment) }}" target="_blank">
                        <x-ui.button variant="primary" size="sm" icon="external-link">
                            View File
                        </x-ui.button>
                    </a>
                </div>
                @endif
            </x-ui.card>
        </div>

        <!-- Right Side: Evaluation Form -->
        <div>
            <x-ui.card>
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Evaluation Control</h3>
                <form action="{{ route('school.homework.evaluate', $submission->id) }}" method="POST">
                    @csrf
                    <!-- Status -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Status <span class="text-danger">*</span></label>
                        <x-ui.select name="status" required placeholder="Select Status">
                            <option value="submitted" {{ old('status', $submission->status) === 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="graded" {{ old('status', $submission->status) === 'graded' ? 'selected' : '' }}>Graded</option>
                            <option value="pending" {{ old('status', $submission->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        </x-ui.select>
                        @error('status') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Marks -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Marks Obtained</label>
                        <x-ui.input type="text" name="marks_obtained" placeholder="e.g. 85/100 or A+" value="{{ old('marks_obtained', $submission->marks_obtained ?? '') }}" />
                        @error('marks_obtained') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Remarks -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Remarks</label>
                        <x-ui.textarea name="remarks" rows="5" placeholder="Feedback for student...">{{ old('remarks', $submission->remarks ?? '') }}</x-ui.textarea>
                        @error('remarks') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Submit Button -->
                    <x-ui.button 
                        variant="primary" 
                        type="submit"
                        class="w-full"
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
                            Save Evaluation
                        </span>
                    </x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
