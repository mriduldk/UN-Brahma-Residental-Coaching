@extends('layouts.administration')

@section('title', isset($form) ? 'Edit Form' : 'Generate Form')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center">
            <a href="{{ route('administration.forms') }}" class="mr-4 text-slate-500 hover:text-slate-800 bg-white p-2 rounded-lg hover:shadow-sm transition-all">
                <i data-lucide="arrow-left" size="24"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">{{ isset($form) ? 'Edit Form Configuration' : 'Generate New Form' }}</h2>
                <p class="text-slate-500">{{ isset($form) ? 'Update form details and field visibility' : 'Configure fields visible to students' }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('administration.forms') }}" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors font-medium">Cancel</a>
            <button type="submit" form="create-form" id="submit-btn" 
                class="bg-blue-600 hover:bg-blue-700 shadow-blue-200 hover:-translate-y-0.5 text-white px-6 py-2 rounded-lg shadow-lg flex items-center font-medium transition-all cursor-pointer">
                <i data-lucide="save" size="18" class="mr-2"></i> <span>{{ isset($form) ? 'Update Form' : 'Save Form' }}</span>
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm">
            <div class="flex items-center mb-2">
                <i data-lucide="alert-circle" class="text-red-500 mr-2" size="20"></i>
                <h3 class="text-sm font-bold text-red-800">Please correct the following errors:</h3>
            </div>
            <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="create-form" action="{{ isset($form) ? route('administration.forms.update', $form->id) : route('administration.forms.store') }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-8" onsubmit="return handleSubmit()">
        @csrf
        @if(isset($form))
            @method('PUT')
        @endif
        
        <!-- Form Basic Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Form Title</label>
                <input type="text" name="application_name" value="{{ old('application_name', $form->application_name ?? '') }}" required placeholder="e.g., Engineering (JEE) Residential Free Coaching 2026" class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                <textarea name="application_description" rows="3" placeholder="Brief description of the coaching programme..." class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">{{ old('application_description', $form->application_description ?? '') }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Academic Session</label>
                <select name="session" required class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    <option value="2026-2027" {{ (old('session', $form->session ?? '') == '2026-2027') ? 'selected' : '' }}>2026 - 2027</option>
                    <option value="2025-2026" {{ (old('session', $form->session ?? '') == '2025-2026') ? 'selected' : '' }}>2025 - 2026</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Institution <span class="text-red-500">*</span></label>
                <select name="institution_id" required class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    <option value="">Select Institution / Center</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ (old('institution_id', $form->institution_id ?? '') == $inst->id) ? 'selected' : '' }}>
                            {{ $inst->institution_name }} ({{ $inst->institution_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Application Fee Config -->
            <div class="md:col-span-2 space-y-4">
                 <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <label class="block text-sm font-bold text-slate-700">Application Fees (by Category)</label>
                        <div class="group relative">
                            <i data-lucide="info" size="14" class="text-slate-400 cursor-help hover:text-blue-500 transition-colors"></i>
                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-72 p-4 bg-slate-900 text-white text-[11px] rounded-xl shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-20 pointer-events-none">
                                <p class="font-bold border-b border-slate-700 pb-2 mb-2">Application Fee Settings</p>
                                <ul class="space-y-1 list-disc pl-3">
                                    <li>Leave ₹0 for 100% Free Coaching / exempted categories.</li>
                                    <li>Optionally set category-wise fees if applicable.</li>
                                </ul>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-8 border-transparent border-t-slate-900"></div>
                            </div>
                        </div>
                    </div>
                     <label class="relative inline-flex items-center {{ ($orgConfig && !$orgConfig->is_registration_fee_enabled) ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                        <input type="checkbox" name="is_application_fee_required" value="1" id="app_fee_toggle" class="sr-only peer" 
                            {{ old('is_application_fee_required', $form->is_application_fee_required ?? false) ? 'checked' : '' }}
                            {{ ($orgConfig && !$orgConfig->is_registration_fee_enabled) ? 'disabled' : '' }}>
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-2 text-xs font-medium text-slate-500">Required</span>
                    </label>
                 </div>
                 @if($orgConfig && !$orgConfig->is_registration_fee_enabled)
                    <div class="text-[10px] text-red-500 font-bold uppercase mt-1">Registration Fee Disabled - Contact Admin to enable</div>
                 @endif
                 <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 transition-all" id="app_fee_container">
                    @php
                        $catFees = isset($form) && is_array($form->category_fees) ? $form->category_fees : [];
                    @endphp
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">General (Unreserved)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                            <input type="number" name="category_fees[general]" value="{{ old('category_fees.general', $catFees['general'] ?? '') }}" min="0" step="0.01" placeholder="0" class="w-full pl-8 p-2.5 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">SC / ST / OBC / MOBC</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                            <input type="number" name="category_fees[reserved]" value="{{ old('category_fees.reserved', $catFees['reserved'] ?? '') }}" min="0" step="0.01" placeholder="0" class="w-full pl-8 p-2.5 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">PwD (Persons with Disabilities)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                            <input type="number" name="category_fees[pwd]" value="{{ old('category_fees.pwd', $catFees['pwd'] ?? '') }}" min="0" step="0.01" placeholder="0" class="w-full pl-8 p-2.5 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admission Fee Config -->
            <div class="md:col-span-1 space-y-3">
                 <div class="flex items-center justify-between">
                    <label class="block text-sm font-bold text-slate-700">Admission Fee</label>
                     <label class="relative inline-flex items-center {{ ($orgConfig && !$orgConfig->is_admission_enabled) ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                        <input type="checkbox" name="is_admission_required" value="1" id="adm_fee_toggle" class="sr-only peer" 
                            {{ old('is_admission_required', $form->is_admission_required ?? false) ? 'checked' : '' }}
                            {{ ($orgConfig && !$orgConfig->is_admission_enabled) ? 'disabled' : '' }}>
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-2 text-xs font-medium text-slate-500">Required</span>
                    </label>
                 </div>
                 @if($orgConfig && !$orgConfig->is_admission_enabled)
                    <div class="text-[10px] text-red-500 font-bold uppercase mt-1">Admission Disabled - Contact Admin to enable</div>
                 @endif
                 <div class="relative transition-all" id="adm_fee_container">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                    <input type="number" name="admission_fee" id="admission_fee" value="{{ old('admission_fee', $form->admission_fee ?? '') }}" min="0" step="0.01" placeholder="0" class="w-full pl-8 p-3 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">No. of Seats / Vacancies</label>
                <input type="number" name="no_of_vacancies" value="{{ old('no_of_vacancies', $form->no_of_vacancies ?? '50') }}" min="1" placeholder="e.g. 50" class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Deadline</label>
                <input type="date" name="deadline" value="{{ old('deadline', isset($form) && $form->deadline ? $form->deadline->format('Y-m-d') : '') }}" class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
            </div>
            @if(isset($form))
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Status</label>
                <select name="status" required class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    <option value="draft" {{ $form->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ $form->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="closed" {{ $form->status == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>
            @endif

            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Advertisement / Information Brochure PDF</label>
                @if(isset($form) && $form->advertisement_pdf)
                    <div class="mb-2 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-sm text-green-700 font-medium">Current PDF uploaded</span>
                        <a href="{{ asset('storage/' . $form->advertisement_pdf) }}" target="_blank" class="text-xs text-blue-600 underline ml-auto">View</a>
                    </div>
                @endif
                <input type="file" name="advertisement_pdf" accept="application/pdf" class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
                <p class="text-xs text-slate-400 mt-1">Upload the official brochure or advertisement PDF (max 10 MB).{{ isset($form) ? ' Leave blank to keep existing.' : '' }}</p>
            </div>

            <!-- Application Approval Config -->
            <div class="md:col-span-2 space-y-3 p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                 <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <label class="block text-sm font-bold text-slate-700">Requires Admin Approval</label>
                        <span class="text-xs text-slate-500">If enabled, submitted applications must be approved by an administrator before they are fully processed.</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_approval_required" value="1" class="sr-only peer" {{ old('is_approval_required', $form->is_approval_required ?? false) ? 'checked' : '' }}>
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-2 text-xs font-medium text-slate-500">Required</span>
                    </label>
                 </div>
            </div>
        </div>

        <hr class="border-slate-100">

        <!-- Field Configuration -->
        <div class="space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-lg">Field Configuration</h3>
                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded font-medium">Select visible fields</span>
            </div>

            @php
                $sections = [
                    'personal' => ['title' => 'Personal Details', 'icon' => 'user', 'color' => 'blue', 'mandatory' => ['full_name', 'email', 'phone_number', 'alternate_mobile_number']],
                    'address' => ['title' => 'Address Details', 'icon' => 'map-pin', 'color' => 'emerald', 'mandatory' => []],
                    'academic' => ['title' => 'Academic Information', 'icon' => 'graduation-cap', 'color' => 'amber', 'mandatory' => []],
                    'parents' => ['title' => 'Parent/Guardian Details', 'icon' => 'users', 'color' => 'indigo', 'mandatory' => ['father_annual_income']],
                    'bank' => ['title' => 'Bank Details', 'icon' => 'university', 'color' => 'rose', 'mandatory' => []],
                    'documents' => ['title' => 'Documents Upload', 'icon' => 'file', 'color' => 'slate', 'mandatory' => []],
                ];

                $defaults = [
                    'personal' => ['full_name', 'gender', 'category', 'dob', 'phone_number', 'email', 'alternate_mobile_number'],
                    'address' => [
                        'permanent_village_town', 'permanent_district', 'permanent_state', 'permanent_country', 'permanent_pin_code',
                        'present_village_town', 'present_district', 'present_state', 'present_country', 'present_pin_code'
                    ],
                    'academic' => ['examination', 'board_university', 'passing_year', 'percentage_grade', 'division'],
                    'documents' => ['photo', 'signature'],
                ];
            @endphp

            @foreach($sections as $key => $section)
            @php
                if(isset($form) && $form->field_config) {
                    $isSectionEnabled = isset($form->field_config[$key]['enabled']);
                } else {
                    $isSectionEnabled = !in_array($key, ['bank', 'parents']);
                }
            @endphp
            <div class="bg-white border md:p-6 p-4 rounded-2xl border-slate-200 shadow-sm transition-all hover:shadow-md" id="section-{{ $key }}">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <h4 class="font-bold text-slate-800 text-lg flex items-center">
                        <div class="p-2 bg-{{ $section['color'] }}-50 text-{{ $section['color'] }}-600 rounded-lg mr-3">
                            <i data-lucide="{{ $section['icon'] }}" size="20"></i>
                        </div>
                        {{ $section['title'] }}
                    </h4>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="field_config[{{ $key }}][enabled]" value="1" class="sr-only peer section-toggle" data-target="{{ $key }}" {{ $isSectionEnabled ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-slate-700">Enable</span>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 transition-all duration-300" id="fields-{{ $key }}">
                    @if(isset($columns[$key]))
                        @if(\Illuminate\Support\Arr::isAssoc($columns[$key]))
                            {{-- Grouped Layout --}}
                            @foreach($columns[$key] as $groupTitle => $groupFields)
                                <div class="col-span-full mt-3 mb-1">
                                    <h5 class="font-bold text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100 pb-1">{{ $groupTitle }}</h5>
                                </div>
                                @foreach($groupFields as $col)
                                    @php
                                        $label = ucwords(str_replace('_', ' ', $col));
                                        if ($col === 'is_permanent_resident_of_assam') $label = 'Permanent Resident Of Assam';
                                        if ($col === 'is_btc_resident') $label = 'BTC Resident';
                                        if ($col === 'employment_exchange_no') $label = 'Employment Exchange No.';
                                        if ($col === 'is_pwd') $label = 'Is PwD';
                                        if ($col === 'alternate_mobile_number') $label = 'What’s App Number';
                                        if ($col === 'father_annual_income') $label = 'Family income per (per anum):';
                                        if ($col === 'permanent_street') $label = 'BTCLA Constituency';
                                        if ($col === 'permanent_city') $label = 'Permanent Post Office';
                                        if ($col === 'permanent_country') $label = 'Permanent Police Station';
                                        if ($col === 'present_city') $label = 'Present Post Office';
                                        if ($col === 'present_country') $label = 'Present Police Station';
                                        $isMandatory = in_array($col, $section['mandatory']);
                                        
                                        if($isMandatory) {
                                            $isChecked = true;
                                        } elseif(isset($form) && $form->field_config) {
                                            $isChecked = isset($form->field_config[$key]['fields']) && in_array($col, $form->field_config[$key]['fields']);
                                        } else {
                                            $isChecked = isset($defaults[$key]) && in_array($col, $defaults[$key]);
                                        }
                                    @endphp
                                    <label class="flex items-start space-x-3 p-3 rounded-xl border border-slate-200 transition-all cursor-pointer {{ $isMandatory ? 'bg-slate-50 opacity-70' : 'hover:border-blue-400 hover:shadow-sm bg-white' }}">
                                        <input type="checkbox" 
                                            name="field_config[{{ $key }}][fields][]" 
                                            value="{{ $col }}" 
                                            {{ $isChecked ? 'checked' : '' }} 
                                            {{ $isMandatory ? 'disabled' : '' }}
                                            class="mt-1 w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                                        
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-slate-700 ">{{ $label }}</span>
                                            @if($isMandatory)
                                                <input type="hidden" name="field_config[{{ $key }}][fields][]" value="{{ $col }}">
                                                <span class="text-[10px] text-red-500 font-bold uppercase mt-0.5">Mandatory</span>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            @endforeach
                        @else
                            {{-- Flat Layout --}}
                            @foreach($columns[$key] as $col)
                                @php
                                    $label = ucwords(str_replace('_', ' ', $col));
                                    if ($col === 'is_permanent_resident_of_assam') $label = 'Permanent Resident Of Assam';
                                    if ($col === 'is_btc_resident') $label = 'BTC Resident';
                                    if ($col === 'employment_exchange_no') $label = 'Employment Exchange No.';
                                    if ($col === 'is_pwd') $label = 'Is PwD';
                                    if ($col === 'alternate_mobile_number') $label = 'What’s App Number';
                                    if ($col === 'father_annual_income') $label = 'Family income per (per anum):';
                                    if ($col === 'permanent_street') $label = 'BTCLA Constituency';
                                    if ($col === 'permanent_city') $label = 'Permanent Post Office';
                                    if ($col === 'permanent_country') $label = 'Permanent Police Station';
                                    if ($col === 'present_city') $label = 'Present Post Office';
                                    if ($col === 'present_country') $label = 'Present Police Station';
                                    if ($col === 'btc_residential_certificate') $label = 'Permanent Resident Certificate';
                                    if ($col === 'hslc_marksheet') $label = 'HSLC Marksheet';
                                    if ($col === 'hslc_pass_certificate') $label = 'HSLC Pass Certificate';
                                    if ($col === 'hs_marksheet') $label = 'HS Marksheet';
                                    if ($col === 'hs_pass_certificate') $label = 'HS Pass Certificate';
                                    if ($col === 'graduation_marksheet') $label = 'Graduation Marksheet';
                                    if ($col === 'graduation_pass_certificate') $label = 'Graduation Pass Certificate';
                                    if ($col === 'post_graduation_marksheet') $label = 'Post Graduation Marksheet';
                                    if ($col === 'post_graduation_pass_certificate') $label = 'Post Graduation Pass Certificate';
                                    if ($col === 'hslc_admit_card') $label = 'HSLC Admit Card';
                                    if ($col === 'hs_admit_card') $label = 'HS Admit Card';
                                    if ($col === 'graduation_admit_card') $label = 'Graduation Admit Card';
                                    if ($col === 'post_graduation_admit_card') $label = 'Post Graduation Admit Card';
                                    if ($col === 'caste_certificate') $label = 'Caste Certificate';
                                    if ($col === 'income_certificate') $label = 'Income Certificate';
                                    if ($col === 'address_proof') $label = 'Address Proof';

                                    $isMandatory = in_array($col, $section['mandatory']);
                                    
                                     if($isMandatory) {
                                        $isChecked = true;
                                    } elseif(isset($form) && $form->field_config) {
                                        $isChecked = isset($form->field_config[$key]['fields']) && in_array($col, $form->field_config[$key]['fields']);
                                    } else {
                                        $isChecked = isset($defaults[$key]) && in_array($col, $defaults[$key]);
                                    }
                                @endphp
                                <label class="flex items-start space-x-3 p-3 rounded-xl border border-slate-200 transition-all cursor-pointer {{ $isMandatory ? 'bg-slate-50 opacity-70' : 'hover:border-blue-400 hover:shadow-sm bg-white' }}">
                                    <input type="checkbox" 
                                        name="field_config[{{ $key }}][fields][]" 
                                        value="{{ $col }}" 
                                        {{ $isChecked ? 'checked' : '' }} 
                                        {{ $isMandatory ? 'disabled' : '' }}
                                        class="mt-1 w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                                    
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-slate-700 ">{{ $label }}</span>
                                        @if($isMandatory)
                                            <input type="hidden" name="field_config[{{ $key }}][fields][]" value="{{ $col }}">
                                            <span class="text-[10px] text-red-500 font-bold uppercase mt-0.5">Mandatory</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        @endif
                    @else
                        <p class="text-sm text-slate-400 italic">No configuration available for this section.</p>
                    @endif

                    @if($key === 'academic')
                        <div class="col-span-full mt-6 pt-4 border-t border-slate-100">
                            <div class="mb-3">
                                <h5 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                    <i data-lucide="graduation-cap" size="18" class="text-amber-500"></i>
                                    Default Qualifications to Show in Student Form
                                </h5>
                                <p class="text-xs text-slate-500">Select which qualifications will appear by default in the applicant's academic qualification table.</p>
                            </div>
                            @php
                                $selectedQuals = isset($form) && isset($form->field_config['academic']['default_qualifications'])
                                    ? $form->field_config['academic']['default_qualifications']
                                    : ['HSLC', 'HS', 'GRADUATION'];
                                $qualOptions = [
                                    'HSLC' => 'HSLC / 10th Standard',
                                    'HS' => 'HS / 12th Standard',
                                    'GRADUATION' => 'Graduation / Degree',
                                    'POST GRADUATION' => 'Post Graduation / Master\'s',
                                ];
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                @foreach($qualOptions as $qCode => $qTitle)
                                    @php $isQChecked = in_array($qCode, $selectedQuals); @endphp
                                    <label class="flex items-start space-x-3 p-3 rounded-xl border border-slate-200 transition-all cursor-pointer hover:border-amber-400 hover:shadow-sm bg-white">
                                        <input type="checkbox" 
                                            name="field_config[academic][default_qualifications][]" 
                                            value="{{ $qCode }}" 
                                            {{ $isQChecked ? 'checked' : '' }} 
                                            class="mt-1 w-5 h-5 text-amber-600 rounded focus:ring-amber-500">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-slate-800">{{ $qCode }}</span>
                                            <span class="text-xs text-slate-500">{{ $qTitle }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Handle Section Toggles
    document.querySelectorAll('.section-toggle').forEach(toggle => {
        const targetId = toggle.dataset.target;
        const fieldsContainer = document.getElementById(`fields-${targetId}`);
        const card = document.getElementById(`section-${targetId}`);
        
        if(!toggle.checked) {
            if(fieldsContainer) {
                fieldsContainer.style.opacity = '0.5';
                fieldsContainer.style.pointerEvents = 'none';
            }
            if(card) card.classList.add('opacity-50', 'grayscale');
        }

        toggle.addEventListener('change', function() {
            const targetId = this.dataset.target;
            const fieldsContainer = document.getElementById(`fields-${targetId}`);
            const card = document.getElementById(`section-${targetId}`);
            
            if(this.checked) {
                if(fieldsContainer) {
                    fieldsContainer.style.opacity = '1';
                    fieldsContainer.style.pointerEvents = 'auto';
                }
                if(card) card.classList.remove('opacity-50', 'grayscale');
            } else {
                if(fieldsContainer) {
                    fieldsContainer.style.opacity = '0.5';
                    fieldsContainer.style.pointerEvents = 'none';
                }
                if(card) card.classList.add('opacity-50', 'grayscale');
            }
        });
    });

    // Handle Submit Loading
    function handleSubmit() {
        const btn = document.getElementById('submit-btn');
        if (!btn) return true;
        const icon = btn.querySelector('[data-lucide]');
        const text = btn.querySelector('span');
        
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-wait');
        
        if (icon) {
            icon.setAttribute('data-lucide', 'loader-2');
            icon.classList.add('animate-spin');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
        
        if (text) text.innerText = 'Saving...';
        return true;
    }

    // Fee Toggles Logic
    function setupFeeToggle(toggleId, containerId) {
        const toggle = document.getElementById(toggleId);
        const container = document.getElementById(containerId);
        if(!toggle || !container) return;

        function updateState() {
            if(toggle.checked) {
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            } else {
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';
            }
        }

        toggle.addEventListener('change', updateState);
        updateState();
    }

    setupFeeToggle('app_fee_toggle', 'app_fee_container');
    setupFeeToggle('adm_fee_toggle', 'adm_fee_container');
</script>
@endpush
@endsection
