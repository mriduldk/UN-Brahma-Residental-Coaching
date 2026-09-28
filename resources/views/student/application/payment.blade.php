@extends('layouts.student')

@section('title', 'Complete Payment')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('student.applications.index') }}" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Complete Payment</h1>
            <p class="text-slate-500">Securely pay your application fee via Razorpay</p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative flex items-center" role="alert">
            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative flex items-center" role="alert">
            <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg relative flex items-center" role="alert">
            <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
            <span class="block sm:inline">{{ session('warning') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg relative flex items-center" role="alert">
            <i data-lucide="info" class="w-5 h-5 mr-2"></i>
            <span class="block sm:inline">{{ session('info') }}</span>
        </div>
    @endif

    <!-- Payment Card --><div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-100 font-medium mb-1">Application Fee</p>
                    <h2 class="text-4xl font-bold">₹ {{ number_format($pendingOrder->amount ?? $application->application_form->getApplicationFeeFor($student), 2) }}</h2>
                </div>
                <div class="bg-white/20 p-3 rounded-xl backdrop-blur-sm">
                    <i data-lucide="credit-card" class="w-8 h-8 text-white"></i>
                </div>
            </div>
            <div class="mt-6 flex items-center text-sm text-blue-100">
                <i data-lucide="shield-check" class="w-4 h-4 mr-2"></i>
                Secure Payment Gateway by Razorpay
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Order Details -->
            <div class="space-y-4">
                <div class="flex justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Application Code</span>
                    <span class="font-mono font-medium text-slate-800">{{ $application->application_code }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Applicant Name</span>
                    <span class="font-medium text-slate-800">{{ $student->full_name }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Course / Trade</span>
                    <span class="font-medium text-slate-800">{{ $application->application_form->application_name }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-slate-100">
                    <span class="text-slate-500">Transaction ID (Order)</span>
                    <span class="font-mono text-sm text-slate-600">{{ $pendingOrder->razorpay_order_id ?? 'Generating...' }}</span>
                </div>
            </div>

            <!-- Action -->
            @if(isset($pendingOrder) && $pendingOrder->razorpay_order_id)
                <form method="POST" action="https://api.razorpay.com/v1/checkout/embedded" id="hosted-checkout-form">
                    <input type="hidden" name="key_id" value="{{ $razorpayKey }}">
                    <input type="hidden" name="amount" value="{{ $pendingOrder->amount * 100 }}">
                    <input type="hidden" name="currency" value="INR">
                    <input type="hidden" name="order_id" value="{{ $pendingOrder->razorpay_order_id }}">
                    <input type="hidden" name="name" value="Directorate of Education, BTC">
                    <input type="hidden" name="description" value="Application Fee for {{ $application->application_form->application_name }}">
                    <input type="hidden" name="image" value="{{ asset('logo.png') }}">
                    <input type="hidden" name="prefill[name]" value="{{ $student->full_name }}">
                    <input type="hidden" name="prefill[contact]" value="{{ $student->phone_number }}">
                    <input type="hidden" name="prefill[email]" value="{{ $student->email }}">
                    <input type="hidden" name="notes[address]" value="Kokrajhar, Bodoland Territorial Council, Assam — 783370">
                    <input type="hidden" name="callback_url" value="{{ route('student.application.payment.process', ['application_id' => $application->id]) }}">
                    <input type="hidden" name="cancel_url" value="{{ route('student.application.payment', ['id' => $application->id]) }}">
                    
                    <button type="submit" id="rzp-button1" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-1 flex items-center justify-center">
                        Pay Now <i data-lucide="arrow-right" class="w-5 h-5 ml-2"></i>
                    </button>
                </form>
                
                <form action="{{ route('student.application.payment.refresh') }}" method="POST" class="mt-4" id="refresh-payment-form">
                    @csrf
                    <input type="hidden" name="application_id" value="{{ $application->id }}">
                    <button type="submit" id="btn-refresh-status" class="w-full bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold py-3 rounded-xl transition-colors flex items-center justify-center">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Check Payment Status
                    </button>
                    <p class="text-xs text-center text-slate-400 mt-2">Click this if you paid but the page didn't update.</p>
                </form>

                <script>
                    document.getElementById('refresh-payment-form').addEventListener('submit', function() {
                        var btn = document.getElementById('btn-refresh-status');
                        btn.disabled = true;
                        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i> Checking...';
                        lucide.createIcons();
                    });

                    document.getElementById('hosted-checkout-form').addEventListener('submit', function() {
                        var btn = document.getElementById('rzp-button1');
                        btn.disabled = true;
                        btn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin mr-2"></i> Connecting...';
                        lucide.createIcons();
                    });
                </script>
            @else
                <div class="bg-red-50 text-red-600 p-4 rounded-xl flex items-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
                    <span>Order creation failed. Please refresh or contact support.</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Security Note -->
    <div class="text-center text-slate-400 text-sm flex items-center justify-center">
        <i data-lucide="lock" class="w-3 h-3 mr-1"></i>
        Your payment information is encrypted and secure.
    </div>
</div>

@endsection
