@extends('layouts.administration')

@section('title', 'Payment Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('administration.finance') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
            <i data-lucide="arrow-left" size="20"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Transaction Details</h2>
            <p class="text-slate-500">Full information for Transaction ID: {{ $transaction->razorpay_payment_id ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Card 1: Payment Purpose -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <div class="flex items-center gap-2 text-slate-800 font-bold">
                    <i data-lucide="info" size="20" class="text-blue-600"></i>
                    Payment Purpose
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
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Student Name</label>
                    <p class="text-slate-900 font-bold text-lg mt-1">{{ $student->full_name ?? 'N/A' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Phone</label>
                        <p class="text-slate-700 mt-1">{{ $student->phone_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Email</label>
                        <p class="text-slate-700 mt-1 truncate" title="{{ $student->email ?? 'N/A' }}">{{ $student->email ?? 'N/A' }}</p>
                    </div>
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
                        <a href="{{ $type == 'admission' ? route('administration.admissions.show', $application->id) : route('administration.applications.detail', $application->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center">
                            View App <i data-lucide="external-link" size="14" class="ml-1"></i>
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
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-xs">Bank/Wallet</label>
                        <p class="text-slate-700 text-sm mt-1">{{ $transaction->bank ?? $transaction->wallet ?? $transaction->vpa ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-xs">Date & Time</label>
                        <p class="text-slate-700 text-sm mt-1">{{ $transaction->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-xs">Gateway Fee</label>
                        <p class="text-slate-600 text-sm mt-1">₹ {{ number_format($transaction->fee ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-xs">Tax (GST)</label>
                        <p class="text-slate-600 text-sm mt-1">₹ {{ number_format($transaction->tax ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Payment Order Data -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <div class="flex items-center gap-2 text-slate-800 font-bold">
                    <i data-lucide="package" size="20" class="text-purple-600"></i>
                    Order Information
                </div>
            </div>
            <div class="p-6 space-y-4 flex-1">
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Razorpay Order ID</label>
                    <p class="text-slate-700 font-mono mt-1 select-all">{{ $order->razorpay_order_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Receipt ID</label>
                    <p class="text-slate-700 mt-1">{{ $order->receipt ?? 'N/A' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Currency</label>
                        <p class="text-slate-700 mt-1 font-bold">{{ $order->currency ?? 'INR' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status</label>
                        @php
                            $status = $order->status ?? 'unknown';
                            $statusColor = match($status) {
                                'paid' => 'bg-green-50 text-green-700 border-green-200',
                                'created' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'attempted' => 'bg-orange-50 text-orange-700 border-orange-200',
                                default => 'bg-slate-50 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <p class="mt-1">
                            <span class="{{ $statusColor }} px-2 py-0.5 rounded text-xs font-bold border capitalize">
                                {{ $status }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Attempts</label>
                        <p class="text-slate-700 mt-1">{{ $order->attempts ?? 0 }}</p>
                    </div>
                    <div class="text-right">
                        @if($order->status != 'paid')
                        <form action="{{ route('administration.finance.refresh') }}" method="POST" class="loading-form">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">
                            <input type="hidden" name="id" value="{{ $transaction->id }}">
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 text-xs font-bold flex items-center shadow-sm transition-all focus:ring-2 focus:ring-blue-500">
                                <i data-lucide="refresh-cw" size="14" class="mr-2"></i> Sync Status
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JSON Response / Raw Data (Accordions) -->
    <div class="space-y-4 pb-12">
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
            <button onclick="toggleRaw('raw-transaction')" class="w-full px-6 py-4 flex justify-between items-center bg-slate-50 hover:bg-slate-100 transition-colors">
                <span class="font-bold text-slate-700 flex items-center gap-2">
                    <i data-lucide="code" size="18"></i> Raw Transaction Response
                </span>
                <i data-lucide="chevron-down" size="20" id="icon-raw-transaction"></i>
            </button>
            <div id="raw-transaction" class="hidden p-6 bg-slate-900 overflow-x-auto">
                <pre class="text-xs text-green-400 leading-relaxed font-mono">{{ json_encode(is_string($transaction->json_response) ? json_decode($transaction->json_response) : $transaction->json_response, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
            <button onclick="toggleRaw('raw-order')" class="w-full px-6 py-4 flex justify-between items-center bg-slate-50 hover:bg-slate-100 transition-colors">
                <span class="font-bold text-slate-700 flex items-center gap-2">
                    <i data-lucide="file-json" size="18"></i> Raw Order Response
                </span>
                <i data-lucide="chevron-down" size="20" id="icon-raw-order"></i>
            </button>
            <div id="raw-order" class="hidden p-6 bg-slate-900 overflow-x-auto">
                <pre class="text-xs text-blue-300 leading-relaxed font-mono">{{ json_encode(is_string($order->json_response) ? json_decode($order->json_response) : $order->json_response, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleRaw(id) {
        const el = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            el.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

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
