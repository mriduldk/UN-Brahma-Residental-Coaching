@extends('layouts.student')

@section('title', 'Available Exams')

@section('content')
<div class="space-y-8">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ __('Available Exams') }}
        </h2>
        <p class="text-gray-500 text-sm mt-1">View and participate in your scheduled examinations.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Available Exams -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="book-open" class="h-5 w-5 mr-2 text-indigo-500"></i>
                Open Exams
            </h3>
            <div class="space-y-4">
                @forelse($exams as $exam)
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-md font-bold text-gray-800">{{ $exam->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ Str::limit($exam->description, 80) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs font-semibold text-indigo-600">{{ $exam->duration_minutes }} Min</span>
                                <span class="block text-[10px] text-gray-400 mt-1 uppercase">{{ $exam->questions->count() }} Questions</span>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-between items-center">
                            <div class="text-xs text-gray-500">
                                <span class="font-medium">Ends:</span> {{ $exam->end_time->format('M d, h:i A') }}
                            </div>
                            @php
                                $myAttempts = $attempts->where('exam_id', $exam->id)->count();
                            @endphp
                            <a href="{{ route('student.exams.show', $exam) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                {{ $myAttempts > 0 ? 'Retake Exam' : 'Enter Exam' }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border-2 border-dashed border-gray-100 rounded-2xl p-10 text-center text-gray-500">
                        <i data-lucide="clipboard-x" size="40" class="mx-auto mb-3 text-gray-200"></i>
                        <p>No active exams available at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Past Attempts -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="check-circle" class="h-5 w-5 mr-2 text-green-500"></i>
                Your Results
            </h3>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Exam</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($attempts as $attempt)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $attempt->exam->title }}</div>
                                    <div class="text-[10px] text-gray-400 capitalize flex items-center gap-1.5 mt-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $attempt->status == 'completed' ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                                        {{ $attempt->status }} • {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'Incomplete' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($attempt->status == 'in_progress')
                                        <span class="text-xs italic text-gray-400">Processing...</span>
                                    @else
                                        <div class="text-sm font-bold {{ $attempt->score >= $attempt->exam->passing_marks ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $attempt->score }}/{{ $attempt->exam->total_marks }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('student.exams.results', $attempt) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-black uppercase tracking-widest">Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400 italic">
                                    You haven't attempted any exams yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
