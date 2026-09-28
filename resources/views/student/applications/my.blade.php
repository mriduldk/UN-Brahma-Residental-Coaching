@extends('layouts.student')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">My Applications</h1>
            <p class="text-slate-500 text-sm mt-1">Track the status and payment updates of your submitted forms.</p>
        </div>
        
        <!-- Filter/Search -->
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative group">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors" size="18"></i>
                <input type="text" placeholder="Search Application..." 
                       class="w-full sm:w-64 pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
            </div>
            
            <div class="relative">
                <i data-lucide="filter" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size="18"></i>
                <select class="w-full sm:w-48 pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none shadow-sm cursor-pointer">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="payment_pending">Payment Pending</option>
                    <option value="submitted">Submitted</option>
                </select>
                <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" size="16"></i>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success') || request()->query('payment_success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in">
            <div class="bg-green-100 p-1.5 rounded-full">
                <i data-lucide="check" class="w-4 h-4"></i>
            </div>
            <span class="font-medium text-sm">{{ session('success') ?? 'Payment successful! Application submitted.' }}</span>
        </div>
    @endif

    @if(session('error') || request()->query('payment_error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in">
            <div class="bg-red-100 p-1.5 rounded-full">
                <i data-lucide="x" class="w-4 h-4"></i>
            </div>
            <span class="font-medium text-sm">{{ session('error') ?? 'Payment verification failed. Please try again or check status.' }}</span>
        </div>
    @endif
    
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in">
            <div class="bg-blue-100 p-1.5 rounded-full">
                <i data-lucide="info" class="w-4 h-4"></i>
            </div>
            <span class="font-medium text-sm">{{ session('info') }}</span>
        </div>
    @endif

    <!-- Applications List -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-100 backdrop-blur-sm">
                    <tr>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Application Details</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Timeline</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Status</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs">Payment</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase tracking-wider text-xs text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                    <tr class="group hover:bg-slate-50/80 transition-colors duration-200">
                        <!-- App Details -->
                        <td class="px-6 py-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="file-text" size="20"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors">
                                        {{ $app->application_form->application_name ?? 'Unknown Form' }}
                                    </h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="font-mono text-[10px] bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200">
                                            {{ $app->application_code ?? '-----' }}
                                        </span>
                                    </div>
                                    
                                    @if($app->status == 'draft')
                                        <div class="mt-3 w-48">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Progress</span>
                                                <span class="text-[10px] font-bold text-blue-600">{{ (($app->current_step - 1) / 5) * 100 }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-200/50">
                                                <div class="bg-blue-500 h-full rounded-full transition-all duration-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]" 
                                                     style="width: {{ (($app->current_step - 1) / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Timeline -->
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                                    <i data-lucide="clock" size="12" class="text-slate-400"></i>
                                    Updated {{ $app->updated_at->diffForHumans() }}
                                </span>
                                <span class="text-[10px] text-slate-400 pl-4.5">
                                    Created {{ $app->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </td>

                        <!-- App Status -->
                        <td class="px-6 py-4">
                            @php
                                $statusConfig = match($app->status) {
                                    'draft' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'icon' => 'edit-3'],
                                    'submitted' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'icon' => 'send'],
                                    'approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'icon' => 'check-circle'],
                                    'rejected' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'icon' => 'x-circle'],
                                    default => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-100', 'icon' => 'help-circle'],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                <i data-lucide="{{ $statusConfig['icon'] }}" size="12"></i>
                                {{ ucfirst($app->status) }}
                            </span>

                            @if($app->application_form->is_approval_required)
                                <div class="mt-2 flex flex-col items-start gap-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                    <span>Approval:</span>
                                    @if($app->approval_status == 'approved')
                                        <span class="text-green-600 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">Approved</span>
                                    @elseif($app->approval_status == 'rejected')
                                        <span class="text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100">Rejected</span>
                                    @else
                                        <span class="text-yellow-600 bg-yellow-50 px-1.5 py-0.5 rounded border border-yellow-100">Pending</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <!-- Payment Status -->
                        <td class="px-6 py-4">
                            @php
                                $form = $app->application_form;
                                $fee = $form->is_application_fee_required ? $form->getApplicationFeeFor($app->student, $app) : 0;
                                $feeRequired = $form->is_application_fee_required && $fee > 0;
                            @endphp
                            
                            @if(!$feeRequired)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                    Not Required
                                </span>
                            @elseif($app->status == 'draft')
                                <span class="text-slate-400 text-xs italic">-</span>
                            @elseif($app->payment_status == 'paid')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-100">
                                    <i data-lucide="check" size="12"></i> Paid
                                </span>
                            @else
                                <div class="flex flex-col items-start gap-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                        <i data-lucide="alert-circle" size="12"></i> Pending
                                    </span>
                                    @if($fee > 0)
                                        <span class="text-[10px] font-bold text-slate-400 pl-1">₹{{ number_format($fee, 0) }} Due</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            @php
                                $form = $app->application_form;
                                $isExpired = $form->status !== 'active' || ($form->deadline && $form->deadline->isPast());
                            @endphp
                            <div class="flex items-center justify-end gap-2">
                                @if($app->status == 'draft')
                                    @if($isExpired)
                                        <button disabled class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-bold rounded-lg border border-slate-200 cursor-not-allowed" title="Application form is no longer accepting submissions">
                                            Closed <i data-lucide="lock" size="14"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('student.application.form', ['form_id' => $app->application_form_id]) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-all shadow-sm shadow-blue-200 hover:shadow-blue-300 transform hover:-translate-y-0.5">
                                            Continue <i data-lucide="arrow-right" size="14"></i>
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('student.application.show', $app->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 rounded-lg transition-all border border-emerald-200 shadow-sm hover:shadow text-xs font-bold" 
                                       title="View Details">
                                        <i data-lucide="eye" size="16"></i> View
                                    </a>

                                    @if($form->is_application_fee_required && $app->payment_status != 'paid')
                                        <a href="{{ route('student.application.payment', ['id' => $app->id]) }}" 
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 text-white text-xs font-bold rounded-lg hover:bg-teal-700 transition-all shadow-sm shadow-teal-200 hover:shadow-teal-300 transform hover:-translate-y-0.5 ml-1">
                                            <i data-lucide="credit-card" size="14"></i> Pay Now
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4 border border-slate-100">
                                    <i data-lucide="folder-open" size="32" class="text-slate-300"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 mb-1">No applications yet</h3>
                                <p class="text-slate-500 text-sm mb-6">Start your journey by applying for a new course or program.</p>
                                <a href="{{ route('student.applications.index') }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white font-bold text-sm rounded-xl hover:bg-blue-700 transition-all shadow-sm shadow-blue-200 hover:shadow-blue-300 transform hover:-translate-y-0.5">
                                    <i data-lucide="plus-circle" size="18"></i> Browse Applications
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Footer / Pagination -->
        @if($applications->count() > 0)
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-between items-center">
            <span class="text-xs font-medium text-slate-500">
                Showing all <span class="font-bold text-slate-700">{{ $applications->count() }}</span> applications
            </span>
            <!-- Add pagination links here if paginated -->
        </div>
        @endif
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out forwards;
    }
</style>
@endsection
