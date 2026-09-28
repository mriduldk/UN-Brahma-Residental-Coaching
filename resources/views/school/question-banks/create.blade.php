@extends('layouts.school')

@section('title', 'Create Question Bank')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('administration.question-banks.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 transition-all">
            <i data-lucide="arrow-left" size="18"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-indigo-950 tracking-tight">Create Question Bank</h1>
            <p class="text-slate-500 text-sm">Organize your academic questions into a centralized repository.</p>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-indigo-50/50 p-10">
        <form action="{{ route('administration.question-banks.store') }}" method="POST" class="space-y-8" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            
            <div class="space-y-2">
                <label for="title" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Bank Title</label>
                <input type="text" name="title" id="title" required placeholder="e.g., Mathematics - Grade 10" 
                    class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-[1.5rem] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-200 transition-all font-bold text-slate-700 outline-none">
                @error('title') <p class="text-rose-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label for="description" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Description (Optional)</label>
                <textarea name="description" id="description" rows="4" placeholder="Briefly describe the purpose of this bank..." 
                    class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-[1.5rem] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-200 transition-all font-bold text-slate-700 outline-none resize-none"></textarea>
                @error('description') <p class="text-rose-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4">
                <button type="submit" :disabled="loading" class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-600/20 text-lg flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
                    <template x-if="!loading">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle" size="22"></i>
                            Initialize Question Bank
                        </div>
                    </template>
                    <template x-if="loading">
                        <div class="flex items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Creating Bank...
                        </div>
                    </template>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
