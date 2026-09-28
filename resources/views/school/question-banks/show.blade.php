@extends('layouts.school')

@section('title', 'Manage Bank - ' . $questionBank->title)

@section('content')
<div class="space-y-10" x-data="{ showAddQuestion: false, showImport: false }">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('administration.question-banks.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 transition-all">
                    <i data-lucide="arrow-left" size="18"></i>
                </a>
                <h1 class="text-3xl font-black text-indigo-950 tracking-tight">{{ $questionBank->title }}</h1>
            </div>
            <p class="text-slate-500 text-sm ml-12">{{ $questionBank->description }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showImport = true" class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-600 font-black rounded-2xl hover:bg-slate-50 transition-all shadow-sm">
                <i data-lucide="upload" size="18"></i>
                Bulk Import
            </button>
            <button @click="showAddQuestion = !showAddQuestion" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20">
                <i data-lucide="plus" size="18"></i>
                Add Question
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-3xl shadow-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
            <i data-lucide="check-circle" size="20"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Add Question Form (Conditional) -->
    <div x-show="showAddQuestion" x-collapse x-cloak class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-indigo-50/20 p-10 mt-8 mb-12">
        <div class="flex items-center justify-between mb-10">
             <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <i data-lucide="file-plus-2" class="text-indigo-500"></i>
                New Question
            </h2>
            <button @click="showAddQuestion = false" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" size="24"></i>
            </button>
        </div>

        <form action="{{ route('administration.question-banks.questions.store', $questionBank) }}" method="POST" class="space-y-8" x-data="{ formLoading: false }" @submit="formLoading = true">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2 md:col-span-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Question Text</label>
                    <textarea name="question_text" rows="3" required placeholder="Type your question here..." 
                        class="w-full px-6 py-5 bg-slate-50 border-transparent rounded-[1.5rem] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-200 transition-all font-bold text-slate-700 outline-none resize-none"></textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Marks Allotted</label>
                    <input type="number" step="0.5" name="marks" required value="1" 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-[1.5rem] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-200 transition-all font-bold text-slate-700 outline-none">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Previous Year (Optional)</label>
                    <input type="text" name="previous_year" placeholder="e.g., 2023" 
                        class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-[1.5rem] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-200 transition-all font-bold text-slate-700 outline-none">
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between px-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Options & Multiple Choice</label>
                    <span class="text-[10px] font-bold text-slate-300 italic">Select the radio button for the correct answer</span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center gap-4 group">
                            <label class="cursor-pointer relative flex items-center justify-center">
                                <input type="radio" name="correct_option_index" value="{{ $i }}" class="peer sr-only" {{ $i == 0 ? 'checked' : '' }}>
                                <div class="w-8 h-8 rounded-xl border-2 border-slate-100 peer-checked:border-emerald-500 peer-checked:bg-emerald-500 flex items-center justify-center transition-all">
                                    <i data-lucide="check" size="16" class="text-white scale-0 peer-checked:scale-100 transition-transform"></i>
                                </div>
                            </label>
                            <input type="text" name="options[{{ $i }}][text]" required placeholder="Option {{ $i+1 }}" 
                                class="flex-1 px-6 py-4 bg-slate-50 border-transparent rounded-[1.25rem] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-200 transition-all font-bold text-slate-700 outline-none">
                        </div>
                    @endfor
                </div>
            </div>

            <div class="pt-6 border-t border-slate-50">
                <button type="submit" :disabled="formLoading" class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-600/20 text-lg flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
                    <template x-if="!formLoading">
                        <div class="flex items-center gap-3">
                            <i data-lucide="save" size="22"></i>
                            Add Question to Bank
                        </div>
                    </template>
                    <template x-if="formLoading">
                        <div class="flex items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving Question...
                        </div>
                    </template>
                </button>
            </div>
        </form>
    </div>

    <!-- Import Modal -->
    <div x-show="showImport" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        <div class="absolute inset-0 bg-indigo-950/40 backdrop-blur-sm" @click="showImport = false"></div>
        <div class="relative bg-white w-full max-w-lg rounded-[3rem] shadow-2xl overflow-hidden flex flex-col p-10">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black text-indigo-950 tracking-tight">Bulk Import Questions</h3>
                <button @click="showImport = false" class="p-2 hover:bg-slate-50 rounded-full transition-all text-slate-400">
                    <i data-lucide="x" size="24"></i>
                </button>
            </div>

            <form action="{{ route('administration.question-banks.questions.import', $questionBank) }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ importLoading: false }" @submit="importLoading = true">
                @csrf
                <div class="space-y-4">
                    <div class="p-6 border-2 border-dashed border-slate-100 rounded-[2rem] bg-slate-50/50 flex flex-col items-center justify-center text-center group hover:border-indigo-200 transition-all relative">
                        <input type="file" name="csv_file" required accept=".csv" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform mb-3">
                            <i data-lucide="upload-cloud" class="text-indigo-600"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-600">Click to upload or drag CSV</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Maximum size 2MB</p>
                    </div>

                    <div class="bg-indigo-50/50 rounded-2xl p-4 border border-indigo-50">
                        <div class="flex items-center gap-3 mb-2">
                            <i data-lucide="info" size="16" class="text-indigo-600"></i>
                            <span class="text-xs font-black text-indigo-900 uppercase tracking-widest">Instructions</span>
                        </div>
                        <ul class="text-[10px] space-y-1 text-indigo-700 font-medium list-disc list-inside">
                            <li>Supported format: **CSV** (Comma Separated Values)</li>
                            <li>Excel users: Save your sheet as "CSV (Comma delimited)"</li>
                            <li>Must follow the exact column structure</li>
                        </ul>
                        <a href="data:text/csv;charset=utf-8,Question Text,Marks,Option A,Option B,Option C,Option D,Correct (A/B/C/D),Prev Year? (Yes/No),Year%0AWhat is 2+2?,1,3,4,5,6,B,No," 
                           download="question_bank_template.csv"
                           class="mt-4 flex items-center justify-center gap-2 py-2 bg-white border border-indigo-100 rounded-xl text-[10px] font-black text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all uppercase tracking-widest relative z-20">
                            <i data-lucide="download" size="14"></i>
                            Download Sample Template
                        </a>
                    </div>
                </div>

                <button type="submit" :disabled="importLoading" class="w-full py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 flex items-center justify-center gap-3 disabled:opacity-70">
                    <template x-if="!importLoading">
                        <span class="flex items-center gap-2">
                            <i data-lucide="check" size="18"></i>
                            Start Import
                        </span>
                    </template>
                    <template x-if="importLoading">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </div>
                    </template>
                </button>
            </form>
        </div>
    </div>

    <!-- Questions List -->
    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <h3 class="text-xl font-black text-indigo-950 flex items-center gap-2">
                <i data-lucide="grid" class="text-indigo-500"></i>
                Available Questions
                <span class="ml-2 px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs">{{ $questionBank->questions->count() }}</span>
            </h3>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @forelse($questionBank->questions as $index => $question)
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 md:p-10 hover:shadow-md transition-all group relative overflow-hidden">
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="flex-1">
                            <div class="flex items-start gap-5 mb-8">
                                <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 font-black text-xs">
                                    {{ $index + 1 }}
                                </span>
                                <div class="text-lg text-slate-800 font-bold leading-relaxed pt-1">
                                    {{ $question->question_text }}
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 ml-14">
                                @foreach($question->options as $option)
                                    <div class="p-4 rounded-2xl border flex items-center justify-between {{ $option->is_correct ? 'bg-emerald-50 border-emerald-100 text-emerald-700 font-bold' : 'bg-slate-50 border-transparent text-slate-500' }}">
                                        <span class="text-sm">{{ $option->option_text }}</span>
                                        @if($option->is_correct)
                                            <i data-lucide="check-circle-2" size="16" class="text-emerald-500"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="w-full md:w-32 shrink-0 flex flex-col items-center md:items-end justify-center gap-6 md:border-l md:border-slate-50 md:pl-8">
                             <div class="text-center md:text-right">
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest block mb-1">Marks</span>
                                <span class="text-2xl font-black text-indigo-600">{{ (float)$question->marks }}</span>
                            </div>
                            
                            <form action="{{ route('administration.questions.destroy', $question) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 bg-rose-50 text-rose-400 hover:bg-rose-600 hover:text-white rounded-xl transition-all" onclick="return confirm('Remove this question from the bank?')">
                                    <i data-lucide="trash-2" size="20"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center bg-white rounded-[2.5rem] border border-slate-100 border-dashed">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i data-lucide="help-circle" size="32"></i>
                    </div>
                    <h3 class="text-slate-800 font-bold text-lg">No Questions Found</h3>
                    <p class="text-slate-400 text-sm mb-6">This bank is currently empty. Start adding questions above.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
