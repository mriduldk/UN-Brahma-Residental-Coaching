<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $application->application_form->application_name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
        
        @page { margin: 15px; }
        body { font-family: 'Roboto', 'Helvetica', sans-serif; font-size: 10px; color: #333; line-height: 1.15; }
        
        .container { padding: 5px; position: absolute; top:0; left:0; right:0; bottom:0; }
        
        .header-table { width: 100%; border-bottom: 2px solid #ccc; padding-bottom: 8px; margin-bottom: 12px; }
        .header-logo { width: 60px; vertical-align: top; }
        .header-content { text-align: center; vertical-align: middle; }
        .header-photo { width: 100px; text-align: right; vertical-align: top; }
        
        .org-name { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #555; margin-bottom: 2px; }
        .inst-name { font-size: 16px; font-weight: bold; text-transform: uppercase; color: #000; margin-bottom: 4px; }
        .app-name { font-size: 11px; font-weight: bold; display: inline-block; padding: 3px 12px; background: #fff; border-radius: 4px; border: 1px solid #000; text-transform: uppercase; }
        
        .photo-box { 
            width: 80px; height: 100px; 
            border: 1px solid #000; 
            margin-left: auto;
            display: flex; align-items: center; justify-content: center;
            background: #f9f9f9;
        }
        .passport-photo { width: 100%; height: 100%; object-fit: cover; }
        
        .meta-table { width: 100%; margin-bottom: 15px; border-collapse: collapse; }
        .meta-table td { padding: 4px; vertical-align: middle; }
        
        .section-header { 
            background: #f1f5f9; color: #334155; 
            font-size: 10px; font-weight: bold; text-transform: uppercase; 
            padding: 4px 8px; margin-top: 10px; margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1; border-top: 1px solid #cbd5e1;
            letter-spacing: 0.5px;
        }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; table-layout: fixed; border: 1px solid #333; }
        .data-table td { padding: 6px 8px; vertical-align: top; border: 1px solid #333; }
        
        .label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #64748b; display: block; margin-bottom: 1px; }
        .value { font-size: 10px; font-weight: 600; color: #0f172a; word-wrap: break-word; }
        
        .bordered-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; border: 1px solid #333; }
        .bordered-table th, .bordered-table td { border: 1px solid #333; padding: 6px 8px; text-align: left; font-size: 9px; }
        .bordered-table th { background: #f8fafc; color: #000; font-weight: bold; text-transform: uppercase; font-size: 8px; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; background: #fff; }
        
        .signature-section { margin-top: 25px; page-break-inside: avoid; }
        .declaration-text { font-size: 9px; color: #444; text-align: justify; line-height: 1.35; margin-bottom: 15px; border: 1px solid #eee; padding: 8px; background: #fcfcfc; border-radius: 4px; }
        
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .status-pending { background: #fff1f2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Table for Layout Control -->
        <table class="header-table" style="border-bottom: 2px solid #000; padding-bottom: 15px;">
            <tr>
                <td class="header-logo" align="center" style="width: 20%; vertical-align: middle;">
                    @php $logoEmPath = public_path('image/logo/logo-em.png'); @endphp
                    @if(file_exists($logoEmPath))
                        <img src="{{ $logoEmPath }}" style="height: 75px; width: auto; max-width: 100%;">
                    @else
                        <!-- Fallback -->
                        <span style="font-size: 10px; color: #999;">Emblem</span>
                    @endif
                </td>
                <td class="header-content" align="center" style="width: 60%; vertical-align: middle;">
                    <div style="font-size: 16px; font-weight: 900; letter-spacing: 1px; color: #000; margin-bottom: 5px; text-transform: uppercase;">
                        GOVERNMENT OF BODOLAND TERRITORIAL COUNCIL
                    </div>
                    <div class="inst-name">{{ $application->application_form->institution->institution_name }}</div>
                    <div class="app-name">APPLICATION FORM FOR {{ $application->application_form->application_name }}</div>
                    <div style="font-size: 11px; font-weight: bold; color: #444; margin-top: 5px;">SESSION: {{ $application->application_form->session }}</div>
                </td>
                <td class="header-photo" align="center" style="width: 20%; vertical-align: middle;">
                    <div class="photo-box" style="width: 90px; height: 110px; border: 1px solid #000; margin-left: auto;">
                        @if($application->application_documents && $application->application_documents->photo)
                            @php $photoPath = storage_path('app/public/' . $application->application_documents->photo); @endphp
                            @if(file_exists($photoPath))
                                <img src="{{ $photoPath }}" class="passport-photo">
                            @else
                               <span style="font-size:8px; color:#999; display:block; padding-top:45px; text-align:center;">Photo N/A</span>
                            @endif
                        @else
                            <span style="font-size:8px; color:#999; display:block; padding-top:45px; text-align:center;">Affix Photo</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Meta Information (Below header to avoid overlap) -->
        <table class="meta-table">
            <tr>
                <td width="22%">
                    <span class="label">Application No.</span>
                    <span class="value" style="font-size: 11px; color: #2563eb;">{{ $application->application_code }}</span>
                </td>
                <td width="28%">
                    <span class="label">Applied For</span>
                    <span class="value" style="font-size: 11px; color: #1e1b4b; font-weight: bold;">{{ strtoupper($application->applied_for ?? $application->application_student?->applied_for ?? '-') }}</span>
                </td>
                <td width="25%">
                    <span class="label">Exam Center</span>
                    <span class="value" style="font-size: 11px; color: #047857; font-weight: bold;">{{ strtoupper($application->examination_center ?? $application->application_student?->examination_center ?? '-') }}</span>
                </td>
                <td width="25%" align="right">
                    <span class="label">Application Date</span>
                    <span class="value">{{ ($application->submitted_at ?? $application->created_at)->format('d/m/Y, h:i A') }}</span>
                    @if($application->application_form->is_application_fee_required)
                     <br><span class="status-badge {{ $application->payment_status == 'paid' ? 'status-paid' : 'status-pending' }}" style="display: inline-block; margin-top: 4px;">
                        Payment: {{ strtoupper($application->payment_status) }}
                    </span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Payment Info (New Request) -->
        @if($application->application_form->is_application_fee_required)
        <div class="section-header">Payment Information</div>
        <table class="data-table">
             @php 
                $txn = $application->payment_transactions->where('status', 'captured')->last();
             @endphp
             <tr>
                <td width="25%"><span class="label">Amount</span><span class="value">{{ $txn ? number_format($txn->amount, 2) : 'N/A' }}</span></td>
                <td width="35%"><span class="label">Transaction ID</span><span class="value">{{ $txn ? $txn->razorpay_payment_id : '-' }}</span></td>
                <td width="20%"><span class="label">Date</span><span class="value">{{ $txn ? $txn->created_at->format('d/m/Y') : '-' }}</span></td>
                <td width="20%"><span class="label">Method</span><span class="value">{{ $txn ? strtoupper($txn->method) : '-' }}</span></td>
            </tr>
        </table>
        @endif

        <!-- Personal Details -->
        <div class="section-header">Personal Details</div>
        <table class="data-table">
            @php 
                $s = $application->application_student; 
                $p = $application->application_parent;
                $age = $s->dob ? \Carbon\Carbon::parse($s->dob)->age . ' Years' : '-';
            @endphp
            <tr>
                <td width="33.33%"><span class="label">Full Name (as per HSLC Certificate)</span><span class="value">{{ strtoupper($s->full_name) }}</span></td>
                <td width="33.33%"><span class="label">Father's Name</span><span class="value">{{ strtoupper($p->father_name ?? '-') }}</span></td>
                <td width="33.33%"><span class="label">Mother's Name</span><span class="value">{{ strtoupper($p->mother_name ?? '-') }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Gender</span><span class="value">{{ strtoupper($s->gender) }}</span></td>
                <td><span class="label">Date of Birth</span><span class="value">{{ $s->dob ? \Carbon\Carbon::parse($s->dob)->format('d-m-Y') : '-' }}</span></td>
                <td><span class="label">Age (Auto Calculate)</span><span class="value">{{ $age }}</span></td>
            </tr>
            <tr>
                <td><span class="label">Category</span><span class="value">{{ strtoupper($s->category ?? '-') }}</span></td>
                <td><span class="label">Community</span><span class="value">{{ strtoupper($s->community ?? $s->nationality ?? '-') }}</span></td>
                <td><span class="label">Mobile Number</span><span class="value">{{ $s->phone_number }}</span></td>
            </tr>
            <tr>
                <td><span class="label">What’s App Number</span><span class="value">{{ $s->alternate_mobile_number ?? '-' }}</span></td>
                <td colspan="2"><span class="label">Email Address</span><span class="value">{{ $s->email ?? $application->student->email ?? '-' }}</span></td>
            </tr>
        </table>

        <!-- Address -->
        <div class="section-header">Address Details</div>
        <table class="data-table">
             @php $a = $application->application_address; @endphp
            <tr>
                <td width="50%">
                    <span class="label">Permanent Address</span>
                    <span class="value">
                        {{ $a->permanent_village_town }}<br>
                        @if($a->permanent_city)P.O.: {{ $a->permanent_city }}, @endif @if($a->permanent_country)P.S.: {{ $a->permanent_country }}, @endif Dist: {{ $a->permanent_district }}@if($a->permanent_street), BTCLA Constituency: {{ $a->permanent_street }}@endif<br>
                        {{ $a->permanent_state }} - {{ $a->permanent_pin_code }}
                    </span>
                </td>
                <td width="50%">
                    <span class="label">Current Address / Communication Address</span>
                    <span class="value">
                        @php
                            $presVillage = !empty($a->present_village_town) ? $a->present_village_town : $a->permanent_village_town;
                            $presStreet = !empty($a->present_street) ? $a->present_street : $a->permanent_street;
                            $presCity = !empty($a->present_city) ? $a->present_city : $a->permanent_city;
                            $presCountry = !empty($a->present_country) ? $a->present_country : $a->permanent_country;
                            $presDistrict = !empty($a->present_district) ? $a->present_district : $a->permanent_district;
                            $presState = !empty($a->present_state) ? $a->present_state : $a->permanent_state;
                            $presPin = !empty($a->present_pin_code) ? $a->present_pin_code : $a->permanent_pin_code;
                        @endphp
                        {{ $presVillage }}<br>
                        @if($presCity)P.O.: {{ $presCity }}, @endif @if($presCountry)P.S.: {{ $presCountry }}, @endif Dist: {{ $presDistrict }}@if($presStreet), BTCLA Constituency: {{ $presStreet }}@endif<br>
                        {{ $presState }} - {{ $presPin }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- Academic Qualifications -->
        <div class="section-header">Academic Qualifications</div>
        <table class="bordered-table">
            <thead>
                <tr>
                    <th width="30%">Exam Passed</th>
                    <th width="30%">School/College/Institute Name</th>
                    <th width="15%" align="center">Year</th>
                    <th width="15%" align="center">% / Grade</th>
                    <th width="10%" align="center">Division</th>
                </tr>
            </thead>
            <tbody>
                @forelse($application->application_academic_qualifications as $q)
                <tr>
                    <td>{{ $q->examination }}</td>
                    <td>{{ $q->board_university }}</td>
                    <td align="center">{{ $q->passing_year }}</td>
                    <td align="center">{{ $q->percentage_grade }}</td>
                    <td align="center">{{ $q->division }}</td>
                </tr>
                @empty
                <tr><td colspan="5" align="center" style="color: #999;">No academic records found</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Provided Documents -->
        <div class="section-header">Provided Documents</div>
        <table class="data-table">
            <tbody>
                @php
                    $docsList = [
                        'photo' => 'Passport Size Photograph',
                        'signature' => 'Signature',
                        'hslc_admit_card' => 'HSLC Admit Card',
                        'hslc_marksheet' => 'HSLC Marksheet',
                        'hslc_pass_certificate' => 'HSLC Certificate',
                        'hs_marksheet' => 'HSSLC Marksheet',
                        'hs_pass_certificate' => 'HSSLC Certificate',
                        'graduation_marksheet' => 'Graduation Mark Sheets',
                        'graduation_pass_certificate' => 'Graduation Degree Certificate',
                        'post_graduation_marksheet' => 'Post Graduation Mark Sheets',
                        'post_graduation_pass_certificate' => 'Post Graduation Certificate',
                        'caste_certificate' => 'Caste Certificate',
                        'btc_residential_certificate' => 'Permanent Resident Certificate',
                        'aadhaar_card' => 'Aadhaar Card',
                        'income_certificate' => 'Income Certificate',
                        'address_proof' => 'Address Proof'
                    ];
                    $docs = $application->application_documents;
                    $providedDocs = [];
                    if($docs) {
                        foreach($docsList as $key => $label) {
                            if(!empty($docs->$key)) {
                                $providedDocs[] = $label;
                            }
                        }
                    }
                    $chunks = array_chunk($providedDocs, 3);
                @endphp
                @forelse($chunks as $chunk)
                    <tr>
                        @foreach($chunk as $doc)
                            <td width="33.33%" style="vertical-align: middle;">
                                <span style="display:inline-block; width:10px; height:10px; border:1px solid #000; text-align:center; line-height:10px; margin-right:4px; font-size:9px; font-family: 'DejaVu Sans', sans-serif;">&#10003;</span> 
                                <span class="value">{{ $doc }}</span>
                            </td>
                        @endforeach
                        @for($i = count($chunk); $i < 3; $i++)
                            <td width="33.33%"></td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" align="center"><span class="value" style="color: #999;">No documents provided</span></td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Declaration & Signature -->
        <div class="signature-section">
            <div class="section-header">Declaration</div>
            <div class="declaration-text">
                I hereby declare that all the information provided by me in this application is true, complete, and correct to the best of my knowledge and belief. I understand that in the event of any information being found false or incorrect at any stage, my candidature/appointment is liable to be cancelled/terminated.
            </div>

            <table width="100%" style="margin-top: 20px;">
                <tr>
                    <td width="40%" valign="bottom">
                        <div style="margin-bottom: 25px;">
                            <span class="label" style="display:inline;">Date:</span> <span class="value" style="display:inline;">{{ date('d-m-Y') }}</span>
                        </div>
                        <div>
                            <span class="label" style="display:inline;">Place:</span> <span class="value" style="display:inline;">___________________</span>
                        </div>
                    </td>
                    <td width="60%" align="right" valign="bottom">
                        <div style="width: 200px; text-align: center; float: right;">
                             @if($application->application_documents && $application->application_documents->signature)
                                @php $sigPath = storage_path('app/public/' . $application->application_documents->signature); @endphp
                                @if(file_exists($sigPath))
                                    <img src="{{ $sigPath }}" style="height: 40px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto;">
                                @else
                                   <div style="height: 45px;"></div>
                                @endif
                            @else
                                <div style="height: 45px;"></div>
                            @endif
                            <div style="border-top: 1px solid #333; padding-top: 5px; font-size: 10px; font-weight: bold;">
                                Signature of Applicant
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="footer">
        Generated on {{ date('d M Y, h:i A') }} • Downloaded by: {{ auth()->user()->name ?? $application->application_student?->full_name ?? '-' }} • Application Number: {{ $application->application_code }}
    </div>
</body>
</html>
