@extends('layouts.school')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('school.notifications.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm">
            <i data-lucide="arrow-left" size="18"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Communication Logs</h1>
            <p class="text-slate-500 mt-1">Detailed history of all outgoing notifications.</p>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Recipient</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Contact Information</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Channel</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Sent Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-[10px]">
                                    {{ substr($log->user->name ?? '?', 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-slate-700">{{ $log->user->name ?? 'System Contact' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <code class="text-xs font-black text-slate-400">{{ $log->recipient_contact }}</code>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <i data-lucide="{{ $log->notification->channel === 'sms' ? 'message-square' : ($log->notification->channel === 'email' ? 'mail' : ($log->notification->channel === 'whatsapp' ? 'phone' : 'bell')) }}" size="14" class="text-indigo-400"></i>
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $log->notification->channel }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 {{ $log->status === 'sent' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }} text-[10px] font-black rounded-lg uppercase tracking-widest">
                                {{ $log->status }}
                            </span>
                            @if($log->error_message)
                                <p class="text-[8px] text-rose-400 font-bold mt-1 max-w-[150px] truncate" title="{{ $log->error_message }}">{{ $log->error_message }}</p>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                            <p class="text-sm font-black text-slate-800">{{ $log->created_at->format('d M, Y') }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $log->created_at->format('h:i A') }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center justify-center opacity-30">
                                <i data-lucide="history" size="48" class="mb-4 text-slate-300"></i>
                                <p class="text-sm font-black text-slate-400 uppercase tracking-widest">No communication logs found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
