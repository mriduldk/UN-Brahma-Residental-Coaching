<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrance Scorecard - Roll {{ $result->roll_no }}</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo/logo-em.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .scorecard-box { border: 2px solid #000 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-6 flex flex-col items-center justify-center min-h-screen text-slate-900 font-sans">

    <!-- Action Bar (Hidden on print) -->
    <div class="max-w-2xl w-full mb-4 flex justify-between items-center no-print">
        <a href="{{ route('administration.entrance-results.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center gap-1">
            &larr; Back to Admin Results
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm">
            Print Scorecard
        </button>
    </div>

    <!-- Official Scorecard Box -->
    <div class="scorecard-box bg-white max-w-2xl w-full p-8 rounded-xl border border-slate-300 shadow-lg space-y-6">
        
        <!-- Header -->
        <div class="flex items-center gap-4 pb-4 border-b-2 border-slate-900">
            <img src="{{ asset('image/logo/logo-em.png') }}" alt="BTC Emblem" class="w-14 h-16 object-contain">
            <div class="flex-1 text-center">
                <div class="text-[11px] font-bold tracking-widest text-slate-600 uppercase">Directorate of Education · Bodoland Territorial Council</div>
                <div class="text-base font-black text-slate-900 uppercase tracking-tight mt-0.5">Upendra Nath Brahma Residential Free Coaching</div>
                <div class="text-[11px] font-semibold text-blue-700 uppercase mt-0.5 tracking-wider">Super 50 Screening Examination 2026 · Candidate Score Card</div>
            </div>
            <div class="w-14"></div> <!-- Balancing Spacer -->
        </div>

        <!-- Candidate Details Table -->
        <div class="border border-slate-200 rounded-lg overflow-hidden text-xs">
            <div class="bg-slate-50 font-bold px-4 py-2 border-b border-slate-200 text-slate-700 uppercase text-[10px] tracking-wider">
                Candidate Information
            </div>
            <div class="grid grid-cols-2 divide-x divide-y divide-slate-200">
                <div class="p-3">
                    <span class="text-slate-500 block text-[10px] uppercase">Candidate Name</span>
                    <span class="font-bold text-slate-900 text-sm uppercase">{{ $result->candidate_name }}</span>
                </div>
                <div class="p-3">
                    <span class="text-slate-500 block text-[10px] uppercase">Roll Number</span>
                    <span class="font-mono font-bold text-slate-900 text-sm tracking-wider">{{ $result->roll_no }}</span>
                </div>
                <div class="p-3">
                    <span class="text-slate-500 block text-[10px] uppercase">Stream / Applied Programme</span>
                    <span class="font-bold text-slate-800">{{ $result->stream }}</span>
                </div>
                <div class="p-3">
                    <span class="text-slate-500 block text-[10px] uppercase">District</span>
                    <span class="font-bold text-slate-800 uppercase">{{ $result->district ?: '—' }}</span>
                </div>
            </div>
        </div>

        <!-- Performance / Score Summary -->
        <div class="border border-slate-200 rounded-lg overflow-hidden text-xs">
            <div class="bg-slate-50 font-bold px-4 py-2 border-b border-slate-200 text-slate-700 uppercase text-[10px] tracking-wider">
                Performance Evaluation
            </div>
            <div class="grid grid-cols-3 divide-x divide-slate-200 text-center py-4 bg-slate-50/50">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase">Marks Obtained</span>
                    <span class="font-mono font-black text-2xl text-blue-700">{{ number_format($result->marks, 2) }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase">Merit Rank</span>
                    <span class="font-mono font-black text-2xl text-slate-900">#{{ $result->rank ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase">Result Status</span>
                    <span class="inline-block font-bold text-xs px-2.5 py-1 rounded bg-slate-200 text-slate-800 uppercase mt-1">
                        {{ strtoupper($result->qualification_status ?? 'Declared') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Footer / Instructions -->
        <div class="pt-4 border-t border-slate-200 flex justify-between items-end text-[10px] text-slate-500">
            <div>
                <div>Published on: {{ $result->published_at ? $result->published_at->format('d M Y, h:i A') : date('d M Y') }}</div>
                <div>System Generated Score Card · Department of Education, BTC</div>
            </div>
            <div class="text-right">
                <div class="w-32 border-b border-slate-400 mb-1"></div>
                <div class="font-semibold text-slate-700">Controller of Examinations</div>
                <div>UNB Coaching Cell, BTC</div>
            </div>
        </div>

    </div>

</body>
</html>
