@extends('layouts.administration')

@section('content')
<div class="px-8 py-8 h-full flex flex-col min-h-0 bg-slate-50">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Public Notices</h1>
            <p class="text-slate-500 mt-2 text-sm">Manage notifications and downloads for the landing page.</p>
        </div>
        <a href="{{ route('administration.notices.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-blue-700 transition-all shadow-sm">
            <i data-lucide="plus" size="18"></i> Add New Notice
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 flex items-start gap-3 border border-emerald-100">
            <i data-lucide="check-circle" size="20" class="shrink-0"></i>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-sm font-semibold text-slate-600">
                        <th class="px-6 py-4 whitespace-nowrap">Title</th>
                        <th class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap">Document</th>
                        <th class="px-6 py-4 whitespace-nowrap">Published At</th>
                        <th class="px-6 py-4 whitespace-nowrap text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($notices as $notice)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900">{{ $notice->title }}</div>
                            @if($notice->description)
                            <div class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $notice->description }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($notice->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($notice->document_path)
                                <a href="{{ asset('storage/' . $notice->document_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-medium">
                                    <i data-lucide="file-text" size="16"></i> View File
                                </a>
                            @else
                                <span class="text-slate-400">No file</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">
                            {{ $notice->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('administration.notices.edit', $notice->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
                                    <i data-lucide="edit-2" size="18"></i>
                                </a>
                                <form action="{{ route('administration.notices.destroy', $notice->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notice?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" size="18"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="bell-off" size="32" class="text-slate-300 mb-3"></i>
                                <p class="text-base font-medium text-slate-900">No notices found</p>
                                <p class="mt-1">Get started by adding a new public notice.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
