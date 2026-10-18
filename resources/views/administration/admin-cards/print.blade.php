<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Card - {{ $application->application_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                border: 2px solid #0f172a !important;
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 16px !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 text-slate-800 font-sans">

    <!-- Top Action Bar for screen only -->
    <div class="max-w-4xl mx-auto mb-6 px-4 no-print flex items-center justify-between gap-4">
        <a href="{{ route('administration.admin-cards.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors shadow-sm">
            <i data-lucide="arrow-left" size="16"></i> Back to Admin Cards
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('administration.admin-cards.download', $application->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white rounded-xl font-bold text-sm hover:bg-slate-900 transition-colors shadow-sm">
                <i data-lucide="download" size="16"></i> Download PDF
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-colors shadow-md shadow-blue-200">
                <i data-lucide="printer" size="16"></i> Print Admin Card
            </button>
        </div>
    </div>

    <!-- Printable Card Container -->
    <div class="max-w-4xl mx-auto bg-white border-2 border-slate-900 shadow-xl print-container p-6 rounded-2xl relative overflow-hidden">
        
        <div class="border border-slate-300 p-5 rounded-xl">
            <!-- Header -->
            <div class="flex items-center justify-between border-b-2 border-slate-900 pb-4 mb-4">
                <div class="w-20 shrink-0">
                    @php $emblem = public_path('image/logo/logo-em.png'); @endphp
                    @if(file_exists($emblem))
                        <img src="{{ asset('image/logo/logo-em.png') }}" class="h-16 w-auto object-contain">
                    @else
                        <div class="w-16 h-16 bg-slate-100 rounded-lg flex items-center justify-center font-bold text-slate-400 text-xs">BTC</div>
                    @endif
                </div>
                <div class="text-center flex-1 px-4">
                    <h1 class="text-lg font-black tracking-wide text-slate-900 uppercase">Bodoland Territorial Council</h1>
                    <h2 class="text-sm font-bold text-blue-900 uppercase mt-0.5">Directorate of Education, Kokrajhar, BTC</h2>
                    <p class="text-xs font-semibold text-slate-600 uppercase mt-0.5">UPENDRA NATH BRAHMA RESIDENTIAL FREE COACHING (SESSION 2026-2027)</p>
                    <div class="mt-2 inline-block bg-slate-900 text-white text-xs font-black px-4 py-1 rounded tracking-wider uppercase">
                        Admit Card
                    </div>
                    <p class="text-[11px] text-slate-500 font-bold mt-1">Written Screening / Entrance Examination</p>
                </div>
                <div class="w-20 shrink-0 flex justify-end">
                    @if(!empty($qrCodeSvg))
                        <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" class="w-18 h-18 border border-slate-200 p-1 bg-white rounded shadow-2xs">
                    @endif
                </div>
            </div>

            <!-- Application ID Banner -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <span class="text-xs uppercase font-bold text-slate-500">Application ID: </span>
                    <span class="text-lg font-mono font-black text-blue-900 ml-1">{{ $application->application_code }}</span>
                </div>
                <div class="text-right">
                    <span class="text-xs uppercase font-bold text-slate-500">Course / Stream: </span>
                    <span class="text-sm font-black text-blue-900 uppercase ml-1">{{ $application->applied_for_display }}</span>
                </div>
            </div>

            <!-- Main Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="md:col-span-3">
                    <table class="w-full text-xs border border-slate-200">
                        <tr class="border-b border-slate-200">
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase w-1/3">Candidate Name</th>
                            <td class="p-2 font-black text-slate-900 uppercase" colspan="3">{{ $application->application_student->full_name ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase">Father's Name</th>
                            <td class="p-2 font-bold text-slate-800" colspan="3">{{ $application->application_student->father_name ?? $application->application_parent?->father_name ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase">Mother's Name</th>
                            <td class="p-2 font-bold text-slate-800" colspan="3">{{ $application->application_student->mother_name ?? $application->application_parent?->mother_name ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase">Date of Birth</th>
                            <td class="p-2 font-bold text-slate-800">
                                @php $dob = $application->application_student->dob ?? null; @endphp
                                {{ $dob ? \Carbon\Carbon::parse($dob)->format('d-m-Y') : 'N/A' }}
                            </td>
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase">Gender</th>
                            <td class="p-2 font-bold text-slate-800 uppercase">{{ $application->application_student->gender ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase">Social Category</th>
                            <td class="p-2 font-bold text-slate-800 uppercase">{{ $application->application_student->category ?? 'GENERAL' }}</td>
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase">Mobile</th>
                            <td class="p-2 font-bold text-slate-800">{{ $application->application_student->phone_number ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase">Email ID</th>
                            <td class="p-2 font-bold text-slate-800">{{ $application->application_student->email ?? 'N/A' }}</td>
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase">Permanent District</th>
                            <td class="p-2 font-bold text-slate-800 uppercase">{{ $application->application_address->permanent_district ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase">Target Institution</th>
                            <td class="p-2 font-bold text-slate-800" colspan="3">{{ $application->application_form->institution->institution_name ?? ($org->organization_name ?? 'BTC Residential Coaching Center') }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Photo & Signature -->
                <div class="flex flex-col items-center justify-between text-center">
                    <div class="w-28 h-36 border-2 border-slate-900 rounded bg-slate-50 overflow-hidden flex items-center justify-center mb-2">
                        @php
                            $resolvedPhotoUrl = $photoUrl ?? \App\Services\DocumentImageHelper::resolveImageUrl($application->application_documents?->photo);
                        @endphp
                        @if(!empty($resolvedPhotoUrl))
                            <img src="{{ $resolvedPhotoUrl }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-[10px] text-slate-400 font-bold uppercase p-2">Affix Recent Passport Photo</div>
                        @endif
                    </div>
                    <div class="w-28 h-10 border border-slate-400 rounded bg-white overflow-hidden flex items-center justify-center">
                        @php
                            $resolvedSigUrl = $signatureUrl ?? \App\Services\DocumentImageHelper::resolveImageUrl($application->application_documents?->signature);
                        @endphp
                        @if(!empty($resolvedSigUrl))
                            <img src="{{ $resolvedSigUrl }}" class="max-h-8 max-w-full object-contain">
                        @else
                            <span class="text-[9px] text-slate-400 italic">Candidate Signature</span>
                        @endif
                    </div>
                    <span class="text-[9px] font-bold text-slate-500 uppercase mt-1">Specimen Signature</span>
                </div>
            </div>

            <!-- Schedule & Venue Details -->
            <div class="bg-slate-900 text-white text-xs font-bold uppercase px-3 py-1.5 rounded-t-lg tracking-wider mb-0">
                Examination Schedule & Venue Details
            </div>
            <table class="w-full text-xs border border-slate-900 mb-4">
                <tr class="border-b border-slate-200">
                    <th class="bg-slate-50 p-2.5 text-left font-bold text-slate-600 uppercase w-1/4">Allotted Examination Center</th>
                    <td class="p-2.5 font-black text-blue-900 text-sm" colspan="3">
                        {{ $application->examination_center_full }}
                    </td>
                </tr>
                <tr>
                    <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase w-1/4">Date of Examination</th>
                    <td class="p-2 font-bold text-slate-800 w-1/4">{{ $application->exam_date }}</td>
                    <th class="bg-slate-50 p-2 text-left font-bold text-slate-600 uppercase w-1/4">Examination Timing</th>
                    <td class="p-2 font-bold text-slate-800 w-1/4">{{ $application->exam_time }}</td>
                </tr>
            </table>



            <!-- Instructions -->
            <div class="border border-slate-200 bg-slate-50 p-3 rounded-lg mt-3 text-[10px] text-slate-700">
                <p class="font-bold text-slate-900 uppercase border-b border-slate-200 pb-1 mb-1.5">Important Instructions for the Candidate</p>
                <ol class="list-decimal pl-4 space-y-1 text-slate-600">
                    <li>The candidate must bring this printed Admit Card along with an original valid Photo Identity Card (Aadhaar Card, Voter ID, PAN Card, Passport, or School/College Photo ID).</li>
                    <li>No candidate will be admitted to the Examination Hall after the commencement of the examination under any circumstances.</li>
                    <li>Use only <strong>Blue or Black Ballpoint Pen</strong> for marking on the OMR sheet.</li>
                    <li>Electronic gadgets, smartwatches, mobile phones, calculators, Bluetooth devices, and study materials are strictly banned in the examination hall.</li>
                    <li>Disqualification and legal action will be taken against anyone attempting impersonation or unfair practices.</li>
                    <li>Preserve this Admit Card carefully until final admissions and counseling are concluded.</li>
                </ol>
            </div>

            <div class="text-center text-[10px] text-slate-400 mt-3 italic">
                Official Document Issued by Directorate of Education, BTC Kokrajhar &bull; Application ID: {{ $application->application_code }}
            </div>
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
