@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <x-ui.page-header 
            title="My Notifications" 
            subtitle="Important updates, alerts, and direct messages sent by the school administration" 
            class="mb-0" 
        />
    </div>

    @if($notifications->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 bg-surface border border-border rounded-3xl text-center p-8 shadow-sm">
            <div class="w-16 h-16 bg-primary/5 rounded-full flex items-center justify-center mb-4 text-primary">
                <i data-lucide="bell-off" size="32"></i>
            </div>
            <h3 class="text-lg font-bold text-foreground mb-1">All caught up!</h3>
            <p class="text-sm text-muted max-w-md">You do not have any notifications from the school administration at the moment.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($notifications as $log)
                @php
                    $notification = $log->notification;
                    $channel = $notification->channel ?? 'push';
                    
                    // Match icons to notification channel
                    $icon = 'bell';
                    $variant = 'info';
                    
                    if ($channel === 'email') {
                        $icon = 'mail';
                        $variant = 'info';
                    } elseif ($channel === 'sms') {
                        $icon = 'message-square';
                        $variant = 'warning';
                    } elseif ($channel === 'whatsapp') {
                        $icon = 'message-circle';
                        $variant = 'success';
                    } elseif ($channel === 'push') {
                        $icon = 'bell';
                        $variant = 'primary';
                    }
                @endphp

                <div class="bg-surface border border-border hover:border-primary/20 transition-all duration-300 rounded-3xl p-5 shadow-sm hover:shadow-md flex items-start gap-4">
                    <!-- Icon container -->
                    <div class="hidden sm:flex w-12 h-12 rounded-2xl bg-primary/5 shrink-0 items-center justify-center text-primary">
                        <i data-lucide="{{ $icon }}" size="22"></i>
                    </div>

                    <!-- Main body -->
                    <div class="flex-grow min-w-0">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
                                {{ $log->created_at ? $log->created_at->format('M d, Y | h:i A') : 'N/A' }}
                            </span>
                            <div class="flex items-center gap-2">
                                <x-ui.badge variant="{{ $variant }}" size="sm">
                                    {{ $channel }}
                                </x-ui.badge>
                            </div>
                        </div>

                        <h3 class="text-base font-extrabold text-foreground mb-2 leading-snug">
                            {{ $notification->title ?: 'Announcement from Administration' }}
                        </h3>

                        <div class="text-sm text-muted leading-relaxed whitespace-pre-wrap">{!! e($notification->content) !!}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
