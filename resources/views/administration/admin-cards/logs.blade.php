@extends('layouts.administration')

@section('title', 'Admit Card Download Logs')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">
                <a href="{{ route('administration.admin-cards.index') }}" class="hover:text-blue-600 transition-colors">Admin Cards</a>
                <i data-lucide="chevron-right" size="14"></i>
                <span>Audit Logs</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
                <i data-lucide="shield-check" class="text-blue-600" size="28"></i>
                Admit Card Download Logs
            </h1>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('administration.admin-cards.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-bold text-xs uppercase tracking-wider shadow-sm transition-all hover:border-slate-300">
                <i data-lucide="arrow-left" size="16"></i> Back to Cards
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Downloads -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="download" size="22"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Downloads</p>
                <h3 class="text-2xl font-black text-slate-900 leading-tight mt-0.5">{{ number_format($stats['total_downloads']) }}</h3>
            </div>
        </div>

        <!-- Student Downloads -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="graduation-cap" size="22"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">By Students</p>
                <h3 class="text-2xl font-black text-emerald-600 leading-tight mt-0.5">{{ number_format($stats['student_downloads']) }}</h3>
            </div>
        </div>

        <!-- Admin Downloads -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i data-lucide="shield" size="22"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">By Administrators</p>
                <h3 class="text-2xl font-black text-indigo-600 leading-tight mt-0.5">{{ number_format($stats['admin_downloads']) }}</h3>
            </div>
        </div>

        <!-- Unique IPs -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i data-lucide="globe" size="22"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Unique IP Addresses</p>
                <h3 class="text-2xl font-black text-amber-700 leading-tight mt-0.5">{{ number_format($stats['unique_ips']) }}</h3>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <form method="GET" action="{{ route('administration.admin-cards.logs') }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-6 relative">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-slate-400" size="18"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Application ID, Candidate / Downloader Name, or IP Address..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-slate-50 focus:bg-white transition-all font-medium text-slate-800">
            </div>

            <div class="sm:col-span-3">
                <select name="user_type" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 bg-white text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 font-medium" onchange="this.form.submit()">
                    <option value="">All User Types</option>
                    <option value="student" {{ request('user_type') === 'student' ? 'selected' : '' }}>Student</option>
                    <option value="admin" {{ request('user_type') === 'admin' ? 'selected' : '' }}>Admin / Staff</option>
                </select>
            </div>

            <div class="sm:col-span-3 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-blue-700 transition-colors shadow-md shadow-blue-200 flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" size="15"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'user_type', 'download_type']))
                    <a href="{{ route('administration.admin-cards.logs') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1" title="Clear Filters">
                        <i data-lucide="rotate-ccw" size="14"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Logs Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase font-black text-[11px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5">Downloader</th>
                        <th class="px-4 py-3.5">IP Address</th>
                        <th class="px-4 py-3.5">Application ID</th>
                        <th class="px-4 py-3.5">Candidate Details</th>
                        <th class="px-4 py-3.5">Action Type</th>
                        <th class="px-4 py-3.5">Time of Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($logs as $log)
                    @php
                        $candidate = $log->application?->application_student;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Downloader -->
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-900">{{ $log->user_name }}</div>
                            <div class="mt-1">
                                @if(strtolower($log->user_type) === 'student')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i data-lucide="user" size="10"></i> Student
                                    </span>
                                @elseif(strtolower($log->user_type) === 'admin')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                        <i data-lucide="shield" size="10"></i> Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-600">
                                        {{ ucfirst($log->user_type) }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- IP Address -->
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-mono text-xs font-bold border border-slate-200">
                                <i data-lucide="map-pin" size="12" class="text-slate-400"></i>
                                <span>{{ $log->ip_address }}</span>
                            </div>
                            @if($log->user_agent)
                                <div class="text-[10px] text-slate-400 mt-1 truncate max-w-[200px]" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 30) }}
                                </div>
                            @endif
                        </td>

                        <!-- Application Code -->
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="font-mono font-black text-slate-900 text-xs">
                                {{ $log->application_code ?? ($log->application?->application_code ?? 'App #' . $log->student_application_id) }}
                            </span>
                        </td>

                        <!-- Candidate Details -->
                        <td class="px-4 py-3.5">
                            @if($candidate)
                                <div class="font-bold text-slate-800 text-xs">{{ $candidate->full_name }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $log->application?->applied_for_display ?? 'N/A' }}</div>
                            @else
                                <span class="text-xs text-slate-400 italic">Candidate details unavailable</span>
                            @endif
                        </td>

                        <!-- Action Type -->
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            @if($log->download_type === 'single_pdf')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                    <i data-lucide="file-text" size="12"></i> PDF Download
                                </span>
                            @elseif($log->download_type === 'bulk_pdf')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                    <i data-lucide="files" size="12"></i> Bulk PDF Download
                                </span>
                            @elseif($log->download_type === 'web_print')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                    <i data-lucide="printer" size="12"></i> Web Print View
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-50 text-slate-700 border border-slate-200">
                                    {{ ucfirst(str_replace('_', ' ', $log->download_type)) }}
                                </span>
                            @endif
                        </td>

                        <!-- Time of Download -->
                        <td class="px-4 py-3.5 whitespace-nowrap text-xs text-slate-500">
                            <div class="font-semibold text-slate-800">
                                {{ $log->downloaded_at ? $log->downloaded_at->timezone('Asia/Kolkata')->format('d M, Y • h:i:s A') : $log->created_at->timezone('Asia/Kolkata')->format('d M, Y • h:i:s A') }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                {{ $log->downloaded_at ? $log->downloaded_at->diffForHumans() : $log->created_at->diffForHumans() }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="clock" size="36" class="text-slate-300 mb-2"></i>
                                <span class="font-bold text-sm text-slate-600">No download activity logged yet</span>
                                <span class="text-xs text-slate-400 mt-1">Logs will appear automatically here whenever students or admins download or print admit cards.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
