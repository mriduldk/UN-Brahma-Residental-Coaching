@extends('layouts.school')

@section('title', 'Exam Results - ' . $exam->title)

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('administration.exams.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 transition-all">
                    <i data-lucide="arrow-left" size="18"></i>
                </a>
                <h1 class="text-3xl font-black text-indigo-950 tracking-tight">Examination Results</h1>
            </div>
            <p class="text-slate-500 text-sm flex items-center gap-2 ml-12">
                <i data-lucide="award" size="16" class="text-indigo-500"></i>
                Performance overview for <span class="font-bold text-slate-800">{{ $exam->title }}</span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition-all">
                <i data-lucide="download" size="18"></i>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                    <i data-lucide="users" size="24"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Attempts</p>
                    <p class="text-2xl font-black text-slate-800">{{ $attempts->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                    <i data-lucide="trending-up" size="24"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Avg. Score</p>
                    <p class="text-2xl font-black text-slate-800">
                        {{ $attempts->count() > 0 ? round($attempts->avg('score'), 1) : 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center">
                    <i data-lucide="file-warning" size="24"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Fail Rate</p>
                    <p class="text-2xl font-black text-slate-800">
                        {{ $attempts->count() > 0 ? round(($attempts->where('score', '<', $exam->passing_marks)->count() / $attempts->count()) * 100) : 0 }}%
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                    <i data-lucide="clock" size="24"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">High Score</p>
                    <p class="text-2xl font-black text-slate-800">{{ $attempts->max('score') ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-50">
                        <th class="px-8 py-5">Student Information</th>
                        <th class="px-8 py-5">Score Card</th>
                        <th class="px-8 py-5">Attempt Timing</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-50">
                    @forelse($attempts as $attempt)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center font-black text-indigo-600 text-xs">
                                        {{ strtoupper(substr($attempt->student->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-800 text-base leading-tight">{{ $attempt->student->name }}</div>
                                        <div class="text-slate-400 font-medium mt-0.5 text-xs">ID: #{{ $attempt->student->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-black {{ $attempt->score >= $exam->passing_marks ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $attempt->score }}
                                        </span>
                                        <span class="text-slate-300 font-bold">/</span>
                                        <span class="text-slate-500 font-bold text-xs">{{ $exam->total_marks }}</span>
                                    </div>
                                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $attempt->score >= $exam->passing_marks ? 'bg-emerald-500' : 'bg-rose-500' }}" 
                                             style="width: {{ ($attempt->score / $exam->total_marks) * 100 }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2 text-slate-700 font-bold">
                                    <i data-lucide="calendar" size="14" class="text-indigo-400"></i>
                                    {{ $attempt->started_at->format('M d, Y') }}
                                </div>
                                <div class="flex items-center gap-2 text-slate-400 text-xs mt-1 font-medium italic">
                                    <i data-lucide="clock" size="14"></i>
                                    Took {{ $attempt->completed_at ? $attempt->completed_at->diffForHumans($attempt->started_at, true) : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($attempt->status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        <i data-lucide="check-circle" size="12"></i>
                                        Completed
                                    </span>
                                @elseif($attempt->status === 'in_progress')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        <i data-lucide="loader" size="12" class="animate-spin"></i>
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        Terminated
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('administration.exams.attempts.show', $attempt) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                                    <i data-lucide="file-bar-chart" size="14"></i>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="frown" size="32" class="text-slate-300"></i>
                                </div>
                                <h3 class="text-slate-800 font-bold text-lg">No Results Available</h3>
                                <p class="text-slate-400 text-sm">Students haven't attempted this examination yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($attempts->hasPages())
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
