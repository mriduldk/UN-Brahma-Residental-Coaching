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
            <p class="text-slate-500 font-medium mt-1">Generate filtered reports based on applicant demographics, location, and documentation.</p>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 rounded-t-2xl">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-widest flex items-center">
                <i data-lucide="filter" size="16" class="mr-2 text-slate-400"></i> Advanced Filters
            </h2>
        </div>
        <div class="p-6">
            <form action="{{ route('administration.reports') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Search Applicant</label>
                        <div class="relative">
                            <i data-lucide="search" size="18" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or phone number..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white transition-colors text-sm font-medium">
                        </div>
                    </div>

                    <!-- District Dropdown -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">District</label>
                        @php
                            $assamDistricts = [
                                'Baksa', 'Barpeta', 'Bajali', 'Biswanath', 'Bongaigaon', 'Cachar', 'Charaideo', 
                                'Chirang', 'Darrang', 'Dhemaji', 'Dhubri', 'Dibrugarh', 'Dima Hasao', 'Goalpara', 
                                'Golaghat', 'Hailakandi', 'Hojai', 'Jorhat', 'Kamrup', 'Kamrup Metropolitan', 
                                'Karbi Anglong', 'Karimganj', 'Kokrajhar', 'Lakhimpur', 'Majuli', 'Morigaon', 
                                'Nagaon', 'Nalbari', 'Sivasagar', 'Sonitpur', 'South Salmara-Mankachar', 
                                'Tamulpur', 'Tinsukia', 'Udalguri', 'West Karbi Anglong'
                            ];
                        @endphp
                        <select name="district" class="searchable-select w-full">
                            <option value="">All Districts</option>
                            @foreach($assamDistricts as $dist)
                                <option value="{{ $dist }}" {{ request('district') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender Dropdown -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Gender</label>
                        <select name="gender" class="searchable-select w-full">
                            <option value="">All Genders</option>
                            <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <!-- Category Dropdown -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                        <select name="category" class="searchable-select w-full">
                            <option value="">All Categories</option>
                            <option value="General" {{ request('category') == 'General' ? 'selected' : '' }}>General</option>
                            <option value="OBC" {{ request('category') == 'OBC' ? 'selected' : '' }}>OBC</option>
                            <option value="MOBC" {{ request('category') == 'MOBC' ? 'selected' : '' }}>MOBC</option>
                            <option value="SC" {{ request('category') == 'SC' ? 'selected' : '' }}>SC</option>
                            <option value="ST" {{ request('category') == 'ST' ? 'selected' : '' }}>ST</option>
                        </select>
                    </div>

                    <!-- Trade/Form Dropdown -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Trade / Form</label>
                        <select name="form_id" class="searchable-select w-full">
                            <option value="">All Trades</option>
                            @foreach($forms as $form)
                                <option value="{{ $form->id }}" {{ request('form_id') == $form->id ? 'selected' : '' }}>{{ $form->application_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Document Type Dropdown -->
                    @php
                        $docLabels = [
                            'photo' => 'Photograph', 'signature' => 'Signature', 'hslc_marksheet' => 'HSLC Marksheet',
                            'hs_marksheet' => 'HS Marksheet', 'graduation_marksheet' => 'Graduation Marksheet',
                            'post_graduation_marksheet' => 'PG Marksheet', 'hslc_pass_certificate' => 'HSLC Certificate',
                            'hs_pass_certificate' => 'HS Certificate', 'graduation_pass_certificate' => 'Graduation Certificate',
                            'post_graduation_pass_certificate' => 'PG Certificate', 'hslc_admit_card' => 'HSLC Admit Card',
                            'hs_admit_card' => 'HS Admit Card', 'graduation_admit_card' => 'Graduation Admit Card',
                            'post_graduation_admit_card' => 'PG Admit Card', 'transfer_certificate' => 'Transfer Certificate',
                            'caste_certificate' => 'Caste Certificate', 'income_certificate' => 'Income Certificate',
                            'address_proof' => 'Address Proof', 'aadhaar_card' => 'Aadhaar Card', 'passport' => 'Passport',
                            'bed_parangat_marksheet' => 'B.Ed./Parangat Mark Sheets', 'bed_parangat_certificate' => 'B.Ed./Parangat Certificate',
                            'tet_certificate' => 'TET Certificate', 'pwd_certificate' => 'PwD Certificate',
                            'ex_serviceman_certificate' => 'Ex-Serviceman Certificate', 'ncc_certificate_doc' => 'NCC Certificate',
                            'sports_certificate' => 'Sports Certificate', 'cultural_certificate' => 'Fine Arts/Cultural Certificate',
                            'btc_residential_certificate' => 'BTC Residential Certificate', 'language_diploma_certificate' => 'Language Diploma Certificate',
                        ];
                    @endphp
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Document Criteria</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <select name="document_type" class="searchable-select w-full sm:w-2/3">
                                <option value="">Select Document to Filter</option>
                                @foreach($docLabels as $key => $label)
                                    <option value="{{ $key }}" {{ request('document_type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <select name="document_status" class="searchable-select w-full sm:w-1/3">
                                <option value="">Select Status</option>
                                <option value="provided" {{ request('document_status') == 'provided' ? 'selected' : '' }}>Provided</option>
                                <option value="not_provided" {{ request('document_status') == 'not_provided' ? 'selected' : '' }}>Not Provided</option>
                            </select>
                        </div>
                    </div>

                    <!-- Payment Status Dropdown -->
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Payment Status</label>
                        <select name="payment_status" class="searchable-select w-full">
                            <option value="">Paid / Exempted</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid Only</option>
                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                        </select>
                    </div>


                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('administration.reports') }}" class="px-6 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">Clear Filters</a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200 flex items-center">
                        <i data-lucide="filter" size="16" class="mr-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
            <h2 class="text-base font-bold text-slate-800">Report Results <span class="ml-2 bg-blue-100 text-blue-700 text-xs py-0.5 px-2 rounded-full font-black">{{ $applications->total() }} Found</span></h2>
        </div>
        
        <div class="overflow-x-auto min-h-[400px]">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Application ID</th>
                        <th class="px-6 py-4">Applicant Profile</th>
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
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-slate-700 font-bold bg-slate-100 px-2 py-1 rounded">{{ $app->application_code }}</span>
                                <div class="mt-2 text-[10px] uppercase font-bold text-slate-400">{{ $app->application_form->application_name ?? 'Form' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $student->full_name ?? 'N/A' }}</div>
                                <div class="text-xs text-slate-500 font-medium mt-1 flex items-center gap-1.5">
                                    <i data-lucide="phone" class="text-slate-400 h-4 w-4"></i> {{ $student->phone_number ?? 'N/A' }}
                                </div>
                                <div class="text-xs text-slate-500 font-medium mt-1.5 flex items-center gap-2">
                                    <span class="bg-slate-100 px-1.5 py-0.5 rounded">{{ strtoupper($student->gender ?? '-') }}</span>
                                    <span class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded">{{ strtoupper($student->category ?? '-') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">{{ $address->present_district ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold mt-1">{{ $address->present_state ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-2">
                                    @if($app->payment_status == 'paid')
                                        <span class="bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide flex w-fit items-center"><i data-lucide="check" size="12" class="mr-1"></i> Paid</span>
                                    @else
                                        <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide flex w-fit items-center"><i data-lucide="clock" size="12" class="mr-1"></i> Unpaid</span>
                                    @endif

                                    <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide flex w-fit items-center"><i data-lucide="check-circle" size="12" class="mr-1"></i> Submitted</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('administration.applications.detail', $app->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors" title="View Detail">
                                    <i data-lucide="eye" size="16"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <i data-lucide="search-x" size="48" class="mb-4 text-slate-300"></i>
                                    <p class="text-lg font-bold text-slate-600 mb-1">No Applications Found</p>
                                    <p class="text-sm">Try adjusting your filters to find what you're looking for.</p>
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
                sortField: {
                    field: "text",
                    direction: "asc"
                }
            });
        });
    });
</script>
@endpush

@endsection
