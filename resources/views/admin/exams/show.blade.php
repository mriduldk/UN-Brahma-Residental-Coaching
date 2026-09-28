<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Questions') }}: {{ $exam->title }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('administration.exams.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Back to Exams
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ showModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Question Form Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 sticky top-4">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Add New Question</h3>
                            <form action="{{ route('administration.exams.questions.store', $exam) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <x-input-label for="question_text" :value="__('Question Text')" />
                                    <textarea id="question_text" name="question_text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" rows="3" required>{{ old('question_text') }}</textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="marks" :value="__('Marks')" />
                                        <x-text-input id="marks" name="marks" type="number" step="0.5" class="mt-1 block w-full text-sm" :value="old('marks', 1)" required />
                                    </div>
                                    <div>
                                        <x-input-label for="previous_year" :value="__('Prev. Year (Opt)')" />
                                        <x-text-input id="previous_year" name="previous_year" type="text" class="mt-1 block w-full text-sm" :value="old('previous_year')" placeholder="e.g. 2023" />
                                        <input type="hidden" name="is_previous_year" value="1" x-show="$el.previous_year">
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <label class="block font-medium text-xs text-gray-700 uppercase tracking-wider">Options & Correct Answer</label>
                                    @for($i = 0; $i < 4; $i++)
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="correct_option_index" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                                            <input type="text" name="options[{{ $i }}][text]" placeholder="Option {{ $i + 1 }}" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                                        </div>
                                    @endfor
                                </div>

                                <div class="pt-4">
                                    <x-primary-button class="w-full justify-center">
                                        {{ __('Add Question') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Questions List Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-6 flex justify-between items-center">
                                <span>Existing Questions ({{ $exam->questions->count() }})</span>
                                <span class="text-xs font-normal text-gray-500">Total Marks Added: {{ $exam->questions->sum('marks') }}/{{ $exam->total_marks }}</span>
                            </h3>

                            <div class="space-y-6">
                                @forelse($exam->questions as $index => $question)
                                    <div class="border rounded-lg p-4 hover:border-indigo-300 transition-colors duration-200">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-start">
                                                <span class="bg-gray-100 text-gray-600 rounded-full h-6 w-6 flex items-center justify-center text-xs font-bold mr-3 shrink-0">{{ $index + 1 }}</span>
                                                <div class="text-sm font-medium text-gray-800">{{ $question->question_text }}</div>
                                            </div>
                                            <div class="flex items-center space-x-2 ml-4 shrink-0">
                                                <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full font-semibold">{{ $question->marks }} pts</span>
                                                @if($question->previous_year)
                                                    <span class="text-xs bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full font-semibold">PYQ {{ $question->previous_year }}</span>
                                                @endif
                                                <form action="{{ route('administration.questions.destroy', $question) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" onclick="return confirm('Delete this question?')">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3 pl-9">
                                            @foreach($question->options as $option)
                                                <div class="text-xs flex items-center p-2 rounded {{ $option->is_correct ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-gray-50 border border-gray-100 text-gray-600' }}">
                                                    @if($option->is_correct)
                                                        <svg class="h-3 w-3 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                    @else
                                                        <div class="h-2 w-2 rounded-full bg-gray-300 mr-2.5"></div>
                                                    @endif
                                                    {{ $option->option_text }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12 text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p>No questions added yet. Use the form on the left to start adding questions.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
