<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Exam') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-8">
                    <form action="{{ route('administration.exams.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="title" :value="__('Exam Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="start_time" :value="__('Start Time')" />
                                <x-text-input id="start_time" name="start_time" type="datetime-local" class="mt-1 block w-full" :value="old('start_time')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
                            </div>
                            <div>
                                <x-input-label for="end_time" :value="__('End Time')" />
                                <x-text-input id="end_time" name="end_time" type="datetime-local" class="mt-1 block w-full" :value="old('end_time')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('end_time')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="duration_minutes" :value="__('Duration (Mins)')" />
                                <x-text-input id="duration_minutes" name="duration_minutes" type="number" class="mt-1 block w-full" :value="old('duration_minutes', 60)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
                            </div>
                            <div>
                                <x-input-label for="total_marks" :value="__('Total Marks')" />
                                <x-text-input id="total_marks" name="total_marks" type="number" class="mt-1 block w-full" :value="old('total_marks', 100)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('total_marks')" />
                            </div>
                            <div>
                                <x-input-label for="passing_marks" :value="__('Passing Marks')" />
                                <x-text-input id="passing_marks" name="passing_marks" type="number" class="mt-1 block w-full" :value="old('passing_marks', 40)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('passing_marks')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="negative_marks_per_wrong" :value="__('Negative Marks (Per Wrong)')" />
                                <x-text-input id="negative_marks_per_wrong" name="negative_marks_per_wrong" type="number" step="0.25" class="mt-1 block w-full" :value="old('negative_marks_per_wrong', 0)" />
                                <x-input-error class="mt-2" :messages="$errors->get('negative_marks_per_wrong')" />
                            </div>
                            <div>
                                <x-input-label for="max_attempts" :value="__('Max Attempts')" />
                                <x-text-input id="max_attempts" name="max_attempts" type="number" class="mt-1 block w-full" :value="old('max_attempts', 1)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('max_attempts')" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('administration.exams.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">Cancel</a>
                            <x-primary-button>
                                {{ __('Create Exam') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
