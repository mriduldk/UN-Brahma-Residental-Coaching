@extends('layouts.administration')

@section('title', 'Payment History')

@section('content')
<div class="space-y-8 py-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('administration.forms') }}" class="w-10 h-10 bg-white rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Payment History</h1>
                <p class="text-slate-500 text-sm font-medium">Trace all your quota purchase transactions</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-blue-50 px-4 py-2 rounded-xl border border-blue-100 flex items-center gap-2">
                <div class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></div>
                <span class="text-xs font-bold text-blue-700 uppercase tracking-widest">Real-time Sync</span>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 flex items-center mb-6">
        <i data-lucide="check-circle" class="mr-2" size="18"></i>
        <span class="text-sm font-bold">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 flex items-center mb-6">
        <i data-lucide="alert-circle" class="mr-2" size="18"></i>
        <span class="text-sm font-bold">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Transaction List -->
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Transaction Date</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Purpose / Form</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Reference ID</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Payment ID</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Amount</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $order)
                        @php
                            $latestTransaction = $order->transactions->first();
                        @endphp
                        <tr class="hover:bg-slate-50/30 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-bold text-slate-700">{{ $order->created_at->format('d M, Y') }}</div>
                                <div class="text-[10px] font-medium text-slate-400 mt-0.5 tracking-wide">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-8 py-6 text-sm font-medium text-slate-500">
                                @if($order->applicationForm)
                                    <div class="font-bold text-slate-700">{{ $order->applicationForm->application_name }}</div>
                                    <div class="text-[10px] font-mono text-blue-600 mt-0.5 tracking-wide">{{ $order->applicationForm->application_code }}</div>
                                @else
                                    <div class="text-slate-400 font-medium italic">Quota Top-up</div>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-sm font-medium text-slate-500">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs">{{ $order->razorpay_order_id }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm font-medium text-slate-500">
                                @if($latestTransaction && $latestTransaction->razorpay_payment_id)
                                    <span class="font-mono text-xs">{{ $latestTransaction->razorpay_payment_id }}</span>
                                @else
                                    <span class="text-slate-300 italic uppercase text-[10px]">No Payment ID</span>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                <span class="font-black text-slate-900">₹{{ number_format($order->amount, 2) }}</span>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $statusClasses = match($order->status) {
                                        'paid' => 'bg-green-50 text-green-600 border-green-100',
                                        'failed' => 'bg-red-50 text-red-600 border-red-100',
                                        default => 'bg-orange-50 text-orange-600 border-orange-100'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $statusClasses }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($order->status === 'paid' && $latestTransaction)
                                        <a href="{{ route('administration.configuration.quota.invoice', $latestTransaction->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-900 hover:text-white rounded-xl text-xs font-bold transition-all group-hover:shadow-md">
                                            <i data-lucide="file-text" size="14"></i>
                                            View Invoice
                                        </a>
                                    @else
                                        <form action="{{ route('administration.configuration.quota.refresh', $order->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl text-xs font-bold transition-all">
                                                <i data-lucide="refresh-cw" size="14"></i>
                                                Check Status
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center">
                                <div class="max-w-xs mx-auto space-y-4">
                                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300 mx-auto border border-slate-100">
                                        <i data-lucide="credit-card" size="32"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-800">No Transactions Found</h3>
                                        <p class="text-xs text-slate-400">You haven't purchased any quota slots yet.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
            <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
