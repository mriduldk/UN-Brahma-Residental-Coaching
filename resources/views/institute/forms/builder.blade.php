@extends('layouts.institute')

@section('title', isset($form) ? 'Edit Form' : 'Generate Form')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center">
            <a href="{{ route('institute.forms.index') }}" class="mr-4 text-slate-500 hover:text-slate-800 bg-white p-2 rounded-lg hover:shadow-sm transition-all">
                <i data-lucide="arrow-left" size="24"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">{{ isset($form) ? 'Edit Form Configuration' : 'Generate New Form' }}</h2>
                <p class="text-slate-500">{{ isset($form) ? 'Update form details and field visibility' : 'Configure fields visible to students' }}</p>
            </div>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('institute.forms.index') }}" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors font-medium">Cancel</a>
            <button type="submit" form="create-form" id="submit-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 flex items-center font-medium transition-all hover:-translate-y-0.5">
                <i data-lucide="save" size="18" class="mr-2"></i> <span>{{ isset($form) ? 'Update Form' : 'Save Form' }}</span>
            </button>
        </div>
    </div>

    <form id="create-form" action="{{ isset($form) ? route('institute.forms.update', $form->id) : route('institute.forms.store') }}" method="POST" class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-8" onsubmit="return handleSubmit()">
        @csrf
        @if(isset($form))
            @method('PUT')
        @endif
        
        <!-- Form Basic Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Form Title</label>
                <input type="text" name="application_name" value="{{ old('application_name', $form->application_name ?? '') }}" required placeholder="e.g., Admission Session 2025-26" class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                <textarea name="application_description" rows="3" placeholder="Brief description..." class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none transition-all">{{ old('application_description', $form->application_description ?? '') }}</textarea>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Academic Session</label>
                <select name="session" required class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    <option value="2025-2026" {{ (old('session', $form->session ?? '') == '2025-2026') ? 'selected' : '' }}>2025 - 2026</option>
                    <option value="2026-2027" {{ (old('session', $form->session ?? '') == '2026-2027') ? 'selected' : '' }}>2026 - 2027</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Institution <span class="text-red-500">*</span></label>
                <select name="institution_id" required class="w-full p-3 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    <option value="">Select Institution</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ (old('institution_id', $form->institution_id ?? '') == $inst->id) ? 'selected' : '' }}>
                            {{ $inst->institution_name }} ({{ $inst->institution_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Application Fee Config -->
            <div class="md:col-span-1 space-y-3">
                 <div class="flex items-center justify-between">
                    <label class="block text-sm font-bold text-slate-700">Application Fee</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_application_fee_required" value="1" id="app_fee_toggle" class="sr-only peer" {{ old('is_application_fee_required', $form->is_application_fee_required ?? true) ? 'checked' : '' }}>
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-2 text-xs font-medium text-slate-500">Required</span>
                    </label>
                 </div>
                 <div class="relative transition-all" id="app_fee_container">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                    <input type="number" name="application_fee" id="application_fee" value="{{ old('application_fee', $form->application_fee ?? '') }}" min="0" step="0.01" placeholder="250" class="w-full pl-8 p-3 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Admission Fee Config -->
            <div class="md:col-span-1 space-y-3">
                 <div class="flex items-center justify-between">
                    <label class="block text-sm font-bold text-slate-700">Admission Fee</label>
                     <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_admission_required" value="1" id="adm_fee_toggle" class="sr-only peer" {{ old('is_admission_required', $form->is_admission_required ?? true) ? 'checked' : '' }}>
                        <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-2 text-xs font-medium text-slate-500">Required</span>
                    </label>
                 </div>
                 <div class="relative transition-all" id="adm_fee_container">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                    <input type="number" name="admission_fee" id="admission_fee" value="{{ old('admission_fee', $form->admission_fee ?? '') }}" min="0" step="0.01" placeholder="5000" class="w-full pl-8 p-3 border border-slate-200 bg-slate-50 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                </div>
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

            <!-- "Applied For" (Course / Stream / Programme) Options Configuration -->
            <div class="md:col-span-2 bg-gradient-to-br from-indigo-50/70 via-blue-50/40 to-slate-50 border-2 border-indigo-200/80 rounded-2xl p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-indigo-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-500/20 flex-shrink-0">
                            <i data-lucide="layers" size="20"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-extrabold text-slate-800">"Applied For" Writable Options</h3>
                                <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Required Student Choice</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Write the courses, streams, or programs applicants can choose from. Applicants must select one before filling personal details.</p>
                        </div>
                    </div>
                    <div class="self-start sm:self-auto flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs flex items-center gap-1.5">
                            <span id="applied-options-count" class="text-indigo-600 font-black">0</span> options active
                        </span>
                    </div>
                </div>

                <!-- Writable Input -->
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Writable Options List <span class="text-slate-400 font-normal lowercase">(one per line or comma-separated)</span>
                            </label>
                            <span class="text-[11px] text-amber-600 font-medium flex items-center gap-1">
                                <i data-lucide="alert-circle" size="13"></i>
                                Once submitted, student cannot change their selection
                            </span>
                        </div>
                        @php
                            $existingOptionsStr = '';
                            if (isset($form)) {
                                $existingOpts = $form->getAppliedForOptions();
                                if (!empty($existingOpts)) {
                                    $existingOptionsStr = implode("\n", $existingOpts);
                                }
                            } else {
                                $existingOptionsStr = "Engineering (JEE)\nMedical (NEET)";
                            }
                        @endphp
                        <textarea 
                            name="applied_for_options" 
                            id="applied_for_options_textarea" 
                            rows="4" 
                            placeholder="Type options here, e.g.:&#10;Engineering (JEE)&#10;Medical (NEET)&#10;Arts / Humanities&#10;Commerce" 
                            class="w-full p-3.5 border border-slate-300 bg-white rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all text-sm font-medium leading-relaxed font-sans placeholder:text-slate-400"
                        >{{ old('applied_for_options', $existingOptionsStr) }}</textarea>
                    </div>

                    <!-- Preset Buttons -->
                    <div class="flex flex-wrap items-center gap-2 pt-0.5">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1">Presets:</span>
                        <button type="button" onclick="appendPreset('Engineering (JEE)\nMedical (NEET)')" class="px-2.5 py-1 text-xs font-medium bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 border border-slate-200 hover:border-indigo-300 rounded-lg transition-all shadow-2xs cursor-pointer">
                            + JEE & NEET
                        </button>
                        <button type="button" onclick="appendPreset('Class XI - Science\nClass XI - Arts\nClass XI - Commerce')" class="px-2.5 py-1 text-xs font-medium bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 border border-slate-200 hover:border-indigo-300 rounded-lg transition-all shadow-2xs cursor-pointer">
                            + Science / Arts / Commerce
                        </button>
                        <button type="button" onclick="appendPreset('Civil Services (UPSC)\nCivil Services (APSC)')" class="px-2.5 py-1 text-xs font-medium bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 border border-slate-200 hover:border-indigo-300 rounded-lg transition-all shadow-2xs cursor-pointer">
                            + UPSC / APSC
                        </button>
                        <button type="button" onclick="clearAppliedOptions()" class="px-2.5 py-1 text-xs font-medium text-slate-400 hover:text-red-600 ml-auto transition-colors cursor-pointer">
                            Clear
                        </button>
                    </div>

                    <!-- Live Student Preview Chips -->
                    <div class="bg-white/90 border border-indigo-100 rounded-xl p-3.5">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 flex items-center justify-between">
                            <span class="flex items-center gap-1.5"><i data-lucide="eye" size="13" class="text-indigo-500"></i> Live Applicant Preview</span>
                            <span class="text-[10px] text-indigo-600 font-semibold lowercase">updates instantly</span>
                        </div>
                        <div id="applied-options-preview-container" class="flex flex-wrap gap-2 min-h-[32px] items-center">
                            <!-- Injected by JS -->
                        </div>
                    </div>
                </div>
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
                // If editing, use saved config to enable/disable section. If creating, use defaults (disable bank/parents)
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
            fieldsContainer.style.opacity = '0.5';
            fieldsContainer.style.pointerEvents = 'none';
            card.classList.add('opacity-50', 'grayscale');
        }

        toggle.addEventListener('change', function() {
            const targetId = this.dataset.target;
            const fieldsContainer = document.getElementById(`fields-${targetId}`);
            const card = document.getElementById(`section-${targetId}`);
            
            if(this.checked) {
                fieldsContainer.style.opacity = '1';
                fieldsContainer.style.pointerEvents = 'auto';
                card.classList.remove('opacity-50', 'grayscale');
            } else {
                fieldsContainer.style.opacity = '0.5';
                fieldsContainer.style.pointerEvents = 'none';
                card.classList.add('opacity-50', 'grayscale');
            }
        });
    });

    function handleSubmit() {
        const btn = document.getElementById('submit-btn');
        const icon = btn.querySelector('i');
        const text = btn.querySelector('span');
        
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-wait');
        
        icon.setAttribute('data-lucide', 'loader-2');
        icon.classList.add('animate-spin');
        lucide.createIcons();
        
        text.innerText = 'Saving...';
        return true;
    }

    function setupFeeToggle(toggleId, inputId, containerId) {
        const toggle = document.getElementById(toggleId);
        const input = document.getElementById(inputId);
        const container = document.getElementById(containerId);

        function updateState() {
            if(toggle.checked) {
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
                input.required = true;
                if(input.value === '0') input.value = '';
            } else {
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';
                input.required = false;
            }
        }

        toggle.addEventListener('change', updateState);
        updateState();
    }

    setupFeeToggle('app_fee_toggle', 'application_fee', 'app_fee_container');
    setupFeeToggle('adm_fee_toggle', 'admission_fee', 'adm_fee_container');

    // Applied For Options Live Preview & Presets
    const appliedTextarea = document.getElementById('applied_for_options_textarea');
    const appliedPreview = document.getElementById('applied-options-preview-container');
    const appliedCountBadge = document.getElementById('applied-options-count');

    function updateAppliedForPreview() {
        if (!appliedTextarea || !appliedPreview) return;
        const raw = appliedTextarea.value || '';
        const lines = raw.split(/[\r\n,]+/).map(s => s.trim()).filter(s => s.length > 0);

        if (appliedCountBadge) {
            appliedCountBadge.innerText = lines.length;
        }

        if (lines.length === 0) {
            appliedPreview.innerHTML = `
                <div class="text-xs text-slate-400 italic flex items-center gap-1.5 py-1">
                    <i data-lucide="info" size="14"></i>
                    <span>No options entered yet. Type in the box above or use presets. (If left blank, form title will be used as default option).</span>
                </div>
            `;
        } else {
            appliedPreview.innerHTML = lines.map((opt, idx) => `
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs font-bold rounded-lg shadow-2xs group">
                    <span class="w-4 h-4 rounded-full bg-indigo-200 text-indigo-700 text-[10px] flex items-center justify-center font-bold">${idx + 1}</span>
                    <span>${escapeHtml(opt)}</span>
                    <button type="button" onclick="removeOptionByIndex(${idx})" class="ml-1 text-indigo-400 hover:text-red-500 transition-colors p-0.5 rounded cursor-pointer" title="Remove this option">
                        <i data-lucide="x" size="12"></i>
                    </button>
                </span>
            `).join('');
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function appendPreset(presetText) {
        if (!appliedTextarea) return;
        const current = (appliedTextarea.value || '').trim();
        if (current.length > 0) {
            appliedTextarea.value = current + '\n' + presetText;
        } else {
            appliedTextarea.value = presetText;
        }
        updateAppliedForPreview();
    }

    function clearAppliedOptions() {
        if (!appliedTextarea) return;
        appliedTextarea.value = '';
        updateAppliedForPreview();
    }

    function removeOptionByIndex(index) {
        if (!appliedTextarea) return;
        const raw = appliedTextarea.value || '';
        const lines = raw.split(/[\r\n,]+/).map(s => s.trim()).filter(s => s.length > 0);
        if (index >= 0 && index < lines.length) {
            lines.splice(index, 1);
            appliedTextarea.value = lines.join('\n');
            updateAppliedForPreview();
        }
    }

    if (appliedTextarea) {
        appliedTextarea.addEventListener('input', updateAppliedForPreview);
        updateAppliedForPreview();
    }
</script>
@endpush
@endsection
