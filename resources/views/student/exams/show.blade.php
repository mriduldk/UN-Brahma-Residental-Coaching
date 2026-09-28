@extends('layouts.student')

@section('title', $exam->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100">
        <div class="p-10 text-center">
            <div class="mb-6 inline-flex p-5 rounded-full bg-indigo-50 text-indigo-600">
                <i data-lucide="scroll-text" size="48"></i>
            </div>
            
            <h3 class="text-2xl font-black text-gray-900 mb-2">Exam Instructions</h3>
            <p class="text-gray-500 text-sm mb-10 max-w-lg mx-auto">{{ $exam->description ?? 'Please read the following instructions carefully before starting the exam.' }}</p>

            <div class="bg-gray-50 rounded-3xl p-8 mb-10 text-left grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white rounded-2xl shadow-sm text-indigo-500">
                        <i data-lucide="timer" size="24"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest">Duration</p>
                        <p class="text-base font-bold text-gray-800">{{ $exam->duration_minutes }} Minutes</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white rounded-2xl shadow-sm text-amber-500">
                        <i data-lucide="help-circle" size="24"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest">Total Questions</p>
                        <p class="text-base font-bold text-gray-800">{{ $exam->questions->count() }} Items</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white rounded-2xl shadow-sm text-emerald-500">
                        <i data-lucide="award" size="24"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest">Total Marks</p>
                        <p class="text-base font-bold text-gray-800">{{ $exam->total_marks }} Marks</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white rounded-2xl shadow-sm text-rose-500">
                        <i data-lucide="alert-circle" size="24"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-black text-gray-400 tracking-widest">Negative Marks</p>
                        <p class="text-base font-bold text-rose-600">-{{ $exam->negative_marks_per_wrong }} / wrong</p>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50/50 rounded-2xl p-6 text-left mb-10 border border-indigo-100">
                <h4 class="text-xs font-black text-indigo-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i data-lucide="info" size="14"></i>
                    Final Guidelines
                </h4>
                <ul class="text-sm text-indigo-700/80 space-y-3 font-medium">
                    <li class="flex items-start gap-2">
                        <i data-lucide="check" size="16" class="mt-0.5 shrink-0"></i>
                        The exam will auto-submit when the timer reaches zero.
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="check" size="16" class="mt-0.5 shrink-0"></i>
                        Do not refresh the page or navigate away during the session.
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="check" size="16" class="mt-0.5 shrink-0"></i>
                        You have used <strong>{{ $attemptCount }}</strong> out of <strong>{{ $exam->max_attempts }}</strong> permitted attempts.
                    </li>
                </ul>
            </div>

            @if(session('error'))
                <div class="mb-8 p-4 rounded-2xl bg-rose-50 text-rose-700 text-sm font-bold border border-rose-100 flex items-center gap-2">
                    <i data-lucide="x-circle" size="18"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col gap-4" x-data="{ starting: false }">
                @if($attemptCount < $exam->max_attempts)
                    <form action="{{ route('student.exams.start', $exam) }}" method="POST" @submit="starting = true">
                        @csrf
                        <button type="submit" :disabled="starting" class="w-full py-5 bg-indigo-600 rounded-2xl text-white font-black text-lg hover:bg-indigo-700 shadow-xl shadow-indigo-600/20 transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center gap-3 disabled:opacity-75 disabled:cursor-not-allowed">
                            <template x-if="!starting">
                                <span>Start Examination Now</span>
                            </template>
                            <template x-if="starting">
                                <div class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span>Preparing Exam...</span>
                                </div>
                            </template>
                        </button>
                    </form>
                @else
                    <button disabled class="w-full py-5 bg-gray-100 rounded-2xl text-gray-400 font-black text-lg cursor-not-allowed">
                        Max Attempts Reached
                    </button>
                @endif
                
                <a href="{{ route('student.exams.index') }}" class="text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest pt-2">
                    Not ready? Go back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
