@extends('layouts.student')

@section('title', 'Examination in Progress')

@php $hideNav = true; @endphp

@section('content')
<div class="min-h-screen bg-slate-50 relative overflow-hidden" 
     x-data="examTimer({{ $remainingSeconds }})" 
     x-init="startTimer()"
     @keydown.window.prevent.f12=""
     @contextmenu.window.prevent="">
    
    <!-- Fullscreen Enforcement Overlay -->
    <div x-show="showFullscreenOverlay" 
         class="fixed inset-0 z-[100] bg-slate-900 flex items-center justify-center p-6 text-center"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="max-w-md w-full bg-white rounded-[2.5rem] p-10 shadow-2xl transition-all transform" :class="showFullscreenOverlay ? 'scale-100' : 'scale-95'">
            <div class="w-20 h-20 bg-indigo-100 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i data-lucide="maximize" size="40"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-4">Secure Mode Required</h3>
            <p class="text-slate-500 mb-8 font-medium">To maintain exam integrity, this session must be taken in full-screen mode. Exiting full-screen more than 2 times will result in <span class="text-rose-600 font-bold">automatic submission</span>.</p>
            <button @click="enterFullscreen()" class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all transform hover:-translate-y-1">
                Enter Full-screen & Continue
            </button>
        </div>
    </div>

    <!-- Fixed Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-slate-200 shadow-sm h-16 md:h-20">
        <div class="max-w-[100rem] mx-auto px-4 md:px-6 h-full flex justify-between items-center text-slate-800">
            <div class="flex items-center gap-3 md:gap-6">
                <!-- Mobile Palette Toggle -->
                <button @click="paletteOpen = true" class="xl:hidden w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 transition-all">
                    <i data-lucide="layout-grid" size="18"></i>
                </button>

                <div class="flex items-center gap-2 md:gap-3">
                    <div class="px-2 py-0.5 md:px-3 md:py-1 bg-rose-600 text-white rounded-lg text-[8px] md:text-[10px] font-black tracking-widest uppercase animate-pulse">LIVE</div>
                    <h2 class="text-sm md:text-lg font-black text-slate-800 truncate max-w-[120px] sm:max-w-xs md:max-w-sm">{{ $exam->title }}</h2>
                </div>
                <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>
                <div class="hidden sm:flex items-center gap-4">
                    <div class="text-center">
                        <p class="text-[8px] md:text-[9px] text-slate-400 uppercase font-black tracking-widest leading-none">Questions</p>
                        <p class="text-xs md:text-sm font-black text-slate-700 mt-1"><span x-text="currentIndex + 1">1</span> / {{ $questions->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3 md:gap-8">
                <div class="hidden lg:block w-48">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">Total Progress</span>
                        <span class="text-[10px] font-black text-indigo-600" x-text="Math.round(progressPercent) + '%'">0%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-indigo-600 h-full transition-all duration-500" :style="'width: ' + progressPercent + '%'"></div>
                    </div>
                </div>

                <div class="bg-rose-50 text-rose-600 px-3 py-1.5 md:px-5 md:py-2.5 rounded-xl md:rounded-2xl border border-rose-100 flex items-center gap-2 md:gap-3 shadow-sm scale-110">
                    <i data-lucide="clock" size="16" class="md:w-[18px] md:h-[18px] animate-spin-slow"></i>
                    <span class="text-base md:text-xl font-mono font-black tabular-nums" x-text="timerDisplay">00:00:00</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="pt-16 md:pt-20 h-screen flex overflow-hidden">
        
        <!-- Left: Question & Navigation -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-8 lg:p-12 transition-all duration-500 bg-slate-50">
            <div class="max-w-3xl mx-auto h-full flex flex-col">
                <form id="examForm" action="{{ route('student.exams.submit', $attempt) }}" method="POST" class="flex-1 flex flex-col">
                    @csrf
                    
                    <div class="flex-1 relative">
                        @foreach($questions as $index => $question)
                            <div class="absolute inset-0 transition-all duration-500 transform flex flex-col" 
                                 x-show="currentIndex === {{ $index }}"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-x-8"
                                 x-transition:enter-end="opacity-100 translate-x-0"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-x-0"
                                 x-transition:leave-end="opacity-0 -translate-x-8">
                                
                                <div class="bg-white rounded-[1.5rem] md:rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden p-6 md:p-10 lg:p-12 flex-1 flex flex-col">
                                    <!-- Question Header -->
                                    <div class="flex items-start mb-6 md:mb-10">
                                        <span class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-xl md:rounded-2xl bg-indigo-600 text-white font-black text-base md:text-lg mr-4 md:mr-6 shadow-lg shadow-indigo-100">
                                            {{ $index + 1 }}
                                        </span>
                                        <div class="text-lg md:text-2xl text-slate-800 font-bold leading-relaxed pt-1">
                                            {{ $question->question_text }}
                                        </div>
                                    </div>
                                    
                                    <!-- Options Grid -->
                                    <div class="grid grid-cols-1 gap-3 md:gap-4 ml-0 md:ml-18 space-y-1 md:space-y-2 mb-10">
                                        @foreach($question->options as $option)
                                            <label class="block cursor-pointer group/opt relative">
                                                <input type="radio" id="opt-{{ $option->id }}" name="answers[{{ $question->id }}]" value="{{ $option->id }}" 
                                                    @change="markAsAnswered({{ $question->id }})"
                                                    class="hidden peer">
                                                <div class="selection-panel p-4 md:p-6 rounded-[1.2rem] md:rounded-[1.5rem] border-2 border-slate-50 bg-slate-50 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:ring-4 peer-checked:ring-emerald-50 hover:bg-white hover:border-slate-200 hover:shadow-lg transition-all flex items-center">
                                                    <div class="selection-indicator w-6 h-6 md:w-8 md:h-8 rounded-full border-2 border-slate-200 flex-shrink-0 mr-4 md:mr-5 flex items-center justify-center peer-checked:border-emerald-500 peer-checked:bg-white transition-all">
                                                        <i data-lucide="check" size="14" class="md:w-[18px] md:h-[18px] text-emerald-600 hidden check-icon"></i>
                                                        <div class="w-2.5 h-2.5 md:w-3.5 md:h-3.5 rounded-full bg-slate-200 dot-icon"></div>
                                                    </div>
                                                    <span class="text-slate-700 font-bold text-sm md:text-lg peer-checked:text-emerald-900 transition-colors">{{ $option->option_text }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    @if($question->explanation)
                                        <div class="mt-auto pt-4 md:pt-10 pb-4 md:pb-10 text-slate-400 italic text-[11px] md:text-sm border-t border-slate-50 flex items-center gap-2 shrink-0">
                                            <i data-lucide="info" size="14"></i>
                                            Multiple choice question. Select the most accurate answer.
                                        </div>
                                    @else
                                        <div class="mt-auto pb-4 md:pb-10 shrink-0"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Bottom Action Controls -->
                    <div class="h-20 md:h-24 flex items-center justify-between shrink-0 gap-4">
                        <button type="button" @click="prevQuestion()" :disabled="currentIndex === 0" class="flex items-center gap-2 px-4 md:px-8 py-3 md:py-4 rounded-xl md:rounded-2xl font-black text-sm md:text-base text-slate-400 hover:text-slate-800 hover:bg-white transition-all disabled:opacity-0">
                            <i data-lucide="arrow-left" size="18" class="md:w-[20px] md:h-[20px]"></i>
                            <span class="hidden sm:inline">Previous</span>
                        </button>

                        <div class="flex items-center gap-3 md:gap-4 flex-1 sm:flex-initial">
                            <button x-show="currentIndex < totalQuestions - 1" type="button" @click="nextQuestion()" class="flex-1 sm:flex-initial flex items-center justify-center gap-2 px-4 md:px-10 py-3 md:py-4 bg-white border-2 border-slate-100 rounded-xl md:rounded-2xl font-black text-sm md:text-base text-slate-800 hover:border-indigo-600 hover:text-indigo-600 transition-all shadow-sm">
                                <span class="hidden sm:inline">Save & Next</span>
                                <span class="sm:hidden">Next</span>
                                <i data-lucide="arrow-right" size="18" class="md:w-[20px] md:h-[20px]"></i>
                            </button>

                            <button x-show="currentIndex === totalQuestions - 1" type="button" @click="confirmSubmit()" :disabled="isSubmitting" class="flex-1 sm:flex-initial flex items-center justify-center gap-2 px-4 md:px-12 py-3 md:py-4 bg-indigo-600 rounded-xl md:rounded-2xl font-black text-sm md:text-base text-white hover:bg-indigo-700 shadow-xl shadow-indigo-200 transition-all transform hover:-translate-y-1">
                                <template x-if="!isSubmitting">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="check-circle" size="18" class="md:w-[20px] md:h-[20px]"></i>
                                        <span>Finish</span>
                                    </div>
                                </template>
                                <template x-if="isSubmitting">
                                    <div class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span class="hidden sm:inline">Submitting...</span>
                                    </div>
                                </template>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>

        <!-- Right: Question Tracker Sidebar -->
        <aside class="w-80 bg-white border-l border-slate-200 flex-col shrink-0 hidden xl:flex">
            <div class="p-8 border-b border-slate-100">
                <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Question Palette</h3>
                
                <div class="grid grid-cols-5 gap-3">
                    @foreach($questions as $index => $question)
                        <button type="button" 
                                @click="currentIndex = {{ $index }}"
                                :class="{
                                    'bg-indigo-600 text-white shadow-lg shadow-indigo-100 ring-4 ring-indigo-50 border-indigo-600': currentIndex === {{ $index }},
                                    'bg-emerald-50 text-emerald-600 border-emerald-100': answeredQuestions['{{ $question->id }}'] && currentIndex !== {{ $index }},
                                    'bg-slate-50 text-slate-400 border-slate-100': !answeredQuestions['{{ $question->id }}'] && currentIndex !== {{ $index }}
                                }"
                                class="w-10 h-10 rounded-xl font-black text-xs border transition-all flex items-center justify-center">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="p-8 flex-1 overflow-y-auto space-y-8 no-scrollbar">
                <button type="button" @click="confirmSubmit()" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-black transition-all flex items-center justify-center gap-2 uppercase tracking-widest shadow-xl shadow-slate-200">
                    <i data-lucide="power" size="16"></i>
                    Final Submission
                </button>

                <div>
                    <h4 class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-4 italic">Summary</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-xs font-bold text-slate-600">Answered</span>
                            </div>
                            <span class="text-sm font-black text-slate-800" x-text="answeredCount">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-slate-200"></div>
                                <span class="text-xs font-bold text-slate-600">Remaining</span>
                            </div>
                            <span class="text-sm font-black text-slate-800" x-text="totalQuestions - answeredCount">0</span>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 rounded-2xl p-5 border border-indigo-100">
                    <p class="text-[10px] text-indigo-400 font-black uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i data-lucide="shield-check" size="14"></i>
                        Proctor Info
                    </p>
                    <p class="text-[11px] text-indigo-900 font-bold leading-relaxed">
                        Exiting full-screen <span class="text-rose-600" x-text="Math.max(0, maxStrikes - fullscreenStrikes)">3</span> more times will terminate your exam.
                    </p>
                </div>
            </div>
        </aside>

        <!-- Mobile Palette Drawer Overlay -->
        <div x-show="paletteOpen" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="paletteOpen = false"
             class="fixed inset-0 bg-slate-900/60 z-[60] xl:hidden backdrop-blur-sm"></div>

        <!-- Mobile Palette Drawer -->
        <div x-show="paletteOpen"
             x-transition:enter="transition-transform ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition-transform ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 w-80 max-w-[80vw] bg-white z-[70] xl:hidden shadow-2xl flex flex-col">
            
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
                <span class="font-black text-slate-800 uppercase tracking-widest text-xs">Navigation Panel</span>
                <button @click="paletteOpen = false" class="text-slate-400 hover:text-rose-600 transition-colors">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-8 no-scrollbar">
                <div>
                     <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-6">Question Palette</h3>
                     <div class="grid grid-cols-4 gap-3">
                        @foreach($questions as $index => $question)
                            <button type="button" 
                                    @click="currentIndex = {{ $index }}; paletteOpen = false"
                                    :class="{
                                        'bg-indigo-600 text-white shadow-lg shadow-indigo-100 ring-4 ring-indigo-50 border-indigo-600': currentIndex === {{ $index }},
                                        'bg-emerald-50 text-emerald-600 border-emerald-100': answeredQuestions['{{ $question->id }}'] && currentIndex !== {{ $index }},
                                        'bg-slate-50 text-slate-400 border-slate-100': !answeredQuestions['{{ $question->id }}'] && currentIndex !== {{ $index }}
                                    }"
                                    class="w-full aspect-square rounded-xl font-black text-xs border transition-all flex items-center justify-center">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="bg-indigo-50 rounded-2xl p-5 border border-indigo-100">
                    <p class="text-[10px] text-indigo-400 font-black uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i data-lucide="info" size="14"></i>
                        Summary
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[8px] text-slate-400 uppercase font-black tracking-widest">Answered</p>
                            <p class="text-lg font-black text-slate-800" x-text="answeredCount">0</p>
                        </div>
                        <div>
                            <p class="text-[8px] text-slate-400 uppercase font-black tracking-widest">Total</p>
                            <p class="text-lg font-black text-slate-800">{{ $questions->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-slate-100">
                <button type="button" @click="confirmSubmit()" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-sm hover:bg-black transition-all flex items-center justify-center gap-2 uppercase tracking-widest">
                    <i data-lucide="power" size="16"></i>
                    Submit Exam
                </button>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function examTimer(seconds) {
        return {
            timeLeft: seconds,
            timerDisplay: '00:00:00',
            answeredQuestions: {},
            answeredCount: 0,
            totalQuestions: {{ $questions->count() }},
            progressPercent: 0,
            currentIndex: 0,
            paletteOpen: false,
            
            isSubmitting: false,
            
            // Fullscreen states
            showFullscreenOverlay: true,
            localStorageKey: 'exam_strikes_{{ $attempt->id }}',
            fullscreenStrikes: 0,
            maxStrikes: 3,

            startTimer() {
                // Initialize strikes from storage
                const storedStrikes = localStorage.getItem(this.localStorageKey);
                if (storedStrikes) {
                    this.fullscreenStrikes = parseInt(storedStrikes);
                    // If they already exceeded strikes, terminate immediately
                    if (this.fullscreenStrikes >= this.maxStrikes) {
                        this.autoSubmit();
                        return;
                    }
                }

                this.updateDisplay();
                const interval = setInterval(() => {
                    if (this.timeLeft > 0) {
                        this.timeLeft--;
                        this.updateDisplay();
                    } else {
                        clearInterval(interval);
                        this.autoSubmit();
                    }
                }, 1000);

                window.addEventListener('beforeunload', (event) => {
                    if (this.isSubmitting) return;
                    event.preventDefault();
                    event.returnValue = '';
                });

                // Listen for fullscreen exit
                document.addEventListener('fullscreenchange', () => this.handleFullscreenChange());
                document.addEventListener('webkitfullscreenchange', () => this.handleFullscreenChange());
                document.addEventListener('mozfullscreenchange', () => this.handleFullscreenChange());
                document.addEventListener('MSFullscreenChange', () => this.handleFullscreenChange());
            },
            
            nextQuestion() {
                if (this.currentIndex < this.totalQuestions - 1) {
                    this.currentIndex++;
                }
            },

            prevQuestion() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                }
            },

            markAsAnswered(id) {
                this.answeredQuestions[id] = true;
                this.updateProgress();
            },

            enterFullscreen() {
                const elem = document.documentElement;
                if (elem.requestFullscreen) elem.requestFullscreen();
                else if (elem.webkitRequestFullscreen) elem.webkitRequestFullscreen();
                else if (elem.msRequestFullscreen) elem.msRequestFullscreen();
                this.showFullscreenOverlay = false;
            },

            handleFullscreenChange() {
                if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement) {
                    if (this.isSubmitting) return;
                    this.fullscreenStrikes++;
                    
                    // Save to storage
                    localStorage.setItem(this.localStorageKey, this.fullscreenStrikes);
                    
                    if (this.fullscreenStrikes >= this.maxStrikes) {
                        Swal.fire({
                            html: `<div class="p-2"><div class="w-16 h-16 md:w-20 md:h-20 bg-rose-100 text-rose-600 rounded-3xl flex items-center justify-center mx-auto mb-6"><i data-lucide="alert-octagon" size="32" class="md:w-[40px] md:h-[40px]"></i></div><h3 class="text-xl md:text-2xl font-black text-slate-800 mb-4">Exam Terminated</h3><p class="text-sm md:text-base text-slate-500 mb-8 font-medium leading-relaxed">Maximum full-screen exit attempts exceeded. Session is <span class="text-rose-600 font-bold">submitting automatically</span>.</p></div>`,
                            showConfirmButton: true, confirmButtonText: 'I Understand', buttonsStyling: false,
                            customClass: { container: 'z-[110]', popup: 'rounded-[1.5rem] md:rounded-[2.5rem] border-none shadow-2xl p-6 md:p-10', confirmButton: 'w-full py-4 px-6 bg-rose-600 text-white rounded-xl md:rounded-2xl font-black text-lg hover:bg-rose-700' },
                            allowOutsideClick: false, didOpen: () => { if(window.lucide) lucide.createIcons(); }
                        }).then(() => this.autoSubmit());
                    } else {
                        this.showFullscreenOverlay = true;
                    }
                }
            },

            updateDisplay() {
                const h = Math.floor(this.timeLeft / 3600), m = Math.floor((this.timeLeft % 3600) / 60), s = this.timeLeft % 60;
                this.timerDisplay = [h, m, s].map(v => v < 10 ? '0' + v : v).join(':');
            },
            
            updateProgress() {
                this.answeredCount = Object.keys(this.answeredQuestions).length;
                this.progressPercent = (this.answeredCount / this.totalQuestions) * 100;
            },
            
            autoSubmit() {
                this.isSubmitting = true;
                localStorage.removeItem(this.localStorageKey);
                if (document.fullscreenElement) document.exitFullscreen();
                document.getElementById('examForm').submit();
            },
            
            confirmSubmit() {
                Swal.fire({
                    html: `<div class="p-2"><div class="w-16 h-16 md:w-20 md:h-20 bg-indigo-100 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-6"><i data-lucide="help-circle" size="32" class="md:w-[40px] md:h-[40px]"></i></div><h3 class="text-xl md:text-2xl font-black text-slate-800 mb-4">Final Submission</h3><p class="text-sm md:text-base text-slate-500 mb-8 font-medium">Are you sure you want to end the exam and submit your answers?</p></div>`,
                    showCancelButton: true, confirmButtonText: 'Yes, Submit Now', cancelButtonText: 'Cancel', buttonsStyling: false,
                    customClass: { container: 'z-[110]', popup: 'rounded-[1.5rem] md:rounded-[2.5rem] border-none shadow-2xl p-6 md:p-10', confirmButton: 'w-full py-4 bg-indigo-600 text-white rounded-xl md:rounded-2xl font-black text-lg hover:bg-indigo-700 shadow-xl shadow-indigo-200 mb-3', cancelButton: 'w-full py-3 text-slate-400 font-bold hover:text-slate-600 text-xs' },
                    didOpen: () => { if(window.lucide) lucide.createIcons(); }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.isSubmitting = true;
                        localStorage.removeItem(this.localStorageKey);
                        if (document.fullscreenElement) document.exitFullscreen();
                        document.getElementById('examForm').submit();
                    }
                });
            }
        }
    }
</script>
<style>
    html, body {
        overflow: hidden !important;
        height: 100% !important;
        width: 100% !important;
        position: fixed;
    }
    
    .animate-spin-slow {
        animation: spin 3s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Selection Indicator Logic */
    .peer:checked ~ .selection-panel .check-icon {
        display: block !important;
    }
    .peer:checked ~ .selection-panel .dot-icon {
        display: none !important;
    }
    
    .ml-18 { margin-left: 4.5rem; }
    
    @media (max-width: 768px) {
        .ml-18 { margin-left: 0; }
    }
</style>
@endpush
@endsection
