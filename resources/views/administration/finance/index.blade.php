@extends('layouts.administration')

@section('title', 'Financial Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Payment Transactions</h2>
            <p class="text-slate-500">Track and manage fee collections</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('administration.finance') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all text-sm font-medium flex items-center">
                <i data-lucide="refresh-cw" size="16" class="mr-2"></i> Reset
            </a>
            <button class="bg-green-600 text-white px-4 py-2.5 rounded-xl hover:bg-green-700 flex items-center text-sm shadow-md shadow-blue-200 transition-all hover:-translate-y-0.5">
                <i data-lucide="download" size="16" class="mr-2"></i> Export
            </button>
        </div>
    </div>

    <!-- Filters and Search Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('administration.finance') }}" method="GET" class="space-y-4">
            <!-- Top Row: Search and Institution -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" size="18"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Student Name, Payment ID, or Order ID..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none text-sm transition-all" autocomplete="off">
                </div>
                <div>
                    <select name="institution_id" onchange="this.form.submit()" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                        <option value="">All Institutions</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->institution_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Bottom Row: Type, Status, Date -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <select name="type" onchange="this.form.submit()" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                        <option value="">All Types</option>
                        <option value="registration" {{ request('type') == 'registration' ? 'selected' : '' }}>Registration</option>
                        <option value="admission" {{ request('type') == 'admission' ? 'selected' : '' }}>Admission</option>
                    </select>
                </div>
                <div>
                    <select name="status" onchange="this.form.submit()" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                        <option value="">All Status</option>
                        <option value="captured" {{ request('status') == 'captured' ? 'selected' : '' }}>Success (Captured)</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="created" {{ request('status') == 'created' ? 'selected' : '' }}>Created/Pending</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 bg-slate-50 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white text-slate-600 transition-all">
                </div>
            </div>

            <!-- Hidden Submit -->
            <button type="submit" class="hidden"></button>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i data-lucide="wallet" size="48" class="text-blue-600"></i>
            </div>
            <p class="text-sm text-slate-500 font-medium">Total Collected</p>
            <h3 class="text-3xl font-bold text-slate-800 mt-1">₹ {{ number_format($totalCollected, 2) }}</h3>
            <div class="w-full h-1 bg-slate-100 mt-4 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 w-[100%]"></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i data-lucide="trending-up" size="48" class="text-green-600"></i>
            </div>
            <p class="text-sm text-slate-500 font-medium">Today's Collection</p>
            <h3 class="text-3xl font-bold text-green-600 mt-1">₹ {{ number_format($todayCollected, 2) }}</h3>
            <div class="w-full h-1 bg-slate-100 mt-4 rounded-full overflow-hidden">
                <div class="h-full bg-green-500 w-[100%]"></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <i data-lucide="alert-circle" size="48" class="text-orange-600"></i>
            </div>
            <p class="text-sm text-slate-500 font-medium">Pending Payments</p>
            <h3 class="text-3xl font-bold text-orange-500 mt-1">₹ {{ number_format($pendingPayments, 2) }}</h3>
            <div class="w-full h-1 bg-slate-100 mt-4 rounded-full overflow-hidden">
                <div class="h-full bg-orange-500 w-[100%]"></div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Transaction ID</th>
                    <th class="px-6 py-4">Order ID</th>
                    <th class="px-6 py-4">Student Name</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Date & Time</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Method</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($transactions as $txn)
                @php
                    $isObject = is_object($txn);
                    $status = $isObject ? $txn->status : $txn['status'];
                    $razorpay_payment_id = $isObject ? $txn->razorpay_payment_id : $txn['razorpay_payment_id'];
                    $order_id = $isObject ? ($txn->order_id ?? $txn->order->razorpay_order_id ?? 'N/A') : $txn['order_id'];
                    $student_name = $isObject ? $txn->student_name : $txn['student_name'];
                    $payment_type = $isObject ? $txn->payment_type : $txn['payment_type'];
                    $created_at = $isObject ? $txn->created_at : $txn['created_at'];
                    $amount = $isObject ? $txn->amount : $txn['amount'];
                    $method = $isObject ? $txn->method : $txn['method'];
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        @if($status == 'captured' || $status == 'authorized' || $status == 'success')
                            <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold border border-green-200">Success</span>
                        @elseif($status == 'failed')
                            <span class="bg-red-50 text-red-700 px-2.5 py-1 rounded-full text-xs font-bold border border-red-200">Failed</span>
                        @else
                            <span class="bg-orange-50 text-orange-700 px-2.5 py-1 rounded-full text-xs font-bold border border-orange-200">{{ ucfirst($status) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-mono font-medium text-slate-600">{{ $razorpay_payment_id ?? 'N/A' }}</td>
                    <td class="px-6 py-4 font-mono text-slate-500">{{ $order_id }}</td>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $student_name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-xs font-bold {{ $payment_type == 'Admission' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $payment_type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500">{{ \Carbon\Carbon::parse($created_at)->format('d M Y, h:i A') }}</td>
                    <td class="px-6 py-4 font-bold text-slate-800">₹{{ number_format($amount, 2) }}</td>
                    <td class="px-6 py-4"><span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">{{ strtoupper($method ?? 'N/A') }}</span></td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            @if($status != 'captured' && $status != 'success')
                                <form action="{{ route('administration.finance.refresh') }}" method="POST" class="inline loading-form">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ strtolower($payment_type) }}">
                                    <input type="hidden" name="id" value="{{ $txn['id'] ?? $txn->id }}">
                                    <button type="submit" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors group" title="Refresh Status">
                                        <i data-lucide="refresh-cw" size="16" class="group-active:animate-spin"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('administration.finance.show', ['type' => strtolower($payment_type), 'id' => $txn['id'] ?? $txn->id]) }}" class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="View Details">
                                <i data-lucide="eye" size="16"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-10 text-center text-slate-500">
                        No transactions found matching your filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $transactions->links() }}
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
