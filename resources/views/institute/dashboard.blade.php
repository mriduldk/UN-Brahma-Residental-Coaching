@extends('layouts.institute')

@section('title', 'Institute Dashboard')

@section('content')
<div class="space-y-8">

    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Institute Dashboard</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your mapped institutions and view performance.</p>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        <!-- Total Applications -->
        <div class="relative group overflow-hidden bg-white p-6 rounded-3xl shadow-sm border border-slate-100 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-blue-600/10 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="users" size="24"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Students</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-3xl font-black text-slate-900">{{ number_format($totalApplications) }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="relative group overflow-hidden bg-white p-6 rounded-3xl shadow-sm border border-slate-100 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-emerald-600/10 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="indian-rupee" size="24"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Revenue Collected</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-3xl font-black text-slate-900">₹{{ number_format($totalRevenue) }}</h3>
                </div>
            </div>
        </div>

        <!-- Pending EMI Balance -->
        <div class="relative group overflow-hidden bg-white p-6 rounded-3xl shadow-sm border border-slate-100 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-indigo-600/10 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="wallet-2" size="24"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pending EMI Balance</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-3xl font-black text-indigo-600">₹{{ number_format($totalPendingEmi) }}</h3>
                </div>
            </div>
        </div>

        <!-- Unsettled Cash -->
        <div class="relative group overflow-hidden bg-white p-6 rounded-3xl shadow-sm border border-slate-100 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-amber-600/10 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="zap" size="24"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Cash to Settle</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-3xl font-black text-amber-600">₹{{ number_format($unsettledCash) }}</h3>
                </div>
            </div>
        </div>

        <!-- Upcoming EMI Dues -->
        <div class="relative group overflow-hidden bg-white p-6 rounded-3xl shadow-sm border border-slate-100 transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative">
                <div class="w-12 h-12 bg-rose-600/10 text-rose-600 rounded-2xl flex items-center justify-center mb-4">
                    <i data-lucide="calendar-clock" size="24"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">EMI Dues (7 Days)</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-3xl font-black text-rose-600">{{ $upcomingDuesCount }}</h3>
                    <span class="text-xs text-slate-400 uppercase font-bold">Students</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Section for Urgent Attention -->
    @if($unsettledCash > 0 || $upcomingDuesCount > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if($unsettledCash > 0)
        <a href="{{ route('institute.admissions.index') }}" class="group bg-gradient-to-br from-amber-500 to-amber-600 rounded-3xl p-6 text-white shadow-lg shadow-amber-200 flex items-center justify-between transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md group-hover:scale-110 transition-transform">
                    <i data-lucide="zap" size="28"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold">Unsettled Cash Alert</h4>
                    <p class="text-amber-50 text-sm">You have ₹{{ number_format($unsettledCash) }} collected in cash that needs to be settled online.</p>
                </div>
            </div>
            <div class="hidden sm:block">
                 <i data-lucide="chevron-right" size="24" class="opacity-50"></i>
            </div>
        </a>
        @endif

        @if($upcomingDuesCount > 0)
        <a href="{{ route('institute.admissions.index') }}" class="group bg-gradient-to-br from-rose-500 to-rose-600 rounded-3xl p-6 text-white shadow-lg shadow-rose-200 flex items-center justify-between transition-all hover:shadow-xl hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md group-hover:scale-110 transition-transform">
                    <i data-lucide="clock" size="28"></i>
                </div>
                <div>
                    <h4 class="text-lg font-bold">Upcoming EMI Dues</h4>
                    <p class="text-rose-50 text-sm">{{ $upcomingDuesCount }} students have payments due in the next 7 days. Better check them!</p>
                </div>
            </div>
            <div class="hidden sm:block">
                 <i data-lucide="chevron-right" size="24" class="opacity-50"></i>
            </div>
        </a>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- My Institutions -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                 <h3 class="font-bold text-slate-800 text-lg">My Institutions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 text-sm border-b border-slate-100">
                            <th class="pb-3 font-medium">Name</th>
                            <th class="pb-3 font-medium">Code</th>
                            <th class="pb-3 font-medium">Role</th>
                            <th class="pb-3 font-medium">Forms</th>
                            <th class="pb-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @forelse($institutions as $inst)
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-3 font-medium text-slate-800">{{ $inst->institution_name }}</td>
                            <td class="py-3 text-slate-500">{{ $inst->institution_code }}</td>
                            <td class="py-3">
                                <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded-md text-xs font-bold uppercase">{{ $inst->pivot->role }}</span>
                            </td>
                            <td class="py-3 text-slate-500">{{ $inst->application_forms_count ?? 0 }}</td>
                            <td class="py-3">
                                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">Manage</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-slate-500">No institutions mapped to your account.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
            <h3 class="font-bold text-slate-800 text-lg mb-1">Recent Applications</h3>
            <p class="text-sm text-slate-500 mb-6">Latest submitted forms</p>
            
            <div class="space-y-4 flex-1">
                @forelse($recentApplications as $app)
                <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors cursor-pointer group">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs group-hover:scale-110 transition-transform">
                            {{ substr($app->application_student->full_name ?? 'NA', 0, 2) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $app->application_student->full_name ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500">{{ $app->application_form->application_name ?? 'Form' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-500 text-center py-4">No recent applications.</p>
                @endforelse
            </div>
            
            <a href="#" class="w-full mt-4 py-2.5 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors flex items-center justify-center">
                View All Transactions <i data-lucide="arrow-right" size="16" class="ml-2"></i>
            </a>
        </div>
    </div>
</div>
@endsection
