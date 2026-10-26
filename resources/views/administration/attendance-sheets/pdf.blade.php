<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Attendance Sheet - {{ $centerTitle }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #0f172a;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .header-container {
            width: 100%;
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .emblem-row {
            text-align: center;
            margin-bottom: 4px;
        }

        .emblem-img {
            height: 48px;
            width: auto;
        }

        .gov-title {
            font-size: 9px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .main-title {
            font-size: 13.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 3px 0;
        }

        .center-title-box {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 3px 14px;
            margin: 3px 0 4px 0;
            border-radius: 2px;
        }

        .meta-strip {
            font-size: 8.5px;
            font-weight: bold;
            color: #334155;
            margin-top: 3px;
        }

        /* Attendance Table */
        table.attendance-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 6px;
        }

        table.attendance-table thead {
            display: table-header-group;
        }

        table.attendance-table tr {
            page-break-inside: avoid;
        }

        table.attendance-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #475569;
            padding: 6px 4px;
            text-align: center;
            letter-spacing: 0.4px;
        }

        table.attendance-table td {
            border: 1px solid #475569;
            padding: 4px 6px;
            font-size: 9px;
            vertical-align: middle;
        }

        .col-sl {
            width: 7%;
            text-align: center;
            font-weight: bold;
            color: #334155;
        }

        .col-roll {
            width: 17%;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            color: #0f172a;
            letter-spacing: 0.5px;
        }

        .col-name {
            width: 30%;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
        }

        .col-father {
            width: 25%;
            text-align: left;
            text-transform: uppercase;
            color: #334155;
        }

        .col-signature {
            width: 21%;
            text-align: center;
            height: 38px; /* Room for physical signature */
            background-color: #ffffff;
        }

        .zebra-row {
            background-color: #fafbfc;
        }

        /* Bottom Signatures Block */
        .signatures-block {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .signatures-block td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .sig-box {
            text-align: center;
            font-size: 8.5px;
            color: #334155;
        }

        .sig-line {
            width: 180px;
            border-top: 1px dashed #475569;
            margin: 30px auto 4px auto;
        }

        .summary-box {
            font-size: 8.5px;
            font-weight: bold;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            background: #f8fafc;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-container">
        @if(!empty($emblemPath) && file_exists($emblemPath))
            <div class="emblem-row">
                <img src="{{ $emblemPath }}" class="emblem-img" alt="BTC Emblem">
            </div>
        @endif

        <div class="gov-title">Department of Education &bull; Bodoland Territorial Council</div>
        
        <!-- Header requested: UN BRAHMA RESIDENTAL COACHING (ENGINEERING) -->
        <div class="main-title">{{ $headerTitle }}</div>

        <!-- Header requested: EXAMINATION CENTER : -->
        <div>
            <span class="center-title-box">EXAMINATION CENTER : {{ $centerTitle }}</span>
        </div>

        <div class="meta-strip">
            ATTENDANCE &amp; SIGNATURE ROLL SHEET &bull; ENTRANCE EXAMINATION 2026<br>
            Date of Exam: <strong>Tuesday, 27th October, 2026</strong> &nbsp;|&nbsp;
            Time: <strong>{{ $examTime }}</strong> &nbsp;|&nbsp;
            Total Candidates: <strong>{{ $applications->count() }}</strong>
        </div>
    </div>

    <!-- Attendance Table -->
    <table class="attendance-table">
        <thead>
            <tr>
                <th class="col-sl">SL NO</th>
                <th class="col-roll">ROLL NO</th>
                <th class="col-name">CANDIDATE NAME</th>
                <th class="col-father">FATHER NAME</th>
                <th class="col-signature">SIGNATURE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $index => $app)
                @php
                    $student = $app->application_student;
                    $parent = $app->application_parent;
                    $candidateName = $student?->full_name ?? $app->student?->name ?? 'N/A';
                    $fatherName = $student?->father_name ?? $parent?->father_name ?? 'N/A';
                    $rollNo = $app->roll_number ?: $app->roll_no ?: $app->application_code;
                @endphp
                <tr class="{{ $index % 2 === 1 ? 'zebra-row' : '' }}">
                    <td class="col-sl">{{ $index + 1 }}</td>
                    <td class="col-roll">{{ $rollNo }}</td>
                    <td class="col-name">{{ strtoupper($candidateName) }}</td>
                    <td class="col-father">{{ strtoupper($fatherName) }}</td>
                    <td class="col-signature"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 25px; color: #64748b; font-weight: bold;">
                        No candidates allotted for this center / stream.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Summary and Signatures -->
    <table class="signatures-block">
        <tr>
            <td style="width: 36%;">
                <div class="summary-box">
                    <div>Total Allotted: <strong>{{ $applications->count() }}</strong></div>
                    <div style="margin-top: 3px;">Total Present: <strong>_______</strong> &nbsp;|&nbsp; Absent: <strong>_______</strong></div>
                </div>
            </td>
            <td style="width: 32%;">
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <div style="font-weight: bold; text-transform: uppercase;">Invigilator's Signature</div>
                    <div style="color: #64748b; font-size: 7.5px;">Name: __________________________</div>
                </div>
            </td>
            <td style="width: 32%;">
                <div class="sig-box">
                    <div class="sig-line"></div>
                    <div style="font-weight: bold; text-transform: uppercase;">Center Superintendent</div>
                    <div style="color: #64748b; font-size: 7.5px;">Signature with Official Seal</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- DomPDF Script for Page Numbering -->
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}  |  UN Brahma Residential Free Coaching Examination Attendance Sheet";
            $size = 8;
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $width = $fontMetrics->getTextWidth($text, $font, $size);
            $pdf->page_text($pdf->get_width() - $width - 34, $pdf->get_height() - 25, $text, $font, $size, array(0.4, 0.4, 0.4));
        }
    </script>

</body>
</html>
