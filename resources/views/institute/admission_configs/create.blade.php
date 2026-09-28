@extends('layouts.institute')

@section('title', 'New Admission Configuration')

@section('content')
<div class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">New Admission Configuration</h1>
            <p class="text-sm text-slate-500 mt-1">Define fee structure and payment options for a new session.</p>
        </div>
        <a href="{{ route('institute.admission-configs.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
            <i data-lucide="arrow-left" size="16" class="mr-2"></i> Back to List
        </a>
    </div>

    <form id="create_config_form" action="{{ route('institute.admission-configs.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        @csrf
        
        <div class="p-8 space-y-8">
            <!-- Section 1: Basic Configuration -->
            <div>
                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2 mb-6 pb-2 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                        <i data-lucide="settings-2" size="18"></i>
                    </div>
                    Basic Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Institution <span class="text-red-500">*</span></label>
                        <select name="institution_id" id="institution_id" required class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 focus:border-blue-500 focus:bg-white focus:ring-blue-500 transition-all p-2.5">
                            <option value="">Select Institution</option>
                            @foreach($institutions as $inst)
                                <option value="{{ $inst->id }}">{{ $inst->institution_name }} ({{ $inst->institution_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Application Form <span class="text-red-500">*</span></label>
                        <select name="application_form_id" id="application_form_id" required disabled class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 focus:border-blue-500 focus:bg-white focus:ring-blue-500 transition-all p-2.5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">Select Institution First</option>
                        </select>
                        @error('application_form_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Academic Session <span class="text-red-500">*</span></label>
                        <input type="text" name="academic_session" id="academic_session" required readonly class="w-full rounded-lg border-slate-200 bg-slate-100 text-slate-500 focus:outline-none cursor-not-allowed p-2.5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" required class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 focus:border-blue-500 focus:bg-white focus:ring-blue-500 transition-all p-2.5">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-2 lg:col-span-1">
                         <label class="block text-sm font-medium text-slate-700 mb-2">Total Admission Fee <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 font-semibold">₹</span>
                            <input type="number" name="admission_fee" id="admission_fee" required min="0" step="0.01" placeholder="0.00" class="w-full pl-8 rounded-lg border-slate-200 bg-slate-50 text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:ring-blue-500 transition-all p-2.5 font-bold">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Payment Options -->
            <div>
                <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2 mb-6 pb-2 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i data-lucide="credit-card" size="18"></i>
                    </div>
                    Payment Options
                </h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Full Payment Card -->
                    <div class="border border-slate-200 rounded-xl p-5 hover:border-blue-300 transition-colors bg-white shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <label for="allow_full_payment" class="flex items-center gap-3 cursor-pointer select-none group">
                                <div class="relative">
                                    <input type="checkbox" id="allow_full_payment" name="allow_full_payment" checked value="1" class="peer sr-only">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="font-semibold text-slate-800 group-hover:text-blue-700 transition-colors">Full Payment</span>
                            </label>
                            <i data-lucide="wallet" class="text-slate-300"></i>
                        </div>
                        <div class="space-y-4 pl-1">
                             <p class="text-xs text-slate-500 mb-2">Offer discounts for legitimate full payments.</p>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Discount Amount (₹)</label>
                                <input type="number" name="full_payment_discount_amount" min="0" step="0.01" class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 focus:border-blue-500 focus:bg-white focus:ring-blue-500 transition-all p-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Discount Percentage (%)</label>
                                <input type="number" name="full_payment_discount_percentage" min="0" max="100" step="0.01" class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 focus:border-blue-500 focus:bg-white focus:ring-blue-500 transition-all p-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- EMI Card -->
                    <div class="border border-slate-200 rounded-xl p-5 hover:border-indigo-300 transition-colors bg-white shadow-sm">
                         <div class="flex items-center justify-between mb-4">
                            <label for="allow_emi" class="flex items-center gap-3 cursor-pointer select-none group">
                                <div class="relative">
                                    <input type="checkbox" id="allow_emi" name="allow_emi" value="1" class="peer sr-only">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </div>
                                <span class="font-semibold text-slate-800 group-hover:text-indigo-700 transition-colors">EMI Payment</span>
                            </label>
                            <i data-lucide="calendar-clock" class="text-slate-300"></i>
                        </div>
                        
                        <div id="emi_options" class="space-y-4 hidden pl-1 transition-all duration-300">
                             <p class="text-xs text-slate-500 mb-2">Enable installment based payments.</p>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Down Payment (₹)</label>
                                <input type="number" name="emi_down_payment" min="0" step="0.01" class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500 transition-all p-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Allowed Tenures</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="inline-flex items-center p-2 border border-slate-200 rounded-lg bg-slate-50 hover:bg-white hover:border-indigo-200 cursor-pointer transition-all">
                                        <input type="checkbox" name="emi_tenures[]" value="3" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-slate-700">3 Months</span>
                                    </label>
                                    <label class="inline-flex items-center p-2 border border-slate-200 rounded-lg bg-slate-50 hover:bg-white hover:border-indigo-200 cursor-pointer transition-all">
                                        <input type="checkbox" name="emi_tenures[]" value="6" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-slate-700">6 Months</span>
                                    </label>
                                    <label class="inline-flex items-center p-2 border border-slate-200 rounded-lg bg-slate-50 hover:bg-white hover:border-indigo-200 cursor-pointer transition-all">
                                        <input type="checkbox" name="emi_tenures[]" value="9" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-slate-700">9 Months</span>
                                    </label>
                                    <label class="inline-flex items-center p-2 border border-slate-200 rounded-lg bg-slate-50 hover:bg-white hover:border-indigo-200 cursor-pointer transition-all">
                                        <input type="checkbox" name="emi_tenures[]" value="12" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-slate-700">12 Months</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                         <div id="emi_disabled_msg" class="text-sm text-slate-400 italic">Enable to configure EMI options.</div>
                    </div>

                    <!-- Cash Card -->
                    <div class="border border-slate-200 rounded-xl p-5 hover:border-emerald-300 transition-colors bg-white shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <label for="allow_cash_payment" class="flex items-center gap-3 cursor-pointer select-none group">
                                <div class="relative">
                                    <input type="checkbox" id="allow_cash_payment" name="allow_cash_payment" value="1" class="peer sr-only">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                </div>
                                <span class="font-semibold text-slate-800 group-hover:text-emerald-700 transition-colors">Cash Payment</span>
                            </label>
                            <i data-lucide="banknote" class="text-slate-300"></i>
                        </div>
                        
                        <div id="cash_options" class="space-y-4 hidden pl-1 transition-all duration-300">
                             <p class="text-xs text-slate-500 mb-2">Allow cash settlements with a deadline.</p>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Settle within (Days)</label>
                                <div class="relative">
                                    <input type="number" name="cash_payment_days" min="0" class="w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 focus:border-emerald-500 focus:bg-white focus:ring-emerald-500 transition-all p-2 text-sm" placeholder="e.g. 7">
                                    <span class="absolute inset-y-0 right-3 flex items-center text-slate-400 text-xs pointer-events-none">Days</span>
                                </div>
                            </div>
                        </div>
                         <div id="cash_disabled_msg" class="text-sm text-slate-400 italic">Enable to configure Cash options.</div>
                    </div>

                </div>
            </div>
        </div>
        
        <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('institute.admission-configs.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700 font-semibold hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm">Cancel</a>
            <button type="submit" id="submit_btn" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition-all shadow-md shadow-blue-200 flex items-center disabled:opacity-70 disabled:cursor-not-allowed">
                <i data-lucide="check" size="18" class="mr-2" id="submit_icon"></i>
                <span id="submit_text">Create Configuration</span>
                <div id="loading_spinner" class="hidden ml-2 animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></div>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('create_config_form');
        const submitBtn = document.getElementById('submit_btn');
        const submitIcon = document.getElementById('submit_icon');
        const submitText = document.getElementById('submit_text');
        const loadingSpinner = document.getElementById('loading_spinner');

        if(form){
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitIcon.classList.add('hidden');
                loadingSpinner.classList.remove('hidden');
                submitText.textContent = 'Creating...';
            });
        }
        const applicationForms = @json($applicationForms);
        
        // Element References
        const institutionSelect = document.getElementById('institution_id');
        const formSelect = document.getElementById('application_form_id');
        const sessionInput = document.getElementById('academic_session');
        const admissionFeeInput = document.getElementById('admission_fee');

        // Initial Logic
        if (institutionSelect.value) {
            populateForms(institutionSelect.value);
        }

        // On Institution Change
        institutionSelect.addEventListener('change', function() {
            populateForms(this.value);
            sessionInput.value = '';
            admissionFeeInput.value = ''; // Reset fee
        });

        // On Form Change
        formSelect.addEventListener('change', function() {
            const instId = institutionSelect.value;
            const formId = parseInt(this.value);

            if (instId && formId && applicationForms[instId]) {
                 const selectedForm = applicationForms[instId].find(f => f.id === formId);
                 if (selectedForm) {
                     sessionInput.value = selectedForm.session;
                     admissionFeeInput.value = selectedForm.course_fee;
                 }
            } else {
                sessionInput.value = '';
                admissionFeeInput.value = '';
            }
        });

        function populateForms(instId) {
            formSelect.innerHTML = '<option value="">Select Application Form</option>';
            formSelect.disabled = true;

            if (instId && applicationForms[instId]) {
                 const forms = applicationForms[instId];
                 
                 if (forms.length > 0) {
                     forms.forEach(form => {
                         const option = document.createElement('option');
                         option.value = form.id;
                         option.textContent = `${form.application_name} (${form.session})`;
                         formSelect.appendChild(option);
                     });
                     formSelect.disabled = false;
                 } else {
                     const option = document.createElement('option');
                     option.value = "";
                     option.textContent = "No Forms Found";
                     formSelect.appendChild(option);
                 }
            } else {
                 const option = document.createElement('option');
                 option.value = "";
                 option.textContent = "No Data Available";
                 formSelect.appendChild(option);
            }
        }

        // Toggle EMI options
        const emiCheckbox = document.getElementById('allow_emi');
        const emiOptions = document.getElementById('emi_options');
        const emiDisabledMsg = document.getElementById('emi_disabled_msg');
        
        function toggleEmi() {
            if (emiCheckbox.checked) {
                emiOptions.classList.remove('hidden');
                emiDisabledMsg.classList.add('hidden');
            } else {
                emiOptions.classList.add('hidden');
                emiDisabledMsg.classList.remove('hidden');
            }
        }
        emiCheckbox.addEventListener('change', toggleEmi);
        if(emiCheckbox) toggleEmi();

        // Toggle Cash options
        const cashCheckbox = document.getElementById('allow_cash_payment');
        const cashOptions = document.getElementById('cash_options');
        const cashDisabledMsg = document.getElementById('cash_disabled_msg');
        
        function toggleCash() {
            if (cashCheckbox.checked) {
                cashOptions.classList.remove('hidden');
                cashDisabledMsg.classList.add('hidden');
            } else {
                cashOptions.classList.add('hidden');
                cashDisabledMsg.classList.remove('hidden');
            }
        }
        cashCheckbox.addEventListener('change', toggleCash);
        if(cashCheckbox) toggleCash();
    });
</script>
@endsection

