@extends('layouts.school')

@section('title', 'Edit Question Bank')

@section('content')
<div class="max-w-2xl mx-auto" x-data="{
    confirmDelete() {
        Swal.fire({
            title: 'Delete Question Bank?',
            text: 'You are about to delete this bank. This action will also remove all questions within it and cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Bank',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-[2rem]',
                confirmButton: 'rounded-xl font-bold py-3 px-6',
                cancelButton: 'rounded-xl font-bold py-3 px-6'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form').submit();
            }
        })
    }
}">
    <div class="mb-8 flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('administration.question-banks.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 transition-all">
                <i data-lucide="arrow-left" size="18"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-indigo-950 tracking-tight">Edit Question Bank</h1>
                <p class="text-slate-500 text-sm">Update the details for this question repository.</p>
            </div>
        </div>
        
        <form action="{{ route('administration.question-banks.destroy', $questionBank) }}" method="POST" id="delete-form" class="hidden">
            @csrf
            @method('DELETE')
        </form>
        <button type="button" @click="confirmDelete()" class="p-4 bg-rose-50 text-rose-500 hover:bg-rose-600 hover:text-white rounded-2xl transition-all border border-rose-100/50" title="Delete Bank">
            <i data-lucide="trash-2" size="22"></i>
        </button>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-indigo-50/50 p-10">
        <form action="{{ route('administration.question-banks.update', $questionBank) }}" method="POST" class="space-y-8" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            @method('PUT')
            
            <div class="space-y-2">
                <label for="title" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Bank Title</label>
                <input type="text" name="title" id="title" required value="{{ $questionBank->title }}"
                    class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-[1.5rem] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-200 transition-all font-bold text-slate-700 outline-none">
                @error('title') <p class="text-rose-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label for="description" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Description (Optional)</label>
                <textarea name="description" id="description" rows="4" 
                    class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-[1.5rem] focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-200 transition-all font-bold text-slate-700 outline-none resize-none">{{ $questionBank->description }}</textarea>
                @error('description') <p class="text-rose-500 text-xs mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4">
                <button type="submit" :disabled="loading" class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl hover:bg-indigo-700 transition-all shadow-2xl shadow-indigo-600/20 text-lg flex items-center justify-center gap-3 disabled:opacity-70 disabled:cursor-not-allowed">
                    <template x-if="!loading">
                        <div class="flex items-center gap-3">
                            <i data-lucide="check-circle" size="22"></i>
                            Update Configuration
                        </div>
                    </template>
                    <template x-if="loading">
                        <div class="flex items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving Changes...
                        </div>
                    </template>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
