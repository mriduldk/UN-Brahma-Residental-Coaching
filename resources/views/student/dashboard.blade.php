@extends('layouts.student')

@section('content')
<div class="space-y-8 pb-12">
    <!-- Global Alerts -->
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

    <x-email-verification-alert />

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden group">
        <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 skew-x-12 transform origin-bottom-left pointer-events-none transition-transform group-hover:skew-x-6 duration-700"></div>
        <div class="absolute -bottom-24 -left-20 w-64 h-64 bg-blue-500/30 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10">
            <h2 class="text-3xl font-black mb-2 tracking-tight">Welcome back, {{ explode(' ', $student->full_name)[0] }}!</h2>
            @if($pendingActions->count() > 0)
                <p class="text-blue-100 text-lg max-w-2xl font-medium">
                    You have <span class="font-bold text-white bg-white/20 px-2 py-0.5 rounded-lg">{{ $pendingActions->count() }} action(s)</span> requiring your attention. Don't miss out!
                </p>
            @else
                <p class="text-blue-100 text-lg max-w-2xl font-medium">You are all caught up! Explore new courses or check your application status.</p>
            @endif
            
            <div class="mt-8 flex gap-4">
                <a href="{{ route('student.applications.index') }}" class="px-6 py-3 bg-white text-blue-700 font-bold rounded-xl shadow-lg shadow-black/5 hover:bg-blue-50 hover:scale-105 transition-all inline-flex items-center group/btn">
                    Browse New Application <i data-lucide="arrow-right" class="ml-2 transition-transform group-hover/btn:translate-x-1" size="18"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Drafts -->
        <a href="{{ route('student.applications.history') }}" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex flex-col h-full justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 bg-orange-100/80 rounded-2xl flex items-center justify-center text-orange-600 shadow-sm">
                        <i data-lucide="file-edit" size="24"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-800 mb-1">{{ $stats['draft'] }}</h3>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wide">Drafts</p>
                </div>
            </div>
        </a>

        <!-- Pending Payment -->
        <a href="{{ route('student.applications.history') }}" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex flex-col h-full justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 bg-red-100/80 rounded-2xl flex items-center justify-center text-red-600 shadow-sm">
                        <i data-lucide="credit-card" size="24"></i>
                    </div>
                     @if($stats['payment_pending'] > 0)
                        <span class="inline-flex w-3 h-3 bg-red-500 rounded-full animate-ping absolute top-0 right-0 -mt-1 -mr-1"></span>
                        <span class="inline-flex w-3 h-3 bg-red-500 rounded-full absolute top-0 right-0 -mt-1 -mr-1"></span>
                    @endif
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-800 mb-1">{{ $stats['payment_pending'] }}</h3>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wide">Payment Pending</p>
                </div>
            </div>
        </a>

        <!-- Submitted/Review -->
        <a href="{{ route('student.applications.history') }}" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex flex-col h-full justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 bg-blue-100/80 rounded-2xl flex items-center justify-center text-blue-600 shadow-sm">
                        <i data-lucide="send" size="24"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-800 mb-1">{{ $stats['submitted'] }}</h3>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wide">Submitted</p>
                </div>
            </div>
        </a>

        <!-- Approved -->
        <a href="{{ route('student.applications.history') }}" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <div class="relative flex flex-col h-full justify-between">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 bg-emerald-100/80 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm">
                        <i data-lucide="check-circle-2" size="24"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-800 mb-1">{{ $stats['approved'] }}</h3>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wide">Approved</p>
                </div>
            </div>
        </a>

    </div>

    @if($admissions->isNotEmpty())
    <!-- My Enrollments Section -->
    <div>
        <div class="flex items-center justify-between mb-6 mt-8">
             <h3 class="font-black text-slate-800 text-xl flex items-center gap-2">
                <i data-lucide="graduation-cap" class="text-indigo-500" size="24"></i>
                My Enrollments
            </h3>
        </div>
        
        <div class="grid gap-6 mb-8">
            @foreach($admissions as $admission)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col md:flex-row relative group hover:shadow-md transition-shadow">
                @if($admission->pending_amount > 0)
                    <div class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-bl-xl z-10 shadow-sm">Payment Pending</div>
                @else
                    <div class="absolute top-0 right-0 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-bl-xl z-10 shadow-sm">Fully Paid</div>
                @endif
                
                <div class="p-6 md:w-2/3 border-b md:border-b-0 md:border-r border-slate-100 flex flex-col justify-center">
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-14 h-14 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 shrink-0 shadow-inner group-hover:scale-105 transition-transform">
                            <i data-lucide="building-2" size="26"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 text-lg md:text-xl">{{ $admission->institution->institution_name ?? 'N/A' }}</h4>
                            <p class="text-indigo-600 font-bold">{{ $admission->application_form->application_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-auto border-t border-slate-100 pt-4">
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Admission Code</p>
                            <p class="font-bold text-slate-700 bg-slate-50 inline-block px-2 py-1 rounded border border-slate-100">{{ $admission->admission_code }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Session</p>
                            <span class="inline-block bg-indigo-50 px-2 py-1 rounded text-sm font-bold text-indigo-700 border border-indigo-100">{{ $admission->academic_session }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 md:w-1/3 bg-slate-50/50 flex flex-col justify-between">
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-bold text-slate-500">Total Payable</span>
                            <span class="font-black text-slate-800">₹{{ number_format($admission->total_payable_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="font-bold text-slate-500">Total Paid</span>
                            <span class="font-black text-emerald-600">₹{{ number_format($admission->total_paid_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm pt-3 border-t border-slate-200/60">
                            <span class="font-bold text-slate-700">Pending Amount</span>
                            <span class="font-black text-lg {{ $admission->pending_amount > 0 ? 'text-red-600' : 'text-slate-400' }}">₹{{ number_format($admission->pending_amount, 2) }}</span>
                        </div>
                    </div>
                    
                    @if($admission->pending_amount > 0)
                        <div class="mt-auto flex flex-col gap-3">
                            <button onclick="initiateEmiPayment('{{ $admission->student_application_id }}', this)" class="w-full flex items-center justify-center px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-all shadow-md shadow-red-500/20 group/btn">
                                <span>Pay ₹{{ number_format($admission->emi_amount > 0 ? min($admission->emi_amount, $admission->pending_amount) : $admission->pending_amount, 2) }}</span>
                                <i data-lucide="arrow-right" class="ml-2 transition-transform group-hover/btn:translate-x-1" size="18"></i>
                            </button>
                            <a href="{{ route('student.application.acknowledgment', $admission->student_application_id) }}" class="w-full flex items-center justify-center px-4 py-3 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl border border-slate-200 transition-all shadow-sm group/btn">
                                <i data-lucide="download" class="mr-2 text-slate-500" size="18"></i> Acknowledgment
                            </a>
                            @if($admission->next_payment_date)
                            <p class="text-center text-xs font-bold text-red-500/80 flex items-center justify-center gap-1">
                                <i data-lucide="calendar-clock" size="12"></i> Due by: {{ \Carbon\Carbon::parse($admission->next_payment_date)->format('d M, Y') }}
                            </p>
                            @endif
                        </div>
                    @else
                        <div class="mt-auto flex flex-col gap-3">
                            <div class="flex items-center justify-center px-4 py-3 bg-emerald-50 text-emerald-700 font-bold rounded-xl border border-emerald-200 shadow-sm">
                                <i data-lucide="check-circle-2" class="mr-2" size="20"></i> All dues cleared
                            </div>
                            <a href="{{ route('student.application.acknowledgment', $admission->student_application_id) }}" class="w-full flex items-center justify-center px-4 py-3 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-xl border border-slate-200 transition-all shadow-sm group/btn">
                                <i data-lucide="download" class="mr-2 text-slate-500" size="18"></i> Acknowledgment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Pending Action Items -->
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                 <h3 class="font-black text-slate-800 text-xl flex items-center gap-2">
                    <i data-lucide="activity" class="text-blue-500" size="24"></i>
                    Actions Required
                </h3>
            </div>
            
            @if($pendingActions->count() > 0)
                <div class="grid gap-4">
                    @foreach($pendingActions as $app)
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col md:flex-row items-center gap-6 group">
                        <div class="w-full md:w-auto flex items-center gap-4 flex-1">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shadow-inner shrink-0
                                {{ $app->status == 'draft' ? 'bg-orange-50 text-orange-500' : 'bg-red-50 text-red-500' }}">
                                <i data-lucide="{{ $app->status == 'draft' ? 'file-edit' : 'credit-card' }}" size="24"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $app->application_form->application_name }}</h4>
                                <div class="flex items-center gap-3 mt-1 text-xs font-medium text-slate-500">
                                    <span class="bg-slate-100 px-2 py-0.5 rounded text-slate-600">ID: {{ $app->application_code }}</span>
                                    <span>Updated {{ $app->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="w-full md:w-auto flex justify-end">
                            @if($app->status == 'draft')
                                <a href="{{ route('student.application.form', ['form_id' => $app->application_form_id]) }}" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-orange-500/20 flex items-center w-full md:w-auto justify-center">
                                    Resume Application <i data-lucide="arrow-right" size="16" class="ml-2"></i>
                                </a>
                            @elseif($app->payment_status == 'pending')
                                <a href="{{ route('student.application.payment', ['id' => $app->id]) }}" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-red-500/20 flex items-center w-full md:w-auto justify-center">
                                    Pay Now <i data-lucide="credit-card" size="16" class="ml-2"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-slate-300">
                        <i data-lucide="check" size="32"></i>
                    </div>
                    <h4 class="font-bold text-slate-600 text-lg">No Pending Actions</h4>
                    <p class="text-slate-400 text-sm mt-1">You're all caught up! Browse new courses to apply.</p>
                </div>
            @endif
        </div>

        <!-- Upcoming Deadlines / Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800">Deadlines & Updates</h3>
                    <i data-lucide="calendar" class="text-slate-400" size="18"></i>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($upcomingDeadlines as $form)
                    <div class="p-4 hover:bg-slate-50 transition-colors group">
                        <div class="flex items-start gap-4">
                            <div class="flex flex-col items-center justify-center w-12 h-12 bg-white rounded-xl text-blue-700 border border-slate-100 shadow-sm shrink-0">
                                <span class="text-[10px] font-bold uppercase text-slate-400">{{ $form->deadline ? $form->deadline->format('M') : 'N/A' }}</span>
                                <span class="text-lg font-black leading-none">{{ $form->deadline ? $form->deadline->format('d') : '--' }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-slate-800 text-sm truncate group-hover:text-blue-600 transition-colors" title="{{ $form->application_name }}">
                                    {{ $form->application_name }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-bold {{ $form->deadline && $form->deadline->isPast() ? 'text-red-500 bg-red-50' : 'text-emerald-600 bg-emerald-50' }} px-2 py-0.5 rounded-full">
                                        {{ $form->deadline ? ($form->deadline->isPast() ? 'Closed' : $form->deadline->diffForHumans()) : 'No Deadline' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-400 text-sm italic">
                        No active deadlines found.
                    </div>
                    @endforelse
                </div>
            </div>
            
             <!-- Quick Help -->
            <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i data-lucide="help-circle" size="16"></i>
                    </div>
                    <h4 class="font-bold text-indigo-900">Need Help?</h4>
                </div>
                <p class="text-indigo-700/80 text-xs mb-4 leading-relaxed">
                    If you're facing issues with your application or payment, please contact the admission helpdesk.
                </p>
                <div class="flex flex-col gap-2 text-sm font-semibold text-indigo-800">
                    <div class="flex items-center gap-2">
                         <i data-lucide="phone" size="14" class="opacity-70"></i> +91 6001025603
                    </div>
                    <div class="flex items-center gap-2">
                         <i data-lucide="mail" size="14" class="opacity-70"></i> info.onlinesetu@gmail.com
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    function initiateEmiPayment(applicationId, btnElement) {
        // Global User Rules: Disable button and show spinner on click
        const originalHtml = btnElement.innerHTML;
        btnElement.disabled = true;
        btnElement.classList.add('opacity-75', 'cursor-not-allowed');
        btnElement.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Processing...</span>';

        fetch(`/student/application/${applicationId}/payment/emi`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var options = {
                    "key": data.key,
                    "amount": data.amount,
                    "currency": "INR",
                    "name": data.name,
                    "description": "EMI Installment Payment",
                    "image": "https://iti.nrds.in/assets/img/nrds-logo.png",
                    "order_id": data.order_id,
                    "handler": function (response) {
                        // Create and submit a virtual form for the callback
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = data.callback_url;

                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        form.appendChild(csrfInput);

                        for(const [key, value] of Object.entries(response)) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = value;
                            form.appendChild(input);
                        }

                        document.body.appendChild(form);
                        form.submit();
                    },
                    "prefill": {
                        "name": data.email,
                        "email": data.email,
                        "contact": data.contact
                    },
                    "theme": {
                        "color": "#3b82f6"
                    },
                    "modal": {
                        "ondismiss": function() {
                            // User Global Rule: Re-enable if dismissed
                            fetch(`/student/application/${applicationId}/payment/emi/failure`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    order_id: data.order_id,
                                    error_code: 'CANCELLED',
                                    error_description: 'Payment popup closed by user.'
                                })
                            });
                            btnElement.disabled = false;
                            btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
                            btnElement.innerHTML = originalHtml;
                        }
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function (response){
                    fetch(`/student/application/${applicationId}/payment/emi/failure`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            payment_id: response.error.metadata.payment_id,
                            order_id: response.error.metadata.order_id,
                            error_code: response.error.code,
                            error_description: response.error.description
                        })
                    });
                    alert("Payment Failed: " + response.error.description);
                    btnElement.disabled = false;
                    btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
                    btnElement.innerHTML = originalHtml;
                });
                rzp1.open();
            } else {
                alert('Payment initiation failed: ' + data.error);
                btnElement.disabled = false;
                btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
                btnElement.innerHTML = originalHtml;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred during payment initiation.');
            btnElement.disabled = false;
            btnElement.classList.remove('opacity-75', 'cursor-not-allowed');
            btnElement.innerHTML = originalHtml;
        });
    }
</script>

@endsection
