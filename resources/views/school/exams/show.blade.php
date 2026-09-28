@extends('layouts.school')

@section('title', 'Manage Questions')

@section('content')
<div class="space-y-8" x-data="{ 
    showModal: false,
    selectedBank: '{{ $exam->question_bank_id }}',
    loading: false,
    search: '',
    bankQuestions: [],
    selectedIds: [],
    mappedQuestionIds: {{ $exam->questions->pluck('id')->toJson() }},
    async loadQuestions() {
        if (!this.selectedBank) return;
        this.loading = true;
        try {
            const response = await fetch(`/administration/question-banks/${this.selectedBank}/questions-json`);
            this.bankQuestions = await response.json();
            this.$nextTick(() => { 
                if (typeof lucide !== 'undefined') lucide.createIcons(); 
            });
        } catch (e) {
            console.error('Failed to fetch questions');
        }
        this.loading = false;
    },
    get filteredQuestions() {
        return this.bankQuestions.filter(q => q.question_text.toLowerCase().includes(this.search.toLowerCase()));
    },
    toggleAll() {
        if (this.selectedIds.length === this.filteredQuestions.length) {
            this.selectedIds = [];
        } else {
            this.selectedIds = this.filteredQuestions.map(q => q.id);
        }
    },
    init() {
        this.$watch('showModal', value => {
            if (value && this.bankQuestions.length === 0) this.loadQuestions();
        });
    }
}">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-indigo-950 tracking-tight">{{ $exam->title }}</h1>
            <p class="text-slate-500 text-sm mt-1 flex items-center gap-2">
                <i data-lucide="help-circle" size="16"></i>
                Build your question bank for this examination.
            </p>
        </div>
        <div>
            <a href="{{ route('administration.exams.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition-all hover:shadow-md">
                <i data-lucide="arrow-left" size="18"></i>
                Back to Exams
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-3xl shadow-sm flex items-center gap-3">
            <i data-lucide="check-circle" size="20"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-100 text-rose-700 px-6 py-4 rounded-3xl shadow-sm">
            <ul class="list-disc list-inside font-bold text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Question Bank Mapping Section -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="p-8 space-y-6">
            <h3 class="text-lg font-black text-indigo-950 flex items-center gap-2">
                <i data-lucide="database" class="text-indigo-500"></i>
                Question Bank Mapping
            </h3>
            
            <form action="{{ route('administration.exams.questions.link', $exam) }}" method="POST" class="space-y-6" x-data="{ bulkLoading: false }" @submit="bulkLoading = true">
                @csrf
                <div>
                    <label for="question_bank_id" class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Select Source Bank</label>
                    <select name="question_bank_id" id="question_bank_id" x-model="selectedBank" @change="bankQuestions = []; selectedIds = []" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-800 font-bold focus:ring-4 focus:ring-indigo-100 transition-all outline-none">
                        <option value="">-- Choose a Bank --</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->title }} ({{ $bank->questions_count }} Qs)</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="selectedBank" class="space-y-4 animate-in fade-in slide-in-from-top-2">
                    <p class="text-[10px] font-bold text-slate-400 italic">Mapping questions will link them from the bank to this exam.</p>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" @click="showModal = true" class="flex-1 py-3 bg-slate-100 text-slate-600 font-black rounded-xl hover:bg-slate-200 transition-all text-sm">
                            Select Individual Questions from Bank
                        </button>
                        <button type="submit" :disabled="bulkLoading" class="flex-1 py-3 bg-indigo-50 text-indigo-600 font-black rounded-xl hover:bg-indigo-100 transition-all text-sm flex items-center justify-center gap-2">
                            <template x-if="!bulkLoading">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="zap" size="16"></i>
                                    Map All Questions from Bank
                                </span>
                            </template>
                            <template x-if="bulkLoading">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Mapping All...
                                </span>
                            </template>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Questions List Section -->
    <div class="w-full">
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-indigo-950 flex items-center gap-2">
                        <i data-lucide="layers" class="text-indigo-500"></i>
                        Question Inventory
                    </h3>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-tight">Count</p>
                            <p class="text-lg font-black text-slate-800">{{ $exam->questions->count() }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-tight">Marks</p>
                            <p class="text-lg font-black {{ $exam->questions->sum('marks') > $exam->total_marks ? 'text-rose-500' : 'text-indigo-600' }}">
                                {{ $exam->questions->sum('marks') }}/{{ $exam->total_marks }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    @forelse($exam->questions as $index => $question)
                        <div class="p-6 bg-slate-50/50 border border-slate-100 rounded-3xl group hover:border-indigo-100 hover:bg-white transition-all">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-start gap-4">
                                    <span class="w-8 h-8 bg-indigo-900 text-white text-xs font-black rounded-full flex items-center justify-center shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="space-y-1">
                                        <p class="text-slate-800 font-bold leading-relaxed">{{ $question->question_text }}</p>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md uppercase tracking-wider">{{ (float)$question->marks }} Points</span>
                                            @if($question->previous_year)
                                                <span class="text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md uppercase tracking-wider">PYQ • {{ $question->previous_year }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('administration.questions.destroy', $question) }}" method="POST" class="inline delete-question-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="p-2 text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all" onclick="confirmDelete(this)">
                                        <i data-lucide="trash-2" size="18"></i>
                                    </button>
                                </form>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-12">
                                @foreach($question->options as $option)
                                    <div class="flex items-center gap-3 p-3 rounded-2xl text-sm border {{ $option->is_correct ? 'bg-emerald-50 border-emerald-100 text-emerald-800 font-bold shadow-sm' : 'bg-white border-slate-100 text-slate-500 font-medium' }}">
                                        @if($option->is_correct)
                                            <i data-lucide="check-circle-2" size="16" class="text-emerald-500"></i>
                                        @else
                                            <div class="w-1.5 h-1.5 rounded-full bg-slate-300 mx-1.5"></div>
                                        @endif
                                        {{ $option->option_text }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16 border-2 border-dashed border-slate-100 rounded-[2.5rem]">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="file-question" size="30" class="text-slate-300"></i>
                            </div>
                            <h4 class="text-slate-800 font-bold">The inventory is empty</h4>
                            <p class="text-slate-400 text-sm">Start mapping questions from a bank to generate the examination.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Question Selection Modal (Alpine.js) -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak>
        
        <div class="absolute inset-0 bg-indigo-950/40 backdrop-blur-sm" @click="showModal = false"></div>
        
        <div class="relative bg-white w-full max-w-4xl max-h-[85vh] rounded-[3rem] shadow-2xl overflow-hidden flex flex-col animate-in zoom-in-95 duration-300">
            
            <!-- Modal Header -->
            <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-2xl font-black text-indigo-950 tracking-tight">Select Questions from Bank</h3>
                    <p class="text-slate-400 text-xs font-bold mt-1 uppercase tracking-widest">Choose the assets you wish to map to this examination</p>
                </div>
                <button @click="showModal = false" class="p-3 bg-white border border-slate-200 rounded-full text-slate-400 hover:text-indigo-600 transition-all shadow-sm">
                    <i data-lucide="x" size="24"></i>
                </button>
            </div>

            <!-- Modal Search & Actions -->
            <div class="px-8 py-4 bg-white border-b border-slate-50 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <div class="relative flex-1 w-full">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300" size="18"></i>
                    <input type="text" x-model="search" placeholder="Search questions in this bank..." 
                        class="w-full pl-12 pr-6 py-3 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-50 transition-all font-bold text-sm text-slate-600 outline-none">
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button @click="toggleAll()" type="button" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 transition-all">
                        <span x-text="selectedIds.length === filteredQuestions.length ? 'Deselect All' : 'Select All'"></span>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-8 space-y-4 scrollbar-hide">
                <template x-if="loading">
                    <div class="py-20 text-center animate-pulse">
                        <div class="w-12 h-12 bg-indigo-100 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                            <i data-lucide="loader" class="text-indigo-600 animate-spin"></i>
                        </div>
                        <p class="text-slate-400 text-sm font-bold uppercase tracking-widest">Synchronizing Bank Assets...</p>
                    </div>
                </template>

                <template x-if="!loading && filteredQuestions.length === 0">
                    <div class="py-20 text-center">
                        <i data-lucide="ghost" size="48" class="text-slate-200 mx-auto mb-4"></i>
                        <p class="text-slate-400 font-bold">No questions match your search.</p>
                    </div>
                </template>

                <div class="w-full">
                    <form id="linkQuestionForm" action="{{ route('administration.exams.questions.link', $exam) }}" method="POST" x-data="{ mappingLoading: false }" @submit="mappingLoading = true" class="grid grid-cols-1 gap-6">
                        @csrf
                        <input type="hidden" name="question_bank_id" :value="selectedBank">
                        <template x-for="question in filteredQuestions" :key="question.id">
                            <label class="block group cursor-pointer relative transform active:scale-[0.99] transition-transform">
                                <input type="checkbox" name="question_ids[]" :value="question.id" x-model="selectedIds" class="peer absolute inset-0 opacity-0 cursor-pointer z-20">
                                <div class="p-6 bg-white border border-slate-100 rounded-[2rem] transition-all peer-checked:border-indigo-500 peer-checked:ring-4 peer-checked:ring-indigo-50 group-hover:border-indigo-200 flex items-start gap-4 shadow-sm group-hover:shadow-md relative overflow-hidden z-10">
                                    <template x-if="mappedQuestionIds.includes(question.id)">
                                        <div class="absolute top-0 right-0 px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest rounded-bl-2xl">
                                            Already Mapped
                                        </div>
                                    </template>
                                    
                                    <div class="w-6 h-6 border-2 border-slate-200 rounded-lg flex items-center justify-center peer-checked:border-indigo-600 peer-checked:bg-indigo-600 transition-all shrink-0 mt-1">
                                        <svg class="text-white w-3.5 h-3.5 scale-0 peer-checked:scale-100 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <p class="text-slate-700 font-bold text-sm" x-text="question.question_text"></p>
                                            <template x-if="mappedQuestionIds.includes(question.id)">
                                                <svg class="text-emerald-500 w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-3 mt-3">
                                            <span class="text-[9px] font-black bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded uppercase tracking-wider" x-text="`${question.marks} Pts`"></span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </template>
                    </form>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-8 border-t border-slate-50 flex items-center justify-between bg-slate-50/50">
                <p class="text-xs font-bold text-slate-400">
                    <span class="text-indigo-600 font-black" x-text="selectedIds.length"></span> assets selected for mapping
                </p>
                <button type="submit" form="linkQuestionForm" :disabled="selectedIds.length === 0"
                        class="px-10 py-4 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 disabled:opacity-50 flex items-center justify-center gap-3">
                    <span>Map Selected Assets</span>
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function confirmDelete(button) {
        const form = button.closest('form');
        Swal.fire({
            title: 'Remove Question?',
            text: "This question will be unlinked from this exam. It will remain in the Question Bank.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#f1f5f9',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-[2.5rem]',
                confirmButton: 'px-6 py-3 rounded-xl font-bold',
                cancelButton: 'px-6 py-3 rounded-xl font-bold text-slate-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection
