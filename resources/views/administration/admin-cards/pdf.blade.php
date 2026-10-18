<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Admit Card - {{ $application->application_code }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .card-container {
            border: 2px solid #0f172a;
            padding: 12px;
            position: relative;
            background: #ffffff;
        }

        .inner-border {
            border: 1px solid #94a3b8;
            padding: 12px;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header-logo {
            width: 75px;
            vertical-align: middle;
            text-align: left;
        }

        .header-text {
            text-align: center;
            vertical-align: middle;
        }

        .gov-title {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
        }

        .dept-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .sub-header {
            font-size: 9px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }

        .doc-badge {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 18px;
            border-radius: 3px;
            margin-top: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .session-text {
            font-size: 8.5px;
            color: #64748b;
            font-weight: bold;
            margin-top: 3px;
        }

        /* Roll Number Strip */
        .roll-strip {
            width: 100%;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            margin-bottom: 12px;
            border-collapse: collapse;
        }

        .roll-strip td {
            padding: 6px 10px;
            vertical-align: middle;
        }

        .roll-label {
            font-size: 9px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }

        .roll-val {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a8a;
            letter-spacing: 1px;
        }

        /* Two Column Layout: Details & Photo/QR */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .details-col {
            width: 76%;
            vertical-align: top;
            padding-right: 10px;
        }

        .photo-col {
            width: 24%;
            vertical-align: top;
            text-align: center;
        }

        /* Details Grid */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e1;
        }

        .info-table th, .info-table td {
            border: 1px solid #cbd5e1;
            padding: 4.5px 7px;
            font-size: 9px;
        }

        .info-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-align: left;
            width: 28%;
            text-transform: uppercase;
            font-size: 8px;
        }

        .info-table td {
            color: #0f172a;
            font-weight: 600;
        }

        /* Photo & Signature Boxes */
        .photo-box {
            width: 110px;
            height: 130px;
            border: 1.5px solid #0f172a;
            margin: 0 auto 6px auto;
            background: #f8fafc;
            overflow: hidden;
            text-align: center;
        }

        .photo-img {
            width: 110px;
            height: 130px;
            object-fit: cover;
        }

        .photo-placeholder {
            padding-top: 45px;
            font-size: 8px;
            color: #94a3b8;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .signature-box {
            width: 110px;
            height: 38px;
            border: 1px solid #94a3b8;
            margin: 0 auto;
            background: #ffffff;
            overflow: hidden;
            text-align: center;
        }

        .signature-img {
            width: 100px;
            height: 34px;
            object-fit: contain;
            margin-top: 2px;
        }

        .signature-placeholder {
            padding-top: 12px;
            font-size: 7.5px;
            color: #94a3b8;
        }

        /* Exam Schedule & Venue Section */
        .section-heading {
            background: #1e293b;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 8px;
            margin-top: 10px;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .exam-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #0f172a;
            margin-bottom: 10px;
        }

        .exam-table th, .exam-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }

        .exam-table th {
            background: #f1f5f9;
            color: #334155;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .exam-table td {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
        }

        .venue-highlight {
            color: #1e3a8a;
            font-size: 10px;
            font-weight: bold;
        }

        /* Signatures Section */
        .sign-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 12px;
        }

        .sign-grid td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 4px;
        }

        .sign-line {
            border-top: 1px solid #475569;
            margin: 0 10px 4px 10px;
        }

        .sign-label {
            font-size: 8px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }

        /* Instructions */
        .instructions-box {
            border: 1px solid #cbd5e1;
            background: #fafafa;
            padding: 8px 10px;
            margin-top: 8px;
        }

        .instructions-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 4px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 3px;
        }

        .instructions-list {
            margin: 0;
            padding-left: 14px;
            font-size: 7.8px;
            color: #334155;
            line-height: 1.35;
        }

        .instructions-list li {
            margin-bottom: 2px;
        }

        .footer-note {
            text-align: center;
            font-size: 7.5px;
            color: #64748b;
            margin-top: 8px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="card-container">
        <div class="inner-border">
            
            <!-- Header Table -->
            <table class="header-table">
                <tr>
                    <td class="header-logo">
                        @if(!empty($emblemPath) && file_exists($emblemPath))
                            <img src="{{ $emblemPath }}" style="height: 65px; width: auto;">
                        @endif
                    </td>
                    <td class="header-text">
                        <div class="gov-title">Bodoland Territorial Council</div>
                        <div class="dept-title">Directorate of Education, Kokrajhar, BTC</div>
                        <div class="sub-header">UPENDRA NATH BRAHMA RESIDENTIAL FREE COACHING (SESSION 2026-2027)</div>
                        <div><span class="doc-badge">Admit Card</span></div>
                        <div class="session-text">Written Screening / Entrance Examination</div>
                    </td>
                    <td style="width: 75px; text-align: right; vertical-align: middle;">
                        @if(!empty($qrCodeSvg))
                            <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" style="width: 65px; height: 65px; border: 1px solid #cbd5e1; padding: 2px; background: #fff;">
                        @endif
                    </td>
                </tr>
            </table>

            <!-- Application ID Strip -->
            <table class="roll-strip">
                <tr>
                    <td style="width: 50%;">
                        <span class="roll-label">Application ID: </span>
                        <span class="roll-val">{{ $application->application_code }}</span>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <span class="roll-label">Course / Stream Applied For: </span>
                        <span style="font-size: 11px; font-weight: bold; color: #1e3a8a; text-transform: uppercase;">
                            {{ $application->applied_for_display }}
                        </span>
                    </td>
                </tr>
            </table>

            <!-- Main Content Area: Details on Left, Photo & Signature on Right -->
            <table class="content-table">
                <tr>
                    <td class="details-col">
                        <table class="info-table">
                            <tr>
                                <th>Candidate Name</th>
                                <td colspan="3" style="font-size: 10.5px; text-transform: uppercase; color: #0f172a;">
                                    {{ $application->application_student->full_name ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Father's Name</th>
                                <td colspan="3">
                                    {{ $application->application_student->father_name ?? $application->application_parent?->father_name ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Mother's Name</th>
                                <td colspan="3">
                                    {{ $application->application_student->mother_name ?? $application->application_parent?->mother_name ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Date of Birth</th>
                                <td>
                                    @php
                                        $dobVal = $application->application_student->dob ?? null;
                                    @endphp
                                    {{ $dobVal ? \Carbon\Carbon::parse($dobVal)->format('d-m-Y') : 'N/A' }}
                                </td>
                                <th>Gender</th>
                                <td>{{ strtoupper($application->application_student->gender ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <th>Social Category</th>
                                <td>{{ strtoupper($application->application_student->category ?? 'GENERAL') }}</td>
                                <th>Mobile Number</th>
                                <td>{{ $application->application_student->phone_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Email ID</th>
                                <td>{{ $application->application_student->email ?? 'N/A' }}</td>
                                <th>Permanent District</th>
                                <td>{{ strtoupper($application->application_address->permanent_district ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <th>Target Institution</th>
                                <td colspan="3">
                                    {{ $application->application_form->institution->institution_name ?? ($org->organization_name ?? 'BTC Residential Coaching Center') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="photo-col">
                        <div class="photo-box">
                            @php
                                $actualPhoto = (!empty($photoPath) && file_exists($photoPath)) 
                                    ? $photoPath 
                                    : \App\Services\DocumentImageHelper::resolveImageAbsolutePath($application->application_documents?->photo);
                            @endphp
                            @if(!empty($actualPhoto) && file_exists($actualPhoto))
                                <img src="{{ $actualPhoto }}" class="photo-img">
                            @else
                                <div class="photo-placeholder">
                                    Affix Recent<br>Passport Size<br>Photograph
                                </div>
                            @endif
                        </div>
                        <div class="signature-box">
                            @php
                                $actualSig = (!empty($signaturePath) && file_exists($signaturePath)) 
                                    ? $signaturePath 
                                    : \App\Services\DocumentImageHelper::resolveImageAbsolutePath($application->application_documents?->signature);
                            @endphp
                            @if(!empty($actualSig) && file_exists($actualSig))
                                <img src="{{ $actualSig }}" class="signature-img">
                            @else
                                <div class="signature-placeholder">Candidate Signature</div>
                            @endif
                        </div>
                        <div style="font-size: 7px; color: #64748b; font-weight: bold; margin-top: 2px;">CANDIDATE SPECIMEN</div>
                    </td>
                </tr>
            </table>

            <!-- Examination Venue & Schedule -->
            <div class="section-heading">Examination Schedule & Venue Details</div>
            <table class="exam-table">
                <tr>
                    <th style="width: 25%;">Allotted Exam Center</th>
                    <td colspan="3" class="venue-highlight">
                        {{ $application->examination_center_full }}
                    </td>
                </tr>
                <tr>
                    <th style="width: 25%;">Date of Examination</th>
                    <td style="width: 25%;">{{ $application->exam_date }}</td>
                    <th style="width: 25%;">Examination Timing</th>
                    <td style="width: 25%;">{{ $application->exam_time }}</td>
                </tr>
            </table>



            <!-- Instructions for Candidate -->
            <div class="instructions-box">
                <div class="instructions-title">Important Instructions for the Candidate</div>
                <ol class="instructions-list">
                    <li>This Admit Card must be presented at the examination hall along with an <strong>Original Valid Photo Identity Proof</strong> (Aadhaar Card, Voter ID, PAN Card, Passport, or School/College Photo ID).</li>
                    <li>No candidate will be admitted to the Examination Hall after the commencement of the examination under any circumstances.</li>
                    <li>Candidates must use only <strong>Blue or Black Ballpoint Pen</strong> to shade the circles on the OMR sheet and write details on the question paper booklet.</li>
                    <li><strong>Prohibited Items:</strong> Mobile phones, calculators, Bluetooth devices, smartwatches, digital earphones, books, notes, or any electronic equipment are strictly prohibited inside the examination premises.</li>
                    <li>Candidates found using unfair means or involved in impersonation will be immediately disqualified and subjected to disciplinary and criminal prosecution.</li>
                    <li>Do not fold, tear, staple, or put any stray marks on the OMR answer sheet.</li>
                    <li>Candidates are advised to visit the examination center one day in advance to familiarize themselves with the location.</li>
                    <li>Retain this Admit Card safely until the entire screening and admission process is finalized.</li>
                </ol>
            </div>

            <div class="footer-note">
                Generated from the Official http://unb.bodoland.co Portal &bull; Application Code: {{ $application->application_code }} &bull; Issued by Directorate of Education, Kokrajhar, BTC
            </div>

        </div>
    </div>

</body>
</html>
