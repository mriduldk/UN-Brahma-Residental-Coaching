<script>
    document.addEventListener('DOMContentLoaded', function() {
        const courseFee = {{ $application->application_form->course_fee ?? 30000 }};
        
        // --- Full Payment Logic ---
        const discountInput = document.getElementById('discount_value');
        const discountType = document.getElementById('discount_type');
        const finalAmountInput = document.getElementById('final_amount');
        const fullAmountPayable = document.getElementById('full_amount_payable');

        function calculateFullPayment() {
            let discount = parseFloat(discountInput.value) || 0;
            let type = discountType.value;
            let final = courseFee;

            if (type === 'percentage') {
                final = courseFee - (courseFee * discount / 100);
            } else {
                final = courseFee - discount;
            }
            
            finalAmountInput.value = final.toFixed(2);
            // Also update the hidden input for submission if needed, but here we just submit total
            // Actually for "Full Payment" tab, amount_paid should be the final amount.
            document.getElementById('full_amount_input').value = final.toFixed(2);
        }

        if(discountInput) {
            discountInput.addEventListener('input', calculateFullPayment);
            discountType.addEventListener('change', calculateFullPayment);
        }

        // --- EMI Logic ---
        const emiMonthsSelect = document.getElementById('emi_months');
        const emiPerMonthDisplay = document.getElementById('emi_per_month');
        const emiFirstInstallmentInput = document.getElementById('emi_first_installment');

        function calculateEMI() {
            let months = parseInt(emiMonthsSelect.value);
            let downPayment = parseFloat(emiFirstInstallmentInput.value) || 0;
            
            if (months > 1) {
                let remainingBalance = courseFee - downPayment;
                let perMonth = remainingBalance / (months - 1);
                emiPerMonthDisplay.innerText = perMonth.toFixed(2);
            } else {
                emiPerMonthDisplay.innerText = "0.00";
            }
        }

        if(emiMonthsSelect) {
            emiMonthsSelect.addEventListener('change', calculateEMI);
            emiFirstInstallmentInput.addEventListener('input', calculateEMI);
        }

        // --- DD Full Logic ---
        const ddFullDiscountInput = document.getElementById('dd_full_discount_value');
        const ddFullDiscountType = document.getElementById('dd_full_discount_type');
        const ddFullFinalAmountInput = document.getElementById('dd_full_final_amount');
        const ddFullAmountInput = document.getElementById('dd_full_amount_input');

        function calculateDdFullPayment() {
            let discount = parseFloat(ddFullDiscountInput.value) || 0;
            let type = ddFullDiscountType.value;
            let final = courseFee;

            if (type === 'percentage') {
                final = courseFee - (courseFee * discount / 100);
            } else {
                final = courseFee - discount;
            }
            
            ddFullFinalAmountInput.value = final.toFixed(2);
            ddFullAmountInput.value = final.toFixed(2);
        }

        if(ddFullDiscountInput) {
            ddFullDiscountInput.addEventListener('input', calculateDdFullPayment);
            ddFullDiscountType.addEventListener('change', calculateDdFullPayment);
        }

        // --- DD EMI Logic ---
        const ddEmiMonthsSelect = document.getElementById('dd_emi_months');
        const ddEmiPerMonthDisplay = document.getElementById('dd_emi_per_month');
        const ddEmiFirstInstallmentInput = document.getElementById('dd_emi_first_installment');

        function calculateDdEMI() {
            let months = parseInt(ddEmiMonthsSelect.value);
            let downPayment = parseFloat(ddEmiFirstInstallmentInput.value) || 0;
            
            if (months > 1) {
                let remainingBalance = courseFee - downPayment;
                let perMonth = remainingBalance / (months - 1);
                ddEmiPerMonthDisplay.innerText = perMonth.toFixed(2);
            } else {
                ddEmiPerMonthDisplay.innerText = "0.00";
            }
        }

        if(ddEmiMonthsSelect) {
            ddEmiMonthsSelect.addEventListener('change', calculateDdEMI);
            ddEmiFirstInstallmentInput.addEventListener('input', calculateDdEMI);
        }

        // --- Loading & Payment Logic ---
        const forms = {
            full: { id: 'full_payment_form', btn: 'full_payment_btn', icon: 'full_payment_icon', spinner: 'full_payment_spinner', text: 'full_payment_text', defaultText: 'Collect Full Payment' },
            emi: { id: 'emi_payment_form', btn: 'emi_payment_btn', icon: 'emi_payment_icon', spinner: 'emi_payment_spinner', text: 'emi_payment_text', defaultText: 'Collect First Installment' },
            next: { id: 'next_installment_form', btn: 'next_installment_btn', icon: 'next_installment_icon', spinner: 'next_installment_spinner', text: 'next_installment_text', defaultText: 'Pay Next Installment Online' },
            cash: { id: 'cash_payment_form', btn: 'cash_payment_btn', icon: 'cash_payment_icon', spinner: 'cash_payment_spinner', text: 'cash_payment_text', defaultText: 'Record Cash Payment' },
            dd_full: { id: 'dd_full_payment_form', btn: 'dd_full_payment_btn', icon: 'dd_full_payment_icon', spinner: 'dd_full_payment_spinner', text: 'dd_full_payment_text', defaultText: 'Confirm DD & Admit' },
            dd_emi: { id: 'dd_emi_payment_form', btn: 'dd_emi_payment_btn', icon: 'dd_emi_payment_icon', spinner: 'dd_emi_payment_spinner', text: 'dd_emi_payment_text', defaultText: 'Start DD EMI Plan' },
            dd_next: { id: 'next_installment_dd_form', btn: 'next_installment_dd_btn', icon: 'next_installment_dd_icon', spinner: 'next_installment_dd_spinner', text: 'next_installment_dd_text', defaultText: 'Record DD Installment' }
        };

        function setBtnLoading(type, isLoading, loadingText = 'Processing...') {
            const f = forms[type];
            const btn = document.getElementById(f.btn);
            const icon = document.getElementById(f.icon);
            const spinner = document.getElementById(f.spinner);
            const text = document.getElementById(f.text);

            if (isLoading) {
                if (btn) btn.disabled = true;
                if (icon) icon.classList.add('hidden');
                if (spinner) spinner.classList.remove('hidden');
                if (text) text.innerText = loadingText;
            } else {
                if (btn) btn.disabled = false;
                if (icon) icon.classList.remove('hidden');
                if (spinner) spinner.classList.add('hidden');
                if (text) text.innerText = f.defaultText;
            }
        }

        // Handle Cash & DD Forms (Direct Submission)
        ['cash', 'dd_full', 'dd_emi', 'dd_next'].forEach(type => {
            const form = document.getElementById(forms[type].id);
            if (form) {
                form.addEventListener('submit', () => setBtnLoading(type, true, 'Recording...'));
            }
        });

        // Handle Online Forms (AJAX + Razorpay)
        ['full', 'emi', 'next'].forEach(type => {
            const form = document.getElementById(forms[type].id);
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    setBtnLoading(type, true);

                    const formData = new FormData(this);
                    
                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success && data.order_id) {
                            const options = {
                                "key": data.key,
                                "amount": data.amount,
                                "currency": "INR",
                                "name": data.name,
                                "description": (type === 'full' ? 'Full Admission Fee' : (type === 'next' ? 'EMI Installment' : 'EMI Initial Payment')),
                                "order_id": data.order_id,
                                "callback_url": data.callback_url,
                                "prefill": {
                                    "name": "{{ $application->application_student->full_name ?? '' }}",
                                    "email": data.email,
                                    "contact": data.contact
                                },
                                "theme": { "color": "#2563eb" },
                                "modal": {
                                    "ondismiss": function() {
                                        setBtnLoading(type, false);
                                    }
                                }
                            };
                            const rzp = new Razorpay(options);
                            
                            rzp.on('payment.failed', function (response){
                                console.error('Payment Failed', response.error);
                                
                                fetch("{{ route('administration.admissions.payment.failure', $application->id) }}", {
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
                        } else {
                            alert('Error: ' + (data.error || 'Could not initiate payment. Please try again.'));
                            setBtnLoading(type, false);
                        }
                    } catch (error) {
                        console.error('Payment Error:', error);
                        alert('Something went wrong. Please check your connection.');
                        setBtnLoading(type, false);
                    }
                });
            }
        });
    });

    // Tab Switching Logic (Simple)
    function openPaymentTab(tabName) {
        document.querySelectorAll('.payment-tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(tabName + '-content').classList.remove('hidden');
        
        document.querySelectorAll('.payment-tab-btn').forEach(el => {
            el.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
            el.classList.add('bg-white', 'text-slate-600', 'hover:bg-slate-50');
        });
        document.getElementById(tabName + '-btn').classList.add('bg-blue-600', 'text-white', 'shadow-md');
        document.getElementById(tabName + '-btn').classList.remove('bg-white', 'text-slate-600', 'hover:bg-slate-50');
    }
</script>
