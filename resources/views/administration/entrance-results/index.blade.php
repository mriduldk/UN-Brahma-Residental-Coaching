@extends('layouts.administration')

@section('title', 'Entrance Results')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{ 
    uploadModalOpen: false,
    editModalOpen: false,
    selectedStreamForTemplate: 'APSC',
    editData: { id: '', roll_no: '', candidate_name: '', stream: '', marks: '', rank: '', district: '', phone_number: '', qualification_status: 'not_qualified' },
    openEdit(item) {
        this.editData = { ...item };
        this.editModalOpen = true;
    }
}">

    <!-- Page Header (Clean & Standard) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Entrance Examination Results</h1>
            <p class="text-xs text-slate-500 mt-1">Super 50 Residential Coaching · Screening Test Marks & Merit Ranking</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Data Reconciliation / Verification Link -->
            <a href="{{ route('administration.entrance-results.reconciliation') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition shadow-sm">
                <i data-lucide="check-check" size="14" class="text-indigo-600"></i>
                <span>Data Reconciliation</span>
            </a>

            <!-- Download Template Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">
                    <i data-lucide="download" size="14"></i>
                    <span>Download Template</span>
                    <i data-lucide="chevron-down" size="12"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-1 w-44 bg-white border border-slate-200 rounded-lg shadow-lg z-20 py-1 text-xs">
                    <div class="px-3 py-1 font-bold text-slate-400 uppercase tracking-wider text-[10px]">Select Stream</div>
                    <a href="{{ route('administration.entrance-results.template', ['stream' => 'APSC']) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-100">APSC Template</a>
                    <a href="{{ route('administration.entrance-results.template', ['stream' => 'UPSC']) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-100">UPSC Template</a>
                    <a href="{{ route('administration.entrance-results.template', ['stream' => 'NEET']) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-100">NEET Template</a>
                    <a href="{{ route('administration.entrance-results.template', ['stream' => 'JEE']) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-100">JEE Template</a>
                </div>
            </div>

            <!-- Upload Button -->
            <button @click="uploadModalOpen = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                <i data-lucide="upload-cloud" size="14"></i>
                <span>Upload Results (CSV)</span>
            </button>
        </div>
    </div>

    <!-- Feedback Messages -->
    @if(session('success'))
    <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i data-lucide="check-circle-2" size="16" class="text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center gap-2">
        <i data-lucide="alert-circle" size="16" class="text-rose-600"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if(session('import_warnings') && count(session('import_warnings')) > 0)
    <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-1">
        <div class="font-bold flex items-center gap-1.5 text-amber-800">
            <i data-lucide="alert-triangle" size="14"></i>
            <span>Import Notice / Skipped Entries:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-slate-700 pl-1">
            @foreach(session('import_warnings') as $w)
            <li>{{ $w }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Compact Stats Row -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Stored</div>
            <div class="text-xl font-bold text-slate-900 mt-0.5">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">APSC</div>
            <div class="text-xl font-bold text-blue-700 mt-0.5">{{ number_format($stats['apsc']) }}</div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">UPSC</div>
            <div class="text-xl font-bold text-indigo-700 mt-0.5">{{ number_format($stats['upsc']) }}</div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">NEET</div>
            <div class="text-xl font-bold text-emerald-700 mt-0.5">{{ number_format($stats['neet']) }}</div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">JEE</div>
            <div class="text-xl font-bold text-amber-700 mt-0.5">{{ number_format($stats['jee']) }}</div>
        </div>
        <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[10px] font-bold text-purple-500 uppercase tracking-wider">High / Avg Score</div>
            <div class="text-xl font-bold text-purple-700 mt-0.5">{{ $stats['max'] }} <span class="text-xs font-normal text-slate-400">/ {{ $stats['avg'] }}</span></div>
        </div>
    </div>

    <!-- Filter & Stream Navigation Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3">
        <!-- Stream Tabs -->
        <div class="flex items-center gap-1 overflow-x-auto pb-1 border-b border-slate-100">
            @php
                $streams = [
                    'ALL'  => 'All Streams',
                    'APSC' => 'APSC (' . $stats['apsc'] . ')',
                    'UPSC' => 'UPSC (' . $stats['upsc'] . ')',
                    'NEET' => 'NEET (' . $stats['neet'] . ')',
                    'JEE'  => 'JEE (' . $stats['jee'] . ')',
                ];
            @endphp
            @foreach($streams as $key => $label)
            <a href="{{ route('administration.entrance-results.index', array_merge(request()->query(), ['stream' => $key, 'page' => 1])) }}"
               class="px-3.5 py-1.5 text-xs font-semibold rounded-lg transition whitespace-nowrap {{ strtoupper($stream) === $key ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        <!-- Search & Filter Controls -->
        <form method="GET" action="{{ route('administration.entrance-results.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <input type="hidden" name="stream" value="{{ $stream }}">
            
            <div class="sm:col-span-6 relative">
                <i data-lucide="search" size="14" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by Roll No, Candidate Name, or Phone..."
                       class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="sm:col-span-4">
                <select name="district" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                    <option value="">All Districts</option>
                    @foreach($districts as $d)
                    <option value="{{ $d }}" {{ strtoupper($district) === strtoupper($d) ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 text-xs font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition">
                    Filter
                </button>
                @if($search || $district)
                <a href="{{ route('administration.entrance-results.index', ['stream' => $stream]) }}" class="p-2 text-slate-500 hover:text-slate-700 border border-slate-300 rounded-lg bg-white" title="Reset filter">
                    <i data-lucide="x" size="14"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Structured Results Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4 w-14 text-center">Rank</th>
                        <th class="py-3 px-4">Roll Number</th>
                        <th class="py-3 px-4">Candidate Name</th>
                        <th class="py-3 px-4">Stream</th>
                        <th class="py-3 px-4 text-right">Marks</th>
                        <th class="py-3 px-4">District</th>
                        <th class="py-3 px-4">Phone No</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-normal text-slate-700">
                    @forelse($results as $res)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-2.5 px-4 text-center font-mono font-semibold text-slate-500">
                            {{ $res->rank ?? '—' }}
                        </td>
                        <td class="py-2.5 px-4 font-mono font-bold text-slate-900">
                            {{ $res->roll_no }}
                        </td>
                        <td class="py-2.5 px-4 font-medium text-slate-900 uppercase">
                            {{ $res->candidate_name }}
                        </td>
                        <td class="py-2.5 px-4">
                            @php
                                $badgeClasses = match($res->stream) {
                                    'APSC' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'UPSC' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'NEET' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'JEE'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-50 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded border {{ $badgeClasses }}">
                                {{ $res->stream }}
                            </span>
                        </td>
                        <td class="py-2.5 px-4 text-right font-mono font-bold text-slate-900">
                            {{ number_format($res->marks, 2) }}
                        </td>
                        <td class="py-2.5 px-4 uppercase text-slate-600">
                            {{ $res->district ?: '—' }}
                        </td>
                        <td class="py-2.5 px-4 font-mono text-slate-600">
                            {{ $res->phone_number ?: '—' }}
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            @if($res->student_application_id)
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                                    <i data-lucide="check" size="10"></i> Linked
                                </span>
                            @else
                                <span class="inline-flex items-center text-[10px] text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
                                    Unlinked
                                </span>
                            @endif
                        </td>
                        <td class="py-2.5 px-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('administration.entrance-results.print', $res->roll_no) }}" 
                                   target="_blank"
                                   class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded transition" 
                                   title="Print / View Official Scorecard">
                                    <i data-lucide="printer" size="14"></i>
                                </a>

                                <button type="button" 
                                        @click="openEdit({{ json_encode($res) }})" 
                                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-slate-100 rounded transition" 
                                        title="Edit Result Details">
                                    <i data-lucide="edit-3" size="14"></i>
                                </button>
                                
                                <form method="POST" action="{{ route('administration.entrance-results.destroy', $res->id) }}" onsubmit="return confirm('Delete result record for Roll No: {{ $res->roll_no }}?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded transition" title="Delete">
                                        <i data-lucide="trash-2" size="14"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                            No entrance results match your filter criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($results->hasPages())
        <div class="p-3 bg-slate-50 border-t border-slate-200">
            {{ $results->links() }}
        </div>
        @endif
    </div>

    <!-- Upload Results Modal (Clean & Standard) -->
    <div x-show="uploadModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="uploadModalOpen = false" class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-md overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Upload Entrance Results</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Import stream-wise screening exam marks from CSV</p>
                </div>
                <button @click="uploadModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" size="16"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('administration.entrance-results.upload') }}" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                
                <!-- Stream Selection -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Academic Stream <span class="text-rose-500">*</span></label>
                    <select name="stream" x-model="selectedStreamForTemplate" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="APSC">APSC (Civil Services)</option>
                        <option value="UPSC">UPSC (Civil Services)</option>
                        <option value="NEET">NEET (Medical)</option>
                        <option value="JEE">JEE (Engineering)</option>
                    </select>
                </div>

                <!-- CSV File Input -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Upload CSV File <span class="text-rose-500">*</span></label>
                    <input type="file" name="result_file" accept=".csv,text/csv,text/plain" required class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer border border-slate-300 rounded-lg">
                    <p class="text-[10px] text-slate-400 mt-1">Accepted columns: ROLL NO, CANDIDATE NAME, MARKS / SCORE, DISTRICT, PH NO, RANK.</p>
                </div>

                <!-- Options -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="auto_link" id="auto_link" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 text-xs">
                    <label for="auto_link" class="text-xs text-slate-600">Auto-link candidate marks with online submitted applications</label>
                </div>

                <!-- Action Footer -->
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <a :href="'{{ url('administration/entrance-results/template') }}?stream=' + selectedStreamForTemplate" class="text-[11px] font-semibold text-blue-600 hover:underline inline-flex items-center gap-1">
                        <i data-lucide="download" size="12"></i>
                        <span>Sample CSV Format</span>
                    </a>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="uploadModalOpen = false" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm">
                            Import Results
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Result Modal (To fix Roll No 2610045, candidate name, marks, etc.) -->
    <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="editModalOpen = false" class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Edit Result Record</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Modify candidate details or update corrected roll number</p>
                </div>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" size="16"></i>
                </button>
            </div>

            <form :action="'{{ url('administration/entrance-results') }}/' + editData.id" method="POST" class="p-5 space-y-3">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Roll Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="roll_no" x-model="editData.roll_no" required class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Stream <span class="text-rose-500">*</span></label>
                        <select name="stream" x-model="editData.stream" required class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                            <option value="APSC">APSC</option>
                            <option value="UPSC">UPSC</option>
                            <option value="NEET">NEET</option>
                            <option value="JEE">JEE</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Candidate Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="candidate_name" x-model="editData.candidate_name" required class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 uppercase">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Marks / Score <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.01" name="marks" x-model="editData.marks" required class="w-full px-3 py-1.5 text-xs font-mono font-bold border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Merit Rank</label>
                        <input type="number" name="rank" x-model="editData.rank" class="w-full px-3 py-1.5 text-xs font-mono border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">District</label>
                        <input type="text" name="district" x-model="editData.district" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                        <input type="text" name="phone_number" x-model="editData.phone_number" class="w-full px-3 py-1.5 text-xs font-mono border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Qualification Status</label>
                    <select name="qualification_status" x-model="editData.qualification_status" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white">
                        <option value="qualified">Qualified</option>
                        <option value="waitlisted">Waitlisted</option>
                        <option value="not_qualified">Not Qualified</option>
                        <option value="absent">Absent</option>
                    </select>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="editModalOpen = false" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
