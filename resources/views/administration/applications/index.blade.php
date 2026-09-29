@extends('layouts.administration')

@section('title', 'Application Management')

@section('content')
<div class="space-y-6">
    
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Application Management</h1>
            <p class="text-slate-500 text-sm mt-1">Welcome back, get an overview of your admission status.</p>
        </div>
        <!-- Dynamic Actions can go here -->
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('administration.applications') }}" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center gap-4">
        <!-- Search Bar Row -->
        <div class="w-full flex">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" size="18"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, Name or Mobile..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm bg-slate-50 focus:bg-white transition-all">
            </div>
        </div>
        
        <!-- Dropdowns Row -->
        <div class="flex flex-wrap gap-2 w-full items-center">
            
            <select name="institution_id" class="border flex-1 min-w-[150px] border-slate-200 rounded-xl px-4 py-2.5 bg-white text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-600 cursor-pointer hover:border-blue-300 transition-colors" onchange="this.form.submit()">
                <option value="">All Institutions</option>
                @foreach($institutions as $inst)
                    <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->institution_name }}</option>
                @endforeach
            </select>

            <select name="form_id" class="border flex-1 min-w-[150px] border-slate-200 rounded-xl px-4 py-2.5 bg-white text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-600 cursor-pointer hover:border-blue-300 transition-colors" onchange="this.form.submit()">
                <option value="">All Trades/Forms</option>
                @foreach($forms as $form)
                    <option value="{{ $form->id }}" {{ request('form_id') == $form->id ? 'selected' : '' }}>{{ $form->application_name }}</option>
                @endforeach
            </select>

            <select name="applied_for" class="border flex-1 min-w-[150px] border-slate-200 rounded-xl px-4 py-2.5 bg-white text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-600 cursor-pointer hover:border-blue-300 transition-colors" onchange="this.form.submit()">
                <option value="">All Applied For</option>
                @if(isset($appliedForOptions))
                    @foreach($appliedForOptions as $opt)
                        <option value="{{ $opt }}" {{ request('applied_for') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                @endif
            </select>

            <select name="status" class="border flex-1 min-w-[150px] border-slate-200 rounded-xl px-4 py-2.5 bg-white text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-600 cursor-pointer hover:border-blue-300 transition-colors" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <button type="submit" formaction="{{ route('administration.applications.export') }}" class="bg-blue-600 flex-none text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 flex items-center justify-center text-sm font-medium shadow-md shadow-blue-200 transition-all hover:-translate-y-0.5 whitespace-nowrap">
                <i data-lucide="download" size="16" class="mr-2"></i> Export CSV
            </button>
        </div>
    </form>

    <!-- Data Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Application ID</th>
                        <th class="px-6 py-4">Applicant</th>
                        <th class="px-6 py-4">Form</th>
                        <th class="px-6 py-4">Applied For</th>
                        <!-- <th class="px-6 py-4">Category</th> -->
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($applications as $app)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-mono font-medium text-slate-600">{{ $app->application_code }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                    {{ substr($app->application_student->full_name ?? 'NA', 0, 2) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $app->application_student->full_name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500">{{ $app->application_student->phone_number ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-blue-100">
                                {{ $app->application_form->application_name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $appliedFor = $app->applied_for ?? $app->application_student?->applied_for ?? null;
                            @endphp
                            @if($appliedFor)
                                <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-indigo-100 shadow-2xs">
                                    <i data-lucide="compass" size="13" class="text-indigo-600"></i> {{ $appliedFor }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs italic">-</span>
                            @endif
                        </td>
                        <!-- <td class="px-6 py-4">
                            <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">
                                {{ $app->application_student->category ?? 'N/A' }}
                            </span>
                        </td> -->
                        <td class="px-6 py-4">
                            @if($app->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> Approved
                                </span>
                            @elseif($app->status == 'rejected')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span> Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span> {{ $app->status == 'submitted' ? 'Submitted' : ($app->status == 'under_review' ? 'Under Review' : ucfirst($app->status)) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('administration.applications.detail', $app->id) }}" class="text-slate-400 hover:text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-all inline-block" title="View Details">
                                <i data-lucide="eye" size="18"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-500">No applications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($applications->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $applications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
