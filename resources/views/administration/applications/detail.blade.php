@extends('layouts.administration')

@section('title', 'Review Application')

@section('content')
<div class="space-y-6 w-full">
    <a href="{{ route('administration.applications') }}" class="flex items-center text-slate-500 hover:text-slate-800 font-medium group w-fit">
        <i data-lucide="arrow-left" size="20" class="mr-2 group-hover:-translate-x-1 transition-transform"></i> Back to Applications
    </a>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <!-- Detail Header -->
        <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start bg-slate-50/50">
            <div class="flex items-center space-x-5 mb-4 sm:mb-0">
                <div class="w-20 h-20 bg-slate-200 rounded-2xl flex items-center justify-center text-slate-400 border-4 border-white shadow-sm overflow-hidden">
                    @if($application->application_documents && !empty($application->application_documents->photo))
                        <img src="{{ Storage::url($application->application_documents->photo) }}" alt="Applicant Photo" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="image" size="36"></i>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">{{ $application->application_student->full_name ?? 'N/A' }}</h2>
                    <p class="text-slate-500 font-medium text-sm">Application ID: <span class="font-mono text-slate-700 font-bold bg-slate-100 px-2 py-0.5 rounded">{{ $application->application_code }}</span></p>
                    <div class="mt-3 flex flex-wrap gap-2 items-center">
                        <span class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border border-blue-200">{{ $application->application_form->application_name ?? 'N/A' }}</span>
                        
                        @php
                            $appliedForVal = $application->applied_for ?? $application->application_student?->applied_for ?? null;
                            $examCenterVal = $application->examination_center ?? $application->application_student?->examination_center ?? null;
                        @endphp
                        @if($appliedForVal)
                            <span class="bg-indigo-100 text-indigo-800 px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wide border border-indigo-200 flex items-center shadow-2xs">
                                <i data-lucide="compass" size="13" class="mr-1 text-indigo-600"></i> Applied For: {{ $appliedForVal }}
                            </span>
                        @endif
                        @if($examCenterVal)
                            <span class="bg-amber-100 text-amber-900 px-2.5 py-1 rounded-lg text-xs font-black uppercase tracking-wide border border-amber-200 flex items-center shadow-2xs">
                                <i data-lucide="map-pin" size="13" class="mr-1 text-amber-600"></i> Exam Center: {{ $examCenterVal }}
                            </span>
                        @endif

                         @if($application->approval_status == 'approved')
                            <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border border-green-200 flex items-center"><i data-lucide="check-circle" size="12" class="mr-1"></i> Approved</span>
                        @elseif($application->approval_status == 'rejected')
                            <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border border-red-200 flex items-center"><i data-lucide="x-circle" size="12" class="mr-1"></i> Rejected</span>
                        @else
                            @if($application->application_form->is_approval_required)
                                <span class="bg-yellow-50 text-yellow-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border border-yellow-100 flex items-center"><i data-lucide="alert-circle" size="12" class="mr-1"></i> Pending Verification</span>
                            @else
                                <span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border border-blue-100 flex items-center"><i data-lucide="info" size="12" class="mr-1"></i> Submitted</span>
                            @endif
                        @endif
                    </div>
                </div>
            @if($application->status === 'submitted')
            <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto mt-4 sm:mt-0">
                <a href="{{ route('administration.admin-cards.print', $application->id) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider rounded-xl transition-colors">
                    <i data-lucide="printer" size="14"></i> Print Admit Card
                </a>
                <a href="{{ route('administration.admin-cards.download', $application->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md shadow-blue-200">
                    <i data-lucide="download" size="14"></i> Download Admit Card
                </a>
            </div>
            @endif
        </div>

        <div class="px-8 py-8 space-y-12">
            <!-- Header-like Quick Info Row: Approval -->
            @if($application->application_form->is_approval_required)
            <section class="bg-indigo-50/30 border border-indigo-100 p-6 rounded-2xl shadow-sm">
                <h3 class="text-xs font-bold text-indigo-800 uppercase tracking-widest flex items-center mb-4">
                    <i data-lucide="clipboard-check" size="14" class="mr-2"></i> Approval Status
                </h3>
                <div class="flex items-center">
                    @if($application->approval_status == 'approved')
                        <div class="bg-green-100 text-green-600 p-2.5 rounded-full mr-4 shadow-sm"><i data-lucide="check-circle" size="20"></i></div>
                    @elseif($application->approval_status == 'rejected')
                        <div class="bg-red-100 text-red-600 p-2.5 rounded-full mr-4 shadow-sm"><i data-lucide="x-circle" size="20"></i></div>
                    @else
                        <div class="bg-yellow-100 text-yellow-600 p-2.5 rounded-full mr-4 shadow-sm"><i data-lucide="clock" size="20"></i></div>
                    @endif
                    <div>
                        <p class="text-lg font-bold text-slate-800">{{ ucfirst($application->approval_status ?? 'Pending') }}</p>
                        @if($application->approval_remarks)
                            <p class="text-xs text-slate-500 mt-1 italic">"{{ $application->approval_remarks }}"</p>
                        @endif
                    </div>
                </div>
                
                @if(auth()->user()->isAdmin(auth()->user()->currentOrganization()->id))
                <form action="{{ route('administration.applications.approval', $application->id) }}" method="POST" class="mt-6 flex flex-col md:flex-row gap-3">
                    @csrf
                    <div class="flex-1 md:flex-none md:w-48">
                        <select name="approval_status" class="w-full text-sm py-2 px-3 border border-slate-200 rounded-lg bg-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-medium">
                            <option value="pending" {{ $application->approval_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $application->approval_status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $application->approval_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <input type="text" name="approval_remarks" class="w-full text-sm py-2 px-3 border border-slate-200 rounded-lg bg-white outline-none focus:ring-2 focus:ring-indigo-500 transition-all" value="{{ $application->approval_remarks }}" placeholder="Quick remarks...">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2 rounded-lg transition-colors flex items-center justify-center">
                        <i data-lucide="save" size="14" class="mr-2"></i> Save Status
                    </button>
                </form>
                @endif
            </section>
            @endif

            <!-- Payment Information -->
            @if($application->application_form->is_application_fee_required)
            @php
                $feeAmount = $application->application_form->getApplicationFeeFor($application->student, $application);
                $capturedTxn = $application->payment_transactions->where('status', 'captured')->last();
                $allTxns = $application->payment_transactions;
            @endphp
            <section>
                <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-6 tracking-wider flex items-center">
                    <i data-lucide="credit-card" size="18" class="mr-2"></i> Payment Information
                </h3>
                
                @if($feeAmount == 0)
                    <div class="bg-slate-50 border border-slate-200 p-8 rounded-2xl shadow-sm flex flex-col items-center justify-center text-center">
                        <div class="bg-slate-100 text-slate-500 p-4 rounded-full w-16 h-16 flex items-center justify-center mb-4 shadow-sm border border-slate-200">
                            <i data-lucide="shield-check" size="32"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-800 mb-1">Fee Exempted</h4>
                        <p class="text-sm text-slate-500 font-medium">No application fee is required for this candidate based on their category criteria.</p>
                    </div>
                @else
                    <!-- Main Payment Status Card -->
                    <div class="bg-white border {{ $application->payment_status == 'paid' ? 'border-green-200' : 'border-red-200' }} rounded-2xl shadow-sm overflow-hidden mb-6">
                        <div class="{{ $application->payment_status == 'paid' ? 'bg-green-50' : 'bg-red-50' }} px-6 py-6 border-b {{ $application->payment_status == 'paid' ? 'border-green-100' : 'border-red-100' }} flex flex-col md:flex-row justify-between items-center gap-6">
                            <div class="flex items-center">
                                <div class="{{ $application->payment_status == 'paid' ? 'bg-green-500 shadow-green-200' : 'bg-red-500 shadow-red-200' }} text-white p-4 rounded-full mr-5 shadow-lg">
                                    <i data-lucide="{{ $application->payment_status == 'paid' ? 'check-circle' : 'alert-circle' }}" size="28"></i>
                                </div>
                                <div>
                                    <p class="text-xs {{ $application->payment_status == 'paid' ? 'text-green-700' : 'text-red-700' }} font-bold uppercase tracking-widest mb-1">Payment Status</p>
                                    <p class="text-3xl font-black text-slate-800 leading-none">{{ ucfirst($application->payment_status) }}</p>
                                </div>
                            </div>
                            <div class="text-center md:text-right bg-white/60 px-6 py-3 rounded-xl border {{ $application->payment_status == 'paid' ? 'border-green-200' : 'border-red-200' }}">
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mb-1">Required Amount</p>
                                <p class="text-3xl font-black text-slate-800 leading-none">₹ {{ number_format($feeAmount, 2) }}</p>
                            </div>
                        </div>
                        
                        @if($application->payment_status == 'paid' && $capturedTxn)
                        <div class="px-6 py-6 grid grid-cols-2 md:grid-cols-4 gap-6 bg-white">
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1.5">Transaction ID</span>
                                <span class="font-mono text-sm font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded">{{ $capturedTxn->razorpay_payment_id ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1.5">Payment Date</span>
                                <span class="font-bold text-sm text-slate-700">{{ $capturedTxn->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1.5">Method</span>
                                <span class="font-bold text-sm text-slate-700 uppercase">{{ $capturedTxn->method ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1.5">Amount Paid</span>
                                <span class="font-bold text-base text-green-600">₹ {{ number_format($capturedTxn->amount, 2) }}</span>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($allTxns && $allTxns->count() > 1)
                    <!-- Transaction History -->
                    <div class="mt-6">
                        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-3 flex items-center"><i data-lucide="history" size="14" class="mr-1.5"></i> Transaction History ({{ $allTxns->count() }})</h4>
                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200">
                                    <tr>
                                        <th class="px-5 py-3">Date</th>
                                        <th class="px-5 py-3">Order ID</th>
                                        <th class="px-5 py-3">Transaction ID</th>
                                        <th class="px-5 py-3 text-center">Amount</th>
                                        <th class="px-5 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($allTxns->sortByDesc('created_at') as $t)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-5 py-4 text-slate-600 font-medium whitespace-nowrap">{{ $t->created_at->format('d M Y, H:i') }}</td>
                                        <td class="px-5 py-4 font-mono text-slate-500 text-xs">{{ $t->razorpay_order_id ?? '-' }}</td>
                                        <td class="px-5 py-4 font-mono font-bold text-slate-700 text-xs">{{ $t->razorpay_payment_id ?? '-' }}</td>
                                        <td class="px-5 py-4 text-center font-bold text-slate-700 whitespace-nowrap">₹ {{ number_format($t->amount, 2) }}</td>
                                        <td class="px-5 py-4 text-center">
                                            @if($t->status == 'captured' || $t->status == 'paid')
                                                <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide">Success</span>
                                            @elseif($t->status == 'failed')
                                                <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide">Failed</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide">{{ $t->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                @endif
            </section>
            @endif

            <!-- Full Width Sections -->
            <section>
                <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-6 tracking-wider flex items-center">
                    <i data-lucide="user" size="18" class="mr-2"></i> Personal Details
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-y-8 gap-x-6 text-sm">
                    @php 
                        $s = $application->application_student; 
                        $p = $application->application_parent;
                        $age = $s->dob ? \Carbon\Carbon::parse($s->dob)->age . ' Years' : 'N/A';
                    @endphp
                    @if($appliedForVal)
                    <div class="col-span-2 md:col-span-4 lg:col-span-5 p-3.5 bg-indigo-50/60 rounded-xl border border-indigo-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-xs">
                                <i data-lucide="compass" size="16"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider block">Course / Stream (Applied For)</span>
                                <span class="text-sm font-extrabold text-slate-900">{{ $appliedForVal }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 bg-white border border-slate-200 px-2.5 py-1 rounded-full flex items-center gap-1 shadow-2xs">
                            <i data-lucide="lock" size="11" class="text-slate-400"></i> Locked Choice
                        </span>
                    </div>
                    @endif
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Full Name</span><span class="font-bold text-slate-800 text-base">{{ $s->full_name ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Father's Name</span><span class="font-bold text-slate-800 text-base">{{ $p->father_name ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Father's Mobile</span><span class="font-bold text-slate-800 text-base">{{ $p->father_mobile ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Mother's Name</span><span class="font-bold text-slate-800 text-base">{{ $p->mother_name ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Mother's Mobile</span><span class="font-bold text-slate-800 text-base">{{ $p->mother_mobile ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Date of Birth</span><span class="font-bold text-slate-800 text-base">{{ $s->dob ? \Carbon\Carbon::parse($s->dob)->format('d-m-Y') : 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Age</span><span class="font-bold text-slate-800 text-base">{{ $age }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Gender</span><span class="font-bold text-slate-800 text-base">{{ strtoupper($s->gender ?? 'N/A') }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Community</span><span class="font-bold text-slate-800 text-base">{{ strtoupper($s->community ?? $s->nationality ?? 'N/A') }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Emp Exchange No</span><span class="font-bold text-slate-800 text-base">{{ $s->employment_exchange_no ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">PR of Assam</span><span class="font-bold text-slate-800 text-base">{{ !is_null($s->is_permanent_resident_of_assam) ? ($s->is_permanent_resident_of_assam ? 'YES' : 'NO') : 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">BTC Resident</span><span class="font-bold text-slate-800 text-base">{{ !is_null($s->is_btc_resident) ? ($s->is_btc_resident ? 'YES' : 'NO') : 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Student Mobile</span><span class="font-bold text-slate-800 text-base">{{ $s->phone_number ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">What’s App Number</span><span class="font-bold text-slate-800 text-base">{{ $s->alternate_mobile_number ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Email ID</span><span class="font-bold text-slate-800 text-base">{{ $s->email ?? 'N/A' }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">Category</span><span class="font-bold text-slate-800 text-base">{{ strtoupper($s->category ?? 'N/A') }}</span></div>
                    <div><span class="text-slate-400 block text-[10px] uppercase font-bold mb-1 tracking-tight">PwD</span><span class="font-bold text-slate-800 text-base">{{ $s->is_pwd ? 'YES' : 'NO' }}</span></div>
                </div>
            </section>

            <section>
                <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-6 tracking-wider flex items-center">
                    <i data-lucide="map-pin" size="18" class="mr-2"></i> Address Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm">
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-widest block mb-4 border-b border-blue-100 pb-2">Permanent Address</span>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-y-6 gap-x-4">
                            <div class="col-span-2 md:col-span-1"><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Village / Town</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->permanent_village_town ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Permanent Post Office</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->permanent_city ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Permanent Police Station</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->permanent_country ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">District</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->permanent_district ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">BTCLA Constituency</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->permanent_street ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">State</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->permanent_state ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Pin Code</span><p class="font-mono font-bold text-blue-600 bg-blue-50 w-fit px-1.5 rounded">{{ $application->application_address->permanent_pin_code ?? 'N/A' }}</p></div>
                        </div>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-sm opacity-90 hover:opacity-100 transition-opacity">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-4 border-b border-slate-200 pb-2">Current Address / Communication Address</span>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-y-6 gap-x-4">
                            <div class="col-span-2 md:col-span-1"><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Village / Town</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->present_village_town ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">District</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->present_district ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">State</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->present_state ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Present Post Office</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->present_city ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Present Police Station</span><p class="font-bold text-slate-700 leading-tight">{{ $application->application_address->present_country ?? 'N/A' }}</p></div>
                            <div><span class="text-[10px] text-slate-400 uppercase font-bold block mb-0.5">Pin Code</span><p class="font-mono font-bold text-slate-600 bg-slate-100 w-fit px-1.5 rounded">{{ $application->application_address->present_pin_code ?? 'N/A' }}</p></div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-6 tracking-wider flex items-center">
                    <i data-lucide="graduation-cap" size="18" class="mr-2"></i> Academic History
                </h3>
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-bold tracking-tight">
                            <tr>
                                <th class="px-6 py-4 border-b border-slate-200">Examination Name</th>
                                <th class="px-6 py-4 border-b border-slate-200">School/College/Institute Name</th>
                                <th class="px-6 py-4 border-b border-slate-200 text-center">Passing Year</th>
                                <th class="px-6 py-4 border-b border-slate-200 text-center">% Marks</th>
                                <th class="px-6 py-4 border-b border-slate-200 text-center">Division</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($application->application_academic_qualifications as $qual)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-800">{{ $qual->examination }}</td>
                                <td class="px-6 py-4 text-slate-700 font-medium">{{ $qual->board_university }}</td>
                                <td class="px-6 py-4 text-slate-600 text-center font-mono">{{ $qual->passing_year }}</td>
                                <td class="px-6 py-4 text-center font-black text-blue-700">{{ $qual->percentage_grade }}%</td>
                                <td class="px-6 py-4 text-center"><span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight">{{ $qual->division }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-5 text-center text-slate-500 italic">No academic history records found for this applicant.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Full Width: Documents -->
        <div class="px-8 pb-12 border-t border-slate-100 bg-slate-50/20">
            <section class="mt-8">
                <h3 class="text-sm font-bold text-blue-800 uppercase border-b border-blue-100 pb-3 mb-6 tracking-wider flex items-center">
                    <i data-lucide="file-text" size="18" class="mr-2"></i> Uploaded Documents Inventory
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $docs = $application->application_documents;
                        $docLabels = [
                            'photo' => 'Photograph',
                            'signature' => 'Signature',
                            'hslc_marksheet' => 'HSLC Marksheet',
                            'hs_marksheet' => 'HS Marksheet',
                            'graduation_marksheet' => 'Graduation Marksheet',
                            'post_graduation_marksheet' => 'PG Marksheet',
                            'hslc_pass_certificate' => 'HSLC Certificate',
                            'hs_pass_certificate' => 'HS Certificate',
                            'graduation_pass_certificate' => 'Graduation Certificate',
                            'post_graduation_pass_certificate' => 'PG Certificate',
                            'hslc_admit_card' => 'HSLC Admit Card',
                            'hs_admit_card' => 'HS Admit Card',
                            'graduation_admit_card' => 'Graduation Admit Card',
                            'post_graduation_admit_card' => 'PG Admit Card',
                            'caste_certificate' => 'Caste Certificate',
                            'income_certificate' => 'Income Certificate',
                            'address_proof' => 'Address Proof',
                            'aadhaar_card' => 'Aadhaar Card',
                            'btc_residential_certificate' => 'Permanent Resident Certificate (PRC)',
                        ];
                        $hasDocs = false;
                        if($docs) {
                            foreach($docLabels as $key => $label) {
                                if(!empty($docs->$key)) { $hasDocs = true; break; }
                            }
                        }
                    @endphp

                    @if($hasDocs)
                        @foreach($docLabels as $key => $label)
                            @if(!empty($docs->$key))
                            <a href="{{ Storage::url($docs->$key) }}" target="_blank" class="flex items-center p-4 border border-slate-200 rounded-2xl hover:bg-white hover:border-blue-400 transition-all group bg-white/50 shadow-sm hover:shadow-lg">
                                <div class="bg-blue-100 text-blue-600 p-3 rounded-xl mr-4 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-sm">
                                    <i data-lucide="{{ $key == 'photo' ? 'image' : 'file-text' }}" size="20"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <span class="text-sm font-bold text-slate-700 block group-hover:text-blue-700 truncate capitalize">{{ $label }}</span>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight flex items-center mt-0.5"><i data-lucide="eye" size="10" class="mr-1"></i> Preview</p>
                                </div>
                            </a>
                            @endif
                        @endforeach
                    @else
                        <div class="col-span-full py-16 text-center bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl">
                           <i data-lucide="file-x-2" size="48" class="mx-auto text-slate-300 mb-4 opacity-50"></i>
                           <p class="text-sm text-slate-400 font-bold uppercase">No documents uploaded</p>
                           <p class="text-xs text-slate-400 mt-1">This applicant has not uploaded any electronic documents yet.</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
