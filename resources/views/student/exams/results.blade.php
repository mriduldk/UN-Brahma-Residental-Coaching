@extends('layouts.student')

@section('title', 'Exam Performance Analysis')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Performance Summary</h2>
        <p class="text-slate-400 text-sm mt-1 uppercase font-bold tracking-widest">{{ $attempt->exam->title }}</p>
    </div>

    <!-- Score Summary Card -->
    <div class="bg-white overflow-hidden shadow-2xl shadow-indigo-100 rounded-[3rem] border border-slate-100">
        <div class="grid grid-cols-1 md:grid-cols-2">
            <div class="p-12 bg-indigo-600 text-white text-center flex flex-col justify-center relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white opacity-5 rounded-full"></div>
                <p class="text-indigo-200 text-[10px] uppercase font-black tracking-[0.3em] mb-4">Final Score</p>
                <div class="text-8xl font-black mb-4 flex items-baseline justify-center gap-1">
                    {{ $attempt->score }}
                    <span class="text-xl text-indigo-300 font-bold">/{{ $attempt->exam->total_marks }}</span>
                </div>
                
                <div class="mt-8">
                    @if($attempt->score >= $attempt->exam->passing_marks)
                        <div class="inline-flex items-center gap-2 bg-emerald-400/20 text-emerald-100 border border-emerald-400/30 px-8 py-3 rounded-2xl font-black tracking-widest text-sm">
                            <i data-lucide="check-circle" size="18"></i>
                            PASSED
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 bg-rose-400/20 text-rose-100 border border-rose-400/30 px-8 py-3 rounded-2xl font-black tracking-widest text-sm">
                            <i data-lucide="x-circle" size="18"></i>
                            FAILED
                        </div>
                    @endif
                </div>
            </div>
            <div class="p-12 flex flex-col justify-center space-y-8 bg-slate-50/50">
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Accuracy</p>
                        <p class="text-2xl font-black text-green-600">{{ $attempt->answers->where('is_correct', true)->count() }} <span class="text-xs text-slate-300">Correct</span></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Mistakes</p>
                        <p class="text-2xl font-black text-rose-500">{{ $attempt->answers->where('is_correct', false)->count() }} <span class="text-xs text-slate-300">Wrong</span></p>
                    </div>
                </div>
                <div class="h-px bg-slate-100"></div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-bold text-xs uppercase tracking-tight">Status</span>
                        <span class="text-indigo-600 font-black capitalize px-3 py-1 bg-indigo-50 rounded-lg text-xs">{{ $attempt->status }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-bold text-xs uppercase tracking-tight">Completed At</span>
                        <span class="text-slate-800 font-black text-xs">{{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y • h:i A') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Answer Review -->
    <div class="space-y-8">
        <div class="flex items-center gap-2 px-2">
            <i data-lucide="list-checks" class="text-indigo-500"></i>
            <h3 class="text-xl font-black text-slate-800">Detailed Question Review</h3>
        </div>
        
        @foreach($attempt->answers as $index => $answer)
            <div class="bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-sm group hover:border-indigo-100 transition-all">
                <div class="flex items-start mb-8">
                    <span class="w-10 h-10 bg-slate-100 text-slate-400 text-xs font-black rounded-2xl flex items-center justify-center shrink-0 mr-5">
                        {{ $index + 1 }}
                    </span>
                    <div class="text-lg text-slate-800 font-bold leading-relaxed pt-1">
                        {{ $answer->question->question_text }}
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 ml-14">
                    @foreach($answer->question->options as $option)
                        @php
                            $isUserChoice = $answer->option_id == $option->id;
                            $isCorrect = $option->is_correct;
                            
                            $styles = 'bg-slate-50 border-slate-50 text-slate-400 opacity-60';
                            if ($isUserChoice && $isCorrect) $styles = 'bg-emerald-50 border-emerald-100 text-emerald-800 font-bold ring-4 ring-emerald-50 opacity-100 shadow-sm';
                            elseif ($isUserChoice && !$isCorrect) $styles = 'bg-rose-50 border-rose-100 text-rose-800 font-bold ring-4 ring-rose-50 opacity-100';
                            elseif ($isCorrect) $styles = 'bg-emerald-50/30 border-emerald-100 border-dashed text-emerald-600 font-bold opacity-100';
                        @endphp
                        
                        <div class="p-5 rounded-2xl border flex items-center justify-between transition-all {{ $styles }}">
                            <span class="text-sm">{{ $option->option_text }}</span>
                            
                            @if($isUserChoice && $isCorrect)
                                <i data-lucide="check-circle-2" size="16" class="text-emerald-500"></i>
                            @elseif($isUserChoice && !$isCorrect)
                                <i data-lucide="x-circle" size="16" class="text-rose-500"></i>
                            @elseif($isCorrect)
                                <span class="text-[9px] font-black bg-emerald-100 px-2 py-0.5 rounded-md uppercase tracking-widest">Correct</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-16 text-center pb-20">
        <a href="{{ route('student.exams.index') }}" class="inline-flex items-center gap-3 px-10 py-5 bg-indigo-600 text-white font-black rounded-[2rem] hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-600/30 hover:scale-105">
            <i data-lucide="home" size="20"></i>
            Return to Exam Centre
        </a>
    </div>
</div>
@endsection
