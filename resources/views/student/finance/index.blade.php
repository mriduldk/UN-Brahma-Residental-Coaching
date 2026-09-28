@extends('layouts.student')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">My Payments</h1>
            <p class="text-slate-500 text-sm mt-1">Track your payment history for applications and admissions.</p>
        </div>
        <div class="flex items-center gap-3">
             <button onclick="window.location.reload()" class="p-2.5 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                <i data-lucide="refresh-cw" size="20"></i>
            </button>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('student.finance.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative md:col-span-2">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size="18"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search Transaction ID or Application..." 
                       class="w-full pl-10 pr-4 py-2 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
            
            <select name="status" class="w-full px-4 py-2 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="captured" {{ request('status') == 'captured' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="created" {{ request('status') == 'created' ? 'selected' : '' }}>Pending</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition-all shadow-sm">
                    Filter
                </button>
                <a href="{{ route('student.finance.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition-all flex items-center">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/50 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Transaction Details</th>
                        <th class="px-6 py-4">Application</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Method</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($transactions as $txn)
                    @php
                        $payment_type = $txn->payment_type ?? 'N/A';
                        $status = strtolower($txn->status);
                        $method = $txn->method;
                        $amount = $txn->amount;
                        $created_at = $txn->created_at;
                        $order = $txn->order;
                        
                        if($payment_type == 'registration') {
                            $app_name = $txn->application->application_form->application_name ?? 'N/A';
                            $details = $txn->razorpay_payment_id ?? 'ID: ' . $txn->id;
                        } else {
                            $app_name = $order->application_form->application_name ?? 'N/A';
                            $details = $txn->razorpay_payment_id ?? 'ID: ' . $txn->id;
                        }
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-900">{{ $details }}</span>
                                <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-0.5">
                                    {{ $payment_type }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-700">{{ $app_name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badgeClass = match($status) {
                                    'captured', 'success' => 'bg-green-50 text-green-700 border-green-100',
                                    'failed' => 'bg-red-50 text-red-700 border-red-100',
                                    'created', 'pending' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    default => 'bg-slate-50 text-slate-600 border-slate-100',
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase border {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ \Carbon\Carbon::parse($created_at)->format('d M Y, h:i A') }}</td>
                        <td class="px-6 py-4 font-bold text-slate-800">₹{{ number_format($amount, 2) }}</td>
                        <td class="px-6 py-4"><span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">{{ strtoupper($method ?? 'N/A') }}</span></td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                @if($status != 'captured' && $status != 'success')
                                    <form action="{{ route('student.finance.refresh') }}" method="POST" class="inline loading-form">
                                        @csrf
                                        <input type="hidden" name="type" value="{{ strtolower($payment_type) }}">
                                        <input type="hidden" name="id" value="{{ $txn['id'] ?? $txn->id }}">
                                        <button type="submit" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors group" title="Refresh Status">
                                            <i data-lucide="refresh-cw" size="16" class="group-active:animate-spin"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('student.finance.show', ['type' => strtolower($payment_type), 'id' => $txn['id'] ?? $txn->id]) }}" class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="View Details">
                                    <i data-lucide="eye" size="16"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i data-lucide="credit-card" size="48" class="text-slate-300 mb-2"></i>
                                <span class="text-slate-500 font-medium">No payment history found.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
