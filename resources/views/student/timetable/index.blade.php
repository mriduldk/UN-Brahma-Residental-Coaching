@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <x-ui.page-header 
            title="Class Timetable" 
            subtitle="Weekly schedule and room assignments for your enrolled class" 
            class="mb-0" 
        />
        
        @if(isset($enrollment))
            <div class="flex items-center gap-2 px-4 py-2 bg-primary/5 border border-primary/10 rounded-2xl">
                <i data-lucide="graduation-cap" class="text-primary shrink-0" size="18"></i>
                <span class="text-sm font-bold text-foreground">
                    {{ $enrollment->schoolClass->name }} 
                    @if($enrollment->section)
                        - {{ $enrollment->section->name }}
                    @endif
                </span>
            </div>
        @endif
    </div>

    <!-- Main Container in Alpine.js -->
    <div x-data="{ activeDay: '{{ date('l') === 'Sunday' ? 'Monday' : date('l') }}' }" class="space-y-6">
        
        <!-- Day Navigation Tabs -->
        <div class="flex flex-wrap gap-2 pb-2 border-b border-border">
            @foreach($daysOfWeek as $day)
                @php
                    $hasClasses = isset($timetableByDay[$day]) && $timetableByDay[$day]->isNotEmpty();
                @endphp
                <button 
                    @click="activeDay = '{{ $day }}'"
                    class="px-5 py-2.5 rounded-2xl text-sm font-bold transition-all duration-200 flex items-center gap-2 border"
                    :class="activeDay === '{{ $day }}' 
                        ? 'bg-primary border-primary text-white shadow-md shadow-primary/10' 
                        : 'bg-surface border-border text-muted hover:text-foreground hover:border-primary/20'"
                >
                    <span>{{ $day }}</span>
                    @if($hasClasses)
                        <span class="w-1.5 h-1.5 rounded-full" 
                            :class="activeDay === '{{ $day }}' ? 'bg-white' : 'bg-primary'"
                        ></span>
                    @endif
                </button>
            @endforeach
        </div>

        <!-- Schedule Contents -->
        @foreach($daysOfWeek as $day)
            @php
                $periods = $timetableByDay[$day] ?? collect();
            @endphp
            
            <div x-show="activeDay === '{{ $day }}'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                @if($periods->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 bg-surface border border-border rounded-3xl text-center p-8 shadow-sm">
                        <div class="w-16 h-16 bg-primary/5 rounded-full flex items-center justify-center mb-4 text-primary">
                            <i data-lucide="party-popper" size="32"></i>
                        </div>
                        <h3 class="text-lg font-bold text-foreground mb-1">No classes scheduled!</h3>
                        <p class="text-sm text-muted max-w-sm">There are no lectures or periods scheduled for {{ $day }}. Rest and recharge!</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($periods as $period)
                            <div class="bg-surface border border-border hover:border-primary/20 transition-all duration-300 rounded-3xl p-5 shadow-sm hover:shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <!-- Time & Subject Info -->
                                <div class="flex items-start gap-4">
                                    <!-- Time Block -->
                                    <div class="w-28 py-2 px-3 bg-primary/5 border border-primary/10 rounded-2xl shrink-0 text-center flex flex-col justify-center">
                                        <span class="text-xs font-bold text-primary block leading-none mb-1">
                                            {{ date('h:i A', strtotime($period->start_time)) }}
                                        </span>
                                        <span class="text-[10px] text-muted block leading-none">
                                            to {{ date('h:i A', strtotime($period->end_time)) }}
                                        </span>
                                    </div>

                                    <!-- Subject Details -->
                                    <div class="space-y-1">
                                        <h4 class="text-base font-extrabold text-foreground leading-snug">
                                            {{ $period->subject_name ?: 'General Lecture' }}
                                        </h4>
                                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-muted">
                                            @if($period->staff)
                                                <span class="flex items-center gap-1">
                                                    <i data-lucide="user" size="14"></i>
                                                    {{ $period->staff->full_name }}
                                                </span>
                                            @endif
                                            @if($period->room_number)
                                                <span class="flex items-center gap-1">
                                                    <i data-lucide="map-pin" size="14"></i>
                                                    Room {{ $period->room_number }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div class="shrink-0 flex items-center justify-end sm:justify-start">
                                    <x-ui.badge variant="success" size="sm">
                                        Active
                                    </x-ui.badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
