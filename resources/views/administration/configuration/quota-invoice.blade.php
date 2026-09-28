@extends('layouts.administration')

@section('title', 'Invoice - ' . $transaction->order->razorpay_order_id)

@section('content')
<div class="max-w-4xl mx-auto py-12 px-6">
    <!-- Action Bar -->
    <div class="flex items-center justify-between mb-12 no-print">
        <a href="{{ route('administration.configuration.quota.transactions') }}" class="flex items-center gap-2 text-slate-500 hover:text-slate-900 font-bold transition-colors">
            <i data-lucide="arrow-left" size="18"></i>
            Back to Transactions
        </a>
        <a href="{{ route('administration.configuration.quota.invoice.download', $transaction->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-black text-sm flex items-center gap-3 transition-all shadow-xl shadow-blue-200">
            <i data-lucide="download" size="18"></i>
            Download Invoice
        </a>
    </div>

    <!-- Invoice Card -->
    <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 overflow-hidden relative" id="invoice-card">
        <!-- Top Branding Bar -->
        <div class="h-2 bg-blue-600"></div>

        <div class="p-12 md:p-16 space-y-12">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start gap-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('image/logo/logo.png') }}" alt="Director of Education, BTC Logo" class="w-12 h-12 shadow-blue-100 object-cover">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Director of Education, BTC</h2>
                    </div>
                    <div class="text-sm text-slate-400 font-medium leading-relaxed">
                        Digital Management System for Institutions<br>
                        Support: info.onlinesetu@gmail.com<br>
                        www.onlinesetu.in
                    </div>
                </div>
                
                <div class="text-right">
                    <h1 class="text-5xl font-black text-slate-100 uppercase tracking-tighter mb-4 select-none">Invoice</h1>
                    <div class="space-y-1">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">Invoice Date</div>
                        <div class="text-lg font-black text-slate-900">{{ $transaction->created_at->format('d M, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Client & Order Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 pt-12 border-t border-slate-50">
                <div class="space-y-4">
                    <h4 class="text-[10px] font-bold text-blue-600 uppercase tracking-[0.2em]">Billed To</h4>
                    <div class="space-y-1">
                        <div class="text-xl font-black text-slate-900 line-clamp-2">{{ $org->organization_name }}</div>
                        <div class="text-[10px] text-slate-500 font-bold tracking-tight mt-1 uppercase italic">Authorized Organization</div>
                    </div>
                    <div class="pt-4 space-y-1.5 border-t border-slate-50">
                        @if($org->organization_code)
                            <div class="flex items-center gap-2 text-[10px] font-medium text-slate-500">
                                <span class="text-slate-400 uppercase tracking-wider w-16">Org Code:</span>
                                <span class="font-bold text-slate-700 font-mono">{{ $org->organization_code }}</span>
                            </div>
                        @endif
                        @if($org->registration_no)
                            <div class="flex items-center gap-2 text-[10px] font-medium text-slate-500">
                                <span class="text-slate-400 uppercase tracking-wider w-16">Reg No:</span>
                                <span class="font-bold text-slate-700">{{ $org->registration_no }}</span>
                            </div>
                        @endif
                        @if($org->gst_no)
                            <div class="flex items-center gap-2 text-[10px] font-medium text-slate-500">
                                <span class="text-slate-400 uppercase tracking-wider w-16">GST No:</span>
                                <span class="font-bold text-slate-700">{{ $org->gst_no }}</span>
                            </div>
                        @endif
                        @if($org->pan_no)
                            <div class="flex items-center gap-2 text-[10px] font-medium text-slate-500">
                                <span class="text-slate-400 uppercase tracking-wider w-16">PAN No:</span>
                                <span class="font-bold text-slate-700">{{ $org->pan_no }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="space-y-4 md:text-right">
                    <h4 class="text-[10px] font-bold text-blue-600 uppercase tracking-[0.2em]">Order Details</h4>
                    <div class="space-y-2">
                        <div class="flex md:justify-end gap-3 text-sm">
                            <span class="text-slate-400 font-medium">Order ID:</span>
                            <span class="text-slate-900 font-bold font-mono">{{ $transaction->order->razorpay_order_id }}</span>
                        </div>
                        <div class="flex md:justify-end gap-3 text-sm">
                            <span class="text-slate-400 font-medium">Payment ID:</span>
                            <span class="text-slate-900 font-bold font-mono">{{ $transaction->razorpay_payment_id }}</span>
                        </div>
                        <div class="flex md:justify-end gap-3 text-sm">
                            <span class="text-slate-400 font-medium">Status:</span>
                            <span class="text-green-600 font-black uppercase tracking-widest text-[10px]">Paid</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itemized Table -->
            <div class="pt-12">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 rounded-xl overflow-hidden">
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Description</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Qty</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Unit Price</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Total</th>
                        </tr>
                    </thead>
                    @php
                        $totalAmount = (float)$transaction->amount;
                        $baseCreationFee = (float)$globalConfig->form_creation_charge;
                        $regFeeSurcharge = (float)$globalConfig->registration_fee_charge;
                        
                        $isIntegrated = !empty($transaction->order->application_form_id);
                        $hasRegFee = $isIntegrated && $totalAmount >= ($baseCreationFee + $regFeeSurcharge);
                        
                        $creationFeeToDisplay = $hasRegFee ? $baseCreationFee : $totalAmount;
                    @endphp
                    <tbody class="divide-y divide-slate-50">
                        <tr>
                            <td class="px-6 py-8">
                                @if($isIntegrated)
                                    <div class="font-bold text-slate-900 line-clamp-1">Form Creation: {{ $transaction->order->applicationForm->application_name }}</div>
                                    <div class="text-[10px] text-blue-600 font-bold mt-1 uppercase tracking-wider">Form Code: {{ $transaction->order->applicationForm->application_code }}</div>
                                @else
                                    <div class="font-bold text-slate-900">Form Creation Quota Slot</div>
                                    <div class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-wider">Expansion of organization form capacity</div>
                                @endif
                            </td>
                            <td class="px-6 py-8 text-center font-bold text-slate-700">1</td>
                            <td class="px-6 py-8 text-right font-bold text-slate-700">&#8377;{{ number_format($creationFeeToDisplay, 2) }}</td>
                            <td class="px-6 py-8 text-right font-black text-slate-900">&#8377;{{ number_format($creationFeeToDisplay, 2) }}</td>
                        </tr>
                        @if($hasRegFee)
                        <tr>
                            <td class="px-6 py-8">
                                <div class="font-bold text-slate-900">Registration Fee Service</div>
                                <div class="text-[10px] text-orange-500 mt-1 uppercase font-bold tracking-wider">System enabled for taking online registration fees</div>
                            </td>
                            <td class="px-6 py-8 text-center font-bold text-slate-700">1</td>
                            <td class="px-6 py-8 text-right font-bold text-slate-700">&#8377;{{ number_format($regFeeSurcharge, 2) }}</td>
                            <td class="px-6 py-8 text-right font-black text-slate-900">&#8377;{{ number_format($regFeeSurcharge, 2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Footer Calculations -->
            <div class="flex justify-end pt-12">
                <div class="w-full md:w-[26rem] space-y-4">
                    <div class="flex justify-between items-center pt-6 border-t-2 border-slate-900">
                        <span class="text-sm font-black text-slate-500 uppercase tracking-wider">Grand Total <span class="text-[10px] font-bold text-slate-400 normal-case italic ml-1">(Inclusive of 18% GST)</span></span>
                        <span class="text-3xl font-black text-blue-600">&#8377;{{ number_format((float)$transaction->amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Signature / Terms -->
            <div class="pt-24 grid grid-cols-1 md:grid-cols-2 gap-12 items-end">
                <div class="space-y-4">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Notes</div>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                        This is a computer-generated invoice and does not require a physical signature. The quota slots have been automatically activated upon successful payment confirmation.
                    </p>
                </div>
                
                <div class="text-right space-y-4">
                    <div class="w-32 h-[1px] bg-slate-200 ml-auto"></div>
                    <div class="text-[10px] font-bold text-slate-900 uppercase tracking-widest">System Authorized</div>
                </div>
            </div>
        </div>

        <!-- Decorative Bottom Element -->
        <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600"></div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    .no-print { display: none !important; }
    #invoice-card { 
        box-shadow: none !important; 
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>
@endsection
