@extends('layouts.school')

@section('title', 'Collect Fee')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    <!-- Breadcrumbs & Header -->
    <div class="flex items-center gap-4 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 no-print">
        <a href="{{ route('school.fees.index') }}" class="hover:text-indigo-600 transition-colors">Monthly Fee Tracking</a>
        <i data-lucide="chevron-right" size="12"></i>
        <span class="text-indigo-600">Collect Fee</span>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 bg-white border border-slate-200 rounded-[1.5rem] shadow-sm flex items-center justify-center">
                <i data-lucide="credit-card" class="text-indigo-600" size="28"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-indigo-900 tracking-tight leading-none mb-2">Collect Monthly Fee</h1>
                <p class="text-slate-500 font-medium text-sm">Record payment for <span class="text-slate-900 font-bold underline decoration-indigo-200 decoration-2">{{ $enrollment->student->full_name }}</span></p>
            </div>
        </div>

        <div class="text-right">
            <div class="bg-indigo-50 px-5 py-3 rounded-2xl border border-indigo-100 inline-block">
                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest leading-none mb-1 text-center">Month/Year</p>
                <p class="text-lg font-black text-indigo-900 leading-none">{{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>
            </div>
            @if($isPaidOnline)
                <div class="mt-3">
                    <span class="px-5 py-2.5 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-2xl border border-emerald-200 shadow-sm flex items-center justify-center gap-2">
                        <i data-lucide="check-circle" size="16"></i> Paid Online
                    </span>
                </div>
            @endif
        </div>
    </div>

    @if($isPaidOnline)
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-8 py-6 rounded-[2rem] flex items-center gap-6 animate-in fade-in slide-in-from-top-4 duration-500 shadow-sm">
            <div class="w-14 h-14 bg-emerald-600/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="shield-check" class="text-emerald-600" size="28"></i>
            </div>
            <div class="flex-grow">
                <h4 class="font-black text-lg text-emerald-900 leading-none mb-1">Transaction Verified</h4>
                <p class="text-sm font-medium tracking-tight text-emerald-700/80 mb-3">Paid via Razorpay on {{ $existingPayment->payment_date->format('d M, Y') }}</p>
                
                <div class="flex flex-wrap gap-4">
                    <div class="bg-white/50 px-4 py-2 rounded-xl border border-emerald-100">
                        <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600/50 leading-none mb-1">Order ID</p>
                        <p class="text-xs font-black text-emerald-900 font-mono">{{ $existingPayment->razorpay_order_id }}</p>
                    </div>
                    <div class="bg-white/50 px-4 py-2 rounded-xl border border-emerald-100">
                        <p class="text-[9px] font-black uppercase tracking-widest text-emerald-600/50 leading-none mb-1">Payment ID</p>
                        <p class="text-xs font-black text-emerald-900 font-mono">{{ $existingPayment->razorpay_payment_id }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i data-lucide="alert-circle" size="20"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Student Info Card -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-8 flex flex-col items-center text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($enrollment->student->full_name) }}&background=f1f5f9&color=64748b&bold=true&size=128" class="w-24 h-24 rounded-[2rem] border-4 border-slate-50 shadow-md mb-4">
                <h3 class="text-xl font-black text-slate-800 tracking-tight mb-1">{{ $enrollment->student->full_name }}</h3>
                <p class="text-xs font-black text-indigo-600 uppercase tracking-widest mb-6">{{ $enrollment->schoolClass->name }} | {{ $enrollment->section->name ?? 'No Section' }}</p>
                
                <div class="w-full space-y-4 pt-6 border-t border-slate-100">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-bold uppercase text-[9px] tracking-widest">Roll Number</span>
                        <span class="text-slate-800 font-black tracking-tight">{{ $enrollment->roll_number ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-400 font-bold uppercase text-[9px] tracking-widest">Expected Fee</span>
                        <span class="text-emerald-600 font-black tracking-tight">₹{{ number_format($config->monthly_amount ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <form action="{{ route('school.fees.payment.store') }}" method="POST" class="p-8 md:p-10 space-y-8">
                    @csrf
                    <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Amount to Pay (₹)</label>
                            <div class="relative group">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                                <input type="number" 
                                       step="0.01" 
                                       name="amount_paid" 
                                       {{ $isPaidOnline ? 'disabled' : '' }}
                                       value="{{ $existingPayment?->amount_paid ?? ($config->monthly_amount ?? '') }}" 
                                       required
                                       class="w-full pl-10 pr-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-black focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all placeholder:text-slate-300 disabled:opacity-70 disabled:cursor-not-allowed">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Payment Date</label>
                            <input type="date" 
                                   name="payment_date" 
                                   {{ $isPaidOnline ? 'disabled' : '' }}
                                   value="{{ $existingPayment?->payment_date?->format('Y-m-d') ?? date('Y-m-d') }}" 
                                   required
                                   class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-black focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all disabled:opacity-70 disabled:cursor-not-allowed">
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Payment Method</label>
                            <select name="payment_method" {{ $isPaidOnline ? 'disabled' : '' }} class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-black focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed">
                                <option value="cash" {{ ($existingPayment?->payment_method ?? 'cash') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="online" {{ ($existingPayment?->payment_method ?? '') == 'online' ? 'selected' : '' }}>Online Transfer / UPI</option>
                                <option value="cheque" {{ ($existingPayment?->payment_method ?? '') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                        </div>

                    </div>

                        <div class="space-y-2" id="remarks-container">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Remarks</label>
                            <textarea name="remarks" 
                                      rows="3" 
                                      {{ $isPaidOnline ? 'readonly' : '' }}
                                      class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-medium focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all placeholder:text-slate-300 disabled:opacity-70"
                                      placeholder="Any additional notes about this payment...">{{ $existingPayment?->remarks }}</textarea>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                            <button type="button" onclick="window.history.back()" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">Cancel</button>
                            
                            @if(!$isPaidOnline)
                            <div class="flex items-center gap-4">
                                <button type="button" 
                                        id="rzp-button"
                                        class="hidden px-8 py-5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl shadow-xl shadow-emerald-100 transition-all text-xs uppercase tracking-widest flex items-center gap-3 group">
                                    <i data-lucide="shield-check" class="group-hover:scale-125 transition-transform" size="18"></i>
                                    Pay Online (Razorpay)
                                </button>
    
                                <button type="submit" 
                                        id="submit-button"
                                        class="px-10 py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 transition-all text-xs uppercase tracking-widest flex items-center gap-3 group">
                                    <i data-lucide="check-circle" class="group-hover:scale-125 transition-transform" size="18"></i>
                                    {{ $existingPayment ? 'Update Payment Record' : 'Submit Payment' }}
                                </button>
                            </div>
                            @endif
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const paymentMethodSelect = document.querySelector('select[name="payment_method"]');
    const rzpButton = document.getElementById('rzp-button');
    const submitButton = document.getElementById('submit-button');
    const remarksContainer = document.getElementById('remarks-container');
    const paymentForm = document.querySelector('form');

    function togglePaymentButtons() {
        if (paymentMethodSelect.value === 'online') {
            rzpButton.classList.remove('hidden');
            submitButton.classList.add('hidden');
            if(remarksContainer) remarksContainer.classList.add('hidden');
        } else {
            rzpButton.classList.add('hidden');
            submitButton.classList.remove('hidden');
            if(remarksContainer) remarksContainer.classList.remove('hidden');
        }
    }

    paymentMethodSelect.addEventListener('change', togglePaymentButtons);
    document.addEventListener('DOMContentLoaded', togglePaymentButtons);

    paymentForm.onsubmit = function() {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="animate-spin" size="18"></i> Submitting...';
        if(window.lucide) lucide.createIcons();
    };

    rzpButton.onclick = async function(e) {
        const amount = document.querySelector('input[name="amount_paid"]').value;
        const enrollment_id = document.querySelector('input[name="enrollment_id"]').value;
        const month = document.querySelector('input[name="month"]').value;
        const year = document.querySelector('input[name="year"]').value;
        const remarks = document.querySelector('textarea[name="remarks"]').value;

        if (!amount || amount <= 0) {
            alert('Please enter a valid amount.');
            return;
        }

        rzpButton.disabled = true;
        rzpButton.innerHTML = '<i data-lucide="loader-2" class="animate-spin" size="18"></i> Processing...';
        if(window.lucide) lucide.createIcons();

        try {
            const response = await fetch('{{ route('school.fees.payment.razorpay.order') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ enrollment_id, amount })
            });

            const order = await response.json();

            if (!order.success) {
                alert('Error creating order: ' + order.error);
                rzpButton.disabled = false;
                rzpButton.innerHTML = '<i data-lucide="shield-check" size="18"></i> Pay Online (Razorpay)';
                if(window.lucide) lucide.createIcons();
                return;
            }

            const options = {
                "key": order.key,
                "amount": order.amount,
                "currency": "INR",
                "name": "Register India School",
                "description": "Monthly School Fee - " + order.student_name,
                "order_id": order.order_id,
                "handler": async function (response) {
                    // Verify payment
                    const verifyResponse = await fetch('{{ route('school.fees.payment.razorpay.verify') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature: response.razorpay_signature,
                            enrollment_id: enrollment_id,
                            amount_paid: amount,
                            month: month,
                            year: year,
                            remarks: remarks
                        })
                    });

                    const result = await verifyResponse.json();
                    if (result.success) {
                        window.location.href = '{{ route('school.fees.index') }}?class_id={{ $enrollment->class_id }}&month=' + month + '&year=' + year + '&success=Payment+Successful';
                    } else {
                        alert('Payment verification failed.');
                    }
                },
                "prefill": {
                    "name": order.student_name,
                    "email": order.student_email,
                    "contact": order.student_phone
                },
                "theme": {
                    "color": "#4f46e5"
                },
                "modal": {
                    "ondismiss": function(){
                        rzpButton.disabled = false;
                        rzpButton.innerHTML = '<i data-lucide="shield-check" size="18"></i> Pay Online (Razorpay)';
                        if(window.lucide) lucide.createIcons();
                    }
                }
            };

            const rzp1 = new Razorpay(options);
            rzp1.open();
        } catch (error) {
            console.error(error);
            alert('An unexpected error occurred.');
            rzpButton.disabled = false;
            rzpButton.innerHTML = '<i data-lucide="shield-check" size="18"></i> Pay Online (Razorpay)';
            if(window.lucide) lucide.createIcons();
        }
    };
</script>
@endpush
@endsection
