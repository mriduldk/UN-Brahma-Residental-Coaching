@extends('layouts.school')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Notification Center</h1>
            <p class="text-slate-500 mt-1">Manage Email, SMS, WhatsApp and Push notifications.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('school.notifications.templates.index') }}" class="px-5 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl shadow-sm hover:bg-slate-50 transition-all text-sm flex items-center gap-2">
                <i data-lucide="layout-template" size="18"></i>
                Message Templates
            </a>
            <a href="{{ route('school.notifications.compose') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-indigo-100 transition-all text-sm flex items-center gap-2">
                <i data-lucide="send" size="18"></i>
                Compose New
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                    <i data-lucide="mail" size="24"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Sent</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($stats['total']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
                    <i data-lucide="clock" size="24"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pending</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($stats['pending']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <i data-lucide="check-circle-2" size="24"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Delivered</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($stats['completed']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
                    <i data-lucide="alert-circle" size="24"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Failed</p>
                    <p class="text-2xl font-black text-slate-800">{{ number_format($stats['failed']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent History -->
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-black text-slate-800 uppercase tracking-tight flex items-center gap-2">
                <i data-lucide="history" class="text-indigo-500" size="20"></i>
                Recent Communication Log
            </h2>
            <a href="{{ route('school.notifications.logs') }}" class="text-xs font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest">View All Logs</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Channel</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Recipient Type</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Subject/Title</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentNotifications as $notification)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center 
                                    @if($notification->channel === 'sms') bg-blue-50 text-blue-600 
                                    @elseif($notification->channel === 'email') bg-purple-50 text-purple-600 
                                    @elseif($notification->channel === 'whatsapp') bg-emerald-50 text-emerald-600
                                    @else bg-rose-50 text-rose-600 @endif">
                                    <i data-lucide="{{ 
                                        $notification->channel === 'sms' ? 'message-square' : (
                                        $notification->channel === 'email' ? 'mail' : (
                                        $notification->channel === 'whatsapp' ? 'phone' : 'bell' )) 
                                    }}" size="16"></i>
                                </div>
                                <span class="text-sm font-bold text-slate-700 uppercase tracking-tight">{{ strtoupper($notification->channel) }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-black rounded-lg uppercase tracking-widest">{{ $notification->recipient_type }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <p class="text-sm font-bold text-slate-800">{{ $notification->title ?? 'No Title' }}</p>
                            <p class="text-[10px] text-slate-400 font-medium truncate max-w-xs">{{ $notification->content }}</p>
                        </td>
                        <td class="px-8 py-5">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'failed' => 'bg-rose-100 text-rose-700',
                                ][$notification->status];
                            @endphp
                            <span class="px-3 py-1.5 {{ $statusClasses }} text-[10px] font-black rounded-full uppercase tracking-widest">{{ $notification->status }}</span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <p class="text-sm font-bold text-slate-800">{{ $notification->created_at->format('d M, Y') }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $notification->created_at->format('h:i A') }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center justify-center opacity-30">
                                <i data-lucide="megaphone" size="48" class="mb-4 text-slate-300"></i>
                                <p class="text-sm font-black text-slate-400 uppercase tracking-widest">No notifications sent yet</p>
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
