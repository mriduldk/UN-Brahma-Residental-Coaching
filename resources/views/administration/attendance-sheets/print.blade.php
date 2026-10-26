<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Attendance Sheet - {{ $centerTitle }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 12mm 12mm;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0 !important;
                background: #ffffff !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            thead {
                display: table-header-group;
            }
            tr {
                page-break-inside: avoid;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body class="bg-slate-100 py-6 px-4">

    <!-- Top floating Action Bar (Hidden when printed) -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-slate-900 text-white p-4 rounded-2xl shadow-xl flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                <i data-lucide="printer" size="20"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold">Print Preview Mode</h3>
                <p class="text-xs text-slate-400">Ready to print directly or save as PDF via your browser's print dialog.</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <button onclick="window.close()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs rounded-xl transition-all">
                Close
            </button>
            <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-2">
                <i data-lucide="printer" size="16"></i>
                <span>Print Sheet</span>
            </button>
        </div>
    </div>

    <!-- Official Printable Sheet Container -->
    <div class="print-container max-w-4xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-lg">

        <!-- Sheet Header -->
        <div class="text-center pb-4 mb-4 border-b-2 border-slate-900">
            @if(!empty($emblemPath) && file_exists($emblemPath))
                <div class="flex justify-center mb-2">
                    <img src="{{ asset('image/logo/logo-em.png') }}" class="h-14 w-auto" alt="Emblem">
                </div>
            @endif

            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-1">
                Department of Education &bull; Bodoland Territorial Council
            </p>

            <h1 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-slate-950">
                {{ $headerTitle }}
            </h1>

            <div class="my-2">
                <span class="inline-block bg-slate-900 text-white text-xs sm:text-sm font-extrabold uppercase tracking-widest px-4 py-1 rounded">
                    EXAMINATION CENTER : {{ $centerTitle }}
                </span>
            </div>

            <p class="text-[11px] font-semibold text-slate-600 tracking-wide mt-1">
                ATTENDANCE &amp; SIGNATURE ROLL SHEET &bull; ENTRANCE EXAMINATION 2026<br>
                Date of Exam: <strong>Tuesday, 27th October, 2026</strong> &nbsp;|&nbsp;
                Time: <strong>{{ $examTime }}</strong> &nbsp;|&nbsp;
                Total Candidates: <strong>{{ $applications->count() }}</strong>
            </p>
        </div>

        <!-- Attendance Table -->
        <table class="w-full border-collapse border border-slate-900 text-xs">
            <thead>
                <tr class="bg-slate-100 text-slate-900 font-black uppercase text-[10px] tracking-wider border-b-2 border-slate-900">
                    <th class="py-2.5 px-3 border border-slate-900 text-center w-14">SL NO</th>
                    <th class="py-2.5 px-3 border border-slate-900 text-center w-36">ROLL NO</th>
                    <th class="py-2.5 px-4 border border-slate-900 text-left">CANDIDATE NAME</th>
                    <th class="py-2.5 px-4 border border-slate-900 text-left">FATHER NAME</th>
                    <th class="py-2.5 px-4 border border-slate-900 text-center w-48">SIGNATURE</th>
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
                    <tr class="border-b border-slate-900 {{ $index % 2 === 1 ? 'bg-slate-50/70' : 'bg-white' }}">
                        <td class="py-3 px-3 border border-slate-900 text-center font-bold text-slate-600">
                            {{ $index + 1 }}
                        </td>
                        <td class="py-3 px-3 border border-slate-900 text-center font-mono font-bold text-slate-900 text-sm">
                            {{ $rollNo }}
                        </td>
                        <td class="py-3 px-4 border border-slate-900 font-bold text-slate-900 uppercase">
                            {{ $candidateName }}
                        </td>
                        <td class="py-3 px-4 border border-slate-900 font-semibold text-slate-700 uppercase">
                            {{ $fatherName }}
                        </td>
                        <td class="py-3 px-4 border border-slate-900 text-center h-11 bg-white">
                            <!-- Blank signature box -->
                            &nbsp;
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400 font-bold border border-slate-900">
                            No candidates found for this selection.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Footer Summary and Signatures -->
        <div class="mt-8 pt-4 border-t border-slate-300">
            <div class="grid grid-cols-3 gap-6 items-end text-xs">
                <!-- Left: Stats -->
                <div class="border border-slate-300 p-3 rounded bg-slate-50">
                    <p class="font-bold text-slate-700">Total Allotted: <span class="text-slate-950">{{ $applications->count() }}</span></p>
                    <p class="mt-1 font-semibold text-slate-600">Present: ________ &nbsp;|&nbsp; Absent: ________</p>
                </div>

                <!-- Middle: Invigilator -->
                <div class="text-center pb-2">
                    <div class="border-b border-dashed border-slate-500 w-40 mx-auto mb-1.5"></div>
                    <p class="font-bold uppercase tracking-wider text-slate-800 text-[11px]">Invigilator's Signature</p>
                    <p class="text-[10px] text-slate-500">Name: ______________________</p>
                </div>

                <!-- Right: Superintendent -->
                <div class="text-center pb-2">
                    <div class="border-b border-dashed border-slate-500 w-44 mx-auto mb-1.5"></div>
                    <p class="font-bold uppercase tracking-wider text-slate-800 text-[11px]">Center Superintendent</p>
                    <p class="text-[10px] text-slate-500">Signature with Official Seal</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        lucide.createIcons();
        // Optional: auto print if query param autoprint=1
        if (window.location.search.includes('autoprint=1')) {
            window.addEventListener('load', () => setTimeout(() => window.print(), 500));
        }
    </script>
</body>
</html>
