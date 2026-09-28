@extends('layouts.student')

@section('content')
<div class="w-full mx-auto bg-white shadow-sm border border-slate-200 rounded-xl overflow-hidden">
    
    <!-- Form Header -->
    <div class="bg-slate-50 p-6 border-b border-slate-200 text-center relative">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white border border-slate-200 rounded-full shadow-sm mb-4">
            <i data-lucide="school" size="14" class="text-blue-600"></i>
            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-widest">{{ $form->institution->institution_name }}</span>
        </div>
        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1">{{ $form->session }} Session</p>
        <h1 class="text-2xl font-extrabold text-slate-800 uppercase mb-2">{{ $form->application_name }}</h1>
        <p class="text-sm text-slate-600 mb-4">{{ $form->application_description }}</p>
        
        
        <!-- Save Draft Button -->
        <button onclick="saveDraft()" class="absolute top-4 right-4 flex items-center space-x-2 text-slate-500 hover:text-blue-600 transition-colors bg-white px-3 py-2 rounded-lg border border-slate-200 shadow-sm text-sm font-medium">
            <i data-lucide="save" size="16"></i>
        </button>
    </div>

    <!-- Dynamic Steps Logic -->
    @php
        $steps = [];
        $steps[] = ['number' => 1, 'id' => 'personal', 'label' => 'Personal'];
        
        if($form->isSectionEnabled('address')) {
            $steps[] = ['number' => 2, 'id' => 'address', 'label' => 'Address'];
        }
        
        if($form->isSectionEnabled('academic')) {
            $steps[] = ['number' => 4, 'id' => 'academic', 'label' => 'Academic'];
        }
        
        $steps[] = ['number' => 5, 'id' => 'documents', 'label' => 'Uploads'];
        
        // Map step numbers for JS
        $stepMap = [];
        foreach($steps as $index => $step) {
            $stepMap[$index + 1] = $step['number']; // Logical Step (1-based index) -> Actual Step ID
        }
        
        $totalVisibleSteps = count($steps);
    @endphp

    <!-- Step Progress Bar -->
    <div class="bg-white p-4 border-b border-slate-100 overflow-x-auto">
        <div class="flex items-center justify-between max-w-4xl mx-auto text-sm font-medium text-slate-500">
            @foreach($steps as $index => $step)
                @php 
                    $stepNum = $step['number'];
                    $displayNum = $index + 1;
                @endphp
                
                <div class="flex items-center space-x-2 {{ $index == 0 ? 'text-blue-600' : '' }}" id="step-container-{{ $stepNum }}">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition-colors duration-300 {{ $index == 0 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-600' }}" id="step-indicator-{{ $stepNum }}">
                        {{ $displayNum }}
                    </div>
                    <span class="hidden md:inline">{{ $step['label'] }}</span>
                </div>
                
                @if(!$loop->last)
                    <div class="h-1 flex-1 bg-slate-200 mx-2 rounded">
                        <div class="h-1 bg-blue-600 rounded transition-all duration-500 w-0" id="line-{{ $stepNum }}"></div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <form id="admissionForm" class="p-6 md:p-8" action="{{ route('student.application.preview') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="form_id" value="{{ $form->id }}">
        
        <!-- STEP 1: PERSONAL DETAILS -->
        <div id="step-1" class="step-content space-y-6">
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 flex items-center">
                <i data-lucide="user" class="mr-2" size="20"></i> Personal Details
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Students Table Fields -->
                @if($form->isFieldVisible('personal', 'full_name'))
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" required value="{{ $existingApp?->application_student?->full_name ?? $student->full_name ?? auth()->user()->name ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'phone_number'))
                <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phone Number <span class="text-red-500">*</span></label>
                     <input type="text" name="phone_number" required value="{{ $student->phone_number ?? auth()->user()->mobile_number ?? $existingApp?->application_student?->phone_number ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500 bg-slate-100 text-slate-500 cursor-not-allowed" readonly>
                </div>
                @endif
                
                @if($form->isFieldVisible('personal', 'email'))
                <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email <span class="text-red-500">*</span></label>
                     <input type="email" name="email" value="{{ $existingApp?->application_student?->email ?? $student->email ?? auth()->user()->email ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500 bg-slate-100 text-slate-500 cursor-not-allowed" readonly>
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'dob'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Date of Birth <span class="text-red-500">*</span></label>
                    <input type="date" name="dob" required value="{{ ($existingApp?->application_student?->dob ?? $student->dob) ? \Carbon\Carbon::parse($existingApp?->application_student?->dob ?? $student->dob)->format('Y-m-d') : '' }}" class="w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'age'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Age <span class="text-red-500">*</span></label>
                    <input type="number" name="age" required value="{{ $existingApp?->application_student?->age ?? $student->age ?? '' }}" class="w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'gender'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Gender <span class="text-red-500">*</span></label>
                    <div class="flex space-x-4 mt-2">
                        @php
                            $currentGender = strtoupper($existingApp?->application_student?->gender ?? $student->gender ?? '');
                        @endphp
                        <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="gender" required value="Male" {{ $currentGender == 'MALE' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500"><span class="text-sm font-medium">Male</span></label>
                        <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="gender" required value="Female" {{ $currentGender == 'FEMALE' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500"><span class="text-sm font-medium">Female</span></label>
                    </div>
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'category'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        <option value="">Select</option>
                        @foreach(['GENERAL', 'SC', 'ST', 'OBC', 'MOBC'] as $cat)
                            <option value="{{ $cat }}" {{ ($existingApp?->application_student?->category ?? $student->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'religion'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Religion <span class="text-red-500">*</span></label>
                    <input type="text" name="religion" required value="{{ $existingApp?->application_student?->religion ?? $student->religion ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500 uppercase">
                </div>
                @endif



                @if($form->isFieldVisible('personal', 'mother_tongue'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mother Tongue <span class="text-red-500">*</span></label>
                    <input type="text" name="mother_tongue" required value="{{ $existingApp?->application_student?->mother_tongue ?? $student->mother_tongue ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'community') || $form->isFieldVisible('personal', 'nationality'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Community <span class="text-red-500">*</span></label>
                    @php
                        $communityList = $communities ?? \App\Models\Community::active()->get();
                        $selectedCommunity = $existingApp?->application_student?->community ?? $existingApp?->application_student?->nationality ?? $student->community ?? $student->nationality ?? '';
                    @endphp
                    <select name="community" id="community_select" required class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500 bg-white" onchange="const natInput = document.getElementById('nationality_input'); if(natInput) natInput.value = this.value;">
                        <option value="">Select Community</option>
                        @foreach($communityList as $comm)
                            <option value="{{ $comm->name }}" {{ strcasecmp($selectedCommunity, $comm->name) === 0 ? 'selected' : '' }}>{{ $comm->name }}</option>
                        @endforeach
                        @if($selectedCommunity && !$communityList->contains('name', $selectedCommunity))
                            <option value="{{ $selectedCommunity }}" selected>{{ $selectedCommunity }}</option>
                        @endif
                    </select>
                    <input type="hidden" name="nationality" id="nationality_input" value="{{ $selectedCommunity }}">
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'blood_group'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Blood Group <span class="text-red-500">*</span></label>
                    <select name="blood_group" required class="w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        <option value="">Select</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                            <option value="{{ $bg }}" {{ ($existingApp?->application_student?->blood_group ?? $student->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'identification_mark'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Identification Mark <span class="text-red-500">*</span></label>
                    <input type="text" name="identification_mark" required value="{{ $existingApp?->application_student?->identification_mark ?? $student->identification_mark ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'alternate_mobile_number'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">What’s App Number <span class="text-red-500">*</span></label>
                    <input type="text" name="alternate_mobile_number" required value="{{ $existingApp?->application_student?->alternate_mobile_number ?? $student->alternate_mobile_number ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'is_pwd'))
                <div class="col-span-1 md:col-span-2 mt-2">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="hidden" name="is_pwd" value="0">
                        <input type="checkbox" name="is_pwd" value="1" {{ ($existingApp?->application_student?->is_pwd ?? $student->is_pwd) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500 transition">
                        <div>
                            <span class="text-sm font-bold text-slate-700">Are you a Person with Benchmark Disability (PwD)?</span>
                        </div>
                    </label>
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'employment_exchange_no'))
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Employment Exchange Registration Number <span class="text-xs font-normal lowercase">(Optional)</span></label>
                    <input type="text" name="employment_exchange_no" value="{{ $existingApp?->application_student?->employment_exchange_no ?? $student->employment_exchange_no ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500 uppercase">
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'is_permanent_resident_of_assam'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Permanent Resident of Assam</label>
                    <select name="is_permanent_resident_of_assam" class="w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        <option value="">Select (Optional)</option>
                        <option value="1" {{ ($existingApp?->application_student?->is_permanent_resident_of_assam ?? $student->is_permanent_resident_of_assam) === true ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ ($existingApp?->application_student?->is_permanent_resident_of_assam ?? $student->is_permanent_resident_of_assam) === false ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                @endif

                @if($form->isFieldVisible('personal', 'is_btc_resident'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">BTC Resident</label>
                    <select name="is_btc_resident" class="w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        <option value="">Select (Optional)</option>
                        <option value="1" {{ ($existingApp?->application_student?->is_btc_resident ?? $student->is_btc_resident) === true ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ ($existingApp?->application_student?->is_btc_resident ?? $student->is_btc_resident) === false ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                @endif


            </div>

            <!-- Parent Details (if enabled) -->
            @if($form->isSectionEnabled('parents'))
            <h4 class="font-bold text-slate-700 bg-slate-50 p-2 rounded mt-6 mb-4 border-l-4 border-blue-500">Parent / Guardian Details</h4>
            
            <!-- Father's Details -->
            @if($form->isFieldVisible('parents', 'father_name') || $form->isFieldVisible('parents', 'father_mobile') || $form->isFieldVisible('parents', 'father_occupation') || $form->isFieldVisible('parents', 'father_annual_income'))
            <h5 class="text-sm font-semibold text-slate-600 mb-3 uppercase border-b pb-1">Father's Details</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                 @if($form->isFieldVisible('parents', 'father_name'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Father's Name <span class="text-red-500">*</span></label>
                    <input type="text" name="father_name" required value="{{ $existingApp?->application_parent?->father_name ?? $student->parent?->father_name ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif
                
                @if($form->isFieldVisible('parents', 'father_mobile'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Father's Mobile <span class="text-red-500">*</span></label>
                    <input type="text" name="father_mobile" required value="{{ $existingApp?->application_parent?->father_mobile ?? $student->parent?->father_mobile ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif
                
                @if($form->isFieldVisible('parents', 'father_occupation'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Father's Occupation <span class="text-red-500">*</span></label>
                    <input type="text" name="father_occupation" required value="{{ $existingApp?->application_parent?->father_occupation ?? $student->parent?->father_occupation ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif
                
                 @if($form->isFieldVisible('parents', 'father_annual_income'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Family income per (per anum): <span class="text-red-500">*</span></label>
                    <input type="number" name="father_annual_income" required value="{{ $existingApp?->application_parent?->father_annual_income ?? $student->parent?->father_annual_income ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif
            </div>
            @endif

            <!-- Mother's Details -->
            @if($form->isFieldVisible('parents', 'mother_name') || $form->isFieldVisible('parents', 'mother_mobile') || $form->isFieldVisible('parents', 'mother_occupation'))
            <h5 class="text-sm font-semibold text-slate-600 mb-3 uppercase border-b pb-1">Mother's Details</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if($form->isFieldVisible('parents', 'mother_name'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mother's Name <span class="text-red-500">*</span></label>
                    <input type="text" name="mother_name" required value="{{ $existingApp?->application_parent?->mother_name ?? $student->parent?->mother_name ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif
                
                @if($form->isFieldVisible('parents', 'mother_mobile'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mother's Mobile <span class="text-red-500">*</span></label>
                    <input type="text" name="mother_mobile" required value="{{ $existingApp?->application_parent?->mother_mobile ?? $student->parent?->mother_mobile ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif

                @if($form->isFieldVisible('parents', 'mother_occupation'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mother's Occupation <span class="text-red-500">*</span></label>
                    <input type="text" name="mother_occupation" required value="{{ $existingApp?->application_parent?->mother_occupation ?? $student->parent?->mother_occupation ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif
            </div>
            @endif

            <!-- Guardian Details -->
            @if($form->isFieldVisible('parents', 'guardian_name') || $form->isFieldVisible('parents', 'guardian_relation') || $form->isFieldVisible('parents', 'guardian_mobile') || $form->isFieldVisible('parents', 'guardian_address'))
             <h5 class="text-sm font-semibold text-slate-600 mb-3 uppercase border-b pb-1">Guardian Details (Optional)</h5>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($form->isFieldVisible('parents', 'guardian_name'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Guardian's Name</label>
                    <input type="text" name="guardian_name" value="{{ $existingApp?->application_parent?->guardian_name ?? $student->parent?->guardian_name ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif
                
                @if($form->isFieldVisible('parents', 'guardian_relation'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Relation</label>
                    <input type="text" name="guardian_relation" value="{{ $existingApp?->application_parent?->guardian_relation ?? $student->parent?->guardian_relation ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif

                @if($form->isFieldVisible('parents', 'guardian_mobile'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Guardian's Mobile</label>
                    <input type="text" name="guardian_mobile" value="{{ $existingApp?->application_parent?->guardian_mobile ?? $student->parent?->guardian_mobile ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif
                
                @if($form->isFieldVisible('parents', 'guardian_address'))
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Guardian's Address</label>
                    <input type="text" name="guardian_address" value="{{ $existingApp?->application_parent?->guardian_address ?? $student->parent?->guardian_address ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                </div>
                @endif
             </div>
             @endif
            @endif

            <div class="flex justify-end pt-4">
                @if(isset($stepMap[2]))
                <button type="button" onclick="nextStep({{ $stepMap[2] }}, this)" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 flex items-center">
                    Next Step <i data-lucide="arrow-right" class="ml-2" size="18"></i>
                </button>
                @endif
            </div>
        </div>

        <!-- STEP 2: ADDRESS DETAILS -->
        <div id="step-2" class="step-content space-y-6">
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 flex items-center">
                <i data-lucide="map-pin" class="mr-2" size="20"></i> Address Details
            </h3>

            @if($form->isSectionEnabled('address'))
            @php
                $assamDistricts = [
                    'Baksa', 'Barpeta', 'Bajali', 'Biswanath', 'Bongaigaon', 'Cachar', 'Charaideo', 
                    'Chirang', 'Darrang', 'Dhemaji', 'Dhubri', 'Dibrugarh', 'Dima Hasao', 'Goalpara', 
                    'Golaghat', 'Hailakandi', 'Hojai', 'Jorhat', 'Kamrup', 'Kamrup Metropolitan', 
                    'Karbi Anglong', 'Karimganj', 'Kokrajhar', 'Lakhimpur', 'Majuli', 'Morigaon', 
                    'Nagaon', 'Nalbari', 'Sivasagar', 'Sonitpur', 'South Salmara-Mankachar', 
                    'Tamulpur', 'Tinsukia', 'Udalguri', 'West Karbi Anglong'
                ];
                $btrDistricts = [
                    'Kokrajhar', 'Chirang', 'Baksa', 'Tamulpur', 'Udalguri'
                ];
                $savedPermanent = $existingApp?->application_address?->permanent_district ?? $student->address?->permanent_district ?? '';
                $savedPresent = $existingApp?->application_address?->present_district ?? $student->address?->present_district ?? '';
            @endphp
            <div class="space-y-8">
                <!-- Permanent Address (First, in one row) -->
                <div class="space-y-4">
                    <div class="bg-blue-50 p-2.5 rounded-lg border border-blue-100 flex items-center">
                        <h4 class="font-bold text-blue-900 text-sm uppercase tracking-wide">Permanent Address</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($form->isFieldVisible('address', 'permanent_village_town'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vill/Town <span class="text-red-500">*</span></label>
                            <input type="text" required name="permanent_village_town" id="perm_vill" value="{{ $existingApp?->application_address?->permanent_village_town ?? $student->address?->permanent_village_town ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif

                        @if($form->isFieldVisible('address', 'permanent_city'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Post Office <span class="text-red-500">*</span></label>
                            <input type="text" required name="permanent_city" id="perm_city" value="{{ $existingApp?->application_address?->permanent_city ?? $student->address?->permanent_city ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif

                        @if($form->isFieldVisible('address', 'permanent_country'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Police Station <span class="text-red-500">*</span></label>
                            <input type="text" required name="permanent_country" id="perm_country" value="{{ $existingApp?->application_address?->permanent_country ?? $student->address?->permanent_country ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif

                        @if($form->isFieldVisible('address', 'permanent_district'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">District <span class="text-red-500">*</span></label>
                            <select required name="permanent_district" id="perm_dist" class="district-tom-select w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                                <option value="">Select District</option>
                                @foreach($btrDistricts as $dist)
                                    <option value="{{ $dist }}" {{ strcasecmp($savedPermanent, $dist) === 0 ? 'selected' : '' }}>{{ $dist }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        @if($form->isFieldVisible('address', 'permanent_street'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">BTCLA Constituency <span class="text-red-500">*</span></label>
                            <input type="text" required name="permanent_street" id="perm_street" value="{{ $existingApp?->application_address?->permanent_street ?? $student->address?->permanent_street ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif
                        
                        @if($form->isFieldVisible('address', 'permanent_state'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">State <span class="text-red-500">*</span></label>
                            <input type="text" required name="permanent_state" id="perm_state" value="Assam" readonly class="input-block w-full p-2 border border-slate-300 rounded outline-none bg-slate-100 text-slate-600 font-semibold cursor-not-allowed">
                        </div>
                        @endif
                        
                        @if($form->isFieldVisible('address', 'permanent_pin_code'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">PIN <span class="text-red-500">*</span></label>
                            <input type="text" required name="permanent_pin_code" id="perm_pin" value="{{ $existingApp?->application_address?->permanent_pin_code ?? $student->address?->permanent_pin_code ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Current Address / Communication Address (Below Permanent Address) -->
                <div class="space-y-4 pt-4 border-t border-slate-200" id="present-address-container">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-blue-50 p-2.5 rounded-lg border border-blue-100 gap-2">
                        <h4 class="font-bold text-blue-900 text-sm uppercase tracking-wide">Current Address / Communication Address</h4>
                        <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 cursor-pointer select-none bg-white px-3 py-1 rounded border border-slate-200 shadow-sm hover:bg-slate-50">
                            <input type="checkbox" id="sameAddress" name="same_as_permanent" value="1" onchange="toggleSameAddress()" {{ ($existingApp?->application_address?->same_as_permanent ?? false) ? 'checked' : '' }} class="rounded text-blue-600 focus:ring-blue-500">
                            <span>Same as Permanent Address</span>
                        </label>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if($form->isFieldVisible('address', 'present_village_town'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vill/Town <span class="text-red-500">*</span></label>
                            <input type="text" required name="present_village_town" id="pre_vill" value="{{ $existingApp?->application_address?->present_village_town ?? $student->address?->present_village_town ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif
                        
                        @if($form->isFieldVisible('address', 'present_street'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Street <span class="text-red-500">*</span></label>
                            <input type="text" required name="present_street" id="pre_street" value="{{ $existingApp?->application_address?->present_street ?? $student->address?->present_street ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif

                        @if($form->isFieldVisible('address', 'present_city'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Post Office <span class="text-red-500">*</span></label>
                            <input type="text" required name="present_city" id="pre_city" value="{{ $existingApp?->application_address?->present_city ?? $student->address?->present_city ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif

                        @if($form->isFieldVisible('address', 'present_country'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Police Station <span class="text-red-500">*</span></label>
                            <input type="text" required name="present_country" id="pre_country" value="{{ $existingApp?->application_address?->present_country ?? $student->address?->present_country ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif

                        @if($form->isFieldVisible('address', 'present_district'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">District <span class="text-red-500">*</span></label>
                            <select required name="present_district" id="pre_dist" class="district-tom-select w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                                <option value="">Select District</option>
                                @foreach($assamDistricts as $dist)
                                    <option value="{{ $dist }}" {{ strcasecmp($savedPresent, $dist) === 0 ? 'selected' : '' }}>{{ $dist }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        @if($form->isFieldVisible('address', 'present_state'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">State <span class="text-red-500">*</span></label>
                            <input type="text" required name="present_state" id="pre_state" value="{{ $existingApp?->application_address?->present_state ?? $student->address?->present_state ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif
                        
                        @if($form->isFieldVisible('address', 'present_pin_code'))
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">PIN <span class="text-red-500">*</span></label>
                            <input type="text" required name="present_pin_code" id="pre_pin" value="{{ $existingApp?->application_address?->present_pin_code ?? $student->address?->present_pin_code ?? '' }}" class="input-block w-full p-2 border border-slate-300 rounded outline-none focus:border-blue-500">
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="flex justify-between pt-6 border-t border-slate-100">
                <button type="button" onclick="prevStep({{ $stepMap[1] }})" class="px-6 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-lg flex items-center">
                    <i data-lucide="arrow-left" class="mr-2" size="18"></i> Previous
                </button>
                
                @php
                    // Find next step after 'address' (which is step 2 in default, but dynamic here)
                    $currentIdx = array_search(2, $stepMap);
                    $nextStepId = isset($stepMap[$currentIdx + 1]) ? $stepMap[$currentIdx + 1] : null;
                @endphp
                
                @if($nextStepId)
                <button type="button" onclick="nextStep({{ $nextStepId }}, this)" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 flex items-center">
                    Next Step <i data-lucide="arrow-right" class="ml-2" size="18"></i>
                </button>
                @endif
            </div>
        </div>

        <!-- STEP 4: ACADEMIC DETAILS -->
        <div id="step-4" class="step-content space-y-6">
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 flex items-center">
                <i data-lucide="graduation-cap" class="mr-2" size="20"></i> Academic Qualification Details
            </h3>
            
            @php
                $academicFieldsConfig = $form->field_config['academic']['fields'] ?? [];
                // Fallback to default if empty or not set
                if (empty($academicFieldsConfig)) {
                    $academicFieldsConfig = ['examination', 'passing_year', 'board_university', 'percentage_grade', 'division'];
                }

                $fieldLabels = [
                    'examination' => 'Qualification',
                    'passing_year' => 'Year',
                    'board_university' => 'Board / University',
                    'percentage_grade' => '% Marks',
                    'division' => 'Division',
                    'roll_number' => 'Roll No',
                    'registration_number' => 'Reg. No',
                    'total_marks' => 'Total Marks',
                    'marks_obtained' => 'Marks Obtained',
                    'medium_of_study' => 'Medium',
                ];

                $defaultQuals = $form->getDefaultQualifications();
                if (empty($defaultQuals)) {
                    $defaultQuals = ['HSLC', 'HS', 'GRADUATION'];
                }

                $savedQuals = collect();
                if ($existingApp && $existingApp->application_academic_qualifications && $existingApp->application_academic_qualifications->count() > 0) {
                    $savedQuals = $existingApp->application_academic_qualifications;
                } elseif ($student && $student->academic_qualifications && $student->academic_qualifications->count() > 0) {
                    $savedQuals = $student->academic_qualifications;
                }

                $qualRows = [];
                $matchedSavedIds = [];

                // 1. Populate rows for each default qualification configured in the form
                foreach ($defaultQuals as $dq) {
                    $matched = $savedQuals->first(function($item, $idx) use ($dq, $matchedSavedIds) {
                        $id = $item->id ?? $idx;
                        return !in_array($id, $matchedSavedIds) && 
                               strcasecmp(trim($item->examination ?? ''), trim($dq)) === 0;
                    });

                    if ($matched) {
                        $matchedSavedIds[] = $matched->id ?? count($matchedSavedIds);
                        $qualRows[] = [
                            'is_default' => true,
                            'examination' => $dq,
                            'data' => $matched,
                        ];
                    } else {
                        $qualRows[] = [
                            'is_default' => true,
                            'examination' => $dq,
                            'data' => null,
                        ];
                    }
                }

                // 2. Append any extra saved qualifications not in the default list
                foreach ($savedQuals as $idx => $sq) {
                    $idKey = $sq->id ?? $idx;
                    if (!in_array($idKey, $matchedSavedIds)) {
                        $qualRows[] = [
                            'is_default' => false,
                            'examination' => $sq->examination,
                            'data' => $sq,
                        ];
                    }
                }

                if (empty($qualRows)) {
                    $qualRows[] = [
                        'is_default' => true,
                        'examination' => 'HSLC',
                        'data' => null,
                    ];
                }
            @endphp

            
            @if($form->isSectionEnabled('academic'))
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-sm text-left" id="qualTable">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                        <tr>
                            @foreach($academicFieldsConfig as $field)
                                <th class="px-4 py-3">{{ $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="qualBody">
                        @foreach($qualRows as $index => $rowItem)
                            @php
                                $examVal = $rowItem['examination'] ?? '';
                                $rowData = $rowItem['data'];
                                $isDefault = $rowItem['is_default'];
                            @endphp
                            <tr>
                                @foreach($academicFieldsConfig as $field)
                                    @php
                                        if ($field === 'examination') {
                                            $val = $examVal;
                                        } else {
                                            $val = $rowData ? ($rowData->$field ?? '') : '';
                                        }
                                    @endphp
                                    <td class="p-2">
                                        @if($field === 'examination')
                                            @if($isDefault)
                                                <div class="relative">
                                                    <input type="text" 
                                                           name="qual[{{ $index }}][examination]" 
                                                           value="{{ $val }}" 
                                                           readonly 
                                                           class="input-block w-full p-2 border border-slate-200 bg-slate-100 text-slate-700 font-bold rounded cursor-not-allowed uppercase">
                                                </div>
                                            @else
                                                <select name="qual[{{ $index }}][examination]" class="exam-tom-select input-block w-full p-2 border rounded">
                                                    <option value="">Select Qualification</option>
                                                    <option value="HSLC" {{ strcasecmp($val, 'HSLC') === 0 ? 'selected' : '' }}>HSLC</option>
                                                    <option value="HS" {{ strcasecmp($val, 'HS') === 0 ? 'selected' : '' }}>HS</option>
                                                    <option value="GRADUATION" {{ strcasecmp($val, 'GRADUATION') === 0 ? 'selected' : '' }}>Graduation</option>
                                                    <option value="POST GRADUATION" {{ strcasecmp($val, 'POST GRADUATION') === 0 ? 'selected' : '' }}>Post Graduation</option>
                                                    <option value="OTHER" {{ strcasecmp($val, 'OTHER') === 0 ? 'selected' : '' }}>Other</option>
                                                    @if($val && !in_array(strtoupper($val), ['HSLC', 'HS', 'GRADUATION', 'POST GRADUATION', 'OTHER']))
                                                        <option value="{{ $val }}" selected>{{ $val }}</option>
                                                    @endif
                                                </select>
                                            @endif
                                        @else
                                            <input type="{{ in_array($field, ['passing_year', 'percentage_grade', 'total_marks', 'marks_obtained', 'division']) ? 'number' : 'text' }}" 
                                                   name="qual[{{ $index }}][{{ $field }}]" 
                                                   value="{{ $val }}" 
                                                   class="input-block w-full p-2 border rounded" 
                                                   placeholder="{{ $fieldLabels[$field] ?? '' }}"
                                                   {{ $field == 'percentage_grade' ? 'step=0.01' : '' }}>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="p-2 text-center">
                                    @if($isDefault)
                                        <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full border border-slate-200" title="Mandatory default qualification">Fixed</span>
                                    @else
                                        <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded transition-colors" title="Remove Qualification">
                                            <i data-lucide="trash-2" size="18"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="button" onclick="addRow()" class="mt-2 flex items-center space-x-2 text-sm text-blue-600 font-medium hover:text-blue-800">
                <i data-lucide="plus" size="16"></i> <span>Add Row</span>
            </button>
            @else
             <div class="p-4 bg-blue-50 text-blue-700 rounded-lg">Academic qualification details are not required for this form.</div>
            @endif

            <div class="flex justify-between pt-6 border-t border-slate-100">
                @php
                    $currentIdx = array_search(4, $stepMap); // 4 is Academic
                    $prevStepId = isset($stepMap[$currentIdx - 1]) ? $stepMap[$currentIdx - 1] : 1;
                    $nextStepId = isset($stepMap[$currentIdx + 1]) ? $stepMap[$currentIdx + 1] : 5;
                @endphp
                <button type="button" onclick="prevStep({{ $prevStepId }})" class="px-6 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-lg flex items-center">
                    <i data-lucide="arrow-left" class="mr-2" size="18"></i> Previous
                </button>
                <button type="button" onclick="nextStep({{ $nextStepId }}, this)" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 flex items-center">
                    Next Step <i data-lucide="arrow-right" class="ml-2" size="18"></i>
                </button>
            </div>
        </div>

        <!-- STEP 5: UPLOADS & DECLARATION -->
        <div id="step-5" class="step-content space-y-6">
            <h3 class="text-lg font-bold text-slate-800 border-b pb-2 flex items-center">
                <i data-lucide="upload" class="mr-2" size="20"></i> Documents & Declaration
            </h3>
            
            @if($form->isSectionEnabled('documents'))
            @php
                $docConfig = $form->field_config['documents']['fields'] ?? [];
                // Fallback
                if (empty($docConfig)) {
                     $docConfig = ['photo', 'caste_certificate', 'signature', 'prc_certificate', 'age_proof', 'mark_sheet_10', 'certificate_10'];
                }
                $docConfig = array_values(array_filter($docConfig, fn($f) => $form->isFieldVisible('documents', $f)));
                
                $docLabels = [
                    'photo' => 'Passport Size Photograph',
                    'caste_certificate' => 'Caste Certificate',
                    'signature' => 'Signature',
                    'prc_certificate' => 'PRC Certificate',
                    'age_proof' => 'Age Proof',
                    'mark_sheet_10' => '10th Marksheet',
                    'hslc_marksheet' => 'HSLC Marksheet',
                    'certificate_10' => '10th Pass Certificate',
                    'hslc_pass_certificate' => 'HSLC Pass Certificate',
                    'mark_sheet_12' => '12th Marksheet',
                    'hs_marksheet' => 'HS Marksheet',
                    'certificate_12' => '12th Pass Certificate',
                    'hs_pass_certificate' => 'HS Pass Certificate',
                    'graduation_marksheet' => 'Graduation Marksheet',
                    'graduation_certificate' => 'Graduation Certificate',
                    'graduation_pass_certificate' => 'Graduation Pass Certificate',
                    'post_graduation_marksheet' => 'Post Graduation Marksheet',
                    'post_graduation_certificate' => 'Post Graduation Certificate',
                    'post_graduation_pass_certificate' => 'Post Graduation Pass Certificate',
                    'hslc_admit_card' => 'HSLC Admit Card',
                    'hs_admit_card' => 'HS Admit Card',
                    'graduation_admit_card' => 'Graduation Admit Card',
                    'post_graduation_admit_card' => 'Post Graduation Admit Card',
                    'income_certificate' => 'Income Certificate',
                    'address_proof' => 'Address Proof',
                    'aadhaar_card' => 'Aadhaar Card',
                    'btc_residential_certificate' => 'Permanent Resident Certificate',
                ];

                $profileMap = [
                    'photo' => 'photo',
                    'signature' => 'signature',
                    'caste_certificate' => 'caste_certificate',
                    'mark_sheet_10' => 'hslc_marksheet',
                    'hslc_marksheet' => 'hslc_marksheet',
                    'certificate_10' => 'hslc_pass_certificate',
                    'hslc_pass_certificate' => 'hslc_pass_certificate',
                    'mark_sheet_12' => 'hs_marksheet',
                    'hs_marksheet' => 'hs_marksheet',
                    'certificate_12' => 'hs_pass_certificate',
                    'hs_pass_certificate' => 'hs_pass_certificate',
                    'graduation_marksheet' => 'graduation_marksheet',
                    'graduation_certificate' => 'graduation_pass_certificate',
                    'graduation_pass_certificate' => 'graduation_pass_certificate',
                    'post_graduation_marksheet' => 'post_graduation_marksheet',
                    'post_graduation_certificate' => 'post_graduation_pass_certificate',
                    'post_graduation_pass_certificate' => 'post_graduation_pass_certificate',
                    'prc_certificate' => 'address_proof', 
                    'address_proof' => 'address_proof',
                    'age_proof' => 'hslc_admit_card', 
                    'hslc_admit_card' => 'hslc_admit_card',
                    'hs_admit_card' => 'hs_admit_card',
                    'graduation_admit_card' => 'graduation_admit_card',
                    'post_graduation_admit_card' => 'post_graduation_admit_card',
                    'income_certificate' => 'income_certificate',
                    'aadhaar_card' => 'aadhaar_card',
                    'btc_residential_certificate' => 'btc_residential_certificate',
                ];

                $optionalDocs = [
                    'btc_residential_certificate',
                ];
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($docConfig as $docField)
                    @php
                        $dbField = $profileMap[$docField] ?? $docField;
                        $docs = $existingApp ? $existingApp->application_documents : null;
                        $uploadedDoc = ($docs && !empty($docs->$dbField)) ? $docs->$dbField : null;
                        $label = $docLabels[$docField] ?? ucfirst(str_replace('_', ' ', $docField));
                        
                        // Check Profile
                        $profileField = $profileMap[$docField] ?? null;
                        $profileDoc = ($profileField && $student->document && !empty($student->document->$profileField)) ? $student->document->$profileField : null;
                    @endphp
                    
                    <div id="doc-container-{{ $docField }}" class="border-2 {{ $uploadedDoc ? 'border-green-200 bg-green-50/30' : ($profileDoc ? 'border-blue-200 bg-blue-50/30' : 'border-dashed border-slate-300') }} rounded-lg p-6 text-center hover:border-blue-500 transition-colors group relative">
                        @if($uploadedDoc)
                            <div class="absolute top-3 right-3">
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase flex items-center">
                                    <i data-lucide="check-circle" size="12" class="mr-1"></i> Uploaded
                                </span>
                            </div>
                        @elseif($profileDoc)
                            <div class="absolute top-3 right-3">
                                <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase flex items-center">
                                    <i data-lucide="user-check" size="12" class="mr-1"></i> From Profile
                                </span>
                            </div>
                        @endif

                        <div class="mx-auto w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 mb-2">
                            <i data-lucide="{{ $uploadedDoc || $profileDoc ? 'file-check' : 'upload-cloud' }}"></i>
                        </div>
                        
                        <span class="text-sm text-slate-600 block font-medium">
                            {{ $label }} 
                            @if(!in_array($docField, $optionalDocs))
                                <span class="text-red-500">*</span>
                            @endif
                        </span>
                        
                        @if($uploadedDoc)
                            <span class="text-xs text-slate-500 block mt-1 mb-3">{{ basename($uploadedDoc) }}</span>
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ Storage::url($uploadedDoc) }}" target="_blank" class="px-3 py-1 bg-white border border-slate-200 text-slate-600 text-xs rounded hover:bg-slate-50 flex items-center">
                                    <i data-lucide="eye" size="14" class="mr-1"></i> View
                                </a>
                                <label for="doc-{{ $docField }}" class="px-3 py-1 bg-white border border-slate-200 text-blue-600 text-xs rounded hover:bg-blue-50 flex items-center cursor-pointer">
                                    <i data-lucide="refresh-cw" size="14" class="mr-1"></i> Change
                                </label>
                            </div>
                        @elseif($profileDoc)
                             <span class="text-xs text-slate-500 block mt-1 mb-3">Available in My Documents</span>
                             <div class="flex items-center justify-center space-x-2">
                                <a href="{{ Storage::url($profileDoc) }}" target="_blank" class="px-3 py-1 bg-white border border-slate-200 text-slate-600 text-xs rounded hover:bg-slate-50 flex items-center">
                                    <i data-lucide="eye" size="14" class="mr-1"></i> View
                                </a>
                                <label for="doc-{{ $docField }}" class="px-3 py-1 bg-white border border-slate-200 text-blue-600 text-xs rounded hover:bg-blue-50 flex items-center cursor-pointer">
                                    <i data-lucide="upload" size="14" class="mr-1"></i> Use Different
                                </label>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 block mt-1">Max 200KB</span>
                            <label for="doc-{{ $docField }}" class="mt-3 inline-block px-4 py-1 text-xs bg-slate-100 text-slate-700 rounded cursor-pointer hover:bg-slate-200">Choose File</label>
                        @endif
                        
                        <input type="file" name="{{ $docField }}" class="sr-only" id="doc-{{ $docField }}" {{ (!$uploadedDoc && !$profileDoc && !in_array($docField, $optionalDocs)) ? 'required' : '' }} onchange="previewFileName(this)">
                        
                        <!-- File Name Preview for new uploads -->
                        <div id="preview-{{ $docField }}" class="hidden mt-2 text-xs text-blue-600 font-medium"></div>
                    </div>
                @endforeach
            </div>
            @else
               <div class="p-4 bg-green-50 text-green-700 rounded-lg">No document uploads required.</div>
            @endif

            <div class="bg-orange-50 p-4 rounded-lg border border-orange-100 mt-6">
                <label class="flex items-start space-x-3 cursor-pointer">
                    <input type="checkbox" required class="mt-1 rounded text-orange-600 focus:ring-orange-500 w-5 h-5">
                    <span class="text-sm text-slate-700 font-medium leading-relaxed">
                        I hereby declare that the above information provided is correct and I agree to abide by all Rules & Regulations of the Institution and consent that I would be liable to punishment for violation of rules & regulations.
                    </span>
                </label>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between pt-6 border-t border-slate-100">
                @php
                    $currentIdx = array_search(5, $stepMap); // 5 is Uploads
                    $prevStepId = isset($stepMap[$currentIdx - 1]) ? $stepMap[$currentIdx - 1] : 4;
                @endphp
                <button type="button" onclick="prevStep({{ $prevStepId }})" class="px-6 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-lg flex items-center">
                    <i data-lucide="arrow-left" class="mr-2" size="18"></i> Previous
                </button>
                <div class="flex space-x-3">
                    <!-- <button type="button" onclick="saveDraft()" class="px-6 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-lg border border-slate-300 flex items-center">
                       <i data-lucide="save" class="mr-2" size="18"></i> Save Draft
                    </button> -->
                    <button type="submit" id="submitBtn" class="px-6 py-3 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 flex items-center">
                        Proceed to Preview <i data-lucide="arrow-right" class="ml-2" size="18"></i>
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<!-- Compression Modal -->
<div id="compressionModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75 backdrop-blur-sm" aria-hidden="true" onclick="closeCompressionModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-slate-200">
            <div class="sm:flex sm:items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-orange-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                    <i data-lucide="file-warning" class="text-orange-600" size="24"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-xl font-bold text-slate-800" id="modal-title">
                        File Too Large (<span id="file-size-display" class="text-orange-600"></span>)
                    </h3>
                    <div class="mt-3">
                        <p class="text-sm text-slate-600 leading-relaxed">
                            The selected file exceeds the 200KB limit. We can attempt to compress this image for you to meet the requirement.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex flex-col sm:flex-row-reverse gap-3">
                <button type="button" id="compress-btn" class="inline-flex justify-center items-center w-full px-6 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 focus:outline-none transition-all" onclick="startCompression()">
                    <i data-lucide="file-minus" size="18" class="mr-2"></i> Compress & Upload
                </button>
                <button type="button" class="inline-flex justify-center items-center w-full px-6 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 focus:outline-none transition-all" onclick="closeCompressionModal()">
                    Cancel
                </button>
            </div>
             <div id="compression-loading" class="hidden mt-6 text-center">
                <div class="flex flex-col items-center justify-center text-blue-600 text-sm font-bold">
                    <div class="flex items-center mb-2">
                        <i data-lucide="loader-2" class="mr-2 animate-spin" size="20"></i> Preparing...
                    </div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full animate-progress-indefinite"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.step-content {
    display: none;
}
.step-content.active {
    display: block;
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes progress-indefinite {
    0% { width: 0%; transform: translateX(-100%); }
    50% { width: 40%; }
    100% { width: 100%; transform: translateX(100%); }
}
.animate-progress-indefinite {
    animation: progress-indefinite 2s infinite linear;
}
</style>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    function initTomSelectExam(element) {
        if (element.tomselect) return;
        const currentVal = element.value;
        const ts = new TomSelect(element, {
            create: true,
            maxItems: 1,
            dropdownParent: 'body',
            placeholder: 'Select Qualification'
        });
        if (currentVal) {
            ts.setValue(currentVal, true);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.exam-tom-select').forEach(el => initTomSelectExam(el));
        
        // Initialize District dropdowns
        document.querySelectorAll('.district-tom-select').forEach(el => {
            new TomSelect(el, {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: 'Select District'
            });
        });
    });
    // --- UI/UX Enhancements ---
    // Custom SweetAlert Mixin for "Beautiful" UI
    const AppSwal = Swal.mixin({
        customClass: {
            popup: 'rounded-2xl shadow-xl border border-slate-100',
            title: 'text-xl font-bold text-slate-800',
            htmlContainer: 'text-slate-600 font-medium',
            confirmButton: 'bg-blue-600 text-white px-6 py-2.5 rounded-lg font-bold hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition-all shadow-lg shadow-blue-500/30',
            cancelButton: 'bg-slate-100 text-slate-600 px-6 py-2.5 rounded-lg font-bold hover:bg-slate-200 focus:ring-4 focus:ring-slate-100 transition-all'
        },
        buttonsStyling: false,
        confirmButtonText: 'OK',
        backdrop: `rgba(15, 23, 42, 0.4) backdrop-blur-sm`
    });

    // --- Multi-Step Form Logic ---
    let currentStep = {{ $existingApp ? ($existingApp->current_step ?? 1) : 1 }};
    const steps = @json($steps);
    const validStepNumbers = steps.map(s => s.number);
    if (!validStepNumbers.includes(currentStep)) {
        if (currentStep == 3 && validStepNumbers.includes(4)) {
            currentStep = 4;
        } else if (validStepNumbers.length > 0) {
            currentStep = validStepNumbers[0];
        } else {
            currentStep = 1;
        }
    }
    const totalVisibleSteps = {{ count($steps) }};
    
    function updateSteps() {
        // Hide all steps first
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));

        // Show current step
        const currentStepEl = document.getElementById(`step-${currentStep}`);
        if(currentStepEl) {
            currentStepEl.classList.add('active');
        }

        // Update Header Indicators (Circles) & Lines
        steps.forEach((step, index) => {
            const stepNum = step.number;
            const indicator = document.getElementById(`step-indicator-${stepNum}`);
            const line = document.getElementById(`line-${stepNum}`);
            const container = document.getElementById(`step-container-${stepNum}`);

            // Active/Completed State
            // Logic: If the step number in the array is less than or equal to currentStep, it's active/completed
            // But we need to check logical position. 
            // Better: find index of currentStep in steps array.
            const currentIndex = steps.findIndex(s => s.number == currentStep);
            
            if (index <= currentIndex) {
                if(indicator) {
                    indicator.classList.remove('bg-slate-200', 'text-slate-600');
                    indicator.classList.add('bg-blue-600', 'text-white');
                }
                if(container) {
                    container.classList.add('text-blue-600');
                }
            } else {
                if(indicator) {
                    indicator.classList.add('bg-slate-200', 'text-slate-600');
                    indicator.classList.remove('bg-blue-600', 'text-white');
                }
                if(container) {
                    container.classList.remove('text-blue-600');
                }
            }

            // Line Logic
            if(line) {
                if (index < currentIndex) {
                    line.style.width = '100%';
                } else {
                    line.style.width = '0%';
                }
            }
        });
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextStep(step, btn) {
        // Current step is stored in global 'currentStep'
        const currentStepEl = document.getElementById(`step-${currentStep}`);
        const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        let firstInvalid = null;

        // Reset previous highlights on labels/containers
        currentStepEl.querySelectorAll('.error-container').forEach(el => {
            el.classList.remove('error-container', 'p-2', 'border', 'border-red-500', 'rounded');
        });

        const radioGroups = new Set();

        inputs.forEach(input => {
            if (input.type === 'radio') {
                radioGroups.add(input.name);
            } else if (input.type === 'checkbox') {
                 if (!input.checked) {
                    isValid = false;
                    input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    if (!firstInvalid) firstInvalid = input;
                 }
            } else {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                    if (!firstInvalid) firstInvalid = input;
                    
                    // Remove error on input
                    input.addEventListener('input', function() {
                        this.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                    }, { once: true });
                }
            }
        });

        // Check radio groups
        radioGroups.forEach(name => {
            const group = currentStepEl.querySelectorAll(`input[name="${name}"]`);
            const checked = Array.from(group).some(r => r.checked);
            if (!checked) {
                isValid = false;
                const container = group[0].closest('div');
                container.classList.add('error-container', 'p-2', 'border', 'border-red-500', 'rounded');
                if (!firstInvalid) firstInvalid = group[0];
                
                // Remove error when any radio in group is clicked
                group.forEach(r => {
                    r.addEventListener('change', function() {
                        container.classList.remove('error-container', 'p-2', 'border', 'border-red-500', 'rounded');
                    }, { once: true });
                });
            }
        });

        if (!isValid) {
            if (firstInvalid) {
                // For radio buttons, focus might not work as expected, but scroll will
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            // Show SweetAlert for validation error
            AppSwal.fire({
                icon: 'error',
                title: 'Incomplete Fields',
                text: 'Please fill in all required fields marked with *',
                confirmButtonText: 'Got it'
            });
            return;
        }

        const stepToSave = currentStep;
        
        // Show loading state
        const originalText = btn.innerHTML;
        const originalPointerEvents = btn.style.pointerEvents;
        const originalOpacity = btn.style.opacity;
        
        btn.innerHTML = '<i data-lucide="loader-2" class="mr-2 animate-spin" size="18"></i> Saving...';
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.7';
        lucide.createIcons();
        
        // Save data for the current step
        saveStepData(stepToSave).then(result => {
            // Restore button state
            btn.innerHTML = originalText;
            btn.style.pointerEvents = originalPointerEvents;
            btn.style.opacity = originalOpacity;
            lucide.createIcons();

            if (result.success) {
                currentStep = step;
                updateSteps();
            } else {
                console.warn('Failed to save step data:', result.message);
                
                let errorMsg = result.message || 'Could not save your progress. Please check your connection.';
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join('<br>');
                }

                AppSwal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `<div class="text-left text-sm">${errorMsg}</div>`,
                    confirmButtonText: 'Correct Errors'
                });
                return; 
            }
        });
    }

    async function saveStepData(step) {
        const form = document.getElementById('admissionForm');
        const formData = new FormData(form);
        formData.append('step', step);

        try {
            const response = await fetch("{{ route('student.application.save-step') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: formData
            });
            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Error saving step:', error);
            return { success: false, message: 'Network error or server unavailable.' };
        }
    }

    function prevStep(step) {
        currentStep = step;
        updateSteps();
    }

    function saveDraft() {
        // Real save
        saveStepData(currentStep).then(result => {
            if(result.success) {
                AppSwal.fire({
                    icon: 'success',
                    title: 'Draft Saved',
                    text: 'Your application progress has been saved successfully.',
                    confirmButtonText: 'Great!'
                });
            } else {
                let errorMsg = result.message || 'Could not save draft. Please try again.';
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join('<br>');
                }

                 AppSwal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    html: `<div class="text-left text-sm">${errorMsg}</div>`,
                    confirmButtonText: 'Retry'
                });
            }
        });
    }

    // --- Address Logic ---
    function toggleSameAddress() {
        const sameCheckbox = document.getElementById('sameAddress');
        if (!sameCheckbox) return;
        const isChecked = sameCheckbox.checked;
        const fields = ['vill', 'street', 'city', 'dist', 'state', 'country', 'pin'];
        
        fields.forEach(field => {
            const permInput = document.getElementById(`perm_${field}`);
            const preInput = document.getElementById(`pre_${field}`);
            
            if (permInput && preInput) {
                if (isChecked) {
                    if (preInput.tomselect) {
                        preInput.tomselect.setValue(permInput.value);
                        preInput.tomselect.lock();
                        preInput.tomselect.control.classList.add('bg-slate-100');
                    } else {
                        preInput.value = permInput.value;
                        preInput.setAttribute('readonly', 'true');
                        preInput.classList.add('bg-slate-100');
                    }
                } else {
                    if (preInput.tomselect) {
                        preInput.tomselect.unlock();
                        preInput.tomselect.control.classList.remove('bg-slate-100');
                    } else {
                        preInput.removeAttribute('readonly');
                        preInput.classList.remove('bg-slate-100');
                    }
                }
            }
        });
    }

    function syncSameAddressLive() {
        const sameCheckbox = document.getElementById('sameAddress');
        if (!sameCheckbox || !sameCheckbox.checked) return;
        const fields = ['vill', 'street', 'city', 'dist', 'state', 'country', 'pin'];
        fields.forEach(field => {
            const permInput = document.getElementById(`perm_${field}`);
            const preInput = document.getElementById(`pre_${field}`);
            if (permInput && preInput) {
                if (preInput.tomselect) {
                    preInput.tomselect.setValue(permInput.value);
                } else {
                    preInput.value = permInput.value;
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('sameAddress') && document.getElementById('sameAddress').checked) {
            toggleSameAddress();
        }

        const fields = ['vill', 'street', 'city', 'dist', 'state', 'country', 'pin'];
        fields.forEach(field => {
            const permInput = document.getElementById(`perm_${field}`);
            if (permInput) {
                permInput.addEventListener('input', syncSameAddressLive);
                permInput.addEventListener('change', syncSameAddressLive);
            }
        });

        updateSteps();
    });

    // --- Dynamic Table Logic ---
    let rowCount = {{ count($qualRows ?? []) }};
    const academicFieldsConfig = @json($academicFieldsConfig ?? []);
    const fieldLabels = @json($fieldLabels ?? []);

    function addRow() {
        const tbody = document.getElementById('qualBody');
        const row = document.createElement('tr');
        
        let cols = '';
        academicFieldsConfig.forEach(field => {
            const type = ['passing_year', 'percentage_grade', 'total_marks', 'marks_obtained', 'division'].includes(field) ? 'number' : 'text';
            const step = field === 'percentage_grade' ? 'step="0.01"' : '';
            const isExam = field === 'examination';
            const placeholder = fieldLabels[field] || '';
            
            if (isExam) {
                cols += `
                    <td class="p-2">
                        <select name="qual[${rowCount}][examination]" class="exam-tom-select input-block w-full p-2 border rounded">
                            <option value="">Select Qualification</option>
                            <option value="HSLC">HSLC</option>
                            <option value="HS">HS</option>
                            <option value="GRADUATION">Graduation</option>
                            <option value="POST GRADUATION">Post Graduation</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </td>
                `;
            } else {
                cols += `
                    <td class="p-2">
                        <input type="${type}" name="qual[${rowCount}][${field}]" class="input-block w-full p-2 border rounded" placeholder="${placeholder}" ${step}>
                    </td>
                `;
            }
        });
        
        cols += `
            <td class="p-2 text-center">
                <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded transition-colors" title="Remove Qualification">
                    <i data-lucide="trash-2" size="18"></i>
                </button>
            </td>
        `;

        row.innerHTML = cols;
        tbody.appendChild(row);
        
        // Initialize TomSelect for the new row if applicable
        const newExamInput = row.querySelector('.exam-tom-select');
        if (newExamInput) {
            initTomSelectExam(newExamInput);
        }
        
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        rowCount++;
    }

    // --- Document Compression & Upload Logic ---
    let activeFileInput = null;
    let currentFile = null;
    const profileMap = @json($profileMap ?? []);

    function previewFileName(input) {
        // ID format: doc-{field}
        const fieldName = input.id.replace('doc-', '');
        const previewEl = document.getElementById(`preview-${fieldName}`);
        
        if (input.files && input.files.length > 0) {
            const file = input.files[0];
            const sizeKB = file.size / 1024;
            
            if (sizeKB > 200) {
                // If it's an image, offer compression
                if (file.type.startsWith('image/')) {
                    activeFileInput = input;
                    currentFile = file;
                    document.getElementById('file-size-display').textContent = sizeKB.toFixed(2) + ' KB';
                    document.getElementById('compressionModal').classList.remove('hidden');
                    lucide.createIcons();
                    return;
                } else {
                    AppSwal.fire({
                        icon: 'warning',
                        title: 'File Too Large',
                        text: 'The file size exceeds 200KB. PDF files must be compressed manually.',
                        confirmButtonText: 'Okay'
                    });
                    input.value = ''; // Clear input
                    previewEl.textContent = '';
                    previewEl.classList.add('hidden');
                    return;
                }
            }

            previewEl.textContent = 'Selected: ' + file.name;
            previewEl.classList.remove('hidden');
        } else {
            previewEl.textContent = '';
            previewEl.classList.add('hidden');
        }
    }

    function closeCompressionModal() {
        document.getElementById('compressionModal').classList.add('hidden');
        if(activeFileInput) activeFileInput.value = '';
        activeFileInput = null;
        currentFile = null;
    }

    async function startCompression() {
        if (!currentFile || !activeFileInput) return;

        const loadingEl = document.getElementById('compression-loading');
        const compressBtn = document.getElementById('compress-btn');
        const fieldName = activeFileInput.id.replace('doc-', '');
        const profileType = profileMap[fieldName] || fieldName;

        loadingEl.classList.remove('hidden');
        compressBtn.classList.add('hidden');
        
        try {
            const compressedBlob = await compressImage(currentFile);
            
            // Upload to profile via AJAX
            const formData = new FormData();
            formData.append('file', compressedBlob, currentFile.name);
            formData.append('type', profileType);
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch("{{ route('student.documents.store') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Update UI for the doc card
                updateDocCardUI(fieldName, result.path);
                
                AppSwal.fire({
                    icon: 'success',
                    title: 'Compressed & Saved!',
                    text: 'Document compressed and saved to your profile successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(result.message || 'Upload failed');
            }
        } catch (error) {
            console.error(error);
            AppSwal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Compression or upload failed. Please try a smaller file.'
            });
        } finally {
            loadingEl.classList.add('hidden');
            compressBtn.classList.remove('hidden');
            document.getElementById('compressionModal').classList.add('hidden');
        }
    }

    function compressImage(file) {
        return new Promise((resolve, reject) => {
            const maxWidth = 1200;
            const maxHeight = 1200;
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function (event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function () {
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    let quality = 0.8;
                    function attempt() {
                        canvas.toBlob((blob) => {
                            if (blob.size <= 200 * 1024 || quality <= 0.1) {
                                resolve(blob);
                            } else {
                                quality -= 0.1;
                                attempt();
                            }
                        }, 'image/jpeg', quality);
                    }
                    attempt();
                };
            };
        });
    }

    function updateDocCardUI(docField, path) {
        const container = document.getElementById(`doc-container-${docField}`);
        if (!container) return;

        // Change card style to "From Profile" (blue)
        container.classList.remove('border-dashed', 'border-slate-300', 'border-green-200', 'bg-green-50/30');
        container.classList.add('border-blue-200', 'bg-blue-50/30');

        // Extract filename
        const storageBase = "{{ Storage::url('') }}".replace(/\/$/, '');
        const assetUrl = storageBase + '/' + path.replace(/^\//, '');

        // Construct new HTML for the card
        container.innerHTML = `
            <div class="absolute top-3 right-3">
                <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase flex items-center">
                    <i data-lucide="user-check" size="12" class="mr-1"></i> From Profile
                </span>
            </div>
            <div class="mx-auto w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 mb-2">
                <i data-lucide="file-check"></i>
            </div>
            <span class="text-sm text-slate-600 block font-medium">${docField.replace(/_/g, ' ').toUpperCase()} <span class="text-red-500">*</span></span>
            <span class="text-xs text-slate-500 block mt-1 mb-3">Available in My Documents</span>
            <div class="flex items-center justify-center space-x-2">
                <a href="${assetUrl}" target="_blank" class="px-3 py-1 bg-white border border-slate-200 text-slate-600 text-xs rounded hover:bg-slate-50 flex items-center">
                    <i data-lucide="eye" size="14" class="mr-1"></i> View
                </a>
                <label for="doc-${docField}" class="px-3 py-1 bg-white border border-slate-200 text-blue-600 text-xs rounded hover:bg-blue-50 flex items-center cursor-pointer">
                    <i data-lucide="upload" size="14" class="mr-1"></i> Use Different
                </label>
            </div>
            <input type="file" name="${docField}" class="sr-only" id="doc-${docField}" onchange="previewFileName(this)">
            <div id="preview-${docField}" class="hidden mt-2 text-xs text-blue-600 font-medium"></div>
        `;
        
        lucide.createIcons();
    }

    // Submit Button Loading State
    document.getElementById('admissionForm').addEventListener('submit', function(e) {
        // Final Step Validation (Last Step)
        // Find the last step number from our steps array
        if(steps.length === 0) return;
        
        const lastStepNum = steps[steps.length - 1].number;
        const currentStepEl = document.getElementById(`step-${lastStepNum}`);
        
        if (!currentStepEl) return;

        const inputs = currentStepEl.querySelectorAll('input[required]');
        let isValid = true;

        inputs.forEach(input => {
             if (input.type === 'checkbox') {
                 if (!input.checked) isValid = false;
             } else if (input.type === 'file') {
                 // Dynamic check: if it's required, user MUST select a file
                 if (!input.value) isValid = false;
             } else {
                 if (!input.value.trim()) isValid = false;
             }
        });

        if (!isValid) {
            e.preventDefault();
            AppSwal.fire({
                icon: 'error',
                title: 'Missing Requirements',
                text: 'Please ensure all mandatory documents are uploaded and you have accepted the declaration.'
            });
            return;
        }

        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i data-lucide="loader-2" class="mr-2 animate-spin" size="18"></i> Processing...';
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.7';
        lucide.createIcons();
    });
</script>
@endpush
