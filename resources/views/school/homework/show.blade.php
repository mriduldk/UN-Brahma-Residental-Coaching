@extends('layouts.school')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <x-ui.page-header 
            title="{{ $homework->title }}" 
            subtitle="Type: {{ ucfirst($homework->type ?? 'homework') }} | Class: {{ $homework->schoolClass->name }} {{ $homework->section ? '| Section: '.$homework->section->name : '' }} | Subject: {{ $homework->subject_name }}" 
            class="mb-0" 
        />
        <a href="{{ route('school.homework.index') }}">
            <x-ui.button variant="outline" icon="arrow-left">
                Back to Homework List
            </x-ui.button>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="col-span-2">
            <x-ui.card>
                <h3 class="text-lg font-bold text-foreground mb-4">Description</h3>
                <div class="prose max-w-none text-muted leading-relaxed text-sm">
                    {!! nl2br(e($homework->description)) !!}
                </div>
                
                @if($homework->attachment)
                <div class="mt-6 p-4 bg-slate-50/50 border border-border rounded-2xl flex items-center justify-between">
                    <span class="text-sm font-semibold text-foreground">Attachment Provided</span>
                    <a href="{{ Storage::url($homework->attachment) }}" target="_blank">
                        <x-ui.button variant="primary" size="sm" icon="download">
                            View Attachment
                        </x-ui.button>
                    </a>
                </div>
                @endif
            </x-ui.card>
        </div>

        <div>
            <x-ui.card class="h-fit">
                <h3 class="text-lg font-bold text-foreground mb-4">Details</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Type</dt>
                        <dd class="mt-1.5">
                            @if(($homework->type ?? 'homework') === 'assignment')
                                <x-ui.badge variant="primary">Assignment</x-ui.badge>
                            @else
                                <x-ui.badge variant="secondary">Homework</x-ui.badge>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Homework Date</dt>
                        <dd class="mt-1.5 text-sm font-semibold text-foreground">{{ $homework->homework_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Due Date</dt>
                        <dd class="mt-1.5 text-sm font-semibold text-foreground">{{ $homework->due_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Created By</dt>
                        <dd class="mt-1.5 text-sm font-semibold text-foreground">{{ $homework->creator ? $homework->creator->name : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status</dt>
                        <dd class="mt-1.5">
                            @if($homework->due_date < now())
                                <x-ui.badge variant="danger">Past Due</x-ui.badge>
                            @else
                                <x-ui.badge variant="success">Active</x-ui.badge>
                            @endif
                        </dd>
                    </div>
                </dl>
            </x-ui.card>
        </div>
    </div>

    <!-- Submissions List -->
    <x-ui.card padding="p-0" class="overflow-hidden">
        <div class="px-6 py-4 border-b border-border">
            <h3 class="text-lg font-bold text-foreground">Student Submissions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Submitted At</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Marks</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($homework->submissions as $submission)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-foreground">{{ $submission->student->full_name }}</div>
                                <div class="text-xs text-muted font-medium mt-0.5">{{ $submission->student->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($submission->status === 'submitted')
                                    <x-ui.badge variant="warning">Submitted</x-ui.badge>
                                @elseif($submission->status === 'graded')
                                    <x-ui.badge variant="success">Graded</x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary">Pending</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-muted font-medium">
                                {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-foreground">
                                {{ $submission->marks_obtained ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('school.homework.evaluate.show', $submission->id) }}">
                                    <x-ui.button variant="ghost" size="sm">
                                        Evaluate
                                    </x-ui.button>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 whitespace-nowrap text-center text-sm text-muted font-medium">
                                No submissions yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</div>
@endsection
