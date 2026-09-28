@extends('layouts.student')

@section('content')
<div class="w-full space-y-6">
    <!-- Header Card -->
    <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 flex flex-col md:flex-row items-center md:space-x-6 space-y-4 md:space-y-0">
        @if($student->document && $student->document->photo)
            <img src="{{ Storage::url($student->document->photo) }}" alt="Profile Photo" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-sm shrink-0">
        @else
            <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 border-4 border-white shadow-sm shrink-0 uppercase text-3xl font-bold">
                {{ substr($student->full_name ?? 'ST', 0, 2) }}
            </div>
        @endif
        <div class="text-center md:text-left flex-1">
            <h2 class="text-2xl font-bold text-slate-800 uppercase">{{ $student->full_name }}</h2>
            <p class="text-slate-500 font-medium">Student ID: <span class="text-blue-600 font-bold">STD-{{ str_pad($student->id, 4, '0', STR_PAD_LEFT) }}</span></p>
            <div class="flex flex-wrap justify-center md:justify-start gap-3 mt-2">
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full border border-green-200">Profile Active</span>
                @if($student->phone_number)
                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full border border-slate-200"><i data-lucide="smartphone" class="w-4 h-4 inline mr-1"></i> {{ $student->phone_number }}</span>
                @endif
            </div>
        </div>
        <div class="flex space-x-3">
             <a href="{{ route('student.profile.edit') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium flex items-center shadow-sm">
                <i data-lucide="edit-2" size="16" class="mr-2"></i> Edit Profile
            </a>
            <button onclick="window.print()" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-medium flex items-center shadow-sm bg-white">
                <i data-lucide="printer" size="16" class="mr-2"></i> Print
            </button>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Personal Info -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 h-full">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center">
                <i data-lucide="user" class="mr-2 text-blue-600" size="20"></i> Personal Information
            </h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Full Name</span><span class="text-slate-800 font-medium text-lg uppercase">{{ $student->full_name }}</span></div>
                    <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Date of Birth</span><span class="text-slate-700 font-medium">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d-M-Y') : 'N/A' }}</span></div>
                    <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Gender</span><span class="text-slate-700 font-medium">{{ $student->gender ?? 'N/A' }}</span></div>
                    
                    <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Category</span><span class="text-slate-700 font-medium">{{ $student->category ?? 'N/A' }}</span></div>
                    <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Religion</span><span class="text-slate-700 font-medium">{{ $student->religion ?? 'N/A' }}</span></div>
                    
                    <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Blood Group</span><span class="text-slate-700 font-medium">{{ $student->blood_group ?? 'N/A' }}</span></div>
                    <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Community</span><span class="text-slate-700 font-medium">{{ $student->community ?? $student->nationality ?? 'N/A' }}</span></div>
                    <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Mother Tongue</span><span class="text-slate-700 font-medium">{{ $student->mother_tongue ?? 'N/A' }}</span></div>
                    
                    <div class="md:col-span-3"><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Identification Mark</span><span class="text-slate-700 font-medium">{{ $student->identification_mark ?? 'N/A' }}</span></div>
                </div>
                <div class="pt-4 border-t border-slate-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Mobile Number</span><span class="text-slate-700 font-medium">{{ $student->phone_number }}</span></div>
                        <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">What’s App Number</span><span class="text-slate-700 font-medium">{{ $student->alternate_mobile_number ?? 'N/A' }}</span></div>
                        <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Email Address</span><span class="text-slate-700 font-medium">{{ $student->email }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent Info -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 h-full">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center">
                <i data-lucide="users" class="mr-2 text-blue-600" size="20"></i> Parent / Guardian Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Father -->
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 h-full">
                    <h4 class="text-xs font-bold text-slate-500 uppercase mb-3 border-b border-slate-200 pb-2">Father's Details</h4>
                    <div class="space-y-3">
                        <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Name</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->father_name ?? 'N/A' }}</span></div>
                         <div class="grid grid-cols-2 gap-4">
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Mobile</span><span class="text-slate-700 font-medium">{{ $student->parent?->father_mobile ?? 'N/A' }}</span></div>
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Email</span><span class="text-slate-700 font-medium">{{ $student->parent?->father_email ?? 'N/A' }}</span></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Occupation</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->father_occupation ?? 'N/A' }}</span></div>
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Family Income (p.a.)</span><span class="text-slate-700 font-medium">₹ {{ $student->parent?->father_annual_income ?? 'N/A' }}</span></div>
                        </div>
                        <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Qualification</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->father_qualification ?? 'N/A' }}</span></div>
                    </div>
                </div>

                <!-- Mother -->
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 h-full">
                    <h4 class="text-xs font-bold text-slate-500 uppercase mb-3 border-b border-slate-200 pb-2">Mother's Details</h4>
                    <div class="space-y-3">
                        <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Name</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->mother_name ?? 'N/A' }}</span></div>
                         <div class="grid grid-cols-2 gap-4">
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Mobile</span><span class="text-slate-700 font-medium">{{ $student->parent?->mother_mobile ?? 'N/A' }}</span></div>
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Email</span><span class="text-slate-700 font-medium">{{ $student->parent?->mother_email ?? 'N/A' }}</span></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Occupation</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->mother_occupation ?? 'N/A' }}</span></div>
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Income</span><span class="text-slate-700 font-medium">₹ {{ $student->parent?->mother_annual_income ?? 'N/A' }}</span></div>
                        </div>
                        <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Qualification</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->mother_qualification ?? 'N/A' }}</span></div>
                    </div>
                </div>

                <!-- Guardian -->
                @if($student->parent?->guardian_name)
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 h-full">
                    <h4 class="text-xs font-bold text-slate-500 uppercase mb-3 border-b border-slate-200 pb-2">Guardian's Details</h4>
                    <div class="space-y-3">
                        <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Name</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->guardian_name ?? 'N/A' }}</span></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Relation</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->guardian_relation ?? 'N/A' }}</span></div>
                            <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Mobile</span><span class="text-slate-700 font-medium">{{ $student->parent?->guardian_mobile ?? 'N/A' }}</span></div>
                        </div>
                         <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Occupation</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->guardian_occupation ?? 'N/A' }}</span></div>
                         <div><span class="text-xs font-bold text-slate-400 uppercase block mb-1">Address</span><span class="text-slate-700 font-medium uppercase">{{ $student->parent?->guardian_address ?? 'N/A' }}</span></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Address Info -->
    <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center">
            <i data-lucide="map-pin" class="mr-2 text-blue-600" size="20"></i> Contact Addresses
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 p-5 rounded-lg border border-blue-100 relative">
                <span class="text-xs font-bold text-blue-600 uppercase block mb-2">Permanent Address</span>
                <p class="text-sm text-slate-700 leading-relaxed font-medium uppercase">
                    Vill/Town: {{ $student->address?->permanent_village_town ?? 'N/A' }}<br>
                    P.O.: {{ $student->address?->permanent_city ?? 'N/A' }}<br>
                    P.S.: {{ $student->address?->permanent_country ?? 'N/A' }}<br>
                    Dist: {{ $student->address?->permanent_district ?? 'N/A' }}<br>
                    BTCLA Constituency: {{ $student->address?->permanent_street ?? 'N/A' }}<br>
                    State: {{ $student->address?->permanent_state ?? 'N/A' }}<br>
                    PIN: {{ $student->address?->permanent_pin_code ?? 'N/A' }}
                </p>
            </div>
            <div class="bg-slate-50 p-5 rounded-lg border border-slate-200 relative">
                <div class="absolute top-4 right-4 text-slate-400"><i data-lucide="map" size="24"></i></div>
                <span class="text-xs font-bold text-slate-500 uppercase block mb-2">Current Address / Communication Address</span>
                <p class="text-sm text-slate-600 leading-relaxed uppercase">
                    Vill/Town: {{ $student->address?->present_village_town ?? 'N/A' }}<br>
                    @if($student->address?->present_street)Street: {{ $student->address?->present_street }}<br>@endif
                    P.O.: {{ $student->address?->present_city ?? 'N/A' }}<br>
                    P.S.: {{ $student->address?->present_country ?? 'N/A' }}<br>
                    Dist: {{ $student->address?->present_district ?? 'N/A' }}<br>
                    State: {{ $student->address?->present_state ?? 'N/A' }}<br>
                    PIN: {{ $student->address?->present_pin_code ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Academic Details -->
    <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center">
            <i data-lucide="graduation-cap" class="mr-2 text-blue-600" size="20"></i> Qualification Details
        </h3>
        <div class="overflow-x-auto border border-slate-200 rounded-lg">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-bold">Exam</th>
                        <th class="px-4 py-3 font-bold">Year</th>
                        <th class="px-4 py-3 font-bold">Board / University</th>
                        <th class="px-4 py-3 font-bold">Roll No</th>
                        <th class="px-4 py-3 font-bold">Reg No</th>
                        <th class="px-4 py-3 font-bold">Medium</th>
                        <th class="px-4 py-3 font-bold">Total</th>
                        <th class="px-4 py-3 font-bold">Obt</th>
                        <th class="px-4 py-3 font-bold">%</th>
                        <th class="px-4 py-3 font-bold">Div</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($student->academic_qualifications as $qual)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800 uppercase">{{ $qual->examination }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $qual->passing_year }}</td>
                        <td class="px-4 py-3 text-slate-600 uppercase">{{ $qual->board_university }}</td>
                        <td class="px-4 py-3 text-slate-600 uppercase">{{ $qual->roll_number ?? '--' }}</td>
                        <td class="px-4 py-3 text-slate-600 uppercase">{{ $qual->registration_number ?? '--' }}</td>
                        <td class="px-4 py-3 text-slate-600 uppercase">{{ $qual->medium_of_study ?? '--' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $qual->total_marks ?? '--' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $qual->marks_obtained ?? '--' }}</td>
                        <td class="px-4 py-3 text-slate-800 font-bold">{{ $qual->percentage_grade }}%</td>
                        <td class="px-4 py-3"><span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded font-bold uppercase">{{ $qual->division }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-3 text-center text-slate-500">No academic details added.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bank Details -->
    <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center">
            <i data-lucide="landmark" class="mr-2 text-blue-600" size="20"></i> Bank Account Details
        </h3>
        <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100">
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                 <div><span class="text-xs font-bold text-indigo-400 uppercase block mb-1">Account Holder</span><span class="text-indigo-900 font-bold uppercase">{{ $student->bank_detail?->account_holder_name ?? 'N/A' }}</span></div>
                 <div><span class="text-xs font-bold text-indigo-400 uppercase block mb-1">Bank Name</span><span class="text-indigo-900 font-bold uppercase">{{ $student->bank_detail?->bank_name ?? 'N/A' }}</span></div>
                 <div><span class="text-xs font-bold text-indigo-400 uppercase block mb-1">Branch</span><span class="text-indigo-900 font-bold uppercase">{{ $student->bank_detail?->branch ?? 'N/A' }}</span></div>
                 <div><span class="text-xs font-bold text-indigo-400 uppercase block mb-1">Account Number</span><span class="text-indigo-900 font-bold font-mono text-lg">{{ $student->bank_detail?->account_number ?? 'N/A' }}</span></div>
                 <div><span class="text-xs font-bold text-indigo-400 uppercase block mb-1">IFSC Code</span><span class="text-indigo-900 font-bold uppercase font-mono">{{ $student->bank_detail?->ifsc_code ?? 'N/A' }}</span></div>
                 <div><span class="text-xs font-bold text-indigo-400 uppercase block mb-1">UPI ID</span><span class="text-indigo-900 font-bold font-mono">{{ $student->bank_detail?->upi_id ?? 'N/A' }}</span></div>
             </div>
        </div>
    </div>

    <!-- Documents Section -->
    <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex items-center">
            <i data-lucide="file-text" class="mr-2 text-blue-600" size="20"></i> Uploaded Documents
        </h3>

        @if($student->document)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Identity Documents -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center"><span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>Identity & Proofs</h4>
                    <ul class="space-y-3">
                        @foreach(['aadhaar_card' => 'Aadhaar Card', 'caste_certificate' => 'Caste Certificate', 'income_certificate' => 'Income Certificate', 'address_proof' => 'Address Proof', 'signature' => 'Signature'] as $field => $label)
                            @if($student->document->$field)
                            <li class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex items-center space-x-3 overflow-hidden">
                                     <div class="bg-white p-1.5 rounded border border-slate-200 text-indigo-600 shrink-0">
                                         <i data-lucide="file" size="16"></i>
                                     </div>
                                     <span class="text-sm font-medium text-slate-700 truncate" title="{{ $label }}">{{ $label }}</span>
                                </div>
                                <a href="{{ Storage::url($student->document->$field) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors" title="View Document">
                                    <i data-lucide="external-link" size="16"></i>
                                </a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <!-- Marksheets -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center"><span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>Marksheets</h4>
                    <ul class="space-y-3">
                        @foreach(['hslc_marksheet' => 'HSLC Marksheet', 'hs_marksheet' => 'HS Marksheet', 'graduation_marksheet' => 'Graduation Marksheet', 'post_graduation_marksheet' => 'PG Marksheet'] as $field => $label)
                            @if($student->document->$field)
                            <li class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex items-center space-x-3 overflow-hidden">
                                     <div class="bg-white p-1.5 rounded border border-slate-200 text-emerald-600 shrink-0">
                                         <i data-lucide="file-bar-chart-2" size="16"></i>
                                     </div>
                                     <span class="text-sm font-medium text-slate-700 truncate" title="{{ $label }}">{{ $label }}</span>
                                </div>
                                <a href="{{ Storage::url($student->document->$field) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors" title="View Document">
                                    <i data-lucide="external-link" size="16"></i>
                                </a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <!-- Certificates -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center"><span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>Certificates & Admit Cards</h4>
                    <ul class="space-y-3">
                        @foreach(['hslc_pass_certificate' => 'HSLC Pass Cert.', 'hs_pass_certificate' => 'HS Pass Cert.', 'graduation_pass_certificate' => 'Graduation Cert.', 'post_graduation_pass_certificate' => 'PG Cert.'] as $field => $label)
                            @if($student->document->$field)
                            <li class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex items-center space-x-3 overflow-hidden">
                                     <div class="bg-white p-1.5 rounded border border-slate-200 text-orange-600 shrink-0">
                                         <i data-lucide="award" size="16"></i>
                                     </div>
                                     <span class="text-sm font-medium text-slate-700 truncate" title="{{ $label }}">{{ $label }}</span>
                                </div>
                                <a href="{{ Storage::url($student->document->$field) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors" title="View Document">
                                    <i data-lucide="external-link" size="16"></i>
                                </a>
                            </li>
                            @endif
                        @endforeach
                        
                         @foreach(['hslc_admit_card' => 'HSLC Admit Card', 'hs_admit_card' => 'HS Admit Card', 'graduation_admit_card' => 'Graduation Admit', 'post_graduation_admit_card' => 'PG Admit Card'] as $field => $label)
                            @if($student->document->$field)
                            <li class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex items-center space-x-3 overflow-hidden">
                                     <div class="bg-white p-1.5 rounded border border-slate-200 text-slate-600 shrink-0">
                                         <i data-lucide="credit-card" size="16"></i>
                                     </div>
                                     <span class="text-sm font-medium text-slate-700 truncate" title="{{ $label }}">{{ $label }}</span>
                                </div>
                                <a href="{{ Storage::url($student->document->$field) }}" target="_blank" class="p-1.5 text-slate-400 hover:text-blue-600 transition-colors" title="View Document">
                                    <i data-lucide="external-link" size="16"></i>
                                </a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            
            @if(collect($student->document->getAttributes())->except(['id', 'student_id', 'created_at', 'updated_at'])->filter()->isEmpty())
                <div class="text-center py-8 text-slate-500">
                    <i data-lucide="folder-open" class="mx-auto mb-2 opacity-50" size="48"></i>
                    <p>No documents uploaded yet.</p>
                </div>
            @endif

        @else
            <div class="text-center py-8 text-slate-500">
                <i data-lucide="folder-open" class="mx-auto mb-2 opacity-50" size="48"></i>
                <p>No documents record found.</p>
                <a href="{{ route('student.documents') }}" class="text-blue-600 hover:underline text-sm font-bold mt-2 inline-block">Upload Documents</a>
            </div>
        @endif
    </div>

    <!-- Declaration Footer -->
    <div class="bg-white rounded-xl p-4 border border-slate-200 flex items-start space-x-3">
        <i data-lucide="check-circle-2" class="text-green-600 mt-0.5 shrink-0" size="18"></i>
        <p class="text-sm text-slate-600">
            <span class="font-bold text-slate-800">Declaration Signed:</span> I hereby declare that the above information provided is correct and I agree to abide by all Rules & Regulations of the Institution.
        </p>
    </div>
</div>
@endsection
