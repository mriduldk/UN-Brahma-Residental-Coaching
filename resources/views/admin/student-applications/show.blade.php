@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.forms.show', $application->application_form_id) }}" class="p-2 bg-white rounded-lg shadow-sm border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-colors">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i data-lucide="user" size="20"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                            {{ $application->application_student->full_name ?? $application->student->full_name ?? 'Unknown Student' }}
                        </h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded text-xs font-bold font-mono">
                                #{{ $application->application_code ?? 'N/A' }}
                            </span>
                            <span class="text-sm font-medium text-slate-500">
                                Applied for <a href="{{ route('admin.forms.show', $application->application_form_id) }}" class="text-indigo-600 hover:underline">{{ $application->application_form->application_name ?? 'Form' }}</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            @php
                $statusStyle = match($application->status) {
                    'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'submitted' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
                    default => 'bg-slate-100 text-slate-600 border-slate-200'
                };
            @endphp
            <div class="px-4 py-2 rounded-lg border {{ $statusStyle }} flex flex-col items-end shadow-sm">
                <span class="text-[10px] font-bold uppercase tracking-wider opacity-70">Application Status</span>
                <span class="font-black text-lg leading-none">{{ ucfirst($application->status) }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Personal Info -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Applicant Profile Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="contact" size="18" class="text-indigo-500"></i> Applicant Profile
                    </h3>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Email Address</p>
                        <p class="font-medium text-slate-800">{{ $application->application_student->email ?? $application->student->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Phone Number</p>
                        <p class="font-medium text-slate-800">{{ $application->application_student->phone_number ?? $application->student->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Date of Birth</p>
                        <p class="font-medium text-slate-800">
                            @if($application->application_student && $application->application_student->dob)
                                {{ \Carbon\Carbon::parse($application->application_student->dob)->format('F d, Y') }}
                            @elseif($application->student && $application->student->date_of_birth)
                                {{ \Carbon\Carbon::parse($application->student->date_of_birth)->format('F d, Y') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Gender</p>
                        <p class="font-medium text-slate-800">{{ ucfirst($application->application_student->gender ?? $application->student->gender ?? '-') }}</p>
                    </div>
                    <div class="md:col-span-2 pt-4 border-t border-slate-100 mt-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Permanent Address</p>
                            @if($application->application_address)
                                <p class="font-medium text-slate-800">
                                    {{ $application->application_address->permanent_village_town ?? '' }}{{ $application->application_address->permanent_street ? ', ' . $application->application_address->permanent_street : '' }}
                                    <br>
                                    {{ $application->application_address->permanent_city ?? '' }}{{ $application->application_address->permanent_district ? ', ' . $application->application_address->permanent_district : '' }}
                                    <br>
                                    {{ $application->application_address->permanent_state ?? '' }}{{ $application->application_address->permanent_country ? ', ' . $application->application_address->permanent_country : '' }}
                                    @if($application->application_address->permanent_pin_code) - {{ $application->application_address->permanent_pin_code }} @endif
                                </p>
                            @else
                                <p class="font-medium text-slate-500 italic">No permanent address provided.</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Present Address</p>
                            @if($application->application_address)
                                <p class="font-medium text-slate-800">
                                    {{ $application->application_address->present_village_town ?? '' }}{{ $application->application_address->present_street ? ', ' . $application->application_address->present_street : '' }}
                                    <br>
                                    {{ $application->application_address->present_city ?? '' }}{{ $application->application_address->present_district ? ', ' . $application->application_address->present_district : '' }}
                                    <br>
                                    {{ $application->application_address->present_state ?? '' }}{{ $application->application_address->present_country ? ', ' . $application->application_address->present_country : '' }}
                                    @if($application->application_address->present_pin_code) - {{ $application->application_address->present_pin_code }} @endif
                                </p>
                            @else
                                <p class="font-medium text-slate-500 italic">No present address provided.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parent / Guardian Details -->
            @if($application->application_parent)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="users" size="18" class="text-orange-500"></i> Parent / Guardian Details
                    </h3>
                </div>
                
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Father's Name</p>
                        <p class="font-medium text-slate-800">{{ $application->application_parent->father_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Father's Phone</p>
                        <p class="font-medium text-slate-800">{{ $application->application_parent->father_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Mother's Name</p>
                        <p class="font-medium text-slate-800">{{ $application->application_parent->mother_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Mother's Phone</p>
                        <p class="font-medium text-slate-800">{{ $application->application_parent->mother_phone ?? '-' }}</p>
                    </div>
                    @if($application->application_parent->guardian_name)
                    <div class="md:col-span-2 pt-4 border-t border-slate-100 mt-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Guardian's Name</p>
                        <p class="font-medium text-slate-800">{{ $application->application_parent->guardian_name }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Academic Qualifications -->
            @if($application->application_academic_qualifications && $application->application_academic_qualifications->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="graduation-cap" size="18" class="text-emerald-500"></i> Academic Background
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4">Qualification</th>
                                <th class="px-6 py-4">Institution / Board</th>
                                <th class="px-6 py-4 text-center">Year</th>
                                <th class="px-6 py-4 text-center">Marks</th>
                                <th class="px-6 py-4 text-right">Percentage</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($application->application_academic_qualifications as $acad)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $acad->examination }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $acad->board_university }}</td>
                                <td class="px-6 py-4 text-slate-600 text-center">{{ $acad->passing_year }}</td>
                                <td class="px-6 py-4 text-slate-600 text-center font-mono">
                                    {{ $acad->marks_obtained ?? 0 }} / {{ $acad->total_marks ?? 0 }}
                                </td>
                                <td class="px-6 py-4 font-bold text-indigo-600 text-right">
                                    @if($acad->percentage_grade)
                                        {{ is_numeric($acad->percentage_grade) ? $acad->percentage_grade.'%' : $acad->percentage_grade }}
                                    @elseif($acad->total_marks > 0)
                                        {{ number_format(($acad->marks_obtained / $acad->total_marks) * 100, 2) }}%
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Uploaded Documents -->
            @if($application->application_documents)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="file-check" size="18" class="text-blue-500"></i> Uploaded Documents
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @php
                            $docFields = [
                                'passport_photo' => 'Passport Photo',
                                'signature' => 'Signature',
                                'hslc_admit_card' => 'HSLC Admit Card',
                                'hslc_mark_sheet' => 'HSLC Mark Sheet',
                                'hslc_pass_certificate' => 'HSLC Pass Certificate',
                                'hs_admit_card' => 'HS Admit Card',
                                'hs_mark_sheet' => 'HS Mark Sheet',
                                'hs_pass_certificate' => 'HS Pass Certificate',
                                'graduation_admit_card' => 'Graduation Admit Card',
                                'graduation_mark_sheet' => 'Graduation Mark Sheet',
                                'graduation_pass_certificate' => 'Graduation Pass Certificate',
                                'post_graduation_admit_card' => 'PG Admit Card',
                                'post_graduation_mark_sheet' => 'PG Mark Sheet',
                                'post_graduation_pass_certificate' => 'PG Pass Certificate',
                                'caste_certificate' => 'Caste Certificate',
                                'aadhaar_card' => 'Aadhaar Card',
                                'pwd_certificate' => 'PWD Certificate',
                                'bpl_certificate' => 'BPL Certificate',
                                'sports_certificate' => 'Sports Certificate',
                                'ncc_certificate' => 'NCC Certificate',
                            ];
                        @endphp
                        
                        @foreach($docFields as $field => $label)
                            @if($application->application_documents->$field)
                            <a href="{{ asset('storage/' . $application->application_documents->$field) }}" target="_blank" class="flex flex-col items-center justify-center p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-indigo-300 transition-colors group">
                                <div class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform mb-3">
                                    <i data-lucide="file-text" size="18"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-700 text-center">{{ $label }}</span>
                            </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Financials & Status -->
        <div class="space-y-6">
            
            <!-- Admission & Fees Snapshot -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="indian-rupee" size="18" class="text-emerald-500"></i> Financial Snapshot
                    </h3>
                </div>
                
                <div class="p-6 space-y-4 shadow-inner">
                    <!-- Registration Fee -->
                    <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                        <div>
                            <p class="font-bold text-slate-700">Form Registration Fee</p>
                            <p class="text-xs text-slate-500">Paid during application</p>
                        </div>
                        @if($application->payment_status === 'paid')
                            <span class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200">
                                Paid
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded bg-amber-100 text-amber-700 text-xs font-bold border border-amber-200">
                                Pending
                            </span>
                        @endif
                    </div>

                    <!-- Admission Fee Details -->
                    @if($application->admission_student)
                        <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                            <div>
                                <p class="font-bold text-slate-700">Admission Plan</p>
                                <p class="text-xs text-slate-500">Selected payment method</p>
                            </div>
                            <span class="px-3 py-1 bg-slate-800 text-white rounded font-bold text-xs shadow-sm uppercase tracking-wider">
                                {{ $application->admission_student->payment_type }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <p class="font-medium text-slate-500">Total Course Fee</p>
                            <p class="font-bold text-slate-800">₹{{ number_format($application->admission_student->total_amount, 2) }}</p>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <p class="font-medium text-emerald-600">Total Paid</p>
                            <p class="font-bold text-emerald-700">₹{{ number_format($application->admission_student->paid_amount, 2) }}</p>
                        </div>
                        
                        <div class="flex justify-between items-center pt-2 mt-2 border-t border-slate-100">
                            <p class="font-bold text-rose-500">Remaining Balance</p>
                            <p class="font-black text-rose-600 text-lg">₹{{ number_format($application->admission_student->pending_amount, 2) }}</p>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm font-medium text-slate-500">No admission record generated yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Metadata / Timestamps -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-bold text-slate-800 mb-4 uppercase tracking-wider text-slate-500">Application Timeline</h3>
                
                <div class="space-y-4 relative before:absolute before:inset-0 before:ml-2 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-4 h-4 rounded-full border border-white bg-indigo-500 text-indigo-500 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2"></div>
                        <div class="w-[calc(100%-1.5rem)] md:w-[calc(50%-1.25rem)] p-2 rounded bg-slate-50">
                            <div class="flex flex-col">
                                <time class="text-[10px] font-bold text-indigo-500 opacity-80 mb-1">Started</time>
                                <div class="text-xs font-bold text-slate-800">{{ $application->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($application->submitted_at)
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div class="flex items-center justify-center w-4 h-4 rounded-full border border-white bg-emerald-500 text-emerald-500 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2"></div>
                        <div class="w-[calc(100%-1.5rem)] md:w-[calc(50%-1.25rem)] p-2 rounded bg-slate-50">
                            <div class="flex flex-col">
                                <time class="text-[10px] font-bold text-emerald-500 opacity-80 mb-1">Submitted</time>
                                <div class="text-xs font-bold text-slate-800">{{ $application->submitted_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
