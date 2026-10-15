@extends('layouts.administration')

@section('title', 'Application Reports')

@section('content')
<div class="space-y-6 w-full">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center">
                <i data-lucide="pie-chart" class="mr-3 text-blue-600"></i> Reports & Analytics
            </h1>
            <p class="text-slate-500 font-medium mt-1">Generate filtered reports based on applicant demographics, location, examination district, and documentation.</p>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 rounded-t-2xl flex justify-between items-center">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest flex items-center">
                <i data-lucide="filter" size="16" class="mr-2 text-slate-400"></i> Advanced Filters
            </h2>
            <span class="text-xs font-semibold text-slate-400">Submitted Applications Only</span>
        </div>
        <div class="p-6">
            <form id="reportFilterForm" action="{{ route('administration.reports') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    <!-- 1. Search by Name -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Search by Name</label>
                        <div class="relative">
                            <i data-lucide="user" size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="name" value="{{ request('name') }}" placeholder="Search applicant name..." class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors text-sm font-medium">
                        </div>
                    </div>

                    <!-- 2. Search by Phone Number -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Phone Number</label>
                        <div class="relative">
                            <i data-lucide="phone" size="16" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="phone" value="{{ request('phone') }}" placeholder="Search phone number..." class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors text-sm font-medium">
                        </div>
                    </div>

                    <!-- 3. Dropdown Address District -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Address District</label>
                        <select name="address_district" class="searchable-select w-full">
                            <option value="">All Address Districts</option>
                            @foreach($addressDistricts as $dist)
                                <option value="{{ $dist }}" {{ (request('address_district') == $dist || request('district') == $dist) ? 'selected' : '' }}>{{ $dist }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 4. Dropdown Examination District -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Examination District</label>
                        <select name="examination_district" class="searchable-select w-full">
                            <option value="">All Examination Districts</option>
                            @foreach($examDistricts as $examDist)
                                <option value="{{ $examDist }}" {{ (request('examination_district') == $examDist || request('examination_center') == $examDist) ? 'selected' : '' }}>{{ $examDist }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 5. Dropdown Gender -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Gender</label>
                        <select name="gender" class="searchable-select w-full">
                            <option value="">All Genders</option>
                            @foreach($genders as $gen)
                                <option value="{{ $gen }}" {{ strcasecmp(request('gender', ''), $gen) === 0 ? 'selected' : '' }}>{{ $gen }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 6. Dropdown Category -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                        <select name="category" class="searchable-select w-full">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ strcasecmp(request('category', ''), $cat) === 0 ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 7. Dropdown Applied For -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Applied For</label>
                        <select name="applied_for" class="searchable-select w-full">
                            <option value="">All Applied For</option>
                            @foreach($appliedForOptions as $appFor)
                                <option value="{{ $appFor }}" {{ request('applied_for') == $appFor ? 'selected' : '' }}>{{ $appFor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 8. Trade / Form Dropdown -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Trade / Form</label>
                        <select name="form_id" class="searchable-select w-full">
                            <option value="">All Trade / Form</option>
                            @foreach($forms as $form)
                                <option value="{{ $form->id }}" {{ request('form_id') == $form->id ? 'selected' : '' }}>{{ $form->application_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 9. Document Criteria -->
                    @php
                        $docLabels = [
                            'photo' => 'Photograph',
                            'signature' => 'Signature',
                            'hslc_marksheet' => 'HSLC Marksheet',
                            'hs_marksheet' => 'HS Marksheet',
                            'graduation_marksheet' => 'Graduation Marksheet',
                            'post_graduation_marksheet' => 'PG Marksheet',
                            'hslc_pass_certificate' => 'HSLC Pass Certificate',
                            'hs_pass_certificate' => 'HS Pass Certificate',
                            'graduation_pass_certificate' => 'Graduation Certificate',
                            'post_graduation_pass_certificate' => 'PG Certificate',
                            'hslc_admit_card' => 'HSLC Admit Card',
                            'hs_admit_card' => 'HS Admit Card',
                            'caste_certificate' => 'Caste Certificate',
                            'income_certificate' => 'Income Certificate',
                            'address_proof' => 'Address Proof',
                            'aadhaar_card' => 'Aadhaar Card',
                            'btc_residential_certificate' => 'Permanent Resident Certificate (PRC / BTC)',
                            'pwd_certificate' => 'PWD Certificate',
                            'sports_certificate' => 'Sports Achievement Certificate',
                            'cultural_certificate' => 'Cultural / Fine Arts Certificate',
                            'ncc_certificate_doc' => 'NCC Certificate',
                        ];
                    @endphp
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Document Criteria</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <select name="document_type" class="searchable-select w-full sm:w-2/3">
                                <option value="">All Documents</option>
                                @foreach($docLabels as $key => $label)
                                    <option value="{{ $key }}" {{ request('document_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <select name="document_status" class="searchable-select w-full sm:w-1/3">
                                <option value="">All Statuses</option>
                                <option value="provided" {{ request('document_status') == 'provided' ? 'selected' : '' }}>Provided</option>
                                <option value="not_provided" {{ request('document_status') == 'not_provided' ? 'selected' : '' }}>Not Provided</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="mt-8 flex flex-wrap justify-between items-center gap-3 pt-6 border-t border-slate-100">
                    <div class="text-xs text-slate-400 font-medium">
                        Showing submitted applications across all forms.
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('administration.reports') }}" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                            Clear Filters
                        </a>
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200 flex items-center">
                            <i data-lucide="filter" size="16" class="mr-2"></i> Apply Filters
                        </button>
                        <button type="submit" formaction="{{ route('administration.reports.export') }}" class="px-6 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-200 flex items-center">
                            <i data-lucide="file-spreadsheet" size="16" class="mr-2"></i> Export to Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="text-base font-bold text-slate-800 flex items-center">
                    Report Results 
                    <span class="ml-2.5 bg-blue-100 text-blue-700 text-xs py-0.5 px-2.5 rounded-full font-black">
                        {{ $applications->total() }} Submitted Found
                    </span>
                </h2>
            </div>
        </div>
        
        <div class="overflow-x-auto min-h-[400px]">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Application ID</th>
                        <th class="px-6 py-4">Applicant Profile</th>
                        <th class="px-6 py-4">Applied For & Exam Center</th>
                        <th class="px-6 py-4">Location</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($applications as $app)
                        @php
                            $student = $app->application_student;
                            $address = $app->application_address;
                            $appliedForDisplay = $app->applied_for ?? $student?->applied_for ?? 'N/A';
                            $examCenterDisplay = $app->examination_center ?? $student?->examination_center ?? 'N/A';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-slate-700 font-bold bg-slate-100 px-2 py-1 rounded">{{ $app->application_code ?? 'Draft/Pending' }}</span>
                                <div class="mt-2 text-[10px] uppercase font-bold text-slate-400">{{ $app->application_form->application_name ?? 'Form' }}</div>
                                @if($app->submitted_at)
                                    <div class="text-[11px] text-slate-400 mt-1">{{ $app->submitted_at->format('d M Y, h:i A') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-base">{{ $student->full_name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-500 font-medium mt-1 flex items-center gap-1.5">
                                    <i data-lucide="phone" class="text-slate-400 h-3.5 w-3.5"></i> {{ $student->phone_number ?? 'N/A' }}
                                    @if($student->alternate_mobile_number)
                                        <span class="text-slate-400">/ {{ $student->alternate_mobile_number }}</span>
                                    @endif
                                </div>
                                @if($student->email)
                                    <div class="text-xs text-slate-500 font-medium mt-0.5 flex items-center gap-1.5">
                                        <i data-lucide="mail" class="text-slate-400 h-3.5 w-3.5"></i> {{ $student->email }}
                                    </div>
                                @endif
                                <div class="text-xs text-slate-500 font-medium mt-2 flex flex-wrap items-center gap-2">
                                    <span class="bg-slate-100 text-slate-700 font-semibold px-2 py-0.5 rounded text-[11px]">{{ strtoupper($student->gender ?? '-') }}</span>
                                    <span class="bg-blue-50 text-blue-700 font-semibold px-2 py-0.5 rounded text-[11px]">{{ strtoupper($student->category ?? '-') }}</span>
                                    @if($student->dob)
                                        <span class="text-slate-400 text-[11px]">DOB: {{ $student->dob instanceof \Carbon\Carbon ? $student->dob->format('d-m-Y') : $student->dob }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1.5 items-start">
                                    <span class="inline-flex items-center text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg">
                                        <i data-lucide="tag" size="12" class="mr-1.5 text-indigo-500"></i> {{ $appliedForDisplay }}
                                    </span>
                                    <span class="inline-flex items-center text-xs font-bold text-teal-700 bg-teal-50 border border-teal-100 px-2.5 py-1 rounded-lg">
                                        <i data-lucide="map-pin" size="12" class="mr-1.5 text-teal-500"></i> Center: {{ $examCenterDisplay }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">{{ $address->present_district ?? $address->permanent_district ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-400 uppercase font-bold mt-1">{{ $address->present_state ?? $address->permanent_state ?? '' }}</div>
                                @if($address->present_pin_code)
                                    <div class="text-xs text-slate-400 mt-0.5">PIN: {{ $address->present_pin_code }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide flex items-center">
                                        <i data-lucide="check-circle" size="12" class="mr-1"></i> Submitted
                                    </span>

                                    @if($app->payment_status == 'paid')
                                        <span class="bg-emerald-100 text-emerald-700 px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wide flex items-center">
                                            <i data-lucide="check" size="11" class="mr-1"></i> Paid
                                        </span>
                                    @endif

                                    @if($app->approval_status && in_array(strtolower($app->approval_status), ['approved', 'rejected']))
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide {{ strtolower($app->approval_status) === 'approved' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ ucfirst($app->approval_status) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('administration.applications.detail', $app->id) }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="View Full Details">
                                    <i data-lucide="eye" size="18"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <i data-lucide="search-x" size="48" class="mb-4 text-slate-300"></i>
                                    <p class="text-lg font-bold text-slate-600 mb-1">No Submitted Applications Found</p>
                                    <p class="text-sm">Try adjusting your filters or click "Clear Filters" to view all submitted applications.</p>
                                    <a href="{{ route('administration.reports') }}" class="mt-4 px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold hover:bg-blue-100 transition-colors">
                                        Reset All Filters
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $applications->links() }}
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    /* Tailwind tweaks for TomSelect */
    .ts-control {
        border-color: #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.625rem 1rem;
        box-shadow: none;
        background-color: #f8fafc;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .ts-control.focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    .ts-dropdown {
        border-color: #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        font-size: 0.875rem;
    }
    .ts-dropdown .option {
        padding: 0.5rem 1rem;
    }
    .ts-dropdown .active {
        background-color: #eff6ff;
        color: #1d4ed8;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('select.searchable-select').forEach((el) => {
            new TomSelect(el, {
                create: false,
                sortField: false,
                allowEmptyOption: true
            });
        });
    });
</script>
@endpush

@endsection
