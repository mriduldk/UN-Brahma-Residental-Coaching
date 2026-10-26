<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@verbatim
<!--[if gte mso 9]>
<xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Attendance Sheet</x:Name>
    <x:WorksheetOptions>
     <x:DisplayGridlines/>
     <x:Print>
      <x:ValidPrinterInfo/>
      <x:PaperSizeIndex>9</x:PaperSizeIndex>
      <x:Scale>100</x:Scale>
     </x:Print>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
@endverbatim
<style>
  body {
    font-family: Calibri, 'Segoe UI', Arial, sans-serif;
  }
  .title-main {
    font-size: 16pt;
    font-weight: bold;
    text-align: center;
    background-color: #0f172a;
    color: #ffffff;
    height: 40px;
    vertical-align: middle;
  }
  .title-center {
    font-size: 13pt;
    font-weight: bold;
    text-align: center;
    background-color: #f1f5f9;
    color: #0f172a;
    height: 32px;
    vertical-align: middle;
    border-bottom: 2px solid #0f172a;
  }
  .title-meta {
    font-size: 10pt;
    text-align: center;
    color: #475569;
    height: 24px;
    vertical-align: middle;
    font-style: italic;
  }
  .table-header th {
    background-color: #1e293b;
    color: #ffffff;
    font-size: 11pt;
    font-weight: bold;
    border: 1px solid #000000;
    text-align: center;
    vertical-align: middle;
    height: 32px;
  }
  .data-row td {
    border: 1px solid #000000;
    font-size: 10.5pt;
    vertical-align: middle;
    height: 30px;
  }
  .text-center {
    text-align: center;
  }
  .text-left {
    text-align: left;
    padding-left: 8px;
  }
  .roll-cell {
    text-align: center;
    font-weight: bold;
    mso-number-format: "\@";
  }
  .sig-cell {
    text-align: center;
    background-color: #ffffff;
  }
  .summary-row td {
    font-size: 10pt;
    font-weight: bold;
    height: 28px;
    vertical-align: middle;
  }
  .signature-row td {
    height: 60px;
    vertical-align: bottom;
    font-weight: bold;
    font-size: 10.5pt;
  }
</style>
</head>
<body>
<table>
  <!-- Row 1: Header requested: UN BRAHMA RESIDENTAL COACHING (ENGINEERING) -->
  <tr>
    <td colspan="5" class="title-main">{{ $headerTitle }}</td>
  </tr>

  <!-- Row 2: Header requested: EXAMINATION CENTER : [Center] -->
  <tr>
    <td colspan="5" class="title-center">EXAMINATION CENTER : {{ $centerTitle }}</td>
  </tr>

  <!-- Row 3: Meta details -->
  <tr>
    <td colspan="5" class="title-meta">
      ENTRANCE EXAMINATION 2026 &nbsp;|&nbsp; Date of Exam: Tuesday, 27th October, 2026 &nbsp;|&nbsp; Time: {{ $examTime }} &nbsp;|&nbsp; Total Candidates: {{ $applications->count() }}
    </td>
  </tr>

  <!-- Row 4: Spacer -->
  <tr>
    <td colspan="5" style="height: 12px;"></td>
  </tr>

  <!-- Row 5: Table Header: SL NO, ROLL NO, CANDIDATE NAME, FATHER NAME, SIGNATURE (Blank) -->
  <tr class="table-header">
    <th style="width: 70px;">SL NO</th>
    <th style="width: 140px;">ROLL NO</th>
    <th style="width: 280px;">CANDIDATE NAME</th>
    <th style="width: 260px;">FATHER NAME</th>
    <th style="width: 200px;">SIGNATURE</th>
  </tr>

  <!-- Data Rows -->
  @foreach($applications as $index => $app)
    @php
      $student = $app->application_student;
      $parent = $app->application_parent;
      $candidateName = $student?->full_name ?? $app->student?->name ?? 'N/A';
      $fatherName = $student?->father_name ?? $parent?->father_name ?? 'N/A';
      $rollNo = $app->roll_number ?: $app->roll_no ?: $app->application_code;
    @endphp
    <tr class="data-row">
      <td class="text-center">{{ $index + 1 }}</td>
      <td class="roll-cell">{{ $rollNo }}</td>
      <td class="text-left">{{ strtoupper($candidateName) }}</td>
      <td class="text-left">{{ strtoupper($fatherName) }}</td>
      <td class="sig-cell"></td>
    </tr>
  @endforeach

  <!-- Blank rows -->
  <tr><td colspan="5" style="height: 15px;"></td></tr>

  <!-- Summary row -->
  <tr class="summary-row">
    <td colspan="2" style="border: 1px solid #475569; background-color: #f8fafc; padding-left: 8px;">
      Total Allotted: {{ $applications->count() }}
    </td>
    <td colspan="2" style="border: 1px solid #475569; background-color: #f8fafc; padding-left: 8px;">
      Present: _______ &nbsp;&nbsp;&nbsp;&nbsp; Absent: _______
    </td>
    <td style="border: 1px solid #475569; background-color: #f8fafc;"></td>
  </tr>

  <tr><td colspan="5" style="height: 25px;"></td></tr>

  <!-- Signatures Row -->
  <tr class="signature-row">
    <td colspan="2" style="text-align: left; padding-left: 8px;">
      Invigilator's Signature: ___________________________
    </td>
    <td></td>
    <td colspan="2" style="text-align: right; padding-right: 8px;">
      Center Superintendent Signature with Seal: ___________________________
    </td>
  </tr>
</table>
</body>
</html>
