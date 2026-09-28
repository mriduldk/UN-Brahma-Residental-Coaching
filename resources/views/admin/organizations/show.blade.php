@extends('layouts.admin')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500 mb-2">
            <a href="{{ route('admin.organizations.index') }}" class="hover:text-purple-600 transition-colors">Organizations</a>
            <i data-lucide="chevron-right" size="14"></i>
            <span class="text-slate-800">Overview</span>
        </div>
        <div class="flex items-center gap-3">
            <h1 class="text-3xl font-black tracking-tight text-slate-800">{{ $organization->organization_name }}</h1>
            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-sm font-bold border border-slate-200">
                {{ $organization->organization_code }}
            </span>
        </div>
    </div>
    
    <div class="flex gap-2">
        <a href="{{ route('admin.organizations.edit', $organization->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition-colors flex items-center gap-2">
            <i data-lucide="edit-2" size="16"></i> Edit Properties
        </a>
    </div>
</div>

<!-- Metrics Quick Look -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                <i data-lucide="graduation-cap" size="20"></i>
            </div>
        </div>
        <p class="text-sm font-bold text-slate-500 mb-1">Total Institutes</p>
        <h3 class="text-2xl font-black text-slate-800">{{ $organization->institutions->count() }}</h3>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <i data-lucide="users" size="20"></i>
            </div>
        </div>
        <p class="text-sm font-bold text-slate-500 mb-1">Total Applications</p>
        <h3 class="text-2xl font-black text-slate-800">{{ number_format($totalApplications) }}</h3>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <i data-lucide="indian-rupee" size="20"></i>
            </div>
        </div>
        <p class="text-sm font-bold text-slate-500 mb-1">Total Gross Revenue</p>
        <h3 class="text-2xl font-black text-slate-800">₹{{ number_format($totalRevenue, 2) }}</h3>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <i data-lucide="clipboard-check" size="20"></i>
            </div>
        </div>
        <p class="text-sm font-bold text-slate-500 mb-1">Approved Admissions</p>
        <h3 class="text-2xl font-black text-slate-800">{{ number_format($approvedApplications) }}</h3>
    </div>

</div>

<!-- Main Details Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Core Organization Info -->
    <div class="lg:col-span-1 space-y-6">
        
        <!-- Organization Master Info Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="info" size="18" class="text-purple-500"></i> Entity Profile
            </h3>
            
            <div class="space-y-4">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Type</span>
                    <span class="font-medium text-slate-800 bg-slate-100 px-2 py-1 rounded text-sm">
                        {{ ucfirst($organization->type) }}
                    </span>
                </div>
                
                @if($organization->registration_no)
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Registration/CIN</span>
                    <span class="font-medium text-slate-800">{{ $organization->registration_no }}</span>
                </div>
                @endif
                
                @if($organization->pan_no)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">PAN</span>
                        <span class="font-medium text-slate-800">{{ $organization->pan_no }}</span>
                    </div>
                    @if($organization->gst_no)
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">GSTIN</span>
                        <span class="font-medium text-slate-800">{{ $organization->gst_no }}</span>
                    </div>
                    @endif
                </div>
                @endif

                @if($organization->industry || $organization->category)
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Sector / Category</span>
                    <span class="font-medium text-slate-800">{{ $organization->industry ?? 'N/A' }} / {{ $organization->category ?? 'N/A' }}</span>
                </div>
                @endif
                
                @if($organization->website)
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Website</span>
                    <a href="{{ $organization->website }}" target="_blank" class="font-medium text-blue-600 hover:underline flex items-center gap-1">
                        {{ $organization->website }} <i data-lucide="external-link" size="12"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Primary Admin Details -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="shield-check" size="18" class="text-blue-500"></i> Root Administrator
            </h3>
            
            @if($organization->user)
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-black shrink-0">
                    {{ substr($organization->user->name, 0, 1) }}
                </div>
                <div>
                    <h4 class="font-bold text-slate-800">{{ $organization->user->name }}</h4>
                    <p class="text-sm text-slate-500 flex items-center gap-1 mt-0.5">
                        <i data-lucide="mail" size="12"></i> {{ $organization->user->email }}
                    </p>
                    <span class="inline-block mt-2 text-xs font-bold px-2 py-0.5 bg-blue-50 text-blue-600 rounded">
                        System Account ID: #{{ $organization->user->id }}
                    </span>
                </div>
            </div>
            @else
            <div class="flex items-center gap-2 text-amber-600 bg-amber-50 p-3 rounded-xl text-sm font-medium">
                <i data-lucide="alert-circle" size="16"></i> No active administrator assigned.
            </div>
            @endif
        </div>

        <!-- Finances & EMI Snapshots -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
            <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                <i data-lucide="wallet" size="18" class="text-emerald-500"></i> Liability Snapshot
            </h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl">
                    <span class="text-sm font-bold text-slate-600">Pending Student EMIs</span>
                    <span class="font-black text-rose-600">₹{{ number_format($totalPendingEmi, 2) }}</span>
                </div>
                <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl border {{ $unsettledCash > 0 ? 'border-amber-200' : 'border-transparent' }}">
                    <span class="text-sm font-bold text-slate-600">Unsettled Cash Collected</span>
                    <span class="font-black {{ $unsettledCash > 0 ? 'text-amber-600' : 'text-slate-800' }}">₹{{ number_format($unsettledCash, 2) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Right Column: Relational Data (Institutions, Forms, etc.) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Connected Institutions Drill-down -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="network" size="18" class="text-indigo-500"></i> Organization Structure & Analytics
                </h3>
                <span class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                    Click an row to open Institute Details
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl w-10 text-center"></th>
                            <th class="px-6 py-4">Institution Name</th>
                            <th class="px-6 py-4">Code</th>
                            <th class="px-6 py-4 rounded-tr-xl text-right">Forms & Analytics</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($organization->institutions as $inst)
                        <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer group" onclick="window.location='{{ route('admin.institutions.show', $inst->id) }}'">
                            <td class="px-6 py-4 text-center">
                                <i data-lucide="external-link" size="16" class="text-slate-300 group-hover:text-indigo-600 transition-colors"></i>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800 flex items-center gap-2">
                                <div class="w-8 h-8 rounded bg-indigo-50 flex items-center justify-center text-indigo-600">
                                    <i data-lucide="building" size="14"></i>
                                </div>
                                {{ $inst->institution_name }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-medium">
                                <span class="px-2 py-1 bg-slate-100 rounded text-xs font-bold border border-slate-200">{{ $inst->institution_code }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    {{ $inst->application_forms->count() }} Forms
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                <i data-lucide="package-open" size="32" class="mx-auto mb-3 text-slate-300"></i>
                                <p class="font-medium text-slate-600">No institutions connected to this organization yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Admission Configurations Snapshot -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="settings-2" size="18" class="text-rose-500"></i> Admission Configurations
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Session/Year</th>
                            <th class="px-6 py-4">Target Form</th>
                            <th class="px-6 py-4">Base Fee</th>
                            <th class="px-6 py-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($admissionConfigs as $config)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $config->academic_session }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $config->application_form->application_name ?? 'Global Fallback' }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">₹{{ number_format($config->admission_fee, 2) }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $config->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $config->is_active ? 'Live' : 'Hidden' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                <i data-lucide="clipboard-x" size="32" class="mx-auto mb-3 text-slate-300"></i>
                                <p class="font-medium text-slate-600">No admission rules configured.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Application Forms Snapshot -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="file-text" size="18" class="text-orange-500"></i> Active Application Forms
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Form Name</th>
                            <th class="px-6 py-4">Session/Year</th>
                            <th class="px-6 py-4 text-right">Reg. Fee</th>
                            <th class="px-6 py-4 text-right">Admission Fee</th>
                            <th class="px-6 py-4 text-right">Total Amount</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($applicationForms as $form)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-800 block">{{ $form->application_name }}</span>
                                <span class="text-xs text-slate-500 block mt-0.5">Code: {{ $form->application_code }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $form->session }}</td>
                            <td class="px-6 py-4 text-right text-slate-600 font-medium">₹{{ number_format($form->application_fee, 2) }}</td>
                            <td class="px-6 py-4 text-right text-slate-600 font-medium">₹{{ number_format($form->admission_fee, 2) }}</td>
                            <td class="px-6 py-4 text-right text-indigo-600 font-black">₹{{ number_format($form->application_fee + $form->admission_fee, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $form->status == 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($form->status ?? 'Draft') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                <i data-lucide="file-minus" size="32" class="mx-auto mb-3 text-slate-300"></i>
                                <p class="font-medium text-slate-600">No application forms created yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>



    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAccordion(targetId, element, event = null) {
        // Prevent event bubbling if clicking a nested accordion row
        if(event) {
            event.stopPropagation();
        }

        const targetRow = document.getElementById(targetId);
        const icon = element.querySelector('.accordion-icon');
        
        if (targetRow.classList.contains('hidden')) {
            // Open
            targetRow.classList.remove('hidden');
            element.classList.add('bg-slate-50'); // Highlight active row
            if(icon) icon.style.transform = 'rotate(90deg)';
        } else {
            // Close
            targetRow.classList.add('hidden');
            element.classList.remove('bg-slate-50');
            if(icon) icon.style.transform = 'rotate(0deg)';
        }
    }
</script>
@endpush
