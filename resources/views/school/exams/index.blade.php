@extends('layouts.school')

@section('title', 'Exam Management')

@section('content')
<div class="space-y-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-indigo-950 tracking-tight">Exam Management</h1>
            <p class="text-slate-500 text-sm mt-1">Create and manage academic examinations for your students.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('administration.question-banks.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-2xl shadow-sm text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all hover:shadow-md hover:-translate-y-0.5">
                <i data-lucide="database" size="18" class="text-indigo-500"></i>
                Manage Question Bank
            </a>
            <a href="{{ route('administration.exams.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white hover:bg-indigo-700 transition-all hover:shadow-lg hover:-translate-y-0.5">
                <i data-lucide="plus-circle" size="18"></i>
                Create New Exam
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-3xl shadow-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
            <i data-lucide="check-circle" size="20"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if(!$setupGuide->has_question_bank || !$setupGuide->has_questions || !$setupGuide->has_exams || !$setupGuide->has_mapped_questions)
        {{-- Compact Horizontal Onboarding Guide --}}
        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-indigo-500/5 p-6 mb-8 overflow-hidden relative">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-indigo-50 rounded-full opacity-30 blur-3xl"></div>
            
            <div class="relative">
                <div class="flex items-center justify-between mb-8 px-2">
                    <div>
                        <h2 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                            <i data-lucide="compass" class="w-5 h-5 text-indigo-600"></i>
                            Exam Setup Roadmap
                        </h2>
                        <p class="text-slate-500 text-xs font-medium">Complete these steps to launch your first exam.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                    {{-- Progress Lines (Desktop Only) --}}
                    <div class="hidden md:block absolute top-6 left-[12.5%] right-[12.5%] h-0.5 bg-slate-100 -z-0"></div>

                    {{-- Step 1 --}}
                    <div class="relative z-10 flex flex-col items-center text-center group">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3 transition-all duration-300 {{ $setupGuide->has_question_bank ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white border-2 border-slate-100 text-slate-400' }}">
                            @if($setupGuide->has_question_bank)
                                <i data-lucide="check" class="w-6 h-6 font-black"></i>
                            @else
                                <span class="text-sm font-black">01</span>
                            @endif
                        </div>
                        <h3 class="text-xs font-black {{ $setupGuide->has_question_bank ? 'text-slate-400' : 'text-slate-800' }} mb-1">Create Question Bank</h3>
                        <p class="text-[10px] text-slate-400 mb-2 leading-tight max-w-[120px]">Container for your subject questions.</p>
                        @if(!$setupGuide->has_question_bank)
                            <a href="{{ route('administration.question-banks.index') }}" class="text-[10px] text-indigo-600 font-black hover:underline uppercase tracking-widest">Setup Now</a>
                        @else
                            <span class="text-[10px] text-emerald-500 font-black uppercase tracking-widest">Done</span>
                        @endif
                    </div>

                    {{-- Step 2 --}}
                    <div class="relative z-10 flex flex-col items-center text-center group">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3 transition-all duration-300 {{ $setupGuide->has_questions ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : ($setupGuide->has_question_bank ? 'bg-white border-2 border-indigo-100 text-indigo-600 shadow-md' : 'bg-slate-50 border-2 border-slate-50 text-slate-300') }}">
                            @if($setupGuide->has_questions)
                                <i data-lucide="check" class="w-6 h-6 font-black"></i>
                            @else
                                <span class="text-sm font-black">02</span>
                            @endif
                        </div>
                        <h3 class="text-xs font-black {{ $setupGuide->has_questions ? 'text-slate-400' : ($setupGuide->has_question_bank ? 'text-slate-800' : 'text-slate-400') }} mb-1">Add Questions</h3>
                        <p class="text-[10px] text-slate-400 mb-2 leading-tight max-w-[120px]">Input questions manually or via CSV in Question Bank.</p>
                        @if($setupGuide->has_question_bank && !$setupGuide->has_questions)
                            <a href="{{ route('administration.question-banks.index') }}" class="text-[10px] text-indigo-600 font-black hover:underline uppercase tracking-widest">Manage Bank</a>
                        @elseif($setupGuide->has_questions)
                            <span class="text-[10px] text-emerald-500 font-black uppercase tracking-widest">Done</span>
                        @endif
                    </div>

                    {{-- Step 3 --}}
                    <div class="relative z-10 flex flex-col items-center text-center group">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3 transition-all duration-300 {{ $setupGuide->has_exams ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : ($setupGuide->has_questions ? 'bg-white border-2 border-indigo-100 text-indigo-600 shadow-md' : 'bg-slate-50 border-2 border-slate-50 text-slate-300') }}">
                            @if($setupGuide->has_exams)
                                <i data-lucide="check" class="w-6 h-6 font-black"></i>
                            @else
                                <i data-lucide="plus" class="w-6 h-6 {{ $setupGuide->has_questions ? 'text-indigo-600' : 'text-slate-300' }}"></i>
                            @endif
                        </div>
                        <h3 class="text-xs font-black {{ $setupGuide->has_exams ? 'text-slate-400' : ($setupGuide->has_questions ? 'text-slate-800' : 'text-slate-400') }} mb-1">Config Exam</h3>
                        <p class="text-[10px] text-slate-400 mb-2 leading-tight max-w-[120px]">Set title, duration and target class.</p>
                        @if($setupGuide->has_questions && !$setupGuide->has_exams)
                            <a href="{{ route('administration.exams.create') }}" class="text-[10px] text-indigo-600 font-black hover:underline uppercase tracking-widest">Create Now</a>
                        @elseif($setupGuide->has_exams)
                            <span class="text-[10px] text-emerald-500 font-black uppercase tracking-widest">Done</span>
                        @endif
                    </div>

                    {{-- Step 4 --}}
                    <div class="relative z-10 flex flex-col items-center text-center group">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-3 transition-all duration-300 {{ $setupGuide->has_mapped_questions ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : ($setupGuide->has_exams ? 'bg-white border-2 border-indigo-100 text-indigo-600 shadow-md animate-pulse' : 'bg-slate-50 border-2 border-slate-50 text-slate-300') }}">
                            @if($setupGuide->has_mapped_questions)
                                <i data-lucide="check" class="w-6 h-6 font-black"></i>
                            @else
                                <i data-lucide="link" class="w-6 h-6 {{ $setupGuide->has_exams ? 'text-indigo-600' : 'text-slate-300' }}"></i>
                            @endif
                        </div>
                        <h3 class="text-xs font-black {{ $setupGuide->has_mapped_questions ? 'text-slate-400' : ($setupGuide->has_exams ? 'text-slate-800' : 'text-slate-400') }} mb-1">Map & Launch</h3>
                        <p class="text-[10px] text-slate-400 mb-2 leading-tight max-w-[120px]">Link question bank to your exam & notify students.</p>
                        @if($setupGuide->has_exams && !$setupGuide->has_mapped_questions)
                            <span class="text-[10px] text-indigo-600 font-black uppercase tracking-widest">Click "Questions" in the table below</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($setupGuide->has_exams)
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-50">
                            <th class="px-8 py-5">Exam Details</th>
                            <th class="px-8 py-5">Timing & Duration</th>
                            <th class="px-8 py-5 text-center">Questions</th>
                            <th class="px-8 py-5 text-center">Status</th>
                            <th class="px-8 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-50">
                        @forelse($exams as $exam)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="font-black text-slate-800 text-base leading-tight">{{ $exam->title }}</div>
                                    <div class="text-slate-400 font-medium mt-1">{{ Str::limit($exam->description, 50) }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2 text-slate-700 font-bold">
                                        <i data-lucide="calendar" size="14" class="text-indigo-400"></i>
                                        {{ $exam->start_time->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-500 text-xs mt-1 font-medium">
                                        <i data-lucide="clock" size="14" class="text-indigo-300"></i>
                                        {{ $exam->start_time->format('h:i A') }} • {{ $exam->duration_minutes }} Mins
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-flex items-center justify-center px-4 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-black">
                                        {{ $exam->questions->count() }} Questions
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @if($exam->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-xs font-black">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-xs font-black">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('administration.exams.results', $exam) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-100 transition-all border border-emerald-100/50">
                                            <i data-lucide="bar-chart-3" size="14"></i>
                                            Results
                                        </a>
                                        <a href="{{ route('administration.exams.show', $exam) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 transition-all border border-indigo-100/50">
                                            <i data-lucide="settings-2" size="14"></i>
                                            Questions
                                        </a>
                                        <div class="flex items-center gap-1 ml-2 border-l border-slate-100 pl-3">
                                            <a href="{{ route('administration.exams.edit', $exam) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" title="Edit Exam">
                                                <i data-lucide="edit-3" size="18"></i>
                                            </a>
                                            <form action="{{ route('administration.exams.destroy', $exam) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all" title="Delete Exam" onclick="return confirm('Are you sure you want to delete this exam?')">
                                                    <i data-lucide="trash-2" size="18"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i data-lucide="clipboard-list" size="32" class="text-slate-300"></i>
                                    </div>
                                    <h3 class="text-slate-800 font-bold text-lg">No Exams Yet</h3>
                                    <p class="text-slate-400 text-sm mb-6">Start by creating your first academic examination.</p>
                                    <a href="{{ route('administration.exams.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20">
                                        <i data-lucide="plus" size="18"></i>
                                        Create Exam
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($exams->hasPages())
                <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
                    {{ $exams->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
