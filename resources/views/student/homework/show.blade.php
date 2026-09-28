@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <x-ui.page-header 
            title="{{ $homework->title }}" 
            subtitle="Subject: {{ $homework->subject_name }} | Due: {{ $homework->due_date->format('M d, Y') }}" 
            class="mb-0" 
        />
        <a href="{{ route('student.homework.index') }}">
            <x-ui.button variant="outline" icon="arrow-left">
                Back
            </x-ui.button>
        </a>
    </div>

    <x-ui.card class="mb-8">
        <h3 class="text-lg font-bold text-foreground mb-4">Description</h3>
        <div class="prose max-w-none text-muted leading-relaxed text-sm">
            {!! nl2br(e($homework->description)) !!}
        </div>
        
        @if($homework->attachment)
        <div class="mt-6 pt-4 border-t border-border flex items-center gap-4">
            <span class="text-sm font-semibold text-foreground">Attachment:</span>
            <a href="{{ Storage::url($homework->attachment) }}" target="_blank">
                <x-ui.button variant="primary" size="sm" icon="download">
                    Download File
                </x-ui.button>
            </a>
        </div>
        @endif
    </x-ui.card>

    <x-ui.card>
        <h3 class="text-lg font-bold text-foreground mb-4">Your Submission</h3>

        @if(session('success'))
            <div class="mb-4 bg-success-light/20 border border-success/30 text-success px-4 py-3 rounded-2xl text-sm font-semibold" role="alert">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-danger-light/20 border border-danger/30 text-danger px-4 py-3 rounded-2xl text-sm" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($submission && $submission->status === 'graded')
            <div class="bg-slate-50/50 border border-border rounded-2xl p-5">
                <div class="flex justify-between items-start mb-4">
                    <x-ui.badge variant="success">Graded</x-ui.badge>
                    <span class="text-sm font-bold text-foreground">Marks: {{ $submission->marks_obtained ?? 'N/A' }}</span>
                </div>
                @if($submission->remarks)
                    <div class="mb-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Teacher's Remarks</p>
                        <p class="text-sm text-muted bg-surface p-4 border border-border rounded-2xl leading-relaxed">{{ $submission->remarks }}</p>
                    </div>
                @endif
                <div class="mt-4 border-t border-border pt-4">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Your Answer:</p>
                    <div class="text-sm text-muted leading-relaxed">
                        {!! nl2br(e($submission->submission_text)) !!}
                    </div>
                    @if($submission->attachment)
                        <div class="mt-4">
                            <a href="{{ Storage::url($submission->attachment) }}" target="_blank" class="inline-flex items-center text-sm text-primary hover:text-primary-hover font-bold">
                                <i data-lucide="paperclip" size="16" class="mr-1"></i> View Submitted Attachment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <form action="{{ route('student.homework.submit', $homework->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Write your answer</label>
                    <x-ui.textarea name="submission_text" rows="5" placeholder="Type your answer here...">{{ old('submission_text', $submission->submission_text ?? '') }}</x-ui.textarea>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Attach a file (Optional)</label>
                    <x-ui.input type="file" name="attachment" class="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" />
                    @if($submission && $submission->attachment)
                        <p class="text-sm text-muted mt-2">Current attachment: <a href="{{ Storage::url($submission->attachment) }}" target="_blank" class="text-primary hover:underline">View File</a></p>
                    @endif
                </div>
                <div class="flex justify-end">
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
                            <span>Submitting...</span>
                        </span>
                        <span x-show="!submitting">
                            {{ $submission ? 'Update Submission' : 'Submit Homework' }}
                        </span>
                    </x-ui.button>
                </div>
            </form>
        @endif
    </x-ui.card>
</div>
@endsection
