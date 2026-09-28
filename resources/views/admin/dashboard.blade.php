@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black tracking-tight text-slate-800">Platform Overview</h1>
    <p class="text-sm font-medium text-slate-500 mt-1">Super Admin global statistics across all organizations.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Total Organizations -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col relative overflow-hidden group hover:border-purple-300 transition-colors">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="flex items-center justify-between z-10">
            <h3 class="font-bold text-slate-500 text-sm">Total Organizations</h3>
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">
                <i data-lucide="building-2" size="20"></i>
            </div>
        </div>
        <div class="mt-4 z-10">
            <span class="text-3xl font-black text-slate-800">{{ $totalOrganizations }}</span>
        </div>
    </div>

    <!-- Total Institutions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col relative overflow-hidden group hover:border-blue-300 transition-colors">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="flex items-center justify-between z-10">
            <h3 class="font-bold text-slate-500 text-sm">Total Institutions</h3>
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <i data-lucide="library" size="20"></i>
            </div>
        </div>
        <div class="mt-4 z-10">
            <span class="text-3xl font-black text-slate-800">{{ $totalInstitutions }}</span>
        </div>
    </div>

    <!-- Total Students -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col relative overflow-hidden group hover:border-emerald-300 transition-colors">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="flex items-center justify-between z-10">
            <h3 class="font-bold text-slate-500 text-sm">Total Students</h3>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <i data-lucide="users" size="20"></i>
            </div>
        </div>
        <div class="mt-4 z-10">
            <span class="text-3xl font-black text-slate-800">{{ $totalStudents }}</span>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col relative overflow-hidden group hover:border-orange-300 transition-colors">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="flex items-center justify-between z-10">
            <h3 class="font-bold text-slate-500 text-sm">Total Platform Revenue</h3>
            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">
                <i data-lucide="banknote" size="20"></i>
            </div>
        </div>
        <div class="mt-4 z-10">
            <span class="text-3xl font-black text-slate-800">₹{{ number_format($totalRevenue, 2) }}</span>
        </div>
    </div>

</div>

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Recent Organizations (Spans 2 columns on lg) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 lg:col-span-2 overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                    <i data-lucide="building" size="20" class="text-purple-500"></i> Recent Organizations
                </h3>
            </div>
            <a href="{{ route('admin.organizations.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 group">
                View All <i data-lucide="arrow-right" size="14" class="group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        
        <div class="flex-1 overflow-auto">
            @if($recentOrganizations->count() > 0)
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-bold">
                    <tr>
                        <th class="px-6 py-3">Organization</th>
                        <th class="px-6 py-3 text-center">Code</th>
                        <th class="px-6 py-3 text-center">Type</th>
                        <th class="px-6 py-3 text-right">Added On</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                    @foreach($recentOrganizations as $org)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 font-bold border border-purple-100">
                                    {{ substr($org->organization_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ $org->organization_name }}</p>
                                    <span class="text-xs text-slate-400">{{ $org->short_name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-md text-xs font-bold border border-slate-200">
                                {{ $org->organization_code }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ ucfirst($org->type) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            {{ $org->created_at->format('d M, Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center flex flex-col items-center justify-center h-full">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                    <i data-lucide="ghost" size="32" class="text-slate-300"></i>
                </div>
                <p class="text-slate-500 font-medium">No organizations registered yet.</p>
                <a href="{{ route('admin.organizations.index') }}" class="mt-4 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-sm rounded-lg transition-colors flex items-center gap-2">
                    <i data-lucide="plus" size="16"></i> Manage Organizations
                </a>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                <i data-lucide="zap" size="20" class="text-amber-500"></i> Quick Actions
            </h3>
        </div>
        <div class="p-6 flex flex-col gap-3">
            <a href="{{ route('admin.organizations.create') }}" class="w-full flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-purple-300 hover:bg-purple-50 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="plus" size="20"></i>
                </div>
                <div>
                    <strong class="block text-slate-800 group-hover:text-purple-700">Add Organization</strong>
                    <span class="text-xs text-slate-500">Register a new client entity</span>
                </div>
            </a>
            
            <a href="{{ route('admin.organizations.index') }}" class="w-full flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-colors group">
                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="list" size="20"></i>
                </div>
                <div>
                    <strong class="block text-slate-800 group-hover:text-blue-700">Manage Organizations</strong>
                    <span class="text-xs text-slate-500">View and edit registered clients</span>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection
