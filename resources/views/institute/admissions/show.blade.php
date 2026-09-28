@extends('layouts.institute')

@section('title', 'Application Details')

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    async function downloadAck(url, btn) {
        const icon = btn.querySelector('.download-icon');
        const loader = btn.querySelector('.loader-icon');
        const span = btn.querySelector('span');
        
        // Start Loading
        btn.classList.add('pointer-events-none', 'opacity-70');
        icon.classList.add('hidden');
        loader.classList.remove('hidden');
        const originalText = span.innerText;
        span.innerText = 'Preparing PDF...';

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Download failed');
            
            const blob = await response.blob();
            const downloadUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = downloadUrl;
            
            // Try to get filename from header
            let filename = 'Acknowledgment.pdf';
            const disposition = response.headers.get('content-disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                if (match) filename = match[1].replace(/['"]/g, '');
            }
            
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(downloadUrl);
        } catch (error) {
            console.error(error);
            alert('Failed to download acknowledgment. Please try again.');
        } finally {
            // Stop Loading
            btn.classList.remove('pointer-events-none', 'opacity-70');
            icon.classList.remove('hidden');
            loader.classList.add('hidden');
            span.innerText = originalText;
        }
    }

    async function settleOnline(url, btn) {
        const zapIcon = btn.querySelector('.zap-icon');
        const loader = btn.querySelector('.loader-icon');
        const text = btn.querySelector('.settle-text');
        
        // Start Loading
        btn.disabled = true;
        btn.classList.add('opacity-70');
        if(zapIcon) zapIcon.classList.add('hidden');
        if(loader) loader.classList.remove('hidden');
        const originalText = text.innerText;
        text.innerText = 'Initializing...';

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Failed to initiate settlement');
            }

            const options = {
                "key": data.key,
                "amount": data.amount,
                "currency": "INR",
                "name": data.name,
                "description": "Cash Settlement Payment",
                "order_id": data.order_id,
                "callback_url": data.callback_url,
                "prefill": {
                    "name": "{{ $application->application_student->full_name }}",
                    "email": data.email,
                    "contact": data.contact
                },
                "theme": { "color": "#4f46e5" }
            };

            const rzp = new Razorpay(options);
            
            rzp.on('payment.failed', function (response){
                console.error('Payment Failed', response.error);
                
                fetch("{{ route('institute.admissions.payment.failure', $application->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        payment_id: response.error.metadata.payment_id,
                        order_id: response.error.metadata.order_id,
                        error_code: response.error.code,
                        error_description: response.error.description
                    })
                }).then(res => console.log('Failure logged'))
                  .catch(err => console.error('Failed to log error', err));
            });

            rzp.open();

        } catch (error) {
            console.error(error);
            alert(error.message);
        } finally {
            // Stop Loading
            btn.disabled = false;
            btn.classList.remove('opacity-70');
            if(zapIcon) zapIcon.classList.remove('hidden');
            if(loader) loader.classList.add('hidden');
            text.innerText = originalText;
        }
    }
</script>
@endpush

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Notifications -->
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i data-lucide="check-circle" size="20"></i>
            </div>
            <div class="flex-1">
                <p class="font-bold text-sm">Success</p>
                <p class="text-emerald-600 text-sm">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="p-2 hover:bg-emerald-100 rounded-lg transition-colors">
                <i data-lucide="x" size="16"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-700 shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex-shrink-0 w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                <i data-lucide="alert-circle" size="20"></i>
            </div>
            <div class="flex-1">
                <p class="font-bold text-sm">Action Failed</p>
                <p class="text-rose-600 text-sm">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="p-2 hover:bg-rose-100 rounded-lg transition-colors">
                <i data-lucide="x" size="16"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-700 shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex-shrink-0 w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="shield-alert" size="20"></i>
                </div>
                <div>
                    <p class="font-bold text-sm">Validation Errors</p>
                    <p class="text-rose-600 text-sm">Please correct the following issues:</p>
                </div>
            </div>
            <ul class="list-disc list-inside text-sm text-rose-600 space-y-1 ml-13">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('institute.admissions.index', ['institution_id' => $application->application_form->institution_id]) }}" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors text-slate-500">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $application->application_student->full_name ?? 'Applicant Name' }}</h1>
                <div class="flex items-center gap-2 text-sm text-slate-500 mt-1">
                    <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 font-medium">{{ $application->application_code }}</span>
                    <span>•</span>
                    <span>{{ $application->application_form->application_name ?? 'Unknown Form' }}</span>
                </div>
            </div>
        </div>
        <div>
            @if($application->status == 'approved')
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold border border-green-200 flex items-center gap-1">
                    <i data-lucide="check-circle" size="14"></i> Approved
                </span>
            @elseif($application->status == 'rejected')
                <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-sm font-semibold border border-red-200 flex items-center gap-1">
                    <i data-lucide="x-circle" size="14"></i> Rejected
                </span>
            @else
                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm font-semibold border border-yellow-200 flex items-center gap-1">
                    <i data-lucide="clock" size="14"></i> {{ ucfirst($application->status) }}
                </span>
            @endif
        </div>
    </div>

    @php
        $institution = $application->application_form->institution;
        $admission = $application->admission_student;
        $isFullyPaid = $admission && $admission->pending_amount <= 0;
    @endphp

    @if(!$institution->razorpay_key || !$institution->razorpay_secret)
    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 mb-6 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                <i data-lucide="shield-alert" size="20"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-rose-900">Online Payments Disabled</h3>
                <p class="text-xs text-rose-600">Razorpay gateway is not configured. Please contact the <strong>Organisation Admin</strong> to enable online payments.</p>
            </div>
        </div>
        <span class="text-[10px] font-bold text-rose-500 uppercase tracking-widest bg-white/50 px-3 py-1.5 rounded-lg border border-rose-100">
            Admin Setup Required
        </span>
    </div>
    @endif

    <!-- Admission Status / Payment Section -->
    @if($admission)
        <div class="bg-white border-2 {{ $isFullyPaid ? 'border-emerald-100 shadow-emerald-50' : 'border-blue-100 shadow-blue-50' }} rounded-2xl overflow-hidden shadow-lg mb-6">
            <div class="p-6 border-b border-slate-100 {{ $isFullyPaid ? 'bg-emerald-50/50' : 'bg-blue-50/50' }} flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl {{ $isFullyPaid ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' }} flex items-center justify-center">
                        <i data-lucide="{{ $isFullyPaid ? 'check-circle' : 'landmark' }}" size="28"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Admission Details</h2>
                        <p class="text-sm font-medium {{ $isFullyPaid ? 'text-emerald-600' : 'text-blue-600' }}">
                            {{ $isFullyPaid ? 'Admission Completed' : 'Active Admission Enrollment' }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if($admission)
                        <button onclick="downloadAck('{{ route('institute.admissions.acknowledgment.download', $application->id) }}', this)" 
                           class="{{ $isFullyPaid ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-200' }} text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2 shadow-lg">
                            <i data-lucide="download" class="download-icon" size="18"></i>
                            <i data-lucide="loader-2" class="loader-icon hidden animate-spin" size="18"></i>
                            <span>Download Acknowledgment</span>
                        </button>
                    @else
                        <div class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-bold border border-blue-200">
                             Code: {{ $admission->admission_code }}
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="p-6">
                <!-- Admission Summary Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Admission Code</span>
                        <span class="text-sm font-bold text-slate-900 font-mono">{{ $admission->admission_code }}</span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Total Payable</span>
                        <span class="text-sm font-bold text-slate-900">₹ {{ number_format($admission->total_payable_amount, 2) }}</span>
                    </div>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest block mb-1">Amount Paid</span>
                        <span class="text-sm font-bold text-emerald-900">₹ {{ number_format($admission->total_paid_amount, 2) }}</span>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Balance Pending</span>
                        <span class="text-sm font-bold {{ $admission->pending_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                            ₹ {{ number_format($admission->pending_amount, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Next Actions for EMI -->
                @if(!$isFullyPaid && $admission->payment_type == 'emi')
                    <div class="mt-8 pt-8 border-t border-slate-100">
                        <div class="flex flex-col lg:flex-row gap-8 items-start">
                            <div class="lg:w-1/2 space-y-4">
                                <div class="flex items-center justify-between gap-4">
                                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                        <i data-lucide="calendar-days" size="20" class="text-indigo-600"></i> Next Installment
                                    </h3>
                                    <button onclick="downloadAck('{{ route('institute.admissions.acknowledgment.download', $application->id) }}', this)" 
                                            class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-indigo-100 hover:bg-indigo-50 transition-all">
                                        <i data-lucide="download" class="download-icon" size="14"></i>
                                        <i data-lucide="loader-2" class="loader-icon hidden animate-spin" size="14"></i>
                                        <span>Download Receipt</span>
                                    </button>
                                </div>
                                <div class="p-5 bg-indigo-50 border border-indigo-100 rounded-2xl">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-slate-600 text-sm">Next Payment Due</span>
                                        <span class="px-3 py-1 bg-white border border-indigo-200 rounded-lg text-indigo-700 text-xs font-bold uppercase">
                                            {{ $admission->next_payment_date ? \Carbon\Carbon::parse($admission->next_payment_date)->format('d M, Y') : 'Contact Office' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mb-6">You are currently on a <span class="font-bold text-indigo-700">{{ $admission->emi_months ?? 'N/A' }} months</span> EMI plan. Ensure timely payments to maintain an active enrollment status.</p>
                                    
                                    <form id="next_installment_form" action="{{ route('institute.admissions.payment.store', $application->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_type" value="emi">
                                        <input type="hidden" name="emi_months" value="{{ $admission->emi_months }}">
                                        <div class="space-y-3">
                                            <label class="block text-sm font-bold text-indigo-900">Amount to Pay (₹)</label>
                                            <div class="relative">
                                                <i data-lucide="indian-rupee" class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400" size="18"></i>
                                                <input type="number" name="amount_paid" 
                                                       value="{{ $admission->emi_amount ?? min($admission->pending_amount, 5000) }}" 
                                                       required min="1" step="0.01" max="{{ $admission->pending_amount }}"
                                                       class="w-full pl-10 pr-4 py-3 bg-white border-2 border-indigo-100 rounded-xl focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 outline-none font-bold text-lg text-indigo-800">
                                            </div>
                                            @if(auth()->user()->isInstitutionAdmin($institution->id))
                                            <button type="submit" id="next_installment_btn" class="w-full mt-4 bg-indigo-600 text-white py-4 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all">
                                                <i data-lucide="credit-card" id="next_installment_icon" size="20"></i>
                                                <i data-lucide="loader-2" id="next_installment_spinner" size="20" class="hidden animate-spin"></i>
                                                <span id="next_installment_text">Pay Next Installment Online</span>
                                            </button>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="lg:w-1/2 w-full space-y-4">
                                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                    <i data-lucide="history" size="20" class="text-slate-400"></i> Plan Overview
                                </h3>
                                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                                     <table class="w-full text-sm">
                                        <tbody class="divide-y divide-slate-50">
                                            <tr>
                                                <td class="px-4 py-3 text-slate-500">Academic Session</td>
                                                <td class="px-4 py-3 text-right font-bold text-slate-800">{{ $admission->academic_session }}</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 text-slate-500">Payment Plan</td>
                                                <td class="px-4 py-3 text-right font-bold text-slate-800 uppercase">{{ $admission->payment_type }}</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-3 text-slate-500">Admitted On</td>
                                                <td class="px-4 py-3 text-right font-bold text-slate-800">{{ $admission->created_at->format('d M, Y') }}</td>
                                            </tr>
                                        </tbody>
                                     </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Original Process Admission Fee Section -->
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm mb-6">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800">Process Admission Fee</h2>
                <p class="text-sm text-slate-500">Course Fee: <span class="font-bold text-slate-900">₹ {{ number_format($application->application_form->course_fee ?? 30000, 2) }}</span></p>
            </div>

            <div class="p-6">
                <!-- Tabs -->
                <div class="flex space-x-4 mb-8">
                    @if($admissionConfig && $admissionConfig->allow_full_payment)
                    <button id="full-btn" onclick="openPaymentTab('full')" class="payment-tab-btn flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all bg-blue-600 text-white border border-slate-200 hover:bg-slate-50 flex shadow-md flex items-center justify-center gap-2">
                        <i data-lucide="check-check" size="18"></i> Full Payment
                    </button>
                    @endif
                    
                    @if($admissionConfig && $admissionConfig->allow_emi)
                    <button id="emi-btn" onclick="openPaymentTab('emi')" class="payment-tab-btn flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 flex items-center justify-center gap-2">
                        <i data-lucide="calendar-clock" size="18"></i> EMI Payment
                    </button>
                    @endif

                    @if($admissionConfig && $admissionConfig->allow_cash_payment)
                    <button id="cash-btn" onclick="openPaymentTab('cash')" class="payment-tab-btn flex-1 py-3 px-4 rounded-xl font-bold text-sm transition-all bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 flex items-center justify-center gap-2">
                        <i data-lucide="banknote" size="18"></i> Cash Record
                    </button>
                    @endif
                    
                    @if(!$admissionConfig)
                        <div class="w-full flex flex-col md:flex-row items-center justify-between gap-4 p-4 bg-rose-50 rounded-xl border border-rose-100 text-rose-700">
                            <div class="flex items-center gap-3">
                                <i data-lucide="alert-circle" size="20" class="text-rose-500"></i>
                                <span class="font-bold">Payment Configuration Not Found for this Session ({{ $application->application_form->session }})</span>
                            </div>
                            <a href="{{ route('institute.admission-configs.create') }}?institution_id={{ $application->application_form->institution_id }}" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-lg hover:bg-rose-700 transition-all uppercase tracking-wider shadow-sm flex items-center gap-2">
                                <i data-lucide="settings" size="14"></i> Configure Now
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Full Payment Content -->
                @if($admissionConfig && $admissionConfig->allow_full_payment)
                <div id="full-content" class="payment-tab-content space-y-6">
                    <form id="full_payment_form" action="{{ route('institute.admissions.payment.store', $application->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_type" value="full">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Total Amount</label>
                                <input type="text" value="{{ number_format($application->application_form->course_fee ?? 30000, 2) }}" readonly class="w-full p-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-bold cursor-not-allowed">
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Discount Amount</label>
                                    <input type="number" id="discount_value" name="discount_value" 
                                           value="{{ $admissionConfig->full_payment_discount_amount ?? 0 }}"
                                           placeholder="0" min="0" step="0.01" class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Type</label>
                                    <select id="discount_type" name="discount_type" class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                        <option value="fixed">Rs (₹)</option>
                                        <option value="percentage">%</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100 flex justify-between items-center">
                            <span class="text-blue-800 font-bold">Net Payable Amount</span>
                            @php
                                $courseFee = $application->application_form->course_fee ?? 30000;
                                $defaultDiscount = $admissionConfig->full_payment_discount_amount ?? 0;
                                $payable = max(0, $courseFee - $defaultDiscount);
                            @endphp
                            <input type="text" id="final_amount" readonly class="bg-transparent text-right font-bold text-2xl text-blue-700 outline-none w-40" value="{{ number_format($payable, 2) }}">
                        </div>
                        
                        <input type="hidden" id="full_amount_input" name="amount_paid" value="{{ $payable }}">

                        @if(auth()->user()->isInstitutionAdmin($institution->id))
                        <div class="mt-6 flex justify-end">
                            <button type="submit" id="full_payment_btn" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all flex items-center gap-2">
                                <i data-lucide="credit-card" id="full_payment_icon" size="18"></i>
                                <i data-lucide="loader-2" id="full_payment_spinner" size="18" class="hidden animate-spin"></i>
                                <span id="full_payment_text">Collect Full Payment</span>
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
                @endif

                <!-- EMI Content -->
                @if($admissionConfig && $admissionConfig->allow_emi)
                <div id="emi-content" class="payment-tab-content hidden space-y-6">
                    <form id="emi_payment_form" action="{{ route('institute.admissions.payment.store', $application->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_type" value="emi">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Select Tenure</label>
                                <select id="emi_months" name="emi_months" class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                    @if($admissionConfig->emi_tenures)
                                        @foreach($admissionConfig->emi_tenures as $month)
                                            <option value="{{ $month }}">{{ $month }} Months</option>
                                        @endforeach
                                    @else
                                        <option value="3">3 Months</option>
                                        <option value="6">6 Months</option>
                                    @endif
                                </select>
                            </div>
                            <div class="bg-indigo-50 p-3 rounded-xl border border-indigo-100 flex flex-col justify-center">
                                <span class="text-xs font-bold text-indigo-500 uppercase">Est. Monthly EMI</span>
                                @php
                                    $firstTenure = $admissionConfig->emi_tenures[0] ?? 3;
                                    $balance = ($application->application_form->course_fee ?? 30000) - ($admissionConfig->emi_down_payment ?? 0);
                                    $remainingMonths = max(1, $firstTenure - 1);
                                    $estEmi = $balance / $remainingMonths;
                                @endphp
                                <span class="text-xl font-bold text-indigo-700">₹ <span id="emi_per_month">{{ number_format($estEmi, 2) }}</span></span>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Down Payment / First Installment (₹)</label>
                            <input type="number" id="emi_first_installment" name="amount_paid" 
                                   value="{{ $admissionConfig->emi_down_payment ?? '' }}"
                                   required min="1" step="0.01" class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-lg" placeholder="Enter amount to pay now">
                            <p class="text-xs text-slate-500 mt-1">Enter the amount being collected today.</p>
                        </div>

                        <div class="mt-6 flex justify-end">
                             <button type="submit" id="emi_payment_btn" class="bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
                                <i data-lucide="calendar-check" id="emi_payment_icon" size="18"></i>
                                <i data-lucide="loader-2" id="emi_payment_spinner" size="18" class="hidden animate-spin"></i>
                                <span id="emi_payment_text">Collect First Installment</span>
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Cash Content -->
                @if($admissionConfig && $admissionConfig->allow_cash_payment)
                <div id="cash-content" class="payment-tab-content hidden space-y-6">
                    <form id="cash_payment_form" action="{{ route('institute.admissions.payment.store', $application->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_type" value="cash">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Payer Name</label>
                                <input type="text" name="payer_name" required placeholder="Who is paying?" class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Payer Phone</label>
                                <input type="text" name="payer_phone" required placeholder="Contact Number" class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Received By</label>
                                <input type="text" value="{{ auth()->user()->name }}" readonly class="w-full p-3 bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-bold cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Amount Received (₹)</label>
                                <input type="number" name="amount_paid" required min="1" step="0.01" class="w-full p-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none font-bold text-lg text-emerald-700">
                            </div>
                        </div>

                        @if(auth()->user()->isInstitutionAdmin($institution->id))
                        <div class="mt-6 flex justify-end">
                            <button type="submit" id="cash_payment_btn" class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all flex items-center gap-2">
                                <i data-lucide="wallet" id="cash_payment_icon" size="18"></i>
                                <i data-lucide="loader-2" id="cash_payment_spinner" size="18" class="hidden animate-spin"></i>
                                <span id="cash_payment_text">Record Cash Payment</span>
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Payment History Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Admission Payment Records -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="receipt" size="20" class="text-blue-600"></i> Admission Receipts
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Details</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($application->admission_payments as $payment)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">
                                {{ $payment->created_at->format('d M, Y') }}
                                <div class="text-xs text-slate-400">{{ $payment->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="capitalize text-sm font-medium text-slate-700 block">{{ $payment->payment_type }} Payment</span>
                                @if($payment->payment_type == 'emi')
                                    <span class="text-xs text-slate-500 block">{{ $payment->emi_months }} Months Plan</span>
                                @endif
                                @if($payment->remarks)
                                    <span class="text-xs text-slate-400 block truncate max-w-[120px]" title="{{ $payment->remarks }}">{{ $payment->remarks }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-900">₹ {{ number_format($payment->paid_amount, 2) }}</span>
                                @if($payment->discount_value > 0)
                                    <div class="text-xs text-green-600">Disc: {{ $payment->discount_type == 'fixed' ? '₹' : '' }}{{ $payment->discount_value }}{{ $payment->discount_type == 'percentage' ? '%' : '' }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex flex-col items-end gap-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                    
                                    @if($payment->payment_type == 'cash')
                                        @if($payment->is_settled)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 uppercase">
                                                <i data-lucide="shield-check" size="10"></i> Settled
                                            </span>
                                            <span class="text-[9px] text-slate-400">{{ $payment->settled_at ? \Carbon\Carbon::parse($payment->settled_at)->format('d M, Y') : '' }}</span>
                                        @else
                                            @if(auth()->user()->isInstitutionAdmin($institution->id))
                                            <button 
                                                onclick="settleOnline('{{ route('institute.admissions.payment.settle', [$application->id, $payment->id]) }}', this)"
                                                class="mt-1 inline-flex items-center gap-1.5 px-2 py-1 bg-amber-500 text-white text-[10px] font-bold rounded hover:bg-amber-600 transition-all shadow-sm">
                                                <i data-lucide="zap" class="zap-icon" size="10"></i>
                                                <i data-lucide="loader-2" class="loader-icon hidden animate-spin" size="10"></i>
                                                <span class="settle-text">Settle Online</span>
                                            </button>
                                            @endif
                                            <span class="text-[9px] text-amber-600 font-medium">Pending Settlement</span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 text-sm">
                                No admission payments recorded.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Online Transactions -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="globe" size="20" class="text-indigo-600"></i> Registration Fee Transactions
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Gateway</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($application->payment_transactions as $txn)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">
                                {{ $txn->created_at->format('d M, Y') }}
                                <div class="text-xs text-slate-400">{{ $txn->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-mono text-slate-600 block text-xs" title="{{ $txn->transaction_id }}">{{ $txn->razorpay_payment_id ?? 'N/A' }}</span>
                                <span class="text-xs text-slate-400 block">{{ $txn->method ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-900">₹ {{ number_format($txn->amount, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($txn->status == 'captured')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Success</span>
                                @elseif($txn->status == 'failed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Failed</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">{{ ucfirst($txn->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500 text-sm">
                                No online transactions found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Application Details Section -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-10">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="user-details" size="20" class="text-indigo-600"></i> Application Details
            </h2>
        </div>
        
        <div class="p-6">
            <!-- Grid Layout for Info -->
            <div class="grid grid-cols-1 gap-8">
                
                <!-- 1. Personal & Contact Information -->
                <div>
                     <h3 class="flex items-center text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                        <i data-lucide="user" size="16" class="mr-2 text-slate-400"></i> Personal & Contact Information
                     </h3>
                     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <span class="text-xs text-slate-500 block mb-1">Full Name</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $application->application_student->full_name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block mb-1">Date of Birth / Age</span>
                            <span class="text-sm font-medium text-slate-900">
                                {{ $application->application_student->dob ? \Carbon\Carbon::parse($application->application_student->dob)->format('d M, Y') : '-' }}
                                <span class="text-slate-400 text-xs">({{ $application->application_student->age ?? 'N/A' }} yrs)</span>
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block mb-1">Gender</span>
                            <span class="text-sm font-medium text-slate-900 capitalize">{{ $application->application_student->gender ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block mb-1">Blood Group</span>
                            <span class="text-sm font-medium text-slate-900">{{ $application->application_student->blood_group ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block mb-1">Category</span>
                            <span class="text-sm font-medium text-slate-900">{{ $application->application_student->category ?? '-' }}</span>
                        </div>
                         <div>
                            <span class="text-xs text-slate-500 block mb-1">Religion</span>
                            <span class="text-sm font-medium text-slate-900">{{ $application->application_student->religion ?? '-' }}</span>
                        </div>
                         <div>
                            <span class="text-xs text-slate-500 block mb-1">Community</span>
                            <span class="text-sm font-medium text-slate-900">{{ $application->application_student->community ?? $application->application_student->nationality ?? '-' }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-500 block mb-1">Mobile Number</span>
                            <span class="text-sm font-medium text-slate-900 font-mono">{{ $application->application_student->phone_number ?? '-' }}</span>
                        </div>
                         <div>
                            <span class="text-xs text-slate-500 block mb-1">What’s App Number</span>
                            <span class="text-sm font-medium text-slate-900 font-mono">{{ $application->application_student->alternate_mobile_number ?? '-' }}</span>
                        </div>
                         <div class="col-span-1 md:col-span-2">
                            <span class="text-xs text-slate-500 block mb-1">Email Address</span>
                            <span class="text-sm font-medium text-slate-900">{{ $application->application_student->email ?? '-' }}</span>
                        </div>
                        <div class="col-span-1 md:col-span-2">
                            <span class="text-xs text-slate-500 block mb-1">Employment Exchange Registration Number</span>
                            <span class="text-sm font-medium text-slate-900">{{ $application->application_student->employment_exchange_no ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block mb-1">Permanent Resident of Assam</span>
                            <span class="text-sm font-medium text-slate-900">
                                @if(isset($application->application_student->is_permanent_resident_of_assam))
                                    {{ $application->application_student->is_permanent_resident_of_assam ? 'Yes' : 'No' }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-500 block mb-1">BTC Resident</span>
                            <span class="text-sm font-medium text-slate-900">
                                @if(isset($application->application_student->is_btc_resident))
                                    {{ $application->application_student->is_btc_resident ? 'Yes' : 'No' }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                     </div>
                </div>

                <!-- 2. Parent / Guardian Information -->
                <div>
                     <h3 class="flex items-center text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                        <i data-lucide="users" size="16" class="mr-2 text-slate-400"></i> Parent / Guardian Details
                     </h3>
                     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-slate-50 p-4 rounded-lg">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Father</span>
                            <p class="text-sm font-bold text-slate-900 mb-1">{{ $application->application_parent->father_name ?? '-' }}</p>
                            <p class="text-xs text-slate-600 mb-1">{{ $application->application_parent->father_occupation ?? 'Occupation N/A' }}</p>
                            @if(!empty($application->application_parent->father_annual_income))
                            <p class="text-xs text-slate-600 mb-1">Family Income (p.a.): ₹{{ number_format($application->application_parent->father_annual_income, 2) }}</p>
                            @endif
                             <p class="text-xs text-slate-500 font-mono">{{ $application->application_parent->father_mobile ?? '' }}</p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-lg">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Mother</span>
                            <p class="text-sm font-bold text-slate-900 mb-1">{{ $application->application_parent->mother_name ?? '-' }}</p>
                            <p class="text-xs text-slate-600 mb-1">{{ $application->application_parent->mother_occupation ?? 'Occupation N/A' }}</p>
                             <p class="text-xs text-slate-500 font-mono">{{ $application->application_parent->mother_mobile ?? '' }}</p>
                        </div>
                         <div class="bg-slate-50 p-4 rounded-lg">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Guardian</span>
                            <p class="text-sm font-bold text-slate-900 mb-1">{{ $application->application_parent->guardian_name ?? '-' }}</p>
                            <p class="text-xs text-slate-600 mb-1">Rel: {{ $application->application_parent->guardian_relation ?? 'N/A' }}</p>
                             <p class="text-xs text-slate-500 font-mono">{{ $application->application_parent->guardian_mobile ?? '' }}</p>
                        </div>
                     </div>
                </div>

                <!-- 3. Address Information -->
                @if($application->application_address)
                <div>
                     <h3 class="flex items-center text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                        <i data-lucide="map-pin" size="16" class="mr-2 text-slate-400"></i> Address Details
                     </h3>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                             <h4 class="text-xs font-semibold text-blue-600 uppercase mb-3">Permanent Address</h4>
                             <div class="text-sm text-slate-700 space-y-1">
                                <p><span class="text-slate-500 w-20 inline-block">Village/Town:</span> {{ $application->application_address->permanent_village_town }}</p>
                                <p><span class="text-slate-500 w-24 inline-block">Post Office:</span> {{ $application->application_address->permanent_city ?? '-' }}</p>
                                <p><span class="text-slate-500 w-24 inline-block">Police Station:</span> {{ $application->application_address->permanent_country ?? '-' }}</p>
                                <p><span class="text-slate-500 w-20 inline-block">District:</span> {{ $application->application_address->permanent_district }}</p>
                                <p><span class="text-slate-500 inline-block mr-2">BTCLA Constituency:</span> {{ $application->application_address->permanent_street ?? '-' }}</p>
                                <p><span class="text-slate-500 w-20 inline-block">State:</span> {{ $application->application_address->permanent_state }} - {{ $application->application_address->permanent_pin_code }}</p>
                             </div>
                        </div>
                        <div>
                             <h4 class="text-xs font-semibold text-green-600 uppercase mb-3">Current Address / Communication Address</h4>
                             <div class="text-sm text-slate-700 space-y-1">
                                <p><span class="text-slate-500 w-20 inline-block">Village/Town:</span> {{ $application->application_address->present_village_town }}</p>
                                <p><span class="text-slate-500 w-20 inline-block">Street:</span> {{ $application->application_address->present_street ?? '-' }}</p>
                                <p><span class="text-slate-500 w-24 inline-block">Post Office:</span> {{ $application->application_address->present_city ?? '-' }}</p>
                                <p><span class="text-slate-500 w-24 inline-block">Police Station:</span> {{ $application->application_address->present_country ?? '-' }}</p>
                                <p><span class="text-slate-500 w-20 inline-block">District:</span> {{ $application->application_address->present_district }}</p>
                                <p><span class="text-slate-500 w-20 inline-block">State:</span> {{ $application->application_address->present_state }} - {{ $application->application_address->present_pin_code }}</p>
                             </div>
                        </div>
                     </div>
                </div>
                @endif

                <!-- 4. Academic Qualification -->
                <div>
                     <h3 class="flex items-center text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                        <i data-lucide="graduation-cap" size="16" class="mr-2 text-slate-400"></i> Academic Qualifications
                     </h3>
                     <div class="overflow-x-auto border border-slate-200 rounded-lg">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Exam Passed</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Board / Council / University</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Year</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Total Marks</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Marks Secured</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Percentage</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Division</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @forelse($application->application_academic_qualifications as $acad)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $acad->examination }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $acad->board_university }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $acad->passing_year }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $acad->total_marks ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $acad->marks_obtained ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-slate-900">{{ $acad->percentage_grade }}%</td>
                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $acad->division }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-sm text-slate-500 text-center">No academic records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. Bank Details -->
                 <div>
                     <h3 class="flex items-center text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                        <i data-lucide="landmark" size="16" class="mr-2 text-slate-400"></i> Bank Details
                     </h3>
                     <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                         <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <span class="text-xs text-slate-500 block mb-1">Account Holder Name</span>
                                <span class="text-sm font-medium text-slate-900">{{ $application->application_bank_detail->account_holder_name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 block mb-1">Bank Name</span>
                                <span class="text-sm font-medium text-slate-900">{{ $application->application_bank_detail->bank_name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 block mb-1">Account Number</span>
                                <span class="text-sm font-medium text-slate-900 font-mono">{{ $application->application_bank_detail->account_number ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 block mb-1">IFSC Code</span>
                                <span class="text-sm font-medium text-slate-900 font-mono">{{ $application->application_bank_detail->ifsc_code ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Documents Section -->
            <div class="mt-10 pt-8 border-t border-slate-100 mb-6">
                <h3 class="flex items-center text-sm font-bold text-slate-800 uppercase tracking-wider mb-6">
                    <i data-lucide="files" size="16" class="mr-2 text-slate-400"></i> Uploaded Documents
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @php
                        $docs = $application->application_documents;
                        $docLabels = [
                            'photo' => 'Photograph',
                            'signature' => 'Signature',
                            'aadhaar_card' => 'Aadhaar Card',
                            'hslc_marksheet' => '10th Marksheet',
                            'hs_marksheet' => '12th Marksheet',
                            'graduation_marksheet' => 'Graduation Marksheet',
                            'post_graduation_marksheet' => 'PG Marksheet',
                            'hslc_pass_certificate' => '10th Pass Cert',
                            'hs_pass_certificate' => '12th Pass Cert',
                            'graduation_pass_certificate' => 'Graduation Pass Cert',
                            'post_graduation_pass_certificate' => 'PG Pass Cert',
                            'hslc_admit_card' => '10th Admit Card',
                            'hs_admit_card' => '12th Admit Card',
                            'graduation_admit_card' => 'Graduation Admit',
                            'post_graduation_admit_card' => 'PG Admit',
                            'caste_certificate' => 'Caste Certificate',
                            'income_certificate' => 'Income Certificate',
                            'address_proof' => 'Address Proof',
                        ];
                        $hasDocs = false;
                    @endphp

                    @if($docs)
                        @foreach($docLabels as $key => $label)
                            @if(!empty($docs->$key))
                                @php $hasDocs = true; @endphp
                                <a href="{{ Storage::url($docs->$key) }}" target="_blank" class="flex flex-col p-4 border border-slate-200 rounded-xl hover:border-blue-300 hover:shadow-md hover:bg-white transition-all group bg-slate-50">
                                    <div class="w-12 h-12 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mb-3 self-center group-hover:scale-110 transition-transform">
                                        <i data-lucide="file-check" size="24"></i>
                                    </div>
                                    <div class="text-center overflow-hidden w-full">
                                        <p class="text-sm font-semibold text-slate-800 truncate capitalize group-hover:text-blue-700 mb-1" title="{{ $label }}">
                                            {{ $label }}
                                        </p>
                                        <span class="text-xs text-slate-500 inline-flex items-center bg-white px-2 py-0.5 rounded-full border border-slate-100">
                                            View File <i data-lucide="external-link" size="10" class="ml-1"></i>
                                        </span>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    @endif

                    @if(!$hasDocs)
                        <p class="text-sm text-slate-500 col-span-full italic text-center py-8">No documents uploaded.</p>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</div>
@include('administration.admissions.payment_script')
@endsection
