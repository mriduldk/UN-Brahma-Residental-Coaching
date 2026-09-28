@extends('layouts.student')

@section('title', 'School Feed')

@section('content')
<div class="max-w-3xl mx-auto space-y-8 pb-16">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2">
            <i data-lucide="rss" class="text-indigo-600" size="26"></i>
            School Feed
        </h1>
        <p class="text-slate-500 text-sm mt-1 font-medium">Stay updated with the latest institutional announcements, files, and polls from your teachers.</p>
    </div>

    <!-- Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <p class="font-bold text-sm leading-none">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <p class="font-bold text-sm leading-none">{{ session('error') }}</p>
        </div>
    @endif

    @if(!$enrollment)
        <!-- No Active Enrollment Warning -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 text-amber-500 shadow-inner">
                <i data-lucide="info" size="36"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2">No Active Enrollment Found</h3>
            <p class="text-slate-500 text-base max-w-md mx-auto font-medium">
                You are currently not mapped to any active class/section session. Please contact the administration to view the school feed.
            </p>
        </div>
    @else
        <!-- Feed Timeline Stream -->
        <div class="space-y-6">
            @forelse($feeds as $feed)
                @php
                    $isLiked = $feed->isLikedByUser(auth()->id());
                    $userVote = $feed->userVote(auth()->id());
                    $totalVotes = $feed->pollVotes->count();
                @endphp
                <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-6 space-y-5 hover:shadow-md transition-all">
                    
                    <!-- Post Author Details -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-black text-sm">
                            {{ substr($feed->user->name ?? 'T', 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 text-sm leading-none">{{ $feed->user->name ?? 'Instructor' }}</h4>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1 block">
                                {{ $feed->user->role === 'administration' ? 'Principal / Office' : 'Teacher' }} • {{ $feed->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <!-- Post Body Description -->
                    <div class="text-sm font-medium text-slate-600 leading-relaxed whitespace-pre-line">
                        {!! nl2br(e($feed->content)) !!}
                    </div>

                    <!-- Attached Image Gallery -->
                    @if($feed->image_path)
                        <div class="rounded-2xl overflow-hidden border border-slate-100 max-h-[350px] shadow-sm">
                            <img src="{{ Storage::url($feed->image_path) }}" 
                                 alt="Announcement Image" 
                                 class="w-full h-full object-cover">
                        </div>
                    @endif

                    <!-- Attached Document Link -->
                    @if($feed->document_path)
                        <div class="bg-slate-50 rounded-2xl border border-slate-100 p-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                                    <i data-lucide="file-text" size="20"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <span class="block font-bold text-xs text-slate-700 truncate max-w-[220px]" title="{{ $feed->document_name }}">{{ $feed->document_name }}</span>
                                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5 block">Document File Attachment</span>
                                </div>
                            </div>
                            <a href="{{ Storage::url($feed->document_path) }}" 
                               download="{{ $feed->document_name }}"
                               class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1.5 shadow-sm">
                                <i data-lucide="download" size="14"></i>
                                Download
                            </a>
                        </div>
                    @endif

                    <!-- Interactive Poll Card -->
                    @if($feed->type === 'poll')
                        <div class="bg-indigo-50/50 rounded-2xl border border-indigo-100/50 p-5 space-y-4">
                            @if($userVote)
                                <!-- Poll Results Mode (Already Voted) -->
                                <div class="flex justify-between items-center pb-2 border-b border-indigo-100/30">
                                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest flex items-center gap-1">
                                        <i data-lucide="shield-check" size="12"></i>
                                        Poll Results (You Voted)
                                    </span>
                                    <span class="text-[10px] text-indigo-400 font-black uppercase tracking-wider">Total: {{ $totalVotes }} Votes</span>
                                </div>

                                <div class="space-y-3.5">
                                    @foreach($feed->pollOptions as $option)
                                        @php
                                            $optionVotesCount = $option->votes->count();
                                            $percentage = $totalVotes > 0 ? round(($optionVotesCount / $totalVotes) * 100) : 0;
                                            $isVotedOption = $userVote->poll_option_id === $option->id;
                                        @endphp
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-xs font-bold items-center">
                                                <span class="flex items-center gap-1.5 {{ $isVotedOption ? 'text-indigo-700 font-black' : 'text-slate-600' }}">
                                                    {{ $option->option_text }}
                                                    @if($isVotedOption)
                                                        <span class="inline-flex px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded-md text-[9px] font-black uppercase tracking-wider">Your Vote</span>
                                                    @endif
                                                </span>
                                                <span class="text-slate-500">{{ $optionVotesCount }} ({{ $percentage }}%)</span>
                                            </div>
                                            <div class="w-full bg-slate-200/50 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all duration-700 {{ $isVotedOption ? 'bg-indigo-600' : 'bg-slate-400' }}" 
                                                     style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Interactive voting mode (Not Voted Yet) -->
                                <div class="flex justify-between items-center pb-2 border-b border-indigo-100/30">
                                    <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest flex items-center gap-1">
                                        <i data-lucide="circle-dashed" size="12"></i>
                                        Submit Your Vote
                                    </span>
                                </div>

                                <form action="{{ route('student.feed.vote', $feed->id) }}" method="POST" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                                    @csrf
                                    <div class="space-y-2">
                                        @foreach($feed->pollOptions as $option)
                                            <label class="flex items-center gap-3 px-4 py-3 bg-white border border-slate-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50/20 transition-all cursor-pointer select-none">
                                                <input type="radio" 
                                                       name="poll_option_id" 
                                                       value="{{ $option->id }}"
                                                       required
                                                       class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer">
                                                <span class="font-bold text-xs text-slate-700">{{ $option->option_text }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" 
                                                :disabled="submitting"
                                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 disabled:cursor-not-allowed text-white font-black text-xs rounded-xl transition-all shadow-md shadow-indigo-500/20 flex items-center gap-1.5">
                                            <svg x-show="submitting" class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <i x-show="!submitting" data-lucide="check" size="14"></i>
                                            <span x-text="submitting ? 'Submitting...' : 'Submit Vote'"></span>
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @endif

                    <!-- Feed Footer (Async Liking & Comments Section) -->
                    <div class="pt-4 border-t border-slate-100 space-y-4" x-data="{ showComments: false }">
                        <div class="flex items-center justify-between text-slate-400 text-xs font-bold select-none">
                            <div class="flex items-center gap-4">
                                <!-- Like Button -->
                                <button type="button" 
                                        onclick="toggleLike('{{ $feed->id }}', this)"
                                        class="flex items-center gap-2 group/like focus:outline-none transition-transform hover:scale-105 duration-200">
                                    <i data-lucide="heart" 
                                       class="transition-colors duration-300 shrink-0
                                       {{ $isLiked ? 'text-rose-500 fill-rose-500' : 'text-slate-400 group-hover/like:text-rose-500' }}" 
                                       size="20"></i>
                                    <span class="like-counter transition-colors duration-300
                                        {{ $isLiked ? 'text-rose-600 font-black' : 'text-slate-500 group-hover/like:text-rose-600' }}">
                                        {{ $feed->likes->count() }} Likes
                                    </span>
                                </button>

                                <!-- Comment Button -->
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
                                    <span>{{ $totalVotes }} total votes</span>
                                </div>
                            @endif
                        </div>

                        <!-- Comments Drawer -->
                        <div x-show="showComments" x-transition class="space-y-4 pt-4 border-t border-slate-50" style="display: none;">
                            <!-- Comment Form -->
                            <form action="{{ route('student.feed.comment', $feed->id) }}" method="POST" class="flex gap-2" x-data="{ submitting: false }" @submit="submitting = true">
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
                    <p class="text-slate-400 text-sm max-w-sm mx-auto font-medium">Your institution's teachers and office administration have not posted anything yet. Stay tuned!</p>
                </div>
            @endforelse
        </div>
    @endif
</div>

<script>
    function toggleLike(feedId, btnElement) {
        const heartIcon = btnElement.querySelector('[data-lucide="heart"]');
        const counterSpan = btnElement.querySelector('.like-counter');
        
        // Asynchronously call the like API endpoint
        fetch(`{{ url('student/feed') }}/${feedId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update Like state in DOM dynamically
                if (data.liked) {
                    heartIcon.classList.remove('text-slate-400');
                    heartIcon.classList.add('text-rose-500', 'fill-rose-500');
                    counterSpan.classList.remove('text-slate-500');
                    counterSpan.classList.add('text-rose-600', 'font-black');
                    
                    // Add subtle bounce keyframe animation manually
                    btnElement.classList.add('scale-110');
                    setTimeout(() => btnElement.classList.remove('scale-110'), 200);
                } else {
                    heartIcon.classList.remove('text-rose-500', 'fill-rose-500');
                    heartIcon.classList.add('text-slate-400');
                    counterSpan.classList.remove('text-rose-600', 'font-black');
                    counterSpan.classList.add('text-slate-500');
                }
                
                // Update the total counts text
                counterSpan.innerText = `${data.likes_count} Likes`;
            }
        })
        .catch(error => {
            console.error('Error liking post:', error);
        });
    }
</script>
@endsection
