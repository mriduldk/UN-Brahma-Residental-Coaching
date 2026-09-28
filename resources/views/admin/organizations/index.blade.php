@extends('layouts.admin')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black tracking-tight text-slate-800">Organizations</h1>
        <p class="text-sm font-medium text-slate-500 mt-1">Manage all registered client organizations and their primary admins.</p>
    </div>
    <a href="{{ route('admin.organizations.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all focus:ring-4 focus:ring-purple-100">
        <i data-lucide="plus" size="18"></i> Add Organization
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">Organization Name</th>
                    <th class="px-6 py-4">Code</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Primary Admin</th>
                    <th class="px-6 py-4">Institutions</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                @forelse($organizations as $org)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 font-bold border border-purple-100 shrink-0">
                                {{ substr($org->organization_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $org->organization_name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $org->short_name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-bold border border-slate-200">
                            {{ $org->organization_code }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold {{ $org->type === 'trust' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $org->type === 'trust' ? 'bg-blue-500' : 'bg-emerald-500' }}"></span>
                            {{ ucfirst($org->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($org->user)
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-700">{{ $org->user->name }}</span>
                                <div class="flex flex-col mt-0.5">
                                    @if($org->user->mobile_number)
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">{{ $org->user->mobile_number }}</span>
                                    @endif
                                    <span class="text-xs text-slate-400 leading-none">{{ $org->user->email }}</span>
                                </div>
                            </div>
                        @else
                            <span class="text-slate-400 italic">Unassigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="library" size="14" class="text-slate-400"></i>
                            <span class="font-bold {{ $org->institutions->count() > 0 ? 'text-slate-700' : 'text-slate-400' }}">
                                {{ $org->institutions->count() }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.organizations.show', $org->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Details">
                                <i data-lucide="eye" size="18"></i>
                            </a>
                            <a href="{{ route('admin.organizations.edit', $org->id) }}" class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Edit Organization">
                                <i data-lucide="edit-2" size="18"></i>
                            </a>
                            <form action="{{ route('admin.organizations.destroy', $org->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this organization? All associated data may be lost.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Organization">
                                    <i data-lucide="trash-2" size="18"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="building-2" size="32" class="text-slate-300"></i>
                        </div>
                        <p class="font-medium">No organizations found.</p>
                        <a href="{{ route('admin.organizations.create') }}" class="text-purple-600 hover:text-purple-700 font-bold text-sm mt-2 inline-block">Add the first one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($organizations->hasPages())
    <div class="p-4 border-t border-slate-200">
        {{ $organizations->links() }}
    </div>
    @endif
</div>
@endsection
