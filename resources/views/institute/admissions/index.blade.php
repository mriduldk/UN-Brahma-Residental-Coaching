@extends('layouts.institute')

@section('title', 'Admissions Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Admissions Dashboard</h1>
            <p class="text-slate-500 mt-1">Manage admissions for your assigned institutions.</p>
        </div>
        
        <!-- Institution Selector -->
        <div>
            <form action="{{ route('institute.admissions.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <!-- Institution Selector -->
                <select name="institution_id" onchange="this.form.submit()" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-auto p-2.5">
                    @if($institutions->isEmpty())
                        <option value="">No Institutions Assigned</option>
                    @else
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ ($selectedInstitution && $selectedInstitution->id == $inst->id) ? 'selected' : '' }}>
                                {{ $inst->institution_name }} ({{ $inst->institution_code }})
                            </option>
                        @endforeach
                    @endif
                </select>

                <!-- Application Form Selector -->
                @if($selectedInstitution)
                    <select name="application_form_id" onchange="this.form.submit()" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-auto p-2.5">
                        <option value="">All Application Forms</option>
                        @foreach($applicationForms as $form)
                            <option value="{{ $form->id }}" {{ ($selectedFormId == $form->id) ? 'selected' : '' }}>
                                {{ $form->application_name }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <!-- Admission/Session Selector -->
                @if($selectedInstitution)
                    <select name="admission_id" onchange="this.form.submit()" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-auto p-2.5">
                         @if($admissions->isEmpty())
                            <option value="">No Admissions Configured</option>
                         @else
                            <option value="">All Active Sessions</option>
                            @foreach($admissions as $adm)
                                <option value="{{ $adm->id }}" {{ ($selectedAdmission && $selectedAdmission->id == $adm->id) ? 'selected' : '' }}>
                                    Admission Session : {{ $adm->academic_session }}
                                </option>
                            @endforeach
                         @endif
                    </select>
                @endif
            </form>
        </div>
    </div>

    @if($selectedInstitution)
    <!-- Institution Details Card -->
    <div x-data="{ expanded: false }" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center justify-between">
             <div class="flex items-center gap-3">
                 <div class="btn-icon bg-blue-100 text-blue-600 rounded-lg p-2">
                     <i data-lucide="building-2" size="20"></i>
                 </div>
                 <div>
                     <h3 class="text-lg font-semibold text-slate-900">{{ $selectedInstitution->institution_name }}</h3>
                     <p class="text-sm text-slate-500">
                        {{ $selectedInstitution->institution_code }} 
                        @php
                            $selectedForm = $selectedFormId ? $applicationForms->find($selectedFormId) : null;
                        @endphp
                        @if($selectedForm)
                            <span class="mx-2 text-slate-300">|</span> 
                            <span class="font-bold text-blue-600">{{ $selectedForm->application_name }}</span>
                        @endif
                        @if($selectedAdmission)
                            <span class="mx-2 text-slate-300">|</span> 
                            <span class="font-medium text-slate-700">Admission Session : {{ $selectedAdmission->academic_session }}</span>
                        @endif
                     </p>
                 </div>
             </div>
             <button @click="expanded = !expanded" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i data-lucide="chevron-down" class="transform transition-transform duration-200" :class="{'rotate-180': expanded}"></i>
             </button>
        </div>
        <div x-show="expanded" x-collapse class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 bg-white border-t border-slate-100">
             <div class="space-y-1">
                 <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Contact</p>
                 <p class="text-sm text-slate-900 font-medium">{{ $selectedInstitution->contact_number ?? 'N/A' }}</p>
                 <p class="text-sm text-slate-500">{{ $selectedInstitution->email ?? 'N/A' }}</p>
             </div>
             <div class="space-y-1">
                 <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Address</p>
                 <p class="text-sm text-slate-900">{{ $selectedInstitution->address ?? 'N/A' }}</p>
             </div>
             <div class="space-y-1">
                 <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Bank Details</p>
                 @if($selectedInstitution->bank_account_no)
                    <p class="text-sm text-slate-900">{{ $selectedInstitution->bank_name }}</p>
                    <p class="text-sm text-slate-500">AC: {{ $selectedInstitution->bank_account_no }}</p>
                 @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        Not Configured
                    </span>
                 @endif
             </div>
             <div class="space-y-1">
                 <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Payment Gateway</p>
                 @if($selectedInstitution->razorpay_key && $selectedInstitution->razorpay_secret)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        <i data-lucide="shield-check" size="12" class="mr-1"></i> Configured
                    </span>
                 @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        <i data-lucide="shield-alert" size="12" class="mr-1"></i> Not Configured
                    </span>
                 @endif
             </div>
             <div class="space-y-1">
                 <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">My Role</p>
                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ auth()->user()->isInstitutionAdmin($selectedInstitution->id) ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-800' }}">
                     {{ auth()->user()->isInstitutionAdmin($selectedInstitution->id) ? 'Admin' : 'Viewer' }}
                 </span>
             </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Admissions</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($stats['total_admissions']) }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <i data-lucide="users" size="24"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Revenue</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">₹ {{ number_format($stats['total_revenue']) }}</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <i data-lucide="indian-rupee" size="24"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Unsettled Cash</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">₹ {{ number_format($stats['unsettled_cash']) }}</h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                <i data-lucide="zap" size="24"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-[10px] font-bold uppercase">
                    <span class="text-indigo-600">Full: {{ $stats['full_payment'] }}</span>
                    <span class="text-slate-300">|</span>
                    <span class="text-violet-600">EMI: {{ $stats['emi_payment'] }}</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-1 font-medium">Payment Plan Distribution</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                <i data-lucide="pie-chart" size="24"></i>
            </div>
        </div>
    </div>

    @if(!$selectedInstitution->razorpay_key || !$selectedInstitution->razorpay_secret)
    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-6 mb-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
        <div class="flex items-center gap-4 text-center md:text-left">
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 mx-auto md:mx-0">
                <i data-lucide="shield-alert" size="24"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-rose-900">Payment Gateway Not Configured</h3>
                <p class="text-sm text-rose-700">Online payments are currently disabled. Please contact your <strong>Organisation Admin</strong> to configure the Razorpay API keys for this institution.</p>
            </div>
        </div>
        <div class="px-5 py-2.5 bg-rose-100 text-rose-700 rounded-xl border border-rose-200 text-[10px] font-bold uppercase tracking-widest flex items-center gap-2">
            <i data-lucide="info" size="14"></i> Admin Action Required
        </div>
    </div>
    @endif

    @if($admissions->isEmpty())
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4 text-center md:text-left">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 mx-auto md:mx-0">
                <i data-lucide="alert-triangle" size="24"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-amber-900">No Admissions Configured</h3>
                <p class="text-sm text-amber-700">Admission fees, EMI plans, and cash rules must be set up before you can process any admissions for this institution.</p>
            </div>
        </div>
        @if(auth()->user()->isInstitutionAdmin($selectedInstitution->id))
        <a href="{{ route('institute.admission-configs.create') }}?institution_id={{ $selectedInstitution->id }}" 
           class="inline-flex items-center px-6 py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-200 uppercase tracking-wider text-xs flex-shrink-0">
            <i data-lucide="plus" size="16" class="mr-2"></i> Add Configuration
        </a>
        @endif
    </div>
    @endif

    <!-- Applications List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-slate-900">Recent Applications</h2>
            
            <!-- Search Box -->
            <form action="{{ route('institute.admissions.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <input type="hidden" name="institution_id" value="{{ request('institution_id') }}">
                @if($selectedFormId)
                <input type="hidden" name="application_form_id" value="{{ $selectedFormId }}">
                @endif
                @if($selectedAdmission)
                <input type="hidden" name="admission_id" value="{{ $selectedAdmission->id }}">
                @endif
                
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="text-slate-400" size="16"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="block w-full pl-10 pr-3 py-2 border border-slate-300 rounded-lg leading-5 bg-white placeholder-slate-500 focus:outline-none focus:placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm" 
                           placeholder="Search by ID, Name, Phone...">
                </div>
                <button type="submit" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                    Search
                </button>
            </form>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Application ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Applicant Info</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Trade / Form</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Enrollment</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Payment Detail</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($applications as $app)
                    @php 
                        $admission = $app->admission_student; 
                        $isPaid = $admission && $admission->pending_amount <= 0;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-blue-600 font-mono">{{ $app->application_code }}</span>
                            <div class="text-xs text-slate-500">{{ $app->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-slate-900">{{ $app->application_student->full_name ?? 'N/A' }}</div>
                            <div class="text-[11px] text-slate-500 flex flex-col">
                                <span>{{ $app->application_student->phone_number ?? '-' }}</span>
                                <span class="text-slate-400">{{ $app->application_student->email ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             <div class="text-sm text-slate-700">{{ $app->application_form->application_name ?? 'N/A' }}</div>
                             <div class="text-xs text-slate-400">{{ $app->application_form->session ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($admission)
                                <div class="flex flex-col gap-1">
                                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-bold rounded-md bg-emerald-100 text-emerald-800 border border-emerald-200 w-fit">
                                        <i data-lucide="check-circle" size="12" class="mr-1"></i> Admitted
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-500">{{ $admission->admission_code }}</span>
                                </div>
                            @else
                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-md bg-slate-100 text-slate-600 border border-slate-200 w-fit">
                                    Not Admitted
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($admission)
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold uppercase {{ $isPaid ? 'text-emerald-600' : 'text-amber-600' }}">
                                            {{ $admission->payment_type }}
                                        </span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded {{ $isPaid ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} font-bold">
                                            {{ $isPaid ? 'COMPLETED' : 'PENDING' }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        Paid: <span class="font-bold text-slate-700">₹{{ number_format($admission->total_paid_amount) }}</span> 
                                        @if($admission->pending_amount > 0)
                                            / Bal: <span class="font-bold text-rose-600">₹{{ number_format($admission->pending_amount) }}</span>
                                        @endif
                                    </div>
                                    
                                    @php
                                        $unsettledCash = $app->admission_payments->where('payment_type', 'cash')->where('is_settled', false)->sum('paid_amount');
                                    @endphp
                                    
                                    @if($unsettledCash > 0)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 text-[9px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100 uppercase animate-pulse">
                                                <i data-lucide="zap" size="10"></i> Settlement Pending: ₹{{ number_format($unsettledCash) }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($admission->next_payment_date && $admission->pending_amount > 0)
                                        <div class="text-[9px] text-indigo-500 font-medium">
                                            Next Due: {{ \Carbon\Carbon::parse($admission->next_payment_date)->format('d M, Y') }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-xs text-slate-400 italic">No Payment Info</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('institute.admissions.show', $app->id) }}" class="text-blue-600 hover:text-blue-900 flex items-center justify-end gap-1">
                                View <i data-lucide="arrow-right" size="14"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="inbox" size="40" class="text-slate-300 mb-2"></i>
                                <p>No applications found for this session.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $applications->links() }}
        </div>
    </div>

    @else
    <div class="text-center py-20 bg-white rounded-xl border border-slate-200 border-dashed">
        <div class="mx-auto h-12 w-12 text-slate-400">
            <i data-lucide="building" size="48"></i>
        </div>
        <h3 class="mt-2 text-sm font-medium text-slate-900">No Institutions Mapped</h3>
        <p class="mt-1 text-sm text-slate-500">You are not mapped to any institution yet.</p>
    </div>
    @endif
</div>
@endsection
