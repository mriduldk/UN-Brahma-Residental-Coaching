@extends('layouts.student')

@section('content')
<div class="max-w-7xl mx-auto pb-12 px-4 md:px-6">
    <!-- Breadcrumbs & Header -->
    <div class="mb-8">
        <nav class="flex mb-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
            <a href="{{ route('student.applications.index') }}" class="hover:text-blue-600 transition-colors">Applications</a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="text-slate-600">Preview Application</span>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Review Your Application</h1>
                <p class="text-slate-500 mt-2">Please double-check all information before final submission.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('student.application.form', ['form_id' => $app->application_form_id]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-50 transition-all">
                    <i data-lucide="edit-3" class="mr-2" size="18"></i> Edit Application
                </a>
                <div class="px-4 py-2 bg-emerald-50 border border-emerald-100 text-emerald-700 font-bold text-sm rounded-xl flex items-center">
                    <i data-lucide="check-circle-2" class="mr-2 text-emerald-500" size="18"></i> 100% Complete
                </div>
            </div>
        </div>
    </div>

    <!-- Application Summary Header Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="bg-gradient-to-br from-slate-50 to-blue-50/30 px-8 py-10 relative">
            <!-- Decorative Background Elements (Subtle) -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/20 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-100/20 rounded-full -ml-24 -mb-24 blur-2xl"></div>
            
            <div class="relative flex flex-col md:flex-row items-center gap-8">
                @php
                    $docs = $app->application_documents;
                @endphp
                <div class="relative group">
                    <div class="w-32 h-32 rounded-2xl bg-white border-4 border-white shadow-xl overflow-hidden ring-1 ring-slate-200">
                        @if($docs && !empty($docs->photo))
                            <img src="{{ Storage::url($docs->photo) }}" alt="Photo" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                <i data-lucide="user" size="48"></i>
                                <span class="text-[10px] uppercase font-bold mt-1 tracking-tighter text-center">No Photo</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="text-center md:text-left flex-1">
                    <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-100/50 border border-blue-200 text-blue-700 text-[10px] font-black uppercase tracking-widest mb-4">
                        Draft Application
                    </div>
                    <h2 class="text-4xl font-black text-slate-900 tracking-tight mb-3">{{ $data['full_name'] ?? 'Incomplete Name' }}</h2>
                    
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-wrap justify-center md:justify-start items-center gap-x-6 gap-y-2 text-slate-500 font-bold text-sm">
                            <div class="flex items-center gap-2">
                                <i data-lucide="school" size="18" class="text-blue-500"></i>
                                <span>{{ $app->application_form->institution->institution_name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="briefcase" size="18" class="text-indigo-500"></i>
                                <span>{{ $app->application_form->application_name }}</span>
                            </div>
                        </div>

                        <div class="flex justify-center md:justify-start items-center gap-3">
                            <div class="flex items-center gap-2 font-mono bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                                <i data-lucide="hash" size="16" class="text-slate-400"></i>
                                <span class="text-xs font-black text-slate-700 tracking-wider">{{ $app->application_code }}</span>
                            </div>
                            @php
                                $calculatedFee = $app->application_form->is_application_fee_required ? $app->application_form->getApplicationFeeFor($app->student, $app) : 0;
                            @endphp
                            <div class="flex items-center gap-2 font-bold px-3 py-1.5 rounded-xl border {{ ($app->application_form->is_application_fee_required && $calculatedFee > 0) ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-slate-50 border-slate-200 text-slate-600' }} shadow-sm">
                                <i data-lucide="banknote" size="16"></i>
                                <span class="text-xs tracking-tight">
                                    @if($app->application_form->is_application_fee_required && $calculatedFee > 0)
                                        Fee: ₹{{ number_format($calculatedFee, 2) }}
                                    @else
                                        Fee: Not Required
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Personal Information -->
        @if($app->application_form->isSectionEnabled('personal'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i data-lucide="user" size="18"></i>
                </div>
                <h3 class="font-bold text-slate-800">Personal Information</h3>
            </div>
            <div class="p-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-y-6 gap-x-8">
                @php
                    $personalFields = [
                        'Email' => 'email',
                        'Gender' => 'gender',
                        'Date of Birth' => 'dob',
                        'Age' => 'age',
                        'Category' => 'category',
                        'Caste' => 'caste',
                        'Mother Tongue' => 'mother_tongue',
                        'Religion' => 'religion',
                        'Marital Status' => 'marital_status',
                        'Nationality' => 'nationality',
                        'Identity Mark' => 'identification_mark',
                        'Blood Group' => 'blood_group',
                        'Mobile No' => 'phone_number',
                        'Alt Mobile No' => 'alternate_mobile_number',
                        'Employment Exchange No' => 'employment_exchange_no',
                        'PR of Assam' => 'is_permanent_resident_of_assam',
                        'BTC Resident' => 'is_btc_resident',
                        'Ex-Serviceman' => 'is_ex_serviceman',
                        'NCC Certificate' => 'ncc_certificate',
                        'National Sports' => 'has_national_sports_achievement',
                        'Cultural/Fine Arts' => 'has_fine_arts_cultural_achievement'
                    ];
                @endphp
                @foreach($personalFields as $label => $key)
                    @if($app->application_form->isFieldVisible('personal', $key))
                    <div class="group">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">{{ $label }}</span>
                        <p class="text-slate-800 font-bold text-sm">
                            @if($key == 'email')
                                {{ $data[$key] ?? $app->student->email ?? '—' }}
                            @elseif(in_array($key, ['is_permanent_resident_of_assam', 'is_btc_resident', 'is_ex_serviceman', 'has_national_sports_achievement', 'has_fine_arts_cultural_achievement']))
                                {{ isset($data[$key]) ? ($data[$key] ? 'Yes' : 'No') : '—' }}
                            @else
                                {{ $data[$key] ?? '—' }}
                            @endif
                        </p>
                    </div>
                    @endif
                @endforeach
            </div>
        </section>
        @endif

        <!-- Parent/Family Details -->
        @if($app->application_form->isSectionEnabled('parents'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                    <i data-lucide="users" size="16"></i>
                </div>
                <h3 class="font-bold text-slate-800">Family Details</h3>
            </div>
            <div class="p-6">
                <!-- Father's Details -->
                @php
                    $fatherFields = [
                        'Father\'s Name' => 'father_name',
                        'Mobile' => 'father_mobile',
                        'Email' => 'father_email',
                        'Occupation' => 'father_occupation',
                        'Qualification' => 'father_qualification',
                        'Annual Income' => 'father_annual_income'
                    ];
                    $hasFather = false;
                    foreach($fatherFields as $label => $key) {
                        if($app->application_form->isFieldVisible('parents', $key)) { $hasFather = true; break; }
                    }
                @endphp
                @if($hasFather)
                <div class="mb-8">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-4 border-b pb-1">Father's Information</span>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($fatherFields as $label => $key)
                            @if($app->application_form->isFieldVisible('parents', $key))
                            <div class="group">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight block mb-1">{{ $label }}</span>
                                <p class="text-slate-800 font-bold text-sm">
                                    @if($key == 'father_annual_income' && !empty($data[$key]))
                                        ₹{{ number_format($data[$key]) }}
                                    @else
                                        {{ $data[$key] ?? '—' }}
                                    @endif
                                </p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Mother's Details -->
                @php
                    $motherFields = [
                        'Mother\'s Name' => 'mother_name',
                        'Mobile' => 'mother_mobile',
                        'Email' => 'mother_email',
                        'Occupation' => 'mother_occupation',
                        'Qualification' => 'mother_qualification',
                        'Annual Income' => 'mother_annual_income'
                    ];
                    $hasMother = false;
                    foreach($motherFields as $label => $key) {
                        if($app->application_form->isFieldVisible('parents', $key)) { $hasMother = true; break; }
                    }
                @endphp
                @if($hasMother)
                <div class="mb-8">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-4 border-b pb-1">Mother's Information</span>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($motherFields as $label => $key)
                            @if($app->application_form->isFieldVisible('parents', $key))
                            <div class="group">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight block mb-1">{{ $label }}</span>
                                <p class="text-slate-800 font-bold text-sm">
                                    @if($key == 'mother_annual_income' && !empty($data[$key]))
                                        ₹{{ number_format($data[$key]) }}
                                    @else
                                        {{ $data[$key] ?? '—' }}
                                    @endif
                                </p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Guardian Details -->
                @php
                    $guardianFields = [
                        'Guardian\'s Name' => 'guardian_name',
                        'Relation' => 'guardian_relation',
                        'Mobile' => 'guardian_mobile',
                        'Occupation' => 'guardian_occupation',
                        'Address' => 'guardian_address'
                    ];
                    $hasGuardian = !empty($data['guardian_name']);
                @endphp
                @if($hasGuardian)
                <div class="bg-blue-50/30 rounded-2xl p-6 border border-blue-100/50">
                    <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest block mb-4 border-b border-blue-100 pb-1">Guardian's Information</span>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($guardianFields as $label => $key)
                            @if($app->application_form->isFieldVisible('parents', $key))
                            <div class="group {{ $key == 'guardian_address' ? 'md:col-span-2' : '' }}">
                                <span class="text-[10px] font-bold text-blue-400 uppercase tracking-tight block mb-1">{{ $label }}</span>
                                <p class="text-slate-800 font-bold text-sm">
                                    {{ $data[$key] ?? '—' }}
                                </p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </section>
        @endif

        <!-- Address Information -->
        @if($app->application_form->isSectionEnabled('address'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="map-pin" size="18"></i>
                </div>
                <h3 class="font-bold text-slate-800">Address Details</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $addressTypes = [
                        'present' => 'Present Address',
                        'permanent' => 'Permanent Address'
                    ];
                    
                    $addressFields = [
                        'village_town' => 'Vill/Town',
                        'street' => 'Street',
                        'city' => 'City',
                        'district' => 'Dist',
                        'state' => 'State',
                        'country' => 'Country',
                        'pin_code' => 'PIN'
                    ];
                @endphp

                @foreach($addressTypes as $type => $label)
                <div class="bg-slate-50/50 rounded-xl p-6 border border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest block">{{ $label }}</span>
                        @if($type === 'permanent' && isset($data['same_as_permanent']))
                            <span class="bg-green-100 text-green-700 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase">Same as Present</span>
                        @endif
                    </div>
                    
                    <div class="text-sm text-slate-700 leading-relaxed font-semibold capitalize space-y-1">
                        @php
                            $vKey = $type.'_village_town'; $sKey = $type.'_street';
                            $cKey = $type.'_city';
                            $dKey = $type.'_district'; $pKey = $type.'_pin_code';
                            $stKey = $type.'_state'; $ctKey = $type.'_country';
                            
                            $hasData = false;
                        @endphp

                        @if($app->application_form->isFieldVisible('address', $vKey) || $app->application_form->isFieldVisible('address', $sKey))
                            @php 
                                $line1 = []; 
                                if(!empty($data[$vKey])) $line1[] = $data[$vKey];
                                if(!empty($data[$sKey])) $line1[] = "ST: " . $data[$sKey];
                            @endphp
                            @if(!empty($line1))
                                @php $hasData = true; @endphp
                                {{ implode(', ', $line1) }} <br>
                            @endif
                        @endif

                        @if($app->application_form->isFieldVisible('address', $cKey) && !empty($data[$cKey]))
                            @php $hasData = true; @endphp
                            City: {{ $data[$cKey] }} <br>
                        @endif

                        @php $l3 = []; @endphp
                        @if($app->application_form->isFieldVisible('address', $dKey) && !empty($data[$dKey])) @php $l3[] = "Dist: " . $data[$dKey]; @endphp @endif
                        @if($app->application_form->isFieldVisible('address', $pKey) && !empty($data[$pKey])) @php $l3[] = $data[$pKey]; @endphp @endif
                        @if(!empty($l3))
                            @php $hasData = true; @endphp
                            {{ implode(' - ', $l3) }} <br>
                        @endif

                        @php $l4 = []; @endphp
                        @if($app->application_form->isFieldVisible('address', $stKey) && !empty($data[$stKey])) @php $l4[] = $data[$stKey]; @endphp @endif
                        @if($app->application_form->isFieldVisible('address', $ctKey) && !empty($data[$ctKey])) @php $l4[] = $data[$ctKey]; @endphp @endif
                        @if(!empty($l4))
                            @php $hasData = true; @endphp
                            <span class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">{{ implode(', ', $l4) }}</span>
                        @endif
                        
                        @if(!$hasData)
                            <span class="text-slate-400 italic font-normal">No address details provided</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Bank Details -->
        @if($app->application_form->isSectionEnabled('bank'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="building-2" size="18"></i>
                </div>
                <h3 class="font-bold text-slate-800">Bank Account Details</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-emerald-50/50 rounded-xl p-4 border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-500 uppercase block mb-1 tracking-widest">Account Holder</span>
                        <p class="text-slate-800 font-bold text-sm">{{ $data['account_holder_name'] ?? '—' }}</p>
                    </div>
                    <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100 grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5 tracking-tight">Account No</span>
                            <p class="text-xs font-bold text-slate-700 font-mono">{{ $data['account_number'] ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5 tracking-tight">IFSC Code</span>
                            <p class="text-xs font-bold text-slate-700 font-mono uppercase">{{ $data['ifsc_code'] ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5 tracking-tight">Bank & Branch</span>
                        <p class="text-xs font-bold text-slate-700 capitalize">
                            {{ strtolower($data['bank_name'] ?? '—') }}
                        </p>
                        <p class="text-[10px] font-medium text-slate-400 capitalize">
                            {{ strtolower($data['branch'] ?? '—') }} branch
                        </p>
                    </div>
                </div>
            </div>
        </section>
        @endif
    </div>

    <!-- Full Width: Academic Qualifications -->
    @if($app->application_form->isSectionEnabled('academic'))
    <div class="mt-8">
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                        <i data-lucide="graduation-cap" size="18"></i>
                    </div>
                    <h3 class="font-bold text-slate-800">Academic Qualifications</h3>
                </div>
            </div>
            <div class="p-0">
                @php
                    $academicFieldsConfig = $app->application_form->field_config['academic']['fields'] ?? [];
                    if (empty($academicFieldsConfig)) {
                        $academicFieldsConfig = ['examination', 'board_university', 'passing_year', 'percentage_grade', 'division'];
                    }

                    $academicLabels = [
                        'examination' => 'Examination',
                        'passing_year' => 'Year',
                        'board_university' => 'Board / University',
                        'percentage_grade' => '%/Grade',
                        'division' => 'Division',
                        'roll_number' => 'Roll No',
                        'registration_number' => 'Reg. No',
                        'total_marks' => 'Total Marks',
                        'marks_obtained' => 'Marks Obtained',
                        'medium_of_study' => 'Medium',
                    ];
                @endphp
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50/50">
                            <tr>
                                @foreach($academicFieldsConfig as $field)
                                    <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[10px] {{ in_array($field, ['passing_year', 'percentage_grade', 'division']) ? 'text-center' : '' }}">
                                        {{ $academicLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($data['qual'] ?? [] as $qual)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                @foreach($academicFieldsConfig as $field)
                                    <td class="px-6 py-4 {{ in_array($field, ['passing_year', 'percentage_grade', 'division']) ? 'text-center' : '' }} {{ $field == 'examination' ? 'font-bold text-slate-700' : 'text-slate-600 font-medium' }}">
                                        @if($field == 'percentage_grade')
                                            <span class="font-extrabold text-blue-600">{{ $qual[$field] ?? '—' }}{{ is_numeric($qual[$field] ?? '') ? '%' : '' }}</span>
                                        @elseif($field == 'division')
                                            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase">{{ $qual[$field] ?? '—' }}</span>
                                        @else
                                            {{ $qual[$field] ?? '—' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ count($academicFieldsConfig) }}" class="px-6 py-8 text-center text-slate-400 italic font-medium">No qualifications provided</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    @endif

    <!-- Full Width: Uploaded Documents -->
    @if($app->application_form->isSectionEnabled('documents'))
    <div class="mt-8">
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                    <i data-lucide="files" size="18"></i>
                </div>
                <h3 class="font-bold text-slate-800">Uploaded Documents</h3>
            </div>
            <div class="p-6">
                @php
                    $docMapping = [
                        'photo' => 'photo',
                        'signature' => 'signature',
                        'caste_certificate' => 'caste_certificate',
                        'prc_certificate' => 'address_proof',
                        'address_proof' => 'address_proof',
                        'age_proof' => 'hslc_admit_card',
                        'hslc_admit_card' => 'hslc_admit_card',
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
                        'hs_admit_card' => 'hs_admit_card',
                        'graduation_admit_card' => 'graduation_admit_card',
                        'post_graduation_admit_card' => 'post_graduation_admit_card',
                        'transfer_certificate' => 'transfer_certificate',
                        'income_certificate' => 'income_certificate',
                        'aadhaar_card' => 'aadhaar_card',
                        'passport' => 'passport',
                        'bed_parangat_marksheet' => 'bed_parangat_marksheet',
                        'bed_parangat_certificate' => 'bed_parangat_certificate',
                        'tet_certificate' => 'tet_certificate',
                        'pwd_certificate' => 'pwd_certificate',
                        'ex_serviceman_certificate' => 'ex_serviceman_certificate',
                        'ncc_certificate_doc' => 'ncc_certificate_doc',
                        'sports_certificate' => 'sports_certificate',
                        'cultural_certificate' => 'cultural_certificate',
                        'btc_residential_certificate' => 'btc_residential_certificate',
                        'language_diploma_certificate' => 'language_diploma_certificate'
                    ];

                    $docLabels = [
                        'photo' => 'Photograph',
                        'signature' => 'Signature',
                        'caste_certificate' => 'Caste Certificate',
                        'prc_certificate' => 'PRC / Address Proof',
                        'address_proof' => 'Address Proof',
                        'age_proof' => 'Age Proof / Admit Card',
                        'hslc_admit_card' => 'HSLC Admit Card',
                        'mark_sheet_10' => '10th Marksheet',
                        'hslc_marksheet' => '10th Marksheet',
                        'certificate_10' => '10th Certificate',
                        'hslc_pass_certificate' => '10th Certificate',
                        'mark_sheet_12' => '12th Marksheet',
                        'hs_marksheet' => '12th Marksheet',
                        'certificate_12' => '12th Pass Certificate',
                        'hs_pass_certificate' => '12th Pass Certificate',
                        'graduation_marksheet' => 'Graduation Marksheet',
                        'graduation_certificate' => 'Graduation Certificate',
                        'graduation_pass_certificate' => 'Graduation Certificate',
                        'post_graduation_marksheet' => 'Post Graduation Marksheet',
                        'post_graduation_certificate' => 'Post Graduation Certificate',
                        'post_graduation_pass_certificate' => 'Post Graduation Certificate',
                        'hs_admit_card' => 'HS Admit Card',
                        'graduation_admit_card' => 'Graduation Admit Card',
                        'post_graduation_admit_card' => 'PG Admit Card',
                        'transfer_certificate' => 'Transfer Certificate',
                        'income_certificate' => 'Income Certificate',
                        'aadhaar_card' => 'Aadhaar Card',
                        'passport' => 'Passport',
                        'bed_parangat_marksheet' => 'B.Ed./Parangat Mark Sheets',
                        'bed_parangat_certificate' => 'B.Ed./Parangat Certificate',
                        'tet_certificate' => 'TET Certificate',
                        'pwd_certificate' => 'PwD Certificate',
                        'ex_serviceman_certificate' => 'Ex-Serviceman Certificate',
                        'ncc_certificate_doc' => 'NCC Certificate',
                        'sports_certificate' => 'Sports Certificate',
                        'cultural_certificate' => 'Fine Arts/Cultural Certificate',
                        'btc_residential_certificate' => 'BTC Residential Certificate',
                        'language_diploma_certificate' => 'Language Diploma Certificate'
                    ];

                    // Define unique keys for display to avoid duplicates if multiple aliases map to same DB key
                    $displayKeys = [
                        'photo', 'signature', 'caste_certificate', 'prc_certificate', 'address_proof', 
                        'age_proof', 'hslc_admit_card', 'mark_sheet_10', 'hslc_marksheet', 
                        'certificate_10', 'hslc_pass_certificate', 'mark_sheet_12', 'hs_marksheet', 
                        'certificate_12', 'hs_pass_certificate', 'graduation_marksheet', 
                        'graduation_certificate', 'graduation_pass_certificate', 
                        'post_graduation_marksheet', 'post_graduation_certificate', 
                        'post_graduation_pass_certificate', 'hs_admit_card', 'graduation_admit_card', 
                        'post_graduation_admit_card', 'transfer_certificate', 'income_certificate', 
                        'aadhaar_card', 'passport',
                        'bed_parangat_marksheet', 'bed_parangat_certificate', 'tet_certificate',
                        'pwd_certificate', 'ex_serviceman_certificate', 'ncc_certificate_doc',
                        'sports_certificate', 'cultural_certificate', 'btc_residential_certificate',
                        'language_diploma_certificate'
                    ];
                    
                    $docs = $app->application_documents;
                @endphp
                
                @if($docs)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($displayKeys as $key)
                            @php 
                                $dbKey = $docMapping[$key] ?? $key; 
                                $label = $docLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
                            @endphp
                            @if($app->application_form->isFieldVisible('documents', $key) && !empty($docs->$dbKey))
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors border border-slate-100 group">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-50 shrink-0">
                                        <i data-lucide="file-check" size="16"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 truncate">{{ $label }}</span>
                                </div>
                                <a href="{{ Storage::url($docs->$dbKey) }}" target="_blank" class="p-2 bg-white rounded-lg text-slate-400 hover:text-blue-600 hover:shadow-md border border-slate-200 transition-all shrink-0">
                                    <i data-lucide="eye" size="14"></i>
                                </a>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-slate-400 text-sm font-medium italic">
                        No documents uploaded yet.
                    </div>
                @endif
            </div>
        </section>
    </div>
    @endif

    <!-- Final Submission Footer -->
    <div class="mt-12 bg-slate-50 rounded-3xl p-8 border border-slate-200 shadow-inner">
        <div class="max-w-3xl mx-auto flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
            <div class="w-16 h-16 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 shrink-0 transform -rotate-3 hover:rotate-0 transition-transform">
                <i data-lucide="spell-check" size="32"></i>
            </div>
            <div class="flex-grow">
                <h3 class="text-xl font-bold text-slate-900">Ready to Submit?</h3>
                <p class="text-slate-500 text-sm mt-1">Please ensure all details are correct. No changes can be made after submission.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                <a href="{{ route('student.application.form', ['form_id' => $app->application_form_id]) }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm">
                    Re-Edit Application
                </a>
                <form action="{{ route('student.application.store') }}" method="POST" id="finalSubmitForm">
                    @csrf
                    <input type="hidden" name="application_id" value="{{ $app->id }}">
                    <button type="button" onclick="confirmSubmit()" class="w-full px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                        Submit & Proceed <i data-lucide="arrow-right" size="18"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmSubmit() {
        // Use custom styled AppSwal from form.blade.php if available via global or local definition
        const PreviewSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-slate-100',
                title: 'text-xl font-bold text-slate-800',
                htmlContainer: 'text-slate-600 font-medium',
                confirmButton: 'bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 focus:ring-4 focus:ring-blue-100 transition-all shadow-lg shadow-blue-500/30',
                cancelButton: 'bg-slate-100 text-slate-600 px-6 py-3 rounded-xl font-bold hover:bg-slate-200 focus:ring-4 focus:ring-slate-100 transition-all'
            },
            buttonsStyling: false,
            backdrop: `rgba(15, 23, 42, 0.4) backdrop-blur-sm`
        });

        @php
            $isFeeRequired = ($app->application_form->is_application_fee_required && $app->application_form->application_fee > 0);
            $swalMessage = $isFeeRequired 
                ? "This is a one-time submission. Once confirmed, you will be redirected to the payment gateway to complete your application."
                : "This is a one-time submission. Once confirmed, your application will be submitted and you can track its status in your dashboard.";
        @endphp

        PreviewSwal.fire({
            title: 'Final Confirmation',
            text: "{{ $swalMessage }}",
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes, Submit Application',
            cancelButtonText: 'Wait, let me review',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading on button
                const btn = document.querySelector('button[onclick="confirmSubmit()"]');
                btn.innerHTML = '<i data-lucide="loader-2" class="mr-2 animate-spin" size="18"></i> Processing...';
                btn.style.pointerEvents = 'none';
                btn.style.opacity = '0.7';
                lucide.createIcons();
                
                document.getElementById('finalSubmitForm').submit();
            }
        });
    }
</script>
@endpush

