@extends('layouts.school')

@section('title', 'Billing & Subscription')

@section('content')
<div class=" mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Billing & Subscription</h1>
            <p class="text-sm text-slate-500 mt-1">Manage your organization's subscription plan and payments.</p>
        </div>
    </div>

    <!-- @if(session('success'))
    <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl border border-emerald-200 flex items-center gap-3">
        <i data-lucide="check-circle" size="20"></i>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 flex items-center gap-3">
        <i data-lucide="alert-circle" size="20"></i>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
    @endif -->

    @if($isBlocked)
    <div class="bg-red-600 text-white p-6 rounded-2xl shadow-lg shadow-red-900/20 flex flex-col sm:flex-row items-center gap-6 justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="lock" size="24"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold">Access Restricted</h3>
                <p class="text-red-100 text-sm mt-1">Your subscription is past due and the grace period has ended. Please renew to restore full access to the portal.</p>
            </div>
        </div>
    </div>
    @elseif(isset($subscription) && $subscription->status !== 'trialing' && $isPaymentDue)
    <div class="bg-amber-500 text-white p-6 rounded-2xl shadow-lg shadow-amber-900/20 flex flex-col sm:flex-row items-center gap-6 justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" size="24"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold">Payment Due</h3>
                <p class="text-amber-100 text-sm mt-1">You have a pending payment for your subscription. Please clear your dues within the grace period to avoid service interruption.</p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Current Plan Overview -->
        <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">Current Plan Overview</h2>
                @if($subscription->status === 'trialing')
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold tracking-wide uppercase">Free Trial</span>
                @elseif($subscription->status === 'active')
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold tracking-wide uppercase">Active</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold tracking-wide uppercase">Past Due</span>
                @endif
            </div>
            
            <div class="p-6 space-y-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                        <i data-lucide="building-2" size="24"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Business Type</p>
                        <h3 class="text-xl font-bold text-slate-900 mt-1">{{ $subscription->plan->business_type ?? 'N/A' }} Plan</h3>
                        <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                            Your organization is currently enrolled in the {{ $subscription->plan->business_type ?? 'N/A' }} plan.
                            @if($subscription->custom_price_per_month)
                            <br><span class="text-blue-600 font-medium">You have a special administrative discount applied.</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <div>
                        <p class="text-sm text-slate-500 font-medium mb-1">Trial Ends On</p>
                        <p class="font-bold text-slate-800">{{ $subscription->trial_ends_at->format('d M, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium mb-1">Current Period Ends</p>
                        <p class="font-bold text-slate-800">{{ $subscription->current_period_end->format('d M, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Action -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl shadow-xl border border-slate-700 overflow-hidden text-white flex flex-col">
            <div class="p-6 flex-1">
                <h2 class="text-lg font-bold text-white mb-2">Amount Due</h2>
                
                <div class="mt-4 mb-6">
                    @if(isset($subscription->plan) && $amountDue < $subscription->plan->standard_price && $amountDue > 0)
                        <div class="flex items-end gap-2 mb-1">
                            <span class="text-lg font-bold text-slate-500 line-through decoration-red-500/50 decoration-2">₹{{ number_format($subscription->plan->standard_price, 2) }}</span>
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20">Special Offer</span>
                        </div>
                    @endif
                    <span class="text-4xl font-black">₹{{ number_format($amountDue, 2) }}</span>
                    <span class="text-slate-400 font-medium">/mo</span>
                </div>

                <ul class="space-y-3 text-sm text-slate-300">
                    <li class="flex items-start gap-2">
                        <i data-lucide="check-circle-2" size="16" class="text-emerald-400 mt-0.5 shrink-0"></i>
                        <span>Includes all premium features</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="check-circle-2" size="16" class="text-emerald-400 mt-0.5 shrink-0"></i>
                        <span>Unlimited support access</span>
                    </li>
                    @if($amountDue == 0)
                    <li class="flex items-start gap-2">
                        <i data-lucide="info" size="16" class="text-blue-400 mt-0.5 shrink-0"></i>
                        <span class="text-blue-300">You are currently in your free trial period. No payment is required yet.</span>
                    </li>
                    @endif
                </ul>
            </div>

            <div class="p-6 bg-slate-900/50 border-t border-slate-700/50">
                @if($isPaymentDue && $amountDue > 0)
                    <button id="rzp-button" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2">
                        <i data-lucide="credit-card" size="18"></i>
                        Pay Now
                    </button>
                    <p class="text-xs text-center text-slate-500 mt-3 flex items-center justify-center gap-1">
                        <i data-lucide="shield-check" size="12"></i> Secured by Razorpay
                    </p>
                @elseif(!$isPaymentDue && $amountDue > 0)
                    <button disabled class="w-full bg-slate-700 text-slate-400 font-bold py-3 px-4 rounded-xl cursor-not-allowed">
                        Payment Not Yet Due
                    </button>
                @else
                    <button disabled class="w-full bg-slate-700 text-slate-400 font-bold py-3 px-4 rounded-xl cursor-not-allowed">
                        No Payment Due
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-800">Payment History</h2>
            <p class="text-sm text-slate-500 mt-1">Review your past transactions and their current status.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-widest border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Order ID</th>
                        <th class="px-6 py-4">Mode</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $payment->created_at->format('d M, Y h:i A') }}</td>
                            <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $payment->razorpay_order_id }}</td>
                            <td class="px-6 py-4 text-slate-600 font-bold uppercase text-[10px] tracking-widest">{{ $payment->payment_mode ?? '-' }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">₹{{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($payment->status === 'successful')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                        <i data-lucide="check-circle-2" size="14"></i> Successful
                                    </span>
                                @elseif($payment->status === 'failed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        <i data-lucide="x-circle" size="14"></i> Failed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                        <i data-lucide="clock" size="14"></i> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($payment->status !== 'successful')
                                    <form action="{{ route('administration.subscription.refresh', $payment->razorpay_order_id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center justify-end gap-1 ml-auto transition-colors" title="Check with bank if payment was deducted">
                                            <i data-lucide="refresh-cw" size="14"></i> Refresh
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Completed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500 italic">No payment history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Hidden form to submit verification -->
<form id="verify-payment-form" method="POST" action="{{ route('administration.subscription.verify') }}" class="hidden">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="rzp_payment_id">
    <input type="hidden" name="razorpay_order_id" id="rzp_order_id">
    <input type="hidden" name="razorpay_signature" id="rzp_signature">
</form>

@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    const payBtn = document.getElementById('rzp-button');
    if (payBtn) {
        payBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            // Disable button, show loading
            const originalText = payBtn.innerHTML;
            payBtn.innerHTML = '<i data-lucide="loader-2" class="animate-spin" size="18"></i> Processing...';
            payBtn.disabled = true;

            try {
                // Fetch order from backend
                const response = await fetch('{{ route("administration.subscription.order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to initialize payment');
                }

                // Initialize Razorpay
                const options = {
                    "key": data.key,
                    "amount": data.amount,
                    "currency": "INR",
                    "name": "Register India",
                    "description": "Subscription Renewal for " + data.organization_name,
                    "image": "{{ asset('image/logo/logo.png') }}",
                    "order_id": data.order_id,
                    "handler": function (response) {
                        document.getElementById('rzp_payment_id').value = response.razorpay_payment_id;
                        document.getElementById('rzp_order_id').value = response.razorpay_order_id;
                        document.getElementById('rzp_signature').value = response.razorpay_signature;
                        
                        // Show loading state and submit
                        Swal.fire({
                            title: 'Verifying Payment',
                            text: 'Please wait while we confirm your payment...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        document.getElementById('verify-payment-form').submit();
                    },
                    "prefill": {
                        "name": "{{ auth()->user()->name }}",
                        "email": "{{ auth()->user()->email }}"
                    },
                    "theme": {
                        "color": "#2563eb" // blue-600
                    },
                    "modal": {
                        "ondismiss": function(){
                            payBtn.innerHTML = originalText;
                            payBtn.disabled = false;
                        }
                    }
                };
                
                const rzp = new Razorpay(options);
                rzp.open();
                
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Error',
                    text: error.message
                });
                payBtn.innerHTML = originalText;
                payBtn.disabled = false;
            }
        });
    }
});
</script>
@endpush
