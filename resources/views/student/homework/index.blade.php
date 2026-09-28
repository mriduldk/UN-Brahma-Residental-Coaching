@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <x-ui.page-header 
        title="My Homework & Assignments" 
        subtitle="View and submit your pending homework." 
    />

    <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        @forelse($homeworks as $homework)
            @php
                $submission = $homework->submissions->first();
                $isPastDue = $homework->due_date < now()->startOfDay() && (!$submission || $submission->status === 'pending');
            @endphp
            <x-ui.card animate="true" class="flex flex-col h-full {{ $isPastDue ? 'border-danger/40 bg-danger-light/10' : '' }}">
                <div class="flex justify-between items-start mb-4">
                    <x-ui.badge variant="secondary">
                        {{ $homework->subject_name }}
                    </x-ui.badge>
                    @if($submission && $submission->status === 'graded')
                        <x-ui.badge variant="success">Graded</x-ui.badge>
                    @elseif($submission && $submission->status === 'submitted')
                        <x-ui.badge variant="warning">Submitted</x-ui.badge>
                    @elseif($isPastDue)
                        <x-ui.badge variant="danger">Overdue</x-ui.badge>
                    @else
                        <x-ui.badge variant="primary">Pending</x-ui.badge>
                    @endif
                </div>
                <h3 class="text-lg font-bold text-foreground line-clamp-2 mb-4">{{ $homework->title }}</h3>
                <div class="mt-auto pt-4 space-y-2 text-xs text-muted">
                    <div class="flex items-center font-semibold uppercase tracking-wider">
                        <i data-lucide="calendar" size="14" class="mr-1.5"></i>
                        Due: <span class="{{ $isPastDue ? 'text-danger font-black ml-1' : 'ml-1 text-foreground' }}">{{ $homework->due_date->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-border">
                    <a href="{{ route('student.homework.show', $homework->id) }}" class="block w-full">
                        <x-ui.button variant="outline" class="w-full">
                            View Details
                        </x-ui.button>
                    </a>
                </div>
            </x-ui.card>
        @empty
            <div class="col-span-full">
                <x-ui.card padding="p-12" class="text-center">
                    <i data-lucide="book-open" class="mx-auto h-12 w-12 text-muted mb-4"></i>
                    <h3 class="text-lg font-bold text-foreground">No homework assigned</h3>
                    <p class="mt-2 text-sm text-muted">You're all caught up! There are no pending assignments.</p>
                </x-ui.card>
            </div>
        @endforelse
    </div>
    
    @if(isset($homeworks) && $homeworks->hasPages())
        <div class="mt-6">
            {{ $homeworks->links() }}
        </div>
    @endif
</div>
@endsection
