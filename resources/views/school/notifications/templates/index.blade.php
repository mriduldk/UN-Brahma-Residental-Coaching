@extends('layouts.school')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('school.notifications.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm">
                <i data-lucide="arrow-left" size="18"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Message Templates</h1>
                <p class="text-slate-500 mt-1">Manage reusable notification templates for quick communication.</p>
            </div>
        </div>
        <a href="{{ route('school.notifications.templates.create') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-indigo-100 transition-all text-sm flex items-center gap-2">
            <i data-lucide="plus" size="18"></i>
            Create New Template
        </a>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col group">
            <div class="p-6 flex-1">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center 
                        @if($template->channel === 'sms') bg-blue-50 text-blue-600 
                        @elseif($template->channel === 'email') bg-purple-50 text-purple-600 
                        @elseif($template->channel === 'whatsapp') bg-emerald-50 text-emerald-600
                        @else bg-rose-50 text-rose-600 @endif group-hover:scale-110 transition-transform">
                        <i data-lucide="{{ 
                            $template->channel === 'sms' ? 'message-square' : (
                            $template->channel === 'email' ? 'mail' : (
                            $template->channel === 'whatsapp' ? 'phone' : 'bell' )) 
                        }}" size="20"></i>
                    </div>
                    <span class="px-3 py-1.5 {{ $template->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }} text-[10px] font-black rounded-lg uppercase tracking-widest">
                        {{ $template->is_active ? 'Active' : 'Disabled' }}
                    </span>
                </div>
                
                <h3 class="text-lg font-black text-slate-800 mb-2 truncate">{{ $template->name }}</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-4">{{ strtoupper($template->channel) }} Template</p>
                
                <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-600 font-medium line-clamp-3">
                    {!! nl2br(e($template->content)) !!}
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('school.notifications.templates.edit', $template) }}" class="text-xs font-black text-slate-400 hover:text-indigo-600 uppercase tracking-widest transition-colors flex items-center gap-2">
                    <i data-lucide="edit-3" size="14"></i>
                    Edit
                </a>
                <form action="{{ route('school.notifications.templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this template?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-black text-slate-400 hover:text-rose-600 uppercase tracking-widest transition-colors flex items-center gap-2">
                        <i data-lucide="trash-2" size="14"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <div class="flex flex-col items-center justify-center opacity-30">
                <i data-lucide="layout-template" size="48" class="mb-4 text-slate-300"></i>
                <p class="text-sm font-black text-slate-400 uppercase tracking-widest">No templates created yet</p>
                <a href="{{ route('school.notifications.templates.create') }}" class="mt-4 text-indigo-600 font-bold hover:underline">Create your first template</a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
