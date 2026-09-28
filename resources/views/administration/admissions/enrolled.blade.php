@extends('layouts.administration')

@section('title', 'Enrolled Students')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Enrolled Students</h1>
            <p class="text-slate-500 mt-1">List of all students successfully admitted across institutions.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('administration.admissions.enrolled.export', request()->all()) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 shadow-sm transition flex items-center gap-2 text-sm font-medium">
                <i data-lucide="file-spreadsheet" size="16"></i> Export to Excel
            </a>
            <a href="{{ route('administration.admissions.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition flex items-center gap-2 text-sm font-medium">
                <i data-lucide="layout-dashboard" size="16"></i> Admission Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <form action="{{ route('administration.admissions.enrolled') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Institution Filter -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Institution</label>
                <select name="institution_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Institutions</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ ($selectedInstitution && $selectedInstitution->id == $inst->id) ? 'selected' : '' }}>
                            {{ $inst->institution_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Form Filter -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Application Form</label>
                <select name="application_form_id" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Forms</option>
                    @foreach($applicationForms as $form)
                        <option value="{{ $form->id }}" {{ ($selectedFormId == $form->id) ? 'selected' : '' }}>
                            {{ $form->application_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Session Filter -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Academic Session</label>
                <select name="session" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="">All Sessions</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session }}" {{ request('session') == $session ? 'selected' : '' }}>
                            {{ $session }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search -->
            <div class="space-y-1.5 lg:col-span-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Search</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="text-slate-400" size="16"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all" 
                               placeholder="Admission Code, Name, Phone...">
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm font-medium flex items-center gap-2">
                        Filter
                    </button>
                    @if(request()->anyFilled(['institution_id', 'application_form_id', 'session', 'search']))
                        <a href="{{ route('administration.admissions.enrolled') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition font-medium flex items-center justify-center">
                            <i data-lucide="rotate-ccw" size="18"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Admission Detail</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Student Info</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Institution & Trade</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Payment Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($enrolledStudents as $enrolled)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-blue-600 font-mono">{{ $enrolled->admission_code }}</span>
                                <span class="text-[11px] text-slate-400 uppercase font-semibold mt-0.5">App: {{ $enrolled->application_code }}</span>
                                <span class="text-[10px] text-slate-400 mt-1">{{ $enrolled->created_at->format('d M, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs ring-2 ring-white">
                                    {{ substr($enrolled->student_application->application_student->full_name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-slate-900">{{ $enrolled->student_application->application_student->full_name }}</span>
                                    <span class="text-xs text-slate-500">{{ $enrolled->student_application->application_student->phone_number }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm text-slate-700 font-medium">{{ $enrolled->institution->institution_name }}</span>
                                <span class="text-xs text-slate-500 mt-0.5">{{ $enrolled->application_form->application_name }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 w-fit mt-1 uppercase tracking-wider">
                                    {{ $enrolled->academic_session }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col gap-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-tight {{ $enrolled->pending_amount <= 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $enrolled->payment_type }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-bold {{ $enrolled->pending_amount <= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} border {{ $enrolled->pending_amount <= 0 ? 'border-emerald-200' : 'border-amber-200' }}">
                                        {{ $enrolled->pending_amount <= 0 ? 'COMPLETED' : 'PENDING' }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500">
                                    Paid: <span class="font-bold text-slate-700">₹{{ number_format($enrolled->total_paid_amount) }}</span>
                                    @if($enrolled->pending_amount > 0)
                                        / Bal: <span class="font-bold text-rose-600">₹{{ number_format($enrolled->pending_amount) }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('administration.admissions.show', $enrolled->student_application_id) }}" 
                               class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-700 font-bold text-xs uppercase tracking-wider transition-colors">
                                View Details <i data-lucide="arrow-right" size="14"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center bg-slate-50/30">
                            <div class="flex flex-col items-center justify-center max-w-xs mx-auto">
                                <div class="w-16 h-16 bg-slate-100 text-slate-300 rounded-2xl flex items-center justify-center mb-4">
                                    <i data-lucide="users" size="32"></i>
                                </div>
                                <h3 class="text-slate-900 font-bold">No Records Found</h3>
                                <p class="text-sm text-slate-500 mt-1">We couldn't find any enrolled students matching your filter criteria.</p>
                                <a href="{{ route('administration.admissions.enrolled') }}" class="mt-4 text-blue-600 text-sm font-bold uppercase tracking-wider hover:text-blue-700">Clear All Filters</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($enrolledStudents->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $enrolledStudents->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Styling for selects to match input heights */
    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7' /%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1rem;
        padding-right: 2.5rem;
    }
</style>
@endsection
