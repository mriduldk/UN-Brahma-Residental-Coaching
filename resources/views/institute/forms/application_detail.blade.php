@extends('layouts.institute')

@section('title', 'Review Application')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <a href="{{ route('institute.forms.applications') }}" class="flex items-center text-slate-500 hover:text-slate-800 font-medium group w-fit">
        <i data-lucide="arrow-left" size="20" class="mr-2 group-hover:-translate-x-1 transition-transform"></i> Back to Applications
    </a>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <!-- Detail Header -->
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start bg-slate-50/50">
            <div class="flex items-center space-x-5 mb-4 sm:mb-0">
                <div class="w-20 h-20 bg-slate-200 rounded-2xl flex items-center justify-center text-slate-400 border-4 border-white shadow-sm overflow-hidden">
                    @if($application->application_documents && $application->application_documents->photo)
                        <img src="{{ Storage::url($application->application_documents->photo) }}" alt="Applicant Photo" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="image" size="36"></i>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">{{ $application->application_student->full_name ?? 'N/A' }}</h2>
                    <p class="text-slate-500 font-medium text-sm">Application ID: <span class="font-mono text-slate-700 font-bold bg-slate-100 px-2 py-0.5 rounded">{{ $application->application_code }}</span></p>
                    <div class="mt-3 flex space-x-2">
                        <span class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border border-blue-200">{{ $application->application_form->application_name ?? 'N/A' }}</span>
                        
                         @if($application->status == 'approved')
                            <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border border-green-200 flex items-center"><i data-lucide="check-circle" size="12" class="mr-1"></i> Approved</span>
                        @elseif($application->status == 'rejected')
                            <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border border-red-200 flex items-center"><i data-lucide="x-circle" size="12" class="mr-1"></i> Rejected</span>
                        @else
                            <span class="bg-yellow-50 text-yellow-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border border-yellow-100 flex items-center"><i data-lucide="alert-circle" size="12" class="mr-1"></i> Pending Verification</span>
                        @endif
                    </div>
                </div>
            </div>
            @if(auth()->user()->isInstitutionAdmin($application->institution_id))
            <div class="flex space-x-3 w-full sm:w-auto">
                <form action="{{ route('institute.forms.applications.status', $application->id) }}" method="POST" class="flex gap-3 w-full sm:w-auto">
                    @csrf
                    <button type="submit" name="status" value="rejected" class="flex-1 sm:flex-none items-center justify-center px-5 py-2.5 border border-red-200 text-red-600 rounded-xl hover:bg-red-50 font-bold transition-colors flex" onclick="return confirm('Are you sure you want to reject this application?')">
                        <i data-lucide="x" size="18" class="mr-2"></i> Reject
                    </button>
                    <button type="submit" name="status" value="approved" class="flex-1 sm:flex-none items-center justify-center px-5 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 font-bold shadow-lg shadow-green-200 transition-all hover:-translate-y-0.5 flex" onclick="return confirm('Are you sure you want to approve this application?')">
                        <i data-lucide="check" size="18" class="mr-2"></i> Approve
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="p-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Col: Personal & Address -->
            <div class="lg:col-span-2 space-y-8">
                <section>
                    <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-5 tracking-wider flex items-center">
                        <i data-lucide="user" size="16" class="mr-2"></i> Personal Details
                    </h3>
                    <div class="grid grid-cols-2 gap-y-6 text-sm">
                         <div><span class="text-slate-400 block text-xs uppercase font-bold mb-1">Father's Name</span><span class="font-medium text-slate-800 text-base">{{ $application->application_parent->father_name ?? 'N/A' }}</span></div>
                        <div><span class="text-slate-400 block text-xs uppercase font-bold mb-1">Mother's Name</span><span class="font-medium text-slate-800 text-base">{{ $application->application_parent->mother_name ?? 'N/A' }}</span></div>
                        <div><span class="text-slate-400 block text-xs uppercase font-bold mb-1">Date of Birth</span><span class="font-medium text-slate-800 text-base">{{ $application->application_student->dob ?? 'N/A' }}</span></div>
                        <div><span class="text-slate-400 block text-xs uppercase font-bold mb-1">Gender</span><span class="font-medium text-slate-800 text-base">{{ $application->application_student->gender ?? 'N/A' }}</span></div>
                        <div><span class="text-slate-400 block text-xs uppercase font-bold mb-1">Category</span><span class="font-medium text-slate-800 text-base">{{ $application->application_student->category ?? 'N/A' }}</span></div>
                        <div><span class="text-slate-400 block text-xs uppercase font-bold mb-1">Email</span><span class="font-medium text-slate-800 text-base">{{ $application->application_student->email ?? 'N/A' }}</span></div>
                         <div><span class="text-slate-400 block text-xs uppercase font-bold mb-1">Phone</span><span class="font-medium text-slate-800 text-base">{{ $application->application_student->phone_number ?? 'N/A' }}</span></div>
                    </div>
                </section>

                <section>
                    <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-5 tracking-wider flex items-center">
                        <i data-lucide="map-pin" size="16" class="mr-2"></i> Address Details
                    </h3>
                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                            <span class="text-xs font-bold text-blue-600 uppercase block mb-2">Present Address</span>
                            <p class="leading-relaxed text-slate-700 font-medium">
                                {{ $application->application_address->present_village_town ?? '' }}, 
                                {{ $application->application_address->present_street ?? '' }}<br>
                                Dist: {{ $application->application_address->present_district ?? '' }}, 
                                State: {{ $application->application_address->present_state ?? '' }}<br>
                                PIN: {{ $application->application_address->present_pin_code ?? '' }}
                            </p>
                        </div>
                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-100">
                            <span class="text-xs font-bold text-slate-500 uppercase block mb-2">Permanent Address</span>
                            <p class="leading-relaxed text-slate-700 font-medium">
                                {{ $application->application_address->permanent_village_town ?? '' }}, 
                                {{ $application->application_address->permanent_street ?? '' }}<br>
                                Dist: {{ $application->application_address->permanent_district ?? '' }}, 
                                State: {{ $application->application_address->permanent_state ?? '' }}<br>
                                PIN: {{ $application->application_address->permanent_pin_code ?? '' }}
                            </p>
                        </div>
                    </div>
                </section>

                <section>
                    <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-5 tracking-wider flex items-center">
                        <i data-lucide="graduation-cap" size="16" class="mr-2"></i> Academic History
                    </h3>
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-bold">
                                <tr>
                                    <th class="px-5 py-3 border-b border-slate-200">Exam</th>
                                    <th class="px-5 py-3 border-b border-slate-200">Year</th>
                                    <th class="px-5 py-3 border-b border-slate-200">Board</th>
                                    <th class="px-5 py-3 border-b border-slate-200">% Marks</th>
                                    <th class="px-5 py-3 border-b border-slate-200">Div</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($application->application_academic_qualifications as $qual)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-slate-800">{{ $qual->examination }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $qual->passing_year }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $qual->board_university }}</td>
                                    <td class="px-5 py-3 font-bold text-slate-800">{{ $qual->percentage_grade }}%</td>
                                    <td class="px-5 py-3"><span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">{{ $qual->division }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-3 text-center text-slate-500">No academic history found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <!-- Right Col: Payment & Docs -->
            <div class="space-y-8">
                @if($application->application_form->is_approval_required)
                <section>
                    <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-5 tracking-wider flex items-center">
                        <i data-lucide="clipboard-check" size="16" class="mr-2"></i> Approval Management
                    </h3>
                    <div class="bg-indigo-50/50 border border-indigo-100 p-5 rounded-2xl">
                        <div class="flex items-center mb-4">
                            @if($application->approval_status == 'approved')
                                <div class="bg-green-100 text-green-600 p-2.5 rounded-full mr-3 shadow-sm"><i data-lucide="check-circle" size="20"></i></div>
                            @elseif($application->approval_status == 'rejected')
                                <div class="bg-red-100 text-red-600 p-2.5 rounded-full mr-3 shadow-sm"><i data-lucide="x-circle" size="20"></i></div>
                            @else
                                <div class="bg-yellow-100 text-yellow-600 p-2.5 rounded-full mr-3 shadow-sm"><i data-lucide="clock" size="20"></i></div>
                            @endif
                            <div>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-wide">Approval Status</p>
                                <p class="text-lg font-bold text-slate-800">{{ ucfirst($application->approval_status ?? 'Pending') }}</p>
                            </div>
                        </div>

                        @if(auth()->user()->isInstitutionAdmin($application->institution_id))
                        <form action="{{ route('institute.forms.applications.approval', $application->id) }}" method="POST" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Update Status</label>
                                <select name="approval_status" class="w-full text-sm p-2 border border-slate-200 rounded-lg bg-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                                    <option value="pending" {{ $application->approval_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $application->approval_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $application->approval_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Remarks (Optional)</label>
                                <textarea name="approval_remarks" rows="2" class="w-full text-sm p-2 border border-slate-200 rounded-lg bg-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Enter remarks...">{{ $application->approval_remarks }}</textarea>
                            </div>
                            <button type="submit" class="w-full flex items-center justify-center p-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg transition-colors">
                                <i data-lucide="save" size="14" class="mr-2"></i> Save Approval
                            </button>
                        </form>
                        @elseif($application->approval_remarks)
                        <div class="mt-4 bg-white p-3 rounded-xl border border-indigo-100 text-sm">
                            <span class="block text-xs font-bold text-slate-500 mb-1">Remarks</span>
                            <p class="text-slate-700">{{ $application->approval_remarks }}</p>
                        </div>
                        @endif
                    </div>
                </section>
                @endif
                <section>
                    <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-5 tracking-wider flex items-center">
                        <i data-lucide="credit-card" size="16" class="mr-2"></i> Payment Info
                    </h3>
                    <div class="{{ $application->payment_status == 'paid' ? 'bg-green-50/50 border-green-200' : 'bg-red-50/50 border-red-200' }} border p-5 rounded-2xl">
                        <div class="flex items-center mb-4">
                            <div class="{{ $application->payment_status == 'paid' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} p-2.5 rounded-full mr-3 shadow-sm">
                                <i data-lucide="{{ $application->payment_status == 'paid' ? 'check-circle' : 'alert-circle' }}" size="20"></i>
                            </div>
                            <div>
                                <p class="text-xs {{ $application->payment_status == 'paid' ? 'text-green-700' : 'text-red-700' }} font-bold uppercase tracking-wide">Status: {{ ucfirst($application->payment_status) }}</p>
                                <p class="text-xl font-bold text-slate-800">₹ {{ number_format($application->application_form->application_fee ?? 0, 2) }}</p>
                            </div>
                        </div>
                        @if($application->payment_status == 'paid')
                        <div class="space-y-3 text-xs text-slate-600 bg-white p-3 rounded-xl border border-green-100">
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span>Txn ID</span> <span class="font-mono font-medium text-slate-800">{{ $application->transaction_id ?? 'N/A' }}</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span>Date</span> <span class="font-medium text-slate-800">{{ $application->submitted_at ? $application->submitted_at->format('d M Y') : 'N/A' }}</span></div>
                             <div class="flex justify-between"><span>Method</span> <span class="font-medium text-slate-800">Online</span></div>
                        </div>
                        @endif
                    </div>
                </section>

                <section>
                    <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-5 tracking-wider flex items-center">
                        <i data-lucide="file" size="16" class="mr-2"></i> Documents
                    </h3>
                    <div class="space-y-3">
                        @php
                            $docs = $application->application_documents;
                            $docLabels = [
                                'photo' => 'Photograph',
                                'signature' => 'Signature',
                                'aadhaar_card' => 'Aadhaar Card',
                                'passport' => 'Passport',
                                'hslc_marksheet' => '10th Marksheet',
                                'hs_marksheet' => '12th Marksheet',
                                'graduation_marksheet' => 'Graduation Marksheet',
                                'post_graduation_marksheet' => 'PG Marksheet',
                                'hslc_pass_certificate' => '10th Pass Cert',
                                'hs_pass_certificate' => '12th Pass Cert',
                                'graduation_pass_certificate' => 'Graduation Pass Cert',
                                'post_graduation_pass_certificate' => 'PG Pass Cert',
                                'hslc_admit_card' => '10th Admit Card',
                                'hs_admit_card' => '12th Admit Card',
                                'graduation_admit_card' => 'Graduation Admit',
                                'post_graduation_admit_card' => 'PG Admit',
                                'transfer_certificate' => 'Transfer Cert',
                                'caste_certificate' => 'Caste Certificate',
                                'income_certificate' => 'Income Certificate',
                                'address_proof' => 'Address Proof',
                            ];
                            $hasDocs = false;
                        @endphp

                        @if($docs)
                            @foreach($docLabels as $key => $label)
                                @if(!empty($docs->$key))
                                    @php $hasDocs = true; @endphp
                                    <a href="{{ Storage::url($docs->$key) }}" target="_blank" class="flex items-center p-3 border border-slate-200 rounded-xl hover:bg-blue-50 hover:border-blue-200 transition-all group bg-white">
                                        <div class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3 group-hover:bg-white group-hover:shadow-sm transition-all">
                                            <i data-lucide="{{ $key == 'photo' ? 'image' : 'file-text' }}" size="20"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <span class="text-sm font-bold text-slate-700 block group-hover:text-blue-700 truncate capitalize" title="{{ $label }}">{{ $label }}</span>
                                            <span class="text-xs text-slate-400">View Document</span>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        @endif

                        @if(!$hasDocs)
                            <p class="text-sm text-slate-500">No documents uploaded.</p>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
