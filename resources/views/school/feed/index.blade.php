@extends('layouts.school')

@section('title', 'School Feed')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-16" x-data="{ 
    activeTab: 'standard', 
    pollOptions: ['Option 1', 'Option 2'],
    submitting: false,
    addPollOption() {
        if (this.pollOptions.length < 5) {
            this.pollOptions.push('Option ' + (this.pollOptions.length + 1));
        } else {
            Swal.fire({
                title: 'Limit Reached',
                text: 'You can add a maximum of 5 options per poll.',
                icon: 'warning',
                confirmButtonColor: '#4f46e5'
            });
        }
    },
    removePollOption(index) {
        if (this.pollOptions.length > 2) {
            this.pollOptions.splice(index, 1);
        } else {
            Swal.fire({
                title: 'Minimum Choices Required',
                text: 'A poll must have at least 2 choices.',
                icon: 'info',
                confirmButtonColor: '#4f46e5'
            });
        }
    }
}">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
            <i data-lucide="rss" class="text-indigo-600" size="26"></i>
            School Feed & Announcements
        </h1>
        <p class="text-slate-500 text-sm mt-1">Broadcast announcements, upload files, or post interactive polls to engage students.</p>
    </div>

    <!-- Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <p class="font-bold text-sm">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Feed Composer Card -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-md p-6 overflow-hidden">
        <form action="{{ route('school.feed.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="submitting = true">
            @csrf
            <input type="hidden" name="type" :value="activeTab">

            <!-- Composer Tabs -->
            <div class="flex border-b border-slate-100 pb-2">
                <button type="button" 
                        @click="activeTab = 'standard'" 
                        :class="activeTab === 'standard' ? 'text-indigo-600 border-indigo-600 font-black' : 'text-slate-400 border-transparent hover:text-slate-600 font-bold'" 
                        class="flex items-center gap-2 px-4 py-2 border-b-2 text-sm transition-all focus:outline-none">
                    <i data-lucide="file-text" size="16"></i>
                    Standard Post
                </button>
                <button type="button" 
                        @click="activeTab = 'poll'" 
                        :class="activeTab === 'poll' ? 'text-indigo-600 border-indigo-600 font-black' : 'text-slate-400 border-transparent hover:text-slate-600 font-bold'" 
                        class="flex items-center gap-2 px-4 py-2 border-b-2 text-sm transition-all focus:outline-none">
                    <i data-lucide="vote" size="16"></i>
                    Create Poll
                </button>
            </div>

            <!-- Content Area -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2" x-text="activeTab === 'standard' ? 'Announcement Message' : 'Poll Question'"></label>
                    <textarea name="content" 
                              rows="4" 
                              required
                              placeholder="Write your announcement or poll question here..."
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-700 placeholder-slate-400 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all"></textarea>
                </div>

                <!-- Standard Post Attachments -->
                <div x-show="activeTab === 'standard'" class="grid grid-cols-1 md:grid-cols-2 gap-4" x-transition>
                    <!-- Image Upload -->
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Attach Image</label>
                        <div class="relative group border border-dashed border-slate-200 rounded-2xl p-4 bg-slate-50/50 hover:bg-slate-50 hover:border-indigo-300 transition-all flex flex-col items-center justify-center cursor-pointer text-center">
                            <i data-lucide="image" class="text-slate-400 group-hover:text-indigo-500 mb-2" size="24"></i>
                            <span class="text-xs font-bold text-slate-500">Click to upload Image</span>
                            <span class="text-[10px] text-slate-400 mt-1 font-semibold">JPG, PNG, WEBP (Max 4MB)</span>
                            <input type="file" 
                                   name="image" 
                                   accept="image/*"
                                   class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                    </div>

                    <!-- File/Document Upload -->
                    <div>
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Attach Document</label>
                        <div class="relative group border border-dashed border-slate-200 rounded-2xl p-4 bg-slate-50/50 hover:bg-slate-50 hover:border-indigo-300 transition-all flex flex-col items-center justify-center cursor-pointer text-center">
                            <i data-lucide="paperclip" class="text-slate-400 group-hover:text-indigo-500 mb-2" size="24"></i>
                            <span class="text-xs font-bold text-slate-500">Click to upload Document</span>
                            <span class="text-[10px] text-slate-400 mt-1 font-semibold">PDF, DOC, XLSX, ZIP (Max 10MB)</span>
                            <input type="file" 
                                   name="document" 
                                   class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                    </div>
                </div>

                <!-- Poll Options Builder -->
                <div x-show="activeTab === 'poll'" class="space-y-3" x-transition>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Poll Choices</label>
                    
                    <template x-for="(option, index) in pollOptions" :key="index">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center font-bold text-xs text-indigo-600 shrink-0" x-text="index + 1"></div>
                            <input type="text" 
                                   name="poll_options[]" 
                                   :placeholder="'Choice ' + (index + 1)"
                                   :required="activeTab === 'poll'"
                                   class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                            <button type="button" 
                                    @click="removePollOption(index)"
                                    class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 hover:bg-rose-50 hover:text-rose-600 transition-colors flex items-center justify-center shrink-0">
                                <i data-lucide="trash-2" size="16"></i>
                            </button>
                        </div>
                    </template>

                    <button type="button" 
                            @click="addPollOption()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition-all mt-2">
                        <i data-lucide="plus" size="14"></i>
                        Add Choice
                    </button>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit" 
                        :disabled="submitting"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 disabled:cursor-not-allowed text-white font-black text-sm rounded-xl transition-all shadow-md shadow-indigo-500/20 flex items-center gap-2">
                    <svg x-show="submitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <i x-show="!submitting" data-lucide="send" size="16"></i>
                    <span x-text="submitting ? 'Publishing...' : 'Publish Post'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Feed Stream Timeline -->
    <div class="space-y-6">
        <h3 class="font-black text-slate-800 text-lg flex items-center gap-2">
            <i data-lucide="history" class="text-slate-400" size="20"></i>
            Chronological Announcements List
        </h3>

        @forelse($feeds as $feed)
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-6 space-y-4 hover:shadow-md transition-all relative group">
                <!-- Delete Action -->
                <div class="absolute right-6 top-6">
                    <form action="{{ route('school.feed.destroy', $feed->id) }}" method="POST" onsubmit="return confirmDelete(event)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-400 transition-colors flex items-center justify-center">
                            <i data-lucide="trash-2" size="14"></i>
                        </button>
                    </form>
                </div>

                <!-- Author details -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-black text-sm">
                        {{ substr($feed->user->name ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-black text-slate-800 text-sm leading-none">{{ $feed->user->name ?? 'Administrator' }}</h4>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1 block">
                            {{ $feed->user->role === 'administration' ? 'Principal / Institution Desk' : 'Teacher' }} • {{ $feed->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <!-- Feed Content -->
                <div class="text-sm font-medium text-slate-600 leading-relaxed whitespace-pre-line">
                    {!! nl2br(e($feed->content)) !!}
                </div>

                <!-- Attached Media -->
                @if($feed->image_path)
                    <div class="rounded-2xl overflow-hidden border border-slate-100 max-h-[350px]">
                        <img src="{{ Storage::url($feed->image_path) }}" 
                             alt="Attached Image" 
                             class="w-full h-full object-cover">
                    </div>
                @endif

                <!-- Attached Document -->
                @if($feed->document_path)
                    <div class="bg-slate-50 rounded-2xl border border-slate-100 p-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                                <i data-lucide="file-text" size="20"></i>
                            </div>
                            <div class="overflow-hidden">
                                <span class="block font-bold text-xs text-slate-700 truncate max-w-[200px]" title="{{ $feed->document_name }}">{{ $feed->document_name }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5 block">Document File Attachment</span>
                            </div>
                        </div>
                        <a href="{{ Storage::url($feed->document_path) }}" 
                           download="{{ $feed->document_name }}"
                           class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5">
                            <i data-lucide="download" size="14"></i>
                            Download
                        </a>
                    </div>
                @endif

                <!-- Poll Results Display -->
                @if($feed->type === 'poll')
                    @php
                        $totalVotes = $feed->pollVotes->count();
                    @endphp
                    <div class="bg-indigo-50/50 rounded-2xl border border-indigo-100/50 p-4 space-y-3.5">
                        <span class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest">Active Poll Voting Results</span>
                        
                        <div class="space-y-3">
                            @foreach($feed->pollOptions as $option)
                                @php
                                    $optionVotesCount = $option->votes->count();
                                    $percentage = $totalVotes > 0 ? round(($optionVotesCount / $totalVotes) * 100) : 0;
                                @endphp
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs font-bold text-slate-700">
                                        <span>{{ $option->option_text }}</span>
                                        <span>{{ $optionVotesCount }} votes ({{ $percentage }}%)</span>
                                    </div>
                                    <div class="w-full bg-slate-200/60 rounded-full h-2">
                                        <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider text-right pt-1.5 border-t border-indigo-100/30">
                            Total Votes: {{ $totalVotes }}
                        </div>
                    </div>
                @endif

                <!-- Footer Summary (Likes & Comments Section) -->
                <div class="pt-4 border-t border-slate-100 space-y-4" x-data="{ showComments: false }">
                    <div class="flex items-center justify-between text-slate-400 text-xs font-bold">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1.5 text-indigo-500">
                                <i data-lucide="heart" class="fill-indigo-500 text-indigo-500" size="16"></i>
                                <span>{{ $feed->likes->count() }} Likes</span>
                            </div>

                            <button type="button" 
                                    @click="showComments = !showComments"
                                    class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors focus:outline-none select-none">
                                <i data-lucide="message-square" size="16"></i>
                                <span x-text="showComments ? 'Hide Comments' : '{{ $feed->comments->count() }} Comments'"></span>
                            </button>
                        </div>

                        @if($feed->type === 'poll')
                            <div class="flex items-center gap-1">
                                <i data-lucide="vote" size="16"></i>
                                <span>{{ $totalVotes }} total student votes</span>
                            </div>
                        @endif
                    </div>

                    <!-- Comments Collapsible Section -->
                    <div x-show="showComments" x-transition class="space-y-4 pt-4 border-t border-slate-50" style="display: none;">
                        <!-- Write Comment Form -->
                        <form action="{{ route('school.feed.comment', $feed->id) }}" method="POST" class="flex gap-2" x-data="{ submitting: false }" @submit="submitting = true">
                            @csrf
                            <input type="text" 
                                   name="comment" 
                                   required 
                                   placeholder="Write a comment..." 
                                   class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 placeholder-slate-400 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                            <button type="submit" 
                                    :disabled="submitting" 
                                    class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 disabled:cursor-not-allowed text-white font-black text-xs rounded-xl transition-all shadow-sm shrink-0 flex items-center gap-1">
                                <i x-show="!submitting" data-lucide="send" size="12"></i>
                                <span x-text="submitting ? '...' : 'Post'"></span>
                            </button>
                        </form>

                        <!-- Comments List -->
                        <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                            @forelse($feed->comments as $comment)
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-3 space-y-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-black text-slate-700">{{ $comment->user->name ?? 'User' }}</span>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-slate-600 font-semibold leading-relaxed">{{ $comment->comment }}</p>
                                </div>
                            @empty
                                <p class="text-center text-slate-400 font-semibold py-3 text-[11px]">No comments yet. Share your thoughts above!</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[2rem] border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <i data-lucide="rss" size="28"></i>
                </div>
                <h4 class="font-black text-slate-800 text-lg mb-1">No Announcements Yet</h4>
                <p class="text-slate-400 text-sm max-w-sm mx-auto font-medium">Use the composer card above to publish your first announcement to students.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target;
        Swal.fire({
            title: 'Delete Announcement?',
            text: 'This action will permanently delete this post and all its attachments.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#4f46e5',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
</script>
@endsection
