@extends('layouts.student')

@section('content')
<div class="max-w-7xl mx-auto pb-12 px-4 md:px-6">
    <!-- Breadcrumbs & Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <nav class="flex mb-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                <a href="{{ route('student.applications.history') }}" class="hover:text-blue-600 transition-colors">Applications</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-600">Application Details</span>
            </nav>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Application Details</h2>
            <p class="text-slate-500 mt-2 text-sm font-medium">Record for application code: <span class="text-blue-600 font-bold font-mono">{{ $application->application_code }}</span></p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('student.application.download', $application->id) }}" id="downloadPdfBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-500/20">
                <i data-lucide="download" class="mr-2" size="18"></i> Download Acknowledgement
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-xs">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-xs">
            <i data-lucide="info" class="w-5 h-5 text-blue-600 flex-shrink-0"></i>
            <span class="font-semibold text-sm">{{ session('info') }}</span>
        </div>
    @endif

    <!-- Application Summary Header Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="bg-gradient-to-br from-slate-50 to-blue-50/30 px-8 py-10 relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100/20 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-100/20 rounded-full -ml-24 -mb-24 blur-2xl"></div>
            
            <div class="relative flex flex-col md:flex-row items-center gap-8">
                @php
                    $docs = $application->application_documents;
                    // The 'data' contains snapshot values for application_students
                    $studentSnapshot = $application->application_student;
                @endphp
                <div class="relative group">
                    <div class="w-28 h-28 rounded-2xl bg-white border-4 border-white shadow-xl overflow-hidden ring-1 ring-slate-200">
                        @if($docs && !empty($docs->photo))
                            <img src="{{ Storage::url($docs->photo) }}" alt="Photo" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                                <i data-lucide="user" size="48"></i>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="text-center md:text-left flex-1">
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-3 mb-3">
                        @if($application->application_form->is_application_fee_required)
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center shadow-sm border
                            {{ $application->payment_status == 'paid' ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-rose-50 border-rose-100 text-rose-700' }}">
                            <i data-lucide="{{ $application->payment_status == 'paid' ? 'check-circle-2' : 'alert-circle' }}" size="14" class="mr-1.5 align-middle"></i> 
                            Payment: {{ strtoupper($application->payment_status) }}
                        </span>
                        @endif

                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center shadow-sm border
                            {{ $application->status == 'approved' ? 'bg-indigo-50 border-indigo-100 text-indigo-700' : 
                               ($application->status == 'rejected' ? 'bg-slate-100 border-slate-200 text-slate-600' : 'bg-blue-50 border-blue-100 text-blue-700') }}">
                            <i data-lucide="info" size="14" class="mr-1.5"></i> Status: {{ strtoupper($application->status) }}
                        </span>
                    </div>

                    <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-3">{{ $studentSnapshot->full_name ?? 'Incomplete Name' }}</h2>
                    
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-wrap justify-center md:justify-start items-center gap-x-6 gap-y-2 text-slate-500 font-bold text-sm">
                            <div class="flex items-center gap-2">
                                <i data-lucide="school" size="18" class="text-blue-500"></i>
                                <span>{{ $application->application_form->institution->institution_name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="briefcase" size="18" class="text-indigo-500"></i>
                                <span>{{ $application->application_form->application_name }}</span>
                            </div>
                            @php
                                $displayAppliedFor = $application->applied_for ?? $application->application_student?->applied_for ?? null;
                                $displayExamCenter = $application->examination_center ?? $application->application_student?->examination_center ?? null;
                            @endphp
                            @if($displayAppliedFor)
                            <div class="flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 text-indigo-900 rounded-xl text-xs font-black shadow-2xs">
                                <i data-lucide="compass" size="15" class="text-indigo-600"></i>
                                <span>Applied For: <span class="text-indigo-700 underline decoration-indigo-300 font-extrabold">{{ $displayAppliedFor }}</span></span>
                            </div>
                            @endif
                            @if($displayExamCenter)
                            <div class="flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 text-emerald-900 rounded-xl text-xs font-black shadow-2xs">
                                <i data-lucide="map-pin" size="15" class="text-emerald-600"></i>
                                <span>Exam Center: <span class="text-emerald-700 underline decoration-emerald-300 font-extrabold">{{ $displayExamCenter }}</span></span>
                            </div>
                            @endif
                        </div>

                        <div class="flex justify-center md:justify-start items-center gap-3">
                            <div class="flex items-center gap-2 font-mono bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-sm">
                                <i data-lucide="hash" size="16" class="text-slate-400"></i>
                                <span class="text-xs font-black text-slate-700 tracking-wider">{{ $application->application_code }}</span>
                            </div>
                            @php
                                $calculatedFee = $application->application_form->is_application_fee_required ? $application->application_form->getApplicationFeeFor($application->student, $application) : 0;
                            @endphp
                            @if($application->application_form->is_application_fee_required && $calculatedFee > 0)
                            <div class="flex items-center gap-2 font-bold px-3 py-1.5 rounded-xl border bg-blue-50 border-blue-200 text-blue-700 shadow-sm">
                                <i data-lucide="banknote" size="16"></i>
                                <span class="text-xs tracking-tight">
                                    Fee: ₹{{ number_format($calculatedFee, 2) }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Sections -->
    <div class="space-y-8">
        <!-- Payment Details Section -->
        @if($application->payment_transactions->count() > 0)
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="credit-card" size="18"></i>
                </div>
                <h3 class="font-bold text-slate-800">Transaction History</h3>
            </div>
            <div class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[10px]">Date</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[10px]">Transaction ID</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[10px]">Amount</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[10px]">Method</th>
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[10px]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($application->payment_transactions as $txn)
                            <tr class="hover:bg-slate-50/50 transition-colors font-medium">
                                <td class="px-6 py-4 text-slate-600">{{ $txn->created_at->format('d M Y, h:i A') }}</td>
                                <td class="px-6 py-4 font-mono text-slate-500 text-xs">{{ $txn->razorpay_payment_id }}</td>
                                <td class="px-6 py-4 font-extrabold text-blue-600">₹{{ number_format($txn->amount, 2) }}</td>
                                <td class="px-6 py-4 uppercase text-[10px] tracking-tight">{{ $txn->method ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase {{ $txn->status == 'captured' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ ucfirst($txn->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @endif

    <div class="space-y-8">
        <!-- Personal Information -->
        @if($application->application_form->isSectionEnabled('personal'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i data-lucide="user" size="18"></i>
                </div>
                <h3 class="font-bold text-slate-800">Personal Information</h3>
            </div>
            <div class="p-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-y-6 gap-x-8">
                @php
                    $displayAppliedFor = $application->applied_for ?? $application->application_student?->applied_for ?? null;
                    $displayExamCenter = $application->examination_center ?? $application->application_student?->examination_center ?? null;
                @endphp
                @if($displayAppliedFor)
                <div class="col-span-2 sm:col-span-3 lg:col-span-4 p-3.5 bg-gradient-to-r from-indigo-50/80 to-blue-50/50 rounded-xl border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-xs flex-shrink-0">
                            <i data-lucide="compass" size="18"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest block">Selected Course / Stream (Applied For)</span>
                            <span class="text-base font-extrabold text-slate-900">{{ $displayAppliedFor }}</span>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-slate-500 bg-slate-100 border border-slate-200 px-3 py-1 rounded-full flex items-center gap-1.5 self-start sm:self-auto">
                        <i data-lucide="lock" size="12" class="text-slate-400"></i> Fixed Upon Submission
                    </span>
                </div>
                @endif

                @if($displayExamCenter)
                <div class="col-span-2 sm:col-span-3 lg:col-span-4 p-3.5 bg-gradient-to-r from-emerald-50/80 to-teal-50/50 rounded-xl border border-emerald-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-xs flex-shrink-0">
                            <i data-lucide="map-pin" size="18"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest block">Selected Examination Center</span>
                            <span class="text-base font-extrabold text-slate-900">{{ $displayExamCenter }}</span>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full flex items-center gap-1.5 self-start sm:self-auto">
                        <i data-lucide="check-circle" size="12" class="text-emerald-600"></i> Center Confirmed
                    </span>
                </div>
                @endif

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
                        'Community' => 'community',
                        'Identity Mark' => 'identification_mark',
                        'Blood Group' => 'blood_group',
                        'Mobile No' => 'phone_number',
                        'What’s App Number' => 'alternate_mobile_number',
                        'PR of Assam' => 'is_permanent_resident_of_assam',
                        'BTC Resident' => 'is_btc_resident'
                    ];
                @endphp
                @foreach($personalFields as $label => $key)
                    @if($application->application_form->isFieldVisible('personal', $key))
                    <div class="group">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">{{ $label }}</span>
                        <p class="text-slate-800 font-bold text-sm">
                            @if($key == 'email')
                                {{ $studentSnapshot->$key ?? $application->student->email ?? '—' }}
                            @elseif($key == 'community')
                                {{ $studentSnapshot->community ?? $studentSnapshot->nationality ?? '—' }}
                            @elseif(in_array($key, ['is_permanent_resident_of_assam', 'is_btc_resident']))
                                {{ !is_null($studentSnapshot->$key) ? ($studentSnapshot->$key ? 'Yes' : 'No') : '—' }}
                            @else
                                {{ $studentSnapshot->$key ?? '—' }}
                            @endif
                        </p>
                    </div>
                    @endif
                @endforeach
            </div>
        </section>
        @endif

        <!-- Parent/Family Details -->
        @if($application->application_form->isSectionEnabled('parents'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                    <i data-lucide="users" size="16"></i>
                </div>
                <h3 class="font-bold text-slate-800">Family Details</h3>
            </div>
            <div class="p-6">
                @php
                    $parentSnapshot = $application->application_parent;
                    $fatherFields = [
                        'Father\'s Name' => 'father_name',
                        'Mobile' => 'father_mobile',
                        'Email' => 'father_email',
                        'Occupation' => 'father_occupation',
                        'Qualification' => 'father_qualification',
                        'Family income per (per anum):' => 'father_annual_income'
                    ];
                    $hasFather = false;
                    foreach($fatherFields as $label => $key) {
                        if($application->application_form->isFieldVisible('parents', $key)) { $hasFather = true; break; }
                    }
                @endphp
                @if($hasFather)
                <div class="mb-8">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-4 border-b pb-1">Father's Information</span>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($fatherFields as $label => $key)
                            @if($application->application_form->isFieldVisible('parents', $key))
                            <div class="group">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight block mb-1">{{ $label }}</span>
                                <p class="text-slate-800 font-bold text-sm">
                                    @if($key == 'father_annual_income' && !empty($parentSnapshot->$key))
                                        ₹{{ number_format((float)$parentSnapshot->$key) }}
                                    @else
                                        {{ $parentSnapshot->$key ?? '—' }}
                                    @endif
                                </p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

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
                        if($application->application_form->isFieldVisible('parents', $key)) { $hasMother = true; break; }
                    }
                @endphp
                @if($hasMother)
                <div class="mb-8">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-4 border-b pb-1">Mother's Information</span>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($motherFields as $label => $key)
                            @if($application->application_form->isFieldVisible('parents', $key))
                            <div class="group">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight block mb-1">{{ $label }}</span>
                                <p class="text-slate-800 font-bold text-sm">
                                    @if($key == 'mother_annual_income' && !empty($parentSnapshot->$key))
                                        ₹{{ number_format((float)$parentSnapshot->$key) }}
                                    @else
                                        {{ $parentSnapshot->$key ?? '—' }}
                                    @endif
                                </p>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @php
                    $guardianFields = [
                        'Guardian\'s Name' => 'guardian_name',
                        'Relation' => 'guardian_relation',
                        'Mobile' => 'guardian_mobile',
                        'Occupation' => 'guardian_occupation',
                        'Address' => 'guardian_address'
                    ];
                    $hasGuardian = !empty($parentSnapshot->guardian_name);
                @endphp
                @if($hasGuardian)
                <div class="bg-blue-50/30 rounded-2xl p-6 border border-blue-100/50">
                    <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest block mb-4 border-b border-blue-100 pb-1">Guardian's Information</span>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        @foreach($guardianFields as $label => $key)
                            @if($application->application_form->isFieldVisible('parents', $key))
                            <div class="group {{ $key == 'guardian_address' ? 'md:col-span-2' : '' }}">
                                <span class="text-[10px] font-bold text-blue-400 uppercase tracking-tight block mb-1">{{ $label }}</span>
                                <p class="text-slate-800 font-bold text-sm">
                                    {{ $parentSnapshot->$key ?? '—' }}
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
        @if($application->application_form->isSectionEnabled('address'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="map-pin" size="18"></i>
                </div>
                <h3 class="font-bold text-slate-800">Address Details</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $addressSnapshot = $application->application_address;
                    $addressTypes = ['permanent' => 'Permanent Address', 'present' => 'Current Address / Communication Address'];
                @endphp

                @foreach($addressTypes as $type => $label)
                <div class="bg-slate-50/50 rounded-xl p-6 border border-slate-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest block">{{ $label }}</span>
                        @if($type === 'present' && isset($addressSnapshot->same_as_permanent) && $addressSnapshot->same_as_permanent)
                            <span class="bg-green-100 text-green-700 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase">Same as Permanent</span>
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

                        @if($application->application_form->isFieldVisible('address', $vKey) || $application->application_form->isFieldVisible('address', $sKey))
                            @php 
                                $line1 = []; 
                                if(!empty($addressSnapshot->$vKey)) $line1[] = $addressSnapshot->$vKey;
                                if(!empty($addressSnapshot->$sKey)) $line1[] = ($type === 'permanent' ? 'BTCLA: ' : 'ST: ') . $addressSnapshot->$sKey;
                            @endphp
                            @if(!empty($line1))
                                @php $hasData = true; @endphp
                                {{ implode(', ', $line1) }} <br>
                            @endif
                        @endif

                        @if($application->application_form->isFieldVisible('address', $cKey) && !empty($addressSnapshot->$cKey))
                            @php $hasData = true; @endphp
                            P.O.: {{ $addressSnapshot->$cKey }} <br>
                        @endif

                        @php $l3 = []; @endphp
                        @if($application->application_form->isFieldVisible('address', $dKey) && !empty($addressSnapshot->$dKey)) @php $l3[] = "Dist: " . $addressSnapshot->$dKey; @endphp @endif
                        @if($application->application_form->isFieldVisible('address', $pKey) && !empty($addressSnapshot->$pKey)) @php $l3[] = $addressSnapshot->$pKey; @endphp @endif
                        @if(!empty($l3))
                            @php $hasData = true; @endphp
                            {{ implode(' - ', $l3) }} <br>
                        @endif

                        @php $l4 = []; @endphp
                        @if($application->application_form->isFieldVisible('address', $ctKey) && !empty($addressSnapshot->$ctKey)) @php $l4[] = "P.S.: " . $addressSnapshot->$ctKey; @endphp @endif
                        @if($application->application_form->isFieldVisible('address', $stKey) && !empty($addressSnapshot->$stKey)) @php $l4[] = $addressSnapshot->$stKey; @endphp @endif
                        @if(!empty($l4))
                            @php $hasData = true; @endphp
                            <span class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">{{ implode(', ', $l4) }}</span>
                        @endif
                        
                        @if(!$hasData)
                            <span class="text-slate-400 italic font-normal">No address provided</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Bank Details -->
        @if($application->application_form->isSectionEnabled('bank'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="building-2" size="18"></i>
                </div>
                <h3 class="font-bold text-slate-800">Bank Account Details</h3>
            </div>
            <div class="p-6">
                @php $bankSnapshot = $application->application_bank_detail; @endphp
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-emerald-50/50 rounded-xl p-4 border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-500 uppercase block mb-1 tracking-widest">Account Holder</span>
                        <p class="text-slate-800 font-bold text-sm">{{ $bankSnapshot->account_holder_name ?? '—' }}</p>
                    </div>
                    <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100 grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5 tracking-tight">Account No</span>
                            <p class="text-xs font-bold text-slate-700 font-mono">{{ $bankSnapshot->account_number ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5 tracking-tight">IFSC Code</span>
                            <p class="text-xs font-bold text-slate-700 font-mono uppercase">{{ $bankSnapshot->ifsc_code ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block mb-0.5 tracking-tight">Bank & Branch</span>
                        <p class="text-xs font-bold text-slate-700 capitalize">{{ strtolower($bankSnapshot->bank_name ?? '—') }}</p>
                        <p class="text-[10px] font-medium text-slate-400 capitalize">{{ strtolower($bankSnapshot->branch ?? '—') }} branch</p>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Academic Qualifications -->
        @if($application->application_form->isSectionEnabled('academic'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                        <i data-lucide="graduation-cap" size="18"></i>
                    </div>
                    <h3 class="font-bold text-slate-800">Academic Qualifications</h3>
                </div>
            </div>
            <div class="p-0 overflow-x-auto">
                @php
                    $fieldsConfig = $application->application_form->field_config['academic']['fields'] ?? ['examination', 'board_university', 'passing_year', 'percentage_grade', 'division'];
                    $labels = ['examination'=>'Exam','passing_year'=>'Year','board_university'=>'School/College/Institute Name','percentage_grade'=>'%','division'=>'Div','roll_number'=>'Roll','registration_number'=>'Reg','total_marks'=>'Total','marks_obtained'=>'Obt','medium_of_study'=>'Medium'];
                @endphp
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/50">
                        <tr>
                            @foreach($fieldsConfig as $f)
                                <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-[10px] {{ in_array($f, ['passing_year', 'percentage_grade', 'division']) ? 'text-center' : '' }}">
                                    {{ $labels[$f] ?? ucfirst(str_replace('_', ' ', $f)) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($application->application_academic_qualifications as $q)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            @foreach($fieldsConfig as $f)
                                <td class="px-6 py-4 {{ in_array($f, ['passing_year', 'percentage_grade', 'division']) ? 'text-center' : '' }} {{ $f == 'examination' ? 'font-bold text-slate-700' : 'text-slate-600 font-medium' }}">
                                    @if($f == 'percentage_grade')
                                        <span class="font-extrabold text-blue-600">{{ $q->$f ?? '—' }}{{ is_numeric($q->$f ?? '') ? '%' : '' }}</span>
                                    @elseif($f == 'division')
                                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase">{{ $q->$f ?? '—' }}</span>
                                    @else
                                        {{ $q->$f ?? '—' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr><td colspan="{{ count($fieldsConfig) }}" class="px-6 py-8 text-center text-slate-400 italic">No qualifications provided</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        @endif

        <!-- Uploaded Documents -->
        @if($application->application_form->isSectionEnabled('documents'))
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                    <i data-lucide="files" size="18"></i>
                </div>
                <h3 class="font-bold text-slate-800">Uploaded Documents</h3>
            </div>
            <div class="p-6">
                @php
                    $docMap = ['photo' => 'photo','signature' => 'signature','caste_certificate' => 'caste_certificate','prc_certificate' => 'address_proof','address_proof' => 'address_proof','age_proof' => 'hslc_admit_card','hslc_admit_card' => 'hslc_admit_card','mark_sheet_10' => 'hslc_marksheet','hslc_marksheet' => 'hslc_marksheet','certificate_10' => 'hslc_pass_certificate','hslc_pass_certificate' => 'hslc_pass_certificate','mark_sheet_12' => 'hs_marksheet','hs_marksheet' => 'hs_marksheet','certificate_12' => 'hs_pass_certificate','hs_pass_certificate' => 'hs_pass_certificate','graduation_marksheet' => 'graduation_marksheet','graduation_certificate' => 'graduation_pass_certificate','graduation_pass_certificate' => 'graduation_pass_certificate','post_graduation_marksheet' => 'post_graduation_marksheet','post_graduation_certificate' => 'post_graduation_pass_certificate','post_graduation_pass_certificate' => 'post_graduation_pass_certificate','hs_admit_card' => 'hs_admit_card','graduation_admit_card' => 'graduation_admit_card','post_graduation_admit_card' => 'post_graduation_admit_card','income_certificate' => 'income_certificate','aadhaar_card' => 'aadhaar_card','btc_residential_certificate' => 'btc_residential_certificate'];
                    $docLbls = ['photo' => 'Photograph','signature' => 'Signature','caste_certificate' => 'Caste Certificate','prc_certificate' => 'PRC / Address','address_proof' => 'Address Proof','age_proof' => 'Age Proof','hslc_admit_card' => 'HSLC Admit','mark_sheet_10' => '10th Marksheet','hslc_marksheet' => '10th Marksheet','certificate_10' => '10th Cert','hslc_pass_certificate' => '10th Cert','mark_sheet_12' => '12th Marksheet','hs_marksheet' => '12th Marksheet','certificate_12' => '12th Cert','hs_pass_certificate' => '12th Cert','graduation_marksheet' => 'Graduation Mark','graduation_certificate' => 'Graduation Cert','graduation_pass_certificate' => 'Graduation Cert','post_graduation_marksheet' => 'PG Mark','post_graduation_certificate' => 'PG Cert','post_graduation_pass_certificate' => 'PG Cert','hs_admit_card' => 'HS Admit','graduation_admit_card' => 'Grad Admit','post_graduation_admit_card' => 'PG Admit','income_certificate' => 'Income Cert','aadhaar_card' => 'Aadhaar','btc_residential_certificate' => 'Permanent Resident Certificate (PRC)'];
                    $displayKeys = array_keys($docLbls);
                @endphp
                @if($docs)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($displayKeys as $key)
                            @php $dbK = $docMap[$key] ?? $key; @endphp
                            @if($application->application_form->isFieldVisible('documents', $key) && !empty($docs->$dbK))
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors border border-slate-100 group">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-50 shrink-0">
                                        <i data-lucide="file-check" size="16"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 truncate">{{ $docLbls[$key] }}</span>
                                </div>
                                <a href="{{ Storage::url($docs->$dbK) }}" target="_blank" class="p-2 bg-white rounded-lg text-slate-400 hover:text-blue-600 hover:shadow-md border border-slate-200 transition-all shrink-0">
                                    <i data-lucide="eye" size="14"></i>
                                </a>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @else <div class="py-12 text-center text-slate-400 text-sm font-medium italic">No documents uploaded</div> @endif
            </div>
        </section>
        @endif
    </div>

    <!-- Actions -->
    <div class="mt-12 flex justify-between items-center">
        <a href="{{ route('student.applications.history') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-all text-sm flex items-center gap-2">
            <i data-lucide="arrow-left" size="18"></i> Back to My Applications
        </a>
        <div class="flex gap-4">
             
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmSubmit() {
        Swal.fire({
            title: 'Final Submission',
            text: "This action cannot be reverted! Please review your application carefully.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563EB', // Blue-600
            cancelButtonColor: '#64748B', // Slate-500
            confirmButtonText: 'Yes, Submit & Pay',
            cancelButtonText: 'Edit Application',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('finalSubmitForm').submit();
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
               window.location.href = "{{ route('student.application.form') }}";
            }
        })
    }

    document.addEventListener('DOMContentLoaded', () => {
        const downloadBtn = document.getElementById('downloadPdfBtn');
        if(downloadBtn) {
            downloadBtn.addEventListener('click', function(e) {
                if(this.classList.contains('pointer-events-none')) {
                    e.preventDefault();
                    return;
                }
                
                const originalHtml = this.innerHTML;
                this.classList.add('pointer-events-none', 'opacity-75', 'cursor-not-allowed');
                this.innerHTML = `<i data-lucide="loader-2" class="mr-2 animate-spin" size="18"></i> Downloading...`;
                
                // Re-initialize icons since we replaced HTML
                if(window.lucide) {
                    lucide.createIcons();
                }

                // Reset after 3 seconds since page won't reload on download
                setTimeout(() => {
                    this.classList.remove('pointer-events-none', 'opacity-75', 'cursor-not-allowed');
                    this.innerHTML = originalHtml;
                    if(window.lucide) {
                        lucide.createIcons();
                    }
                }, 3000);
            });
        }
    });
</script>
@endpush
