@extends('layouts.school')

@section('title', 'Attempt Analysis - ' . $attempt->student->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-10">
    <!-- Header with Breadcrumbs -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="space-y-1">
            <div class="flex items-center gap-3 text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">
                <a href="{{ route('administration.exams.index') }}" class="hover:text-indigo-600 transition-colors">Exams</a>
                <i data-lucide="chevron-right" size="12"></i>
                <a href="{{ route('administration.exams.results', $attempt->exam_id) }}" class="hover:text-indigo-600 transition-colors">Results</a>
                <i data-lucide="chevron-right" size="12"></i>
                <span class="text-slate-600">Attempt Analysis</span>
            </div>
            <h1 class="text-3xl font-black text-indigo-950 tracking-tight">Student Response Review</h1>
            <p class="text-slate-500 text-sm font-medium">Detailed evaluation for <span class="text-indigo-600 font-bold">{{ $attempt->student->name }}</span> • {{ $attempt->exam->title }}</p>
        </div>
        
        <div class="flex items-center gap-3">
             <div class="px-6 py-3 bg-white border border-slate-200 rounded-2xl shadow-sm text-center min-w-[120px]">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Final Score</p>
                <p class="text-xl font-black {{ $attempt->score >= $attempt->exam->passing_marks ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $attempt->score }} <span class="text-slate-300 text-xs">/ {{ $attempt->exam->total_marks }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Performance Summary Card -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-indigo-50/50 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3">
            <div class="p-10 bg-slate-50/50 border-r border-slate-100 flex flex-col justify-center space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-indigo-600">
                        <i data-lucide="user" size="24"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Student</p>
                        <p class="text-base font-black text-slate-800">{{ $attempt->student->name }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-emerald-600">
                         <i data-lucide="calendar-check" size="24"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Completed On</p>
                        <p class="text-base font-black text-slate-800">{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'Ongoing' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="p-10 md:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-8 items-center">
                <div class="text-center md:text-left">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Duration</p>
                    <p class="text-xl font-black text-slate-800">
                        {{ $attempt->completed_at ? $attempt->completed_at->diffForHumans($attempt->started_at, true) : 'N/A' }}
                    </p>
                    <p class="text-[10px] text-slate-400 font-bold mt-1">Total time spent</p>
                </div>
                
                <div class="text-center md:text-left">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Accuracy</p>
                    <div class="flex items-center justify-center md:justify-start gap-2">
                        <span class="text-xl font-black text-emerald-600">{{ $attempt->answers->where('is_correct', true)->count() }}</span>
                        <span class="text-slate-300 font-bold">/</span>
                        <span class="text-slate-500 font-bold">{{ $attempt->exam->questions->count() }}</span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-bold mt-1">Correct answers</p>
                </div>

                <div class="text-center md:text-left">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Result Status</p>
                    @if($attempt->score >= $attempt->exam->passing_marks)
                        <span class="inline-flex px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest">Qualified</span>
                    @else
                        <span class="inline-flex px-4 py-1.5 bg-rose-50 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-widest">Not Qualified</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Question-by-Question Review -->
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <h3 class="text-xl font-black text-indigo-950 flex items-center gap-2">
                <i data-lucide="list-checks" class="text-indigo-500"></i>
                Detailed Question Matrix
            </h3>
            <div class="flex gap-4">
                <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div> Correct
                </div>
                <div class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <div class="w-2 h-2 rounded-full bg-rose-500"></div> Incorrect
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @foreach($attempt->answers as $index => $answer)
                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-slate-100 shadow-sm transition-all hover:shadow-md relative overflow-hidden group">
                    <!-- Status Indicator Strip -->
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $answer->is_correct ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                    
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="flex-1">
                            <div class="flex items-start gap-4 mb-8">
                                <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 font-black text-xs">
                                    {{ $index + 1 }}
                                </span>
                                <div class="text-lg text-slate-800 font-bold leading-relaxed pt-1">
                                    {{ $answer->question->question_text }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 ml-14">
                                @foreach($answer->question->options as $option)
                                    @php
                                        $isStudentChoice = $answer->option_id == $option->id;
                                        $isCorrect = $option->is_correct;
                                        
                                        $boxStyle = 'bg-slate-50/50 border-slate-100 text-slate-400 opacity-60';
                                        if ($isStudentChoice && $isCorrect) $boxStyle = 'bg-emerald-50 border-emerald-500 text-emerald-900 font-bold opacity-100 shadow-sm ring-4 ring-emerald-50';
                                        elseif ($isStudentChoice && !$isCorrect) $boxStyle = 'bg-rose-50 border-rose-500 text-rose-900 font-bold opacity-100 ring-4 ring-rose-50';
                                        elseif ($isCorrect) $boxStyle = 'bg-emerald-50/30 border-emerald-200 border-dashed text-emerald-700 font-bold opacity-100';
                                    @endphp
                                    
                                    <div class="p-4 rounded-2xl border flex items-center justify-between {{ $boxStyle }}">
                                        <span class="text-sm">{{ $option->option_text }}</span>
                                        @if($isStudentChoice && $isCorrect)
                                            <i data-lucide="check-circle-2" size="16" class="text-emerald-600"></i>
                                        @elseif($isStudentChoice && !$isCorrect)
                                            <i data-lucide="x-circle" size="16" class="text-rose-600"></i>
                                        @elseif($isCorrect)
                                            <span class="text-[8px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded">Correct Key</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="w-full md:w-48 shrink-0 flex flex-col justify-center items-center md:items-end gap-3 md:border-l md:border-slate-50 md:pl-8">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Question Pts</p>
                            <div class="text-center md:text-right">
                                <p class="text-3xl font-black {{ $answer->is_correct ? 'text-emerald-600' : 'text-slate-200' }}">
                                    {{ $answer->is_correct ? $answer->question->marks : '0' }}
                                </p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1">Out of {{ $answer->question->marks }}</p>
                            </div>
                            
                            @if($answer->is_correct)
                                <div class="mt-4 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                    <i data-lucide="award" size="14"></i>
                                    Awarded
                                </div>
                            @else
                                <div class="mt-4 px-4 py-2 bg-rose-50 text-rose-400 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                    <i data-lucide="minus-circle" size="14"></i>
                                    Negative
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Back Action -->
    <div class="text-center pb-20">
        <a href="{{ route('administration.exams.results', $attempt->exam_id) }}" class="inline-flex items-center gap-3 px-10 py-5 bg-white border-2 border-slate-100 text-slate-600 font-black rounded-[2rem] hover:border-indigo-600 hover:text-indigo-600 transition-all shadow-lg hover:shadow-indigo-100/50">
            <i data-lucide="arrow-left" size="20"></i>
            Back to All Results
        </a>
    </div>
</div>
@endsection
