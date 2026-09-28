@extends('layouts.school')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('school.notifications.templates.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm">
            <i data-lucide="arrow-left" size="18"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Edit Template</h1>
            <p class="text-slate-500 mt-1">Update your reusable message template.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden" x-data="{ loading: false }">
                <form action="{{ route('school.notifications.templates.update', $template) }}" method="POST" class="p-8 md:p-10 space-y-8" @submit="loading = true">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-6">
                        <!-- Template Name -->
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Template Name</label>
                            <input type="text" name="name" value="{{ $template->name }}" required placeholder="e.g. Fee Payment Reminder" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all placeholder:text-slate-300">
                        </div>

                        <!-- Channel Selection -->
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Communication Channel</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach(['sms' => 'MessageSquare', 'email' => 'Mail', 'whatsapp' => 'Phone', 'push' => 'Bell'] as $value => $icon)
                                <label class="relative group cursor-pointer">
                                    <input type="radio" name="channel" value="{{ $value }}" {{ $template->channel === $value ? 'checked' : '' }} class="peer sr-only">
                                    <div class="flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-slate-100 bg-white text-slate-400 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 group-hover:border-slate-200 transition-all">
                                        <i data-lucide="{{ $icon }}" size="20" class="mb-2"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest">{{ $value }}</span>
                                    </div>
                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-indigo-600 text-white rounded-full items-center justify-center hidden peer-checked:flex shadow-sm">
                                        <i data-lucide="check" size="10"></i>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Email Subject (Hidden for non-email) -->
                        <div class="space-y-2 transition-all duration-300" id="subject-container" style="{{ $template->channel === 'email' ? '' : 'display: none;' }}">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email Subject</label>
                            <input type="text" name="subject" value="{{ $template->subject }}" placeholder="Enter email subject line..." class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all placeholder:text-slate-300">
                        </div>

                        <!-- Message Content -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between ml-1">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest">Message Content</label>
                                <span class="text-[10px] text-indigo-500 font-black uppercase tracking-widest cursor-help underline decoration-dotted" title="Use {name}, {class}, {amount} etc.">Placeholders Help</span>
                            </div>
                            <textarea name="content" rows="6" required placeholder="Type your message here... Use {name} for student name." class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-medium focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all placeholder:text-slate-300">{{ $template->content }}</textarea>
                        </div>

                        <!-- Active Toggle -->
                        <label class="flex items-center gap-3 cursor-pointer group w-fit">
                            <div class="relative inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ $template->is_active ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 transition-all"></div>
                            </div>
                            <span class="text-xs font-black text-slate-500 uppercase tracking-widest group-hover:text-slate-700 transition-colors">Template Active</span>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" onclick="window.history.back()" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">Cancel</button>
                        <button type="submit" 
                                :disabled="loading"
                                class="px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 transition-all text-sm uppercase tracking-widest flex items-center gap-4 group disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="!loading">
                                <div class="flex items-center gap-4">
                                    <i data-lucide="refresh-cw" size="18" class="group-hover:rotate-180 transition-transform duration-500"></i>
                                    <span>Update Template</span>
                                </div>
                            </template>
                            <template x-if="loading">
                                <div class="flex items-center gap-4">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Updating...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar / Preview -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-indigo-900 rounded-[2.5rem] p-8 text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                <div class="relative z-10">
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] opacity-60 mb-6 flex items-center gap-2">
                        <i data-lucide="eye" size="12"></i>
                        Live Preview
                    </h4>
                    
                    <div id="phone-preview" class="bg-white rounded-3xl p-4 text-slate-800 shadow-2xl relative">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                <i data-lucide="user" size="16"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-tight text-slate-400 leading-none mb-1">Preview</p>
                                <p class="text-xs font-bold text-slate-700 leading-none">Your School</p>
                            </div>
                        </div>
                        <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100/50">
                            <p id="preview-text" class="text-sm font-medium leading-relaxed italic text-slate-400">{{ $template->content ?: 'Your message content will appear here...' }}</p>
                        </div>
                        <p class="text-[8px] text-slate-300 font-bold uppercase tracking-widest mt-3 text-center">Standard rates may apply</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm">
                <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                    <i data-lucide="info" size="14"></i>
                    Available Placeholders
                </h4>
                <div class="space-y-4">
                    <div class="p-3 bg-slate-50 rounded-xl group cursor-pointer hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100">
                        <code class="text-xs font-black text-indigo-600">{student_name}</code>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tight">Student's Full Name</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl group cursor-pointer hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100">
                        <code class="text-xs font-black text-indigo-600">{class_name}</code>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tight">Class & Section</p>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl group cursor-pointer hover:bg-indigo-50 transition-all border border-transparent hover:border-indigo-100">
                        <code class="text-xs font-black text-indigo-600">{amount}</code>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tight">Fee Amount</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contentInput = document.querySelector('textarea[name="content"]');
        const previewText = document.getElementById('preview-text');
        const channelRadios = document.querySelectorAll('input[name="channel"]');
        const subjectContainer = document.getElementById('subject-container');

        // Initial preview state
        if (contentInput.value.trim() !== '') {
            previewText.classList.remove('italic', 'text-slate-400');
            previewText.innerText = contentInput.value;
        }

        // Update preview
        contentInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                previewText.innerText = 'Your message content will appear here...';
                previewText.classList.add('italic', 'text-slate-400');
            } else {
                previewText.innerText = this.value;
                previewText.classList.remove('italic', 'text-slate-400');
            }
        });

        // Toggle subject field
        channelRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'email') {
                    subjectContainer.style.display = 'block';
                } else {
                    subjectContainer.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush
@endsection
