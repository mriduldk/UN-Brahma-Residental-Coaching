@extends('layouts.administration')

@section('title', 'Admission Configurations')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
             <a href="{{ route('administration.institutions.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition-all shadow-sm">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Admission Configurations</h1>
                <p class="text-slate-500 mt-1">Manage admission fees, sessions, and payment rules globally.</p>
            </div>
        </div>
        <a href="{{ route('administration.admission-configs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
            <i data-lucide="plus" size="16" class="mr-2"></i> New Configuration
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Institution</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Application Form</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Fee</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Options</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Created By</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($admissions as $config)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-900">{{ $config->institution->institution_name }}</div>
                            <div class="text-xs text-slate-500">{{ $config->institution->institution_code }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-900">{{ $config->application_form->application_name ?? 'N/A' }}</div>
                            <div class="text-xs text-slate-500">{{ $config->academic_session }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-slate-900">₹ {{ number_format($config->admission_fee, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 space-y-1">
                            @if($config->allow_full_payment)
                                <span class="flex items-center gap-1 text-xs"><i data-lucide="check" size="12" class="text-green-500"></i> Full Payment</span>
                            @endif
                            @if($config->allow_emi)
                                <span class="flex items-center gap-1 text-xs"><i data-lucide="check" size="12" class="text-indigo-500"></i> EMI</span>
                            @endif
                            @if($config->allow_cash_payment)
                                <span class="flex items-center gap-1 text-xs"><i data-lucide="check" size="12" class="text-emerald-500"></i> Cash</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             <div class="text-sm text-slate-900">{{ $config->creator->name ?? 'Unknown' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($config->status == 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('administration.admission-configs.edit', $config->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-500">
                            No admission configurations found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $admissions->links() }}
        </div>
    </div>
</div>
@endsection
