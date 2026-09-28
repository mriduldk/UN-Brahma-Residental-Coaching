@extends('layouts.administration')

@section('title', 'Quota Checkout')

@section('content')
<!-- Processing Overlay -->
<div id="processing-overlay" class="fixed inset-0 bg-slate-900/80 backdrop-blur-md z-[9999] flex items-center justify-center hidden opacity-0 transition-opacity duration-500">
    <div class="text-center space-y-6 max-w-sm px-6">
        <div class="relative w-24 h-24 mx-auto">
            <div class="absolute inset-0 border-4 border-blue-500/20 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-t-blue-600 rounded-full animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <i data-lucide="shield-check" class="text-blue-600 animate-pulse" size="32"></i>
            </div>
        </div>
        <div>
            <h2 class="text-2xl font-black text-white mb-2">Verifying Payment</h2>
            <p class="text-slate-400 text-sm leading-relaxed">Please do not close this window or refresh the page. We are confirming your transaction with the bank...</p>
        </div>
    </div>
</div>

    <div class="mb-8">
        <a href="{{ route('administration.configuration') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-blue-600 font-bold transition-colors group">
            <i data-lucide="arrow-left" size="18" class="group-hover:-translate-x-1 transition-transform"></i>
            Back to Configuration
        </a>
    </div>

    <div class="text-center">
        <h1 class="text-4xl font-black text-slate-900 mb-2">Complete Your Purchase</h1>
        <p class="text-slate-500 font-medium tracking-wide uppercase text-xs">Instantly upgrade your form creation quota</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
        <!-- Order Summary -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-xl relative overflow-hidden">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i data-lucide="shopping-cart" size="24"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800">Order Summary</h3>
                        <p class="text-slate-500 text-sm">Review your selection before payment</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-blue-600 border border-slate-200">
                                <i data-lucide="file-plus-2" size="20"></i>
                            </div>
                            <div>
                                <div class="font-bold text-slate-800">Additional Form Quota</div>
                                <div class="text-xs text-slate-400">Qty: 1 Unit</div>
                            </div>
                        </div>
                        <div class="text-lg font-black text-slate-900">₹{{ number_format($amount, 2) }}</div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-slate-100">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-slate-500 font-medium">Subtotal</span>
                        <span class="text-slate-700 font-bold">₹{{ number_format($amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-6">
                        <span class="text-slate-500 font-medium">Tax</span>
                        <span class="text-slate-700 font-bold">₹0.00</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold text-slate-900">Total Amount</span>
                        <span class="text-3xl font-black text-blue-600">₹{{ number_format($amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Secure Payment Info -->
            <div class="bg-blue-50 border border-blue-100 p-6 rounded-[1.5rem] flex gap-4">
                <div class="w-12 h-12 bg-blue-600/10 rounded-xl flex items-center justify-center text-blue-600 shrink-0">
                    <i data-lucide="shield-check" size="24"></i>
                </div>
                <div>
                    <h4 class="font-bold text-blue-900 mb-1">Secure Transaction</h4>
                    <p class="text-sm text-blue-700 leading-relaxed">Your payment is processed securely via Razorpay. We do not store your card or bank details on our servers.</p>
                </div>
            </div>
        </div>

        <!-- Payment Action -->
        <div class="space-y-6">
            <div class="bg-slate-900 p-8 rounded-[2rem] border border-slate-800 shadow-2xl relative overflow-hidden sticky top-8">
                <div class="relative z-10 text-center">
                    <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-6 shadow-xl shadow-blue-600/20">
                        <i data-lucide="credit-card" size="32"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Ready to Pay?</h3>
                    <p class="text-slate-400 text-sm mb-8">Click the button below to open the secure payment gateway.</p>

                    <button id="rzp-button" class="group w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black text-sm transition-all flex items-center justify-center gap-3 shadow-xl shadow-blue-600/20 hover:-translate-y-1 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="btn-text" class="flex items-center gap-3">
                            Pay ₹{{ number_format($amount, 0) }}
                            <i data-lucide="arrow-right" size="18" class="group-hover:translate-x-1 transition-transform"></i>
                        </span>
                        <span id="btn-loader" class="hidden">
                            <div class="w-5 h-5 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                        </span>
                    </button>

                    <div class="mt-8 pt-8 border-t border-white/10">
                        <img src="https://razorpay.com/assets/razorpay-glyph.svg" alt="Razorpay" class="h-6 mx-auto opacity-50 contrast-0 filter invert">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('administration.configuration.quota.verify') }}" method="POST" id="verify-form" class="hidden">
    @csrf
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
</form>

@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const btn = document.getElementById('rzp-button');
    const btnText = document.getElementById('btn-text');
    const btnLoader = document.getElementById('btn-loader');
    const overlay = document.getElementById('processing-overlay');

    const showProcessing = () => {
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
    };

    const showLoading = () => {
        btn.disabled = true;
        btnText.classList.add('hidden');
        btnLoader.classList.remove('hidden');
    };

    const hideLoading = () => {
        btn.disabled = false;
        btnText.classList.remove('hidden');
        btnLoader.classList.add('hidden');
    };

    const options = {
        "key": "{{ config('services.razorpay.key') }}",
        "amount": "{{ $amount * 100 }}",
        "currency": "INR",
        "name": "Director of Education, BTC",
        "description": "Additional Form Quota Purchase",
        "image": "{{ asset('logo.png') }}",
        "order_id": "{{ $order_id }}",
        "handler": function (response){
            showProcessing();
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('verify-form').submit();
        },
        "modal": {
            "ondismiss": function(){
                hideLoading();
            }
        },
        "prefill": {
            "name": "{{ auth()->user()->name }}",
            "email": "{{ auth()->user()->email }}"
        },
        "theme": {
            "color": "#2563eb"
        }
    };

    const rzp1 = new Razorpay(options);

    rzp1.on('payment.failed', function (response){
        hideLoading();
        
        // Report failure to backend
        fetch("{{ route('administration.configuration.quota.fail') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                razorpay_order_id: response.error.metadata.order_id,
                razorpay_payment_id: response.error.metadata.payment_id,
                error_code: response.error.code,
                error_description: response.error.description,
                error_source: response.error.source,
                error_step: response.error.step,
                error_reason: response.error.reason
            })
        });

        alert("Payment failed: " + response.error.description);
    });

    btn.onclick = function(e){
        showLoading();
        rzp1.open();
        e.preventDefault();
    }
</script>
@endpush
