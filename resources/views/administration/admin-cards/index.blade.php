@extends('layouts.administration')

@section('title', 'Admin Card Generation')

@section('content')
<div class="space-y-6" x-data="adminCardManager()">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2.5">
                <i data-lucide="id-card" class="text-blue-600" size="28"></i>
                Admin Card Generation
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('administration.admin-cards.logs') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-bold text-xs uppercase tracking-wider shadow-sm transition-all hover:border-slate-300">
                <i data-lucide="history" size="16" class="text-blue-600"></i> Download Logs
            </a>

            <a href="{{ route('administration.admin-cards.export', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 font-bold text-xs uppercase tracking-wider shadow-sm transition-all hover:border-slate-300">
                <i data-lucide="file-spreadsheet" size="16" class="text-emerald-600"></i> Export Roll Sheet (CSV)
            </a>

            <button type="button" @click="submitBulkDownload()" :disabled="selectedCount === 0" :class="selectedCount > 0 ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-200 text-white' : 'bg-slate-200 text-slate-400 cursor-not-allowed'" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md transition-all">
                <i data-lucide="download" size="16"></i>
                <span>Bulk Download Cards (<span x-text="selectedCount"></span>)</span>
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <form method="GET" action="{{ route('administration.admin-cards.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <!-- Search Row -->
        <div class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 transform -translate-y-1/2 text-slate-400" size="18"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Application ID, Candidate Name, Mobile, Email or Center..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-slate-50 focus:bg-white transition-all font-medium text-slate-800">
            </div>
            
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 transition-colors shadow-md shadow-blue-200 flex items-center justify-center gap-2">
                <i data-lucide="search" size="16"></i> Search
            </button>
            
            @if(request()->anyFilled(['search', 'institution_id', 'form_id', 'applied_for', 'examination_center']))
                <a href="{{ route('administration.admin-cards.index') }}" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-sm rounded-xl transition-colors flex items-center justify-center gap-1.5" title="Clear Filters">
                    <i data-lucide="rotate-ccw" size="15"></i> Reset
                </a>
            @endif
        </div>

        <!-- Filter Dropdowns Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-2 border-t border-slate-100">
            <!-- Institution -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Institution</label>
                <select name="institution_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-white text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Institutions</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->institution_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Form / Trade -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Application Form</label>
                <select name="form_id" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-white text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Forms</option>
                    @foreach($forms as $form)
                        <option value="{{ $form->id }}" {{ request('form_id') == $form->id ? 'selected' : '' }}>{{ $form->application_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Applied For / Stream -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Course / Stream</label>
                <select name="applied_for" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-white text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Streams</option>
                    @if(isset($appliedForOptions))
                        @foreach($appliedForOptions as $opt)
                            <option value="{{ $opt }}" {{ request('applied_for') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Examination Center -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Examination Center</label>
                <select name="examination_center" class="w-full border border-slate-200 rounded-xl px-3 py-2 bg-white text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 cursor-pointer" onchange="this.form.submit()">
                    <option value="">All Exam Centers</option>
                    @if(isset($examDistricts))
                        @foreach($examDistricts as $dist)
                            <option value="{{ $dist }}" {{ request('examination_center') == $dist ? 'selected' : '' }}>{{ $dist }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </form>

    <!-- Hidden Bulk Download Form -->
    <form id="bulk-download-form" action="{{ route('administration.admin-cards.bulk-download') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="selected_ids" :value="selectedIds.join(',')">
        <input type="hidden" name="form_id" value="{{ request('form_id') }}">
        <input type="hidden" name="institution_id" value="{{ request('institution_id') }}">
        <input type="hidden" name="applied_for" value="{{ request('applied_for') }}">
        <input type="hidden" name="examination_center" value="{{ request('examination_center') }}">
        <input type="hidden" name="search" value="{{ request('search') }}">
    </form>

    <!-- Selection Helper Banner -->
    <div x-show="selectedCount > 0" x-cloak class="bg-blue-600 text-white px-5 py-3 rounded-2xl shadow-lg flex items-center justify-between transition-all animate-fadeIn">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center font-bold">
                <span x-text="selectedCount"></span>
            </div>
            <p class="text-sm font-bold">Candidates selected for bulk card generation</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="submitBulkDownload()" class="bg-white text-blue-700 hover:bg-blue-50 px-4 py-1.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow">
                Download Selected PDF
            </button>
            <button type="button" @click="deselectAll()" class="text-white hover:bg-white/10 px-3 py-1.5 rounded-xl font-bold text-xs">
                Clear
            </button>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase font-black text-[11px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-4 w-10 text-center">
                            <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected()" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 cursor-pointer">
                        </th>
                        <th class="px-4 py-4">Application ID</th>
                        <th class="px-4 py-4">Candidate Details</th>
                        <th class="px-4 py-4">Course / Stream</th>
                        <th class="px-4 py-4">Allotted Center</th>
                        <th class="px-4 py-4">Submitted On</th>
                        <th class="px-4 py-4 text-center">Admin Card</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($applications as $app)
                    @php
                        $student = $app->application_student;
                        $appliedFor = $app->applied_for ?? $student?->applied_for ?? 'N/A';
                        $examCenter = $app->examination_center ?? $student?->examination_center ?? 'N/A';
                        $hasPhoto = $app->application_documents && !empty($app->application_documents->photo);
                        $photoUrl = $hasPhoto ? asset('storage/' . $app->application_documents->photo) : null;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <!-- Checkbox -->
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" value="{{ $app->id }}" @change="toggleItem({{ $app->id }})" :checked="selectedIds.includes({{ $app->id }})" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 cursor-pointer">
                        </td>

                        <!-- Roll No / Application Code -->
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-black text-blue-900 bg-blue-50 border border-blue-200/60 px-2.5 py-1 rounded-lg text-xs tracking-wide">
                                    {{ $app->application_code }}
                                </span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium block mt-1">App #{{ $app->id }}</span>
                        </td>

                        <!-- Candidate Details -->
                        <td class="px-4 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0 flex items-center justify-center shadow-2xs">
                                    @if($hasPhoto)
                                        <img src="{{ $photoUrl }}" alt="Photo" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold flex items-center justify-center text-xs">
                                            {{ substr($student->full_name ?? 'NA', 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 leading-tight">{{ $student->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-2">
                                        <span><i data-lucide="phone" size="11" class="inline text-slate-400"></i> {{ $student->phone_number ?? 'N/A' }}</span>
                                        @if($student->category)
                                            <span class="bg-slate-100 text-slate-600 px-1.5 py-0.2 rounded text-[10px] font-bold">{{ strtoupper($student->category) }}</span>
                                        @endif
                                    </div>
                                    @if($student->father_name ?? $app->application_parent?->father_name)
                                        <div class="text-[10px] text-slate-400 mt-0.5">F: {{ $student->father_name ?? $app->application_parent?->father_name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Course / Stream -->
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-indigo-100">
                                <i data-lucide="compass" size="13" class="text-indigo-600"></i> {{ $appliedFor }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-1 truncate max-w-[180px]">
                                {{ $app->application_form->application_name ?? 'BTC Residential Coaching' }}
                            </div>
                        </td>

                        <!-- Allotted Exam Center -->
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-900 px-2.5 py-1 rounded-lg text-xs font-black border border-amber-200">
                                <i data-lucide="map-pin" size="13" class="text-amber-600"></i> {{ $app->examination_center_full }}
                            </span>
                            <div class="text-[10px] text-slate-500 font-medium mt-1">27 Oct 2026 &bull; {{ $app->exam_time_short }}</div>
                        </td>

                        <!-- Submitted Date -->
                        <td class="px-4 py-4 text-xs text-slate-500 whitespace-nowrap">
                            <div class="font-semibold text-slate-700">
                                {{ $app->submitted_at ? $app->submitted_at->format('d M, Y') : ($app->created_at ? $app->created_at->format('d M, Y') : 'N/A') }}
                            </div>
                            <div class="text-[10px] text-slate-400">
                                {{ $app->submitted_at ? $app->submitted_at->format('h:i A') : '' }}
                            </div>
                        </td>

                        <!-- Action Buttons -->
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <a href="{{ route('administration.admin-cards.download', $app->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-sm shadow-blue-200 transition-all transform hover:-translate-y-0.5" 
                                   title="Download Admit Card">
                                    <i data-lucide="download" size="14"></i>
                                    <span>Download Admit Card</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 px-4">
                            <div class="max-w-md mx-auto text-center">
                                <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                                    <i data-lucide="id-card" size="32"></i>
                                </div>
                                <h3 class="text-base font-bold text-slate-800">No Submitted Applications Found</h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    @if(request()->anyFilled(['search', 'institution_id', 'form_id', 'applied_for', 'examination_center']))
                                        No submitted applications match the selected filter criteria. Try adjusting or clearing your filters.
                                    @else
                                        Only applications with <strong>submitted</strong> status will appear on this page for admin card generation.
                                    @endif
                                </p>
                                @if(request()->anyFilled(['search', 'institution_id', 'form_id', 'applied_for', 'examination_center']))
                                    <a href="{{ route('administration.admin-cards.index') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-md hover:bg-blue-700 transition-colors">
                                        <i data-lucide="rotate-ccw" size="14"></i> Reset Filters
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(method_exists($applications, 'hasPages') && $applications->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $applications->links() }}
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    function adminCardManager() {
        return {
            selectedIds: [],

            get selectedCount() {
                return this.selectedIds.length;
            },

            toggleItem(id) {
                const index = this.selectedIds.indexOf(id);
                if (index > -1) {
                    this.selectedIds.splice(index, 1);
                } else {
                    this.selectedIds.push(id);
                }
            },

            isAllSelected() {
                const currentIds = @json($applications->pluck('id'));
                return currentIds.length > 0 && currentIds.every(id => this.selectedIds.includes(id));
            },

            toggleSelectAll(e) {
                const currentIds = @json($applications->pluck('id'));
                if (e.target.checked) {
                    currentIds.forEach(id => {
                        if (!this.selectedIds.includes(id)) {
                            this.selectedIds.push(id);
                        }
                    });
                } else {
                    this.selectedIds = this.selectedIds.filter(id => !currentIds.includes(id));
                }
            },

            deselectAll() {
                this.selectedIds = [];
            },

            submitBulkDownload() {
                if (this.selectedCount === 0) return;
                document.getElementById('bulk-download-form').submit();
            }
        };
    }
</script>
@endpush
@endsection
