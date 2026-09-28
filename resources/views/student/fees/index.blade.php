@extends('layouts.student')

@section('title', 'Academic Fees')

@section('content')
<div class="space-y-8 pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Academic Fees</h1>
            <p class="text-slate-500 text-sm mt-1">Manage and pay your monthly academic tuition fees, and view payment history.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in-down">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    @if(!$enrollment)
        <!-- No Enrollment Warning -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200 p-12 text-center shadow-sm">
            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 text-amber-500 shadow-inner">
                <i data-lucide="info" size="36"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mb-2">No Active Enrollment Found</h3>
            <p class="text-slate-500 text-base max-w-md mx-auto">
                You are currently not enrolled in any active academic session. Please contact the administration to get enrolled.
            </p>
        </div>
    @else
        <!-- Enrollment Details Card -->
        <div class="bg-gradient-to-br from-indigo-900 via-indigo-850 to-slate-900 rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden group">
            <div class="absolute right-0 top-0 h-full w-1/3 bg-white/5 skew-x-12 transform origin-bottom-left pointer-events-none transition-transform group-hover:skew-x-6 duration-700"></div>
            <div class="absolute -bottom-24 -left-20 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/10 border border-white/20 rounded-2xl flex items-center justify-center text-white shadow-inner">
                            <i data-lucide="building-2" size="24"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-indigo-300 uppercase tracking-widest block leading-none mb-1">Enrolled Institution</span>
                            <h2 class="text-2xl font-black tracking-tight">{{ optional(optional($enrollment->schoolClass)->organization)->name ?? 'N/A' }}</h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 pt-2">
                        <div>
                            <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider mb-1">Class & Section</p>
                            <p class="font-black text-base">{{ optional($enrollment->schoolClass)->name ?? 'N/A' }} - {{ optional($enrollment->section)->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider mb-1">Roll Number</p>
                            <p class="font-black text-base">#{{ $enrollment->roll_number }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider mb-1">Session</p>
                            <p class="font-black text-base">{{ optional($enrollment->academicYear)->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-wider mb-1">Monthly Tuition Fee</p>
                            <p class="font-black text-base text-yellow-400">₹{{ number_format($config->monthly_amount ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Status Indicator -->
                <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/10 shrink-0">
                    <p class="text-xs font-bold text-indigo-200 uppercase tracking-wider mb-1">Payment Method Enabled</p>
                    @if($config && $config->allow_student_payment)
                        <div class="flex items-center gap-2 text-emerald-400">
                            <i data-lucide="shield-check" size="20"></i>
                            <span class="font-black text-sm uppercase tracking-wider">Online Dashboard Payments Enabled</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-amber-400">
                            <i data-lucide="alert-triangle" size="20"></i>
                            <span class="font-black text-sm uppercase tracking-wider">Office Desk / Offline Payments Only</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Monthly Fees Checklist Section -->
        <div>
            <h3 class="font-black text-slate-800 text-xl mb-6 flex items-center gap-2">
                <i data-lucide="calendar" class="text-indigo-500" size="24"></i>
                Monthly Pending Fees Checklist
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($months as $month)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-all group flex flex-col justify-between relative overflow-hidden">
                        @if($month['is_paid'])
                            <div class="absolute top-0 right-0 bg-emerald-500 text-white text-[9px] font-black uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow-sm flex items-center gap-1">
                                <i data-lucide="check" size="10"></i> Paid
                            </div>
                        @else
                            <div class="absolute top-0 right-0 bg-amber-500 text-white text-[9px] font-black uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow-sm">
                                Unpaid
                            </div>
                        @endif

                        <div class="mb-6">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 shrink-0
                                {{ $month['is_paid'] ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                                <i data-lucide="{{ $month['is_paid'] ? 'check-circle' : 'circle-dashed' }}" size="22"></i>
                            </div>

                            <h4 class="font-bold text-slate-800 text-lg group-hover:text-indigo-600 transition-colors">{{ $month['formatted'] }}</h4>
                            <p class="text-slate-400 text-xs font-semibold mt-1">Tuition Fee: ₹{{ number_format($config->monthly_amount ?? 0, 2) }}</p>

                            @if($month['is_paid'] && $month['payment'])
                                <div class="mt-4 pt-4 border-t border-slate-100 space-y-1.5 text-xs text-slate-500">
                                    <div class="flex justify-between">
                                        <span class="font-semibold">Paid On:</span>
                                        <span class="font-bold text-slate-700">{{ $month['payment']->payment_date->format('d M, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold">Method:</span>
                                        <span class="font-bold text-slate-700 capitalize">{{ $month['payment']->payment_method }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="font-semibold">Txn ID:</span>
                                        <span class="font-mono text-[10px] text-slate-400 truncate max-w-[120px]" title="{{ $month['payment']->razorpay_payment_id ?? $month['payment']->transaction_id }}">{{ $month['payment']->razorpay_payment_id ?? $month['payment']->transaction_id ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Button -->
                        @if(!$month['is_paid'])
                            @if($config && $config->allow_student_payment && ($config->monthly_amount ?? 0) > 0)
                                <button type="button" 
                                        onclick="payMonthlyFee('{{ $enrollment->id }}', '{{ $month['month_number'] }}', '{{ $month['year'] }}', '{{ $config->monthly_amount }}', this)" 
                                        class="w-full flex items-center justify-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-md shadow-indigo-500/20 group/btn">
                                    <span>Pay Now</span>
                                    <i data-lucide="arrow-right" class="ml-2 transition-transform group-hover/btn:translate-x-1" size="16"></i>
                                </button>
                            @else
                                <div class="w-full flex items-center justify-center gap-1.5 px-4 py-3 bg-slate-50 text-slate-500 font-bold rounded-xl border border-slate-200 text-xs">
                                    <i data-lucide="lock" size="14"></i> Pay at Counter Only
                                </div>
                            @endif
                        @else
                            <div class="w-full flex items-center justify-center gap-1.5 px-4 py-3 bg-emerald-50 text-emerald-700 font-bold rounded-xl border border-emerald-200 text-xs">
                                <i data-lucide="shield-check" size="14"></i> Payment Complete
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment History Section -->
        <div>
            <h3 class="font-black text-slate-800 text-xl mb-6 flex items-center gap-2">
                <i data-lucide="history" class="text-indigo-500" size="24"></i>
                Academic Payment History
            </h3>

            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Purpose</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Payment Month</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Method</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Amount Paid</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Transaction details</th>
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($history as $payment)
                                <tr class="group hover:bg-slate-50/30 transition-colors text-sm">
                                    <td class="px-8 py-5">
                                        <span class="font-bold text-slate-700">Tuition Fee</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="font-semibold text-slate-600">{{ Carbon\Carbon::create()->month($payment->month)->format('F') }} {{ $payment->year }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="font-medium text-slate-500">{{ $payment->payment_date->format('d M, Y') }}</span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-bold capitalize
                                            {{ $payment->payment_method === 'online' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span class="font-black text-slate-800">₹{{ number_format($payment->amount_paid, 2) }}</span>
                                    </td>
                                    <td class="px-8 py-5 font-mono text-xs text-slate-400">
                                        @if($payment->razorpay_payment_id)
                                            <div>Pay ID: {{ $payment->razorpay_payment_id }}</div>
                                            <div class="text-[10px]">Ord ID: {{ $payment->razorpay_order_id }}</div>
                                        @else
                                            <div>Txn ID: {{ $payment->transaction_id ?? 'N/A' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-50 text-emerald-700">
                                            Success
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-12 text-center text-slate-400 italic">
                                        No tuition fee transactions recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

@if($enrollment && $config && $config->allow_student_payment)
<!-- Razorpay Checkout Integration -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    function payMonthlyFee(enrollmentId, month, year, amount, btnElement) {
        // Disable button and show spinner
        const originalHtml = btnElement.innerHTML;
        btnElement.disabled = true;
        btnElement.classList.add('opacity-75', 'cursor-not-allowed');
        btnElement.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Processing...</span>';

        fetch("{{ route('student.fees.order') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                enrollment_id: enrollmentId,
                month: month,
                year: year,
                amount: amount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var options = {
                    "key": data.key,
                    "amount": data.amount,
                    "currency": "INR",
                    "name": data.student_name,
                    "description": "Monthly Fee Payment",
                    "image": "https://iti.nrds.in/assets/img/nrds-logo.png",
                    "order_id": data.order_id,
                    "handler": function (response) {
                        // Submit signature for verification
                        fetch("{{ route('student.fees.verify') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                enrollment_id: enrollmentId,
                                month: month,
                                year: year,
                                amount_paid: amount,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_signature: response.razorpay_signature
                            })
                        })
                        .then(res => res.json())
                        .then(verifyData => {
                            if (verifyData.success) {
                                Swal.fire({
                                    title: 'Payment Successful!',
                                    text: 'Your monthly tuition fee payment has been successfully recorded.',
                                    icon: 'success',
                                    confirmButtonText: 'Great'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', 'Payment verification failed: ' + verifyData.error, 'error');
                                btnElement.disabled = false;
                                btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
                                btnElement.innerHTML = originalHtml;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Verification request failed.', 'error');
                            btnElement.disabled = false;
                            btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
                            btnElement.innerHTML = originalHtml;
                        });
                    },
                    "prefill": {
                        "name": data.student_name,
                        "email": data.student_email,
                        "contact": data.student_phone
                    },
                    "theme": {
                        "color": "#4f46e5"
                    },
                    "modal": {
                        "ondismiss": function() {
                            btnElement.disabled = false;
                            btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
                            btnElement.innerHTML = originalHtml;
                        }
                    }
                };
                var rzp = new Razorpay(options);
                rzp.open();
            } else {
                Swal.fire('Error', data.error || 'Failed to initiate payment.', 'error');
                btnElement.disabled = false;
                btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
                btnElement.innerHTML = originalHtml;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An error occurred during payment initiation.', 'error');
            btnElement.disabled = false;
            btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
            btnElement.innerHTML = originalHtml;
        });
    }
</script>
@endif
@endsection
