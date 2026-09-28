@extends('layouts.student')

@section('content')
    <style>
        /* Disable Header/Navigation for Onboarding */
        header {
            pointer-events: none;
            opacity: 1;
            filter: grayscale(100%);
        }
        
        /* Enable Logout Button (Desktop & Mobile) */
        header form[action*="logout"], 
        header form[action*="logout"] button {
            pointer-events: auto;
            cursor: pointer;
        }
        
        /* Enable Mobile Menu Toggle so users can reach Logout on mobile */
        header button[onclick*="mobile-menu"] {
            pointer-events: auto;
            cursor: pointer;
        }
    </style>

    <div class="min-h-screen bg-slate-50 py-4 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
            
            <!-- Header -->
            <div class="bg-blue-600 p-6 text-center text-white">
                <div class="mx-auto h-12 w-12 bg-white/20 rounded-full flex items-center justify-center mb-4 backdrop-blur-sm">
                    <i data-lucide="user-plus" class="text-white" size="24"></i>
                </div>
                <h2 class="text-3xl font-extrabold uppercase tracking-wide">
                    Student Profile Setup
                </h2>
                <p class="mt-2 text-blue-100 text-sm">
                    Complete your profile to proceed with applications.
                </p>
            </div>

            <div class="px-8 pt-8">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                        <div class="flex items-center gap-3 mb-2">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            <h3 class="font-bold">Please fix the following errors:</h3>
                        </div>
                        <ul class="list-disc list-inside text-sm space-y-1 ml-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <form action="{{ route('student.onboarding.store') }}" method="POST" class="px-8 pb-8 space-y-8" id="onboardingForm">
                @csrf
                
                <!-- Personal Details Section -->
                <div>
                    <h3 class="text-xl font-bold text-slate-800 border-b pb-3 mb-6 flex items-center">
                        <i data-lucide="user" class="mr-2 text-blue-600" size="24"></i> Personal Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name -->
                        <div class="md:col-span-2">
                            <label for="full_name" class="block text-xs font-bold text-slate-500 uppercase mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input id="full_name" name="full_name" type="text" required class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition uppercase" placeholder="Your Full Name" value="{{ auth()->user()->name }}">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone_number" class="block text-xs font-bold text-slate-500 uppercase mb-1">Phone Number <span class="text-red-500">*</span></label>
                            <input id="phone_number" name="phone_number" type="tel" required class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" placeholder="10-digit Number" value="{{ auth()->user()->mobile_number }}">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-500 uppercase mb-1">Email Address</label>
                            <input id="email" name="email" type="email" class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition uppercase" placeholder="email@example.com (Optional)" value="{{ auth()->user()->email }}">
                        </div>

                        <!-- DOB -->
                        <div>
                            <label for="dob" class="block text-xs font-bold text-slate-500 uppercase mb-1">Date of Birth</label>
                            <input id="dob" name="dob" type="date" class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" onchange="calculateAge()">
                        </div>

                        <!-- Age (Auto-calculated) -->
                        <div>
                            <label for="age" class="block text-xs font-bold text-slate-500 uppercase mb-1">Age</label>
                            <input id="age" name="age" type="number" readonly class="w-full p-2 border border-slate-200 bg-slate-100 rounded text-slate-500 cursor-not-allowed">
                        </div>

                        <!-- Gender -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Gender</label>
                            <div class="flex space-x-4 mt-2">
                                <label class="flex items-center space-x-2 cursor-pointer bg-slate-50 px-3 py-2 rounded hover:bg-slate-100 border border-transparent hover:border-slate-200 transition">
                                    <input type="radio" name="gender" value="Male" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-slate-700">Male</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer bg-slate-50 px-3 py-2 rounded hover:bg-slate-100 border border-transparent hover:border-slate-200 transition">
                                    <input type="radio" name="gender" value="Female" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-slate-700">Female</span>
                                </label>
                                <label class="flex items-center space-x-2 cursor-pointer bg-slate-50 px-3 py-2 rounded hover:bg-slate-100 border border-transparent hover:border-slate-200 transition">
                                    <input type="radio" name="gender" value="Other" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-slate-700">Other</span>
                                </label>
                            </div>
                        </div>


                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-xs font-bold text-slate-500 uppercase mb-1">Category</label>
                            <select id="category" name="category" class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                <option value="">Select</option>
                                <option value="General">General</option>
                                <option value="SC">SC</option>
                                <option value="ST">ST</option>
                                <option value="OBC">OBC</option>
                                <option value="MOBC">MOBC</option>
                            </select>
                        </div>

                        <!-- PwD Checkbox -->
                        <div class="col-span-1 md:col-span-2 pt-2">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="is_pwd" value="1" class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500 transition">
                                <div>
                                    <span class="text-sm font-bold text-slate-700">Are you a Person with Benchmark Disability (PwD)?</span>
                                    <p class="text-xs text-slate-500 mt-0.5">Check this box if you hold a valid PwD certificate.</p>
                                </div>
                            </label>
                        </div>

                        <!-- Employment Exchange Number -->
                        <div class="md:col-span-2">
                            <label for="employment_exchange_no" class="block text-xs font-bold text-slate-500 uppercase mb-1">Employment Exchange Registration Number <span class="text-xs font-normal lowercase">(Optional)</span></label>
                            <input id="employment_exchange_no" name="employment_exchange_no" type="text" class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition uppercase" placeholder="Enter Registration Number" value="{{ old('employment_exchange_no') }}">
                        </div>

                        <!-- Residency -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Permanent Resident of Assam <span class="text-red-500">*</span></label>
                                <select name="is_permanent_resident_of_assam" required class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    <option value="">Select Yes/No</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">BTC Resident <span class="text-red-500">*</span></label>
                                <select name="is_btc_resident" required class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    <option value="">Select Yes/No</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <!-- Achievements & Services -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:col-span-2 mt-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ex-Serviceman <span class="text-red-500">*</span></label>
                                <select name="is_ex_serviceman" required class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    <option value="">Select Yes/No</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">NCC Certificate <span class="text-red-500">*</span></label>
                                <select name="ncc_certificate" required class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    <option value="None">None</option>
                                    <option value="B Certificate">B Certificate</option>
                                    <option value="C Certificate">C Certificate</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">National Sports Achievement <span class="text-red-500">*</span></label>
                                <select name="has_national_sports_achievement" required class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    <option value="">Select Yes/No</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fine Arts/Cultural Achievement <span class="text-red-500">*</span></label>
                                <select name="has_fine_arts_cultural_achievement" required class="w-full p-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    <option value="">Select Yes/No</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>





                    </div>
                </div>

                <div class="pt-6 border-t border-slate-200">
                    <button type="submit" id="submitBtn" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all uppercase tracking-wide disabled:opacity-75 disabled:cursor-not-allowed">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" id="submitSpinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="submitBtnText">Save Profile & Continue</span>
                    </button>
                    <p class="text-center text-xs text-slate-500 mt-4">
                        Please ensure all details are correct. You can update them later from your profile.
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function calculateAge() {
        const dob = document.getElementById('dob').value;
        if(dob) {
            const birthDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            document.getElementById('age').value = age;
        }
    }

    document.getElementById('onboardingForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const spinner = document.getElementById('submitSpinner');
        const btnText = document.getElementById('submitBtnText');
        
        // Check form validity before disabling (optional but good UI)
        // Since we are using standard form submission, the browser handles validity mostly.
        // We just disable immediately on submit event which fires if validity passes.
        
        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.innerText = 'Saving...';
    });
</script>
@endpush
