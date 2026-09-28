@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('student.finance.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
            <i data-lucide="arrow-left" size="20"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Transaction Details</h2>
            <p class="text-slate-500">Transaction ID: {{ $transaction->razorpay_payment_id ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Card 1: Payment Purpose -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <div class="flex items-center gap-2 text-slate-800 font-bold">
                    <i data-lucide="info" size="20" class="text-blue-600"></i>
                    Payment Info
                </div>
            </div>
            <div class="p-6 space-y-4 flex-1">
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Payment Type</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 rounded text-xs font-bold {{ $type == 'admission' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ ucfirst($type) }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Purpose</label>
                    <p class="text-slate-700 font-medium mt-1">{{ $purpose_title }}</p>
                </div>
                
                @if($application)
                <div class="pt-4 border-t border-slate-100">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                        {{ $type == 'admission' ? 'Admission Code' : 'Application Code' }}
                    </label>
                    <div class="flex items-center justify-between mt-1">
                        <span class="font-mono bg-slate-50 px-2 py-1 rounded text-slate-600 border border-slate-100">
                            {{ ($type == 'admission' && $admission_student) ? ($admission_student->admission_code ?? 'N/A') : ($application->application_code ?? 'N/A') }}
                        </span>
                        <a href="{{ route('student.application.show', $application->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center">
                             View Application <i data-lucide="external-link" size="14" class="ml-1"></i>
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Card 2: Payment Transaction Data -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <div class="flex items-center gap-2 text-slate-800 font-bold">
                    <i data-lucide="credit-card" size="20" class="text-green-600"></i>
                    Transaction Details
                </div>
            </div>
            <div class="p-6 space-y-4 flex-1">
                <div class="bg-green-50/50 p-4 rounded-xl border border-green-100 flex justify-between items-center mb-2">
                    <div>
                        <label class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Total Amount</label>
                        <p class="text-2xl font-black text-green-700">₹ {{ number_format($transaction->amount, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-4 pt-2">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-xs">Payment ID</label>
                        <p class="text-slate-700 font-mono text-sm mt-1 truncate">{{ $transaction->razorpay_payment_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-xs">Method</label>
                        <p class="text-slate-700 font-bold text-sm mt-1">{{ strtoupper($transaction->method ?? 'N/A') }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-xs">Date & Time</label>
                        <p class="text-slate-700 text-sm mt-1">{{ $transaction->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                     <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-xs">Order ID</label>
                        <p class="text-slate-700 font-mono text-sm mt-1 truncate">{{ $order->razorpay_order_id ?? 'N/A' }}</p>
                    </div>
                </div>
                
                 @if($order->status != 'paid')
                    <div class="pt-4 border-t border-slate-100 mt-2">
                        <form action="{{ route('student.finance.refresh') }}" method="POST" class="loading-form w-full">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="id" value="{{ $transaction->id }}">
                            <button type="submit" class="w-full bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 font-bold flex items-center justify-center shadow-sm transition-all focus:ring-2 focus:ring-blue-500">
                                <i data-lucide="refresh-cw" size="16" class="mr-2"></i> Sync Payment Status
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.loading-form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button');
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.add('animate-spin');
                }
            });
        });
    });
</script>
@endsection
