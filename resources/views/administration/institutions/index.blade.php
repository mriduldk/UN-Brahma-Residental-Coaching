@extends('layouts.administration')

@section('title', 'Institutions')

@section('content')
@php
    $org = auth()->user()->currentOrganization();
    $isAdmin = $org && auth()->user()->isAdmin($org->id);
@endphp

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Institutions</h1>
            <p class="text-slate-500 mt-1">Manage all institutions under your organization.</p>
        </div>
        @if($isAdmin)
        <a href="{{ route('administration.institutions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
            <i data-lucide="plus" size="18" class="mr-2"></i> Add Institution
        </a>
        @endif
    </div>

    <!-- Alert -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Institutions List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Institution</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contact</th>
                        @if(optional($org->configuration)->is_admission_enabled)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Admission Config</th>
                        @endif
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bank Details</th>
                        @if($isAdmin)
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($institutions as $institution)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 font-bold">
                                    {{ substr($institution->institution_name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-slate-900">{{ $institution->institution_name }}</div>
                                    <div class="text-sm text-slate-500">Code: {{ $institution->institution_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-slate-900">{{ $institution->contact_number ?? '-' }}</div>
                            <div class="text-sm text-slate-500">{{ $institution->email ?? '-' }}</div>
                        </td>
                        @if(optional($org->configuration)->is_admission_enabled)
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($institution->admissions->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($institution->admissions as $config)
                                        <div class="flex items-center justify-between gap-4 p-2 bg-slate-50 rounded-lg border border-slate-100 group">
                                                <div class="flex flex-col">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold text-slate-900">{{ $config->academic_session }}</span>
                                                    </div>
                                                    @if($config->application_form)
                                                        <span class="text-[11px] font-medium text-blue-600 truncate max-w-[150px]" title="{{ $config->application_form->application_name }}">
                                                            {{ $config->application_form->application_name }}
                                                        </span>
                                                    @endif
                                                    <span class="text-xs text-slate-500 font-medium">Fee: ₹{{ number_format($config->admission_fee, 2) }}</span>
                                                </div>
                                            @if($isAdmin)
                                            <a href="{{ route('administration.admission-configs.edit', $config->id) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-white rounded-md transition-all shadow-sm opacity-0 group-hover:opacity-100" title="Edit Session Config">
                                                <i data-lucide="edit-2" size="14"></i>
                                            </a>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($isAdmin)
                                    <div class="pt-1">
                                        <a href="{{ route('administration.admission-configs.index') }}?institution_id={{ $institution->id }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 transition-colors uppercase tracking-wider">
                                            <i data-lucide="settings-2" size="14" class="mr-1"></i>
                                            Manage All
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex flex-col gap-2">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 w-fit">
                                        Not Configured
                                    </span>
                                    @if($isAdmin)
                                    <a href="{{ route('administration.admission-configs.create') }}?institution_id={{ $institution->id }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wider">
                                        + ADD NEW
                                    </a>
                                    @endif
                                </div>
                            @endif
                        </td>
                        @endif
                         <td class="px-6 py-4 whitespace-nowrap">
                            @if($institution->bank_account_no)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Configured
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">
                                    Pending
                                </span>
                            @endif
                        </td>
                        @if($isAdmin)
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('administration.institutions.users', $institution->id) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="Manage Users">
                                    <i data-lucide="users" size="18"></i>
                                </a>
                                <a href="{{ route('administration.institutions.edit', $institution->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Edit">
                                    <i data-lucide="edit-3" size="18"></i>
                                </a>
                                <form action="{{ route('administration.institutions.destroy', $institution->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this institution?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" size="18"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? (optional($org->configuration)->is_admission_enabled ? 5 : 4) : (optional($org->configuration)->is_admission_enabled ? 4 : 3) }}" class="px-6 py-10 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="building" size="48" class="text-slate-300 mb-4"></i>
                                <p class="text-lg font-medium text-slate-900">No institutions found</p>
                                @if($isAdmin)
                                <p class="text-sm text-slate-500 mb-4">Get started by creating your first institution.</p>
                                <a href="{{ route('administration.institutions.create') }}" class="text-blue-600 hover:text-blue-800 font-medium">Create Institution &rarr;</a>
                                @else
                                <p class="text-sm text-slate-500 mb-4">No institutions available.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
