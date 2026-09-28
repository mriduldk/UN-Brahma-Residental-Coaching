@extends('layouts.school')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-12">
    <!-- Header -->
    <div class="flex items-center gap-4 text-left">
        <a href="{{ route('school.notifications.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm">
            <i data-lucide="arrow-left" size="18"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight text-left">Compose Notification</h1>
            <p class="text-slate-500 mt-1 text-left">Blast messages to your students, staff, and parents across multiple channels.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i data-lucide="alert-circle" size="18"></i>
            <p class="text-sm font-bold">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-[3rem] border border-slate-200 shadow-sm overflow-hidden" x-data="{ loading: false }">
                <form action="{{ route('school.notifications.send') }}" method="POST" class="p-8 md:p-12 space-y-10" @submit="loading = true">
                    @csrf
                    
                    <!-- 1. Targeting -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-black">1</span>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight uppercase tracking-widest text-sm">Select Recipients</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Recipient Type</label>
                                <select name="recipient_type" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer">
                                    <option value="all_students">All Students</option>
                                    <option value="class">Specific Class</option>
                                    <option value="staff">All Staff</option>
                                    <option value="custom">Manual Entry</option>
                                </select>
                            </div>

                            <div class="space-y-2 hidden" id="class-selector">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Select Class</label>
                                <select name="class_id" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer">
                                    <option value="">Choose a class...</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Channel & Template -->
                    <div class="space-y-6 border-t border-slate-100 pt-10">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-black">2</span>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight uppercase tracking-widest text-sm">Message & Channel</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Channel</label>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach(['sms' => 'SMS', 'email' => 'Email', 'whatsapp' => 'WhatsApp', 'push' => 'Push'] as $val => $label)
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="channel" value="{{ $val }}" {{ $loop->first ? 'checked' : '' }} class="peer sr-only">
                                        <div class="px-4 py-3 rounded-xl border-2 border-slate-100 bg-white text-slate-500 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-600 font-bold text-xs text-center transition-all group-hover:border-slate-200 uppercase tracking-widest">{{ $label }}</div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Use Template (Optional)</label>
                                <select name="template_id" id="template_select" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer">
                                    <option value="">Dynamic New Message</option>
                                    @foreach($templates as $tmpl)
                                        <option value="{{ $tmpl->id }}" data-content="{{ $tmpl->content }}" data-channel="{{ $tmpl->channel }}" data-subject="{{ $tmpl->subject }}">{{ $tmpl->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-2 hidden" id="subject-field">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Subject</label>
                            <input type="text" name="title" placeholder="Enter message title or email subject..." class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all placeholder:text-slate-300">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Message Content</label>
                            <textarea name="content" rows="6" required placeholder="Write your message here..." class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-medium focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all placeholder:text-slate-300"></textarea>
                        </div>
                    </div>

                    <!-- 3. Finalize -->
                    <div class="pt-8 border-t border-slate-100 flex items-center justify-between">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Est. Recipients: <span class="text-indigo-600" id="recipient-count">Calculating...</span></p>
                        <button type="submit" 
                                :disabled="loading"
                                class="px-12 py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-2xl shadow-indigo-200 transition-all text-xs uppercase tracking-[0.2em] flex items-center gap-4 group disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="!loading">
                                <div class="flex items-center gap-4">
                                    <i data-lucide="send" size="20" class="group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                                    <span>Blast Notification</span>
                                </div>
                            </template>
                            <template x-if="loading">
                                <div class="flex items-center gap-4">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Sending Blast...</span>
                                </div>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar Helper -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Delivery Status Card -->
            <div class="bg-slate-900 rounded-[3rem] p-8 text-white relative overflow-hidden shadow-2xl shadow-slate-200">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-8 flex items-center gap-2">
                    <i data-lucide="shield-check" size="14"></i>
                    Delivery Quality
                </h4>
                
                <div class="space-y-6 relative z-10">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400">Target Segment</span>
                        <span class="text-sm font-black text-indigo-400 uppercase" id="preview-target">All Students</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400">Total Credits</span>
                        <span class="text-sm font-black text-white">4,281</span>
                    </div>
                    <div class="w-full bg-slate-800 rounded-full h-1.5 mt-2">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-8 space-y-6">
                <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                    <i data-lucide="eye" size="14"></i>
                    Message Preview
                </h4>
                
                <div class="bg-slate-50 rounded-2xl p-6 border border-dashed border-slate-200">
                    <p id="live-preview" class="text-sm text-slate-400 italic leading-relaxed">Your message will appear here in real-time as you type or select a template...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.querySelector('select[name="recipient_type"]');
        const classSelector = document.getElementById('class-selector');
        const templateSelect = document.getElementById('template_select');
        const contentArea = document.querySelector('textarea[name="content"]');
        const previewArea = document.getElementById('live-preview');
        const channelRadios = document.querySelectorAll('input[name="channel"]');
        const subjectField = document.getElementById('subject-field');

        // Toggle class selector
        typeSelect.addEventListener('change', function() {
            if (this.value === 'class') {
                classSelector.classList.remove('hidden');
            } else {
                classSelector.classList.add('hidden');
            }
            document.getElementById('preview-target').innerText = this.options[this.selectedIndex].text;
        });

        // Template Selection
        templateSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value !== "") {
                const content = selected.getAttribute('data-content');
                const channel = selected.getAttribute('data-channel');
                const subject = selected.getAttribute('data-subject');

                contentArea.value = content;
                updatePreview(content);

                // Set channel radio
                document.querySelector(`input[name="channel"][value="${channel}"]`).checked = true;
                
                // Set subject if exists
                if (channel === 'email' && subject) {
                    subjectField.querySelector('input').value = subject;
                    subjectField.classList.remove('hidden');
                }
            }
        });

        // Live Preview
        contentArea.addEventListener('input', function() {
            updatePreview(this.value);
        });

        function updatePreview(text) {
            if (text.trim() === '') {
                previewArea.innerText = 'Your message will appear here in real-time...';
                previewArea.classList.add('italic', 'text-slate-400');
            } else {
                previewArea.innerText = text;
                previewArea.classList.remove('italic', 'text-slate-400');
            }
        }

        // Channel Subject Toggle
        channelRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'email') {
                    subjectField.classList.remove('hidden');
                } else {
                    subjectField.classList.add('hidden');
                }
            });
        });
    });
</script>
@endpush
@endsection
