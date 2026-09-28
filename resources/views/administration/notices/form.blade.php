@extends('layouts.administration')

@section('content')
<div class="px-8 py-8 h-full flex flex-col min-h-0 bg-slate-50">
    <div class="mb-8 flex items-center gap-4">
        <a href="{{ route('administration.notices.index') }}" class="p-2 bg-white text-slate-400 hover:text-slate-600 rounded-xl border border-slate-200 shadow-sm transition-all">
            <i data-lucide="arrow-left" size="20"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ isset($notice) ? 'Edit Notice' : 'Create New Notice' }}</h1>
            <p class="text-slate-500 mt-1 text-sm">Publish public notifications and documents to the landing page.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 max-w-4xl">
        <form action="{{ isset($notice) ? route('administration.notices.update', $notice->id) : route('administration.notices.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($notice))
                @method('PUT')
            @endif

            @if($errors->any())
                <div class="bg-red-50 text-red-700 p-4 rounded-xl text-sm border border-red-100">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Notice Title <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $notice->title ?? '') }}" required class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
            </div>

            <div>
                <label for="description" class="block text-sm font-bold text-slate-700 mb-2">Description (Optional)</label>
                <textarea id="description" name="description" rows="3" class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">{{ old('description', $notice->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Attachment (PDF/Image)</label>
                @if(isset($notice) && $notice->document_path)
                    <div class="mb-3">
                        <a href="{{ asset('storage/' . $notice->document_path) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium border border-blue-100">
                            <i data-lucide="file-text" size="16"></i> Current Document
                        </a>
                    </div>
                @endif
                <div class="relative w-full border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 p-6 flex flex-col items-center justify-center hover:bg-slate-100 transition-colors">
                    <i data-lucide="upload-cloud" size="32" class="text-blue-500 mb-3"></i>
                    <p class="text-sm text-slate-600 font-medium">Click to upload or drag and drop</p>
                    <p class="text-xs text-slate-400 mt-1">PDF, JPG, PNG (Max. 10MB)</p>
                    <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                </div>
            </div>

            <div class="flex items-center gap-3 py-2 border-t border-slate-100 pt-6">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $notice->is_active ?? true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
                <div>
                    <p class="text-sm font-bold text-slate-700">Publish Immediately</p>
                    <p class="text-xs text-slate-500">If unchecked, it will be saved as a draft and won't appear on the landing page.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6">
                <a href="{{ route('administration.notices.index') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-sm shadow-blue-600/20 transition-all">
                    {{ isset($notice) ? 'Update Notice' : 'Save Notice' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
