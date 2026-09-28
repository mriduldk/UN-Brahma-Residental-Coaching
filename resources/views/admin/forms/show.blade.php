@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.institutions.show', $form->institution_id) }}" class="p-2 bg-white rounded-lg shadow-sm border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-colors">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-amber-200">
                        <i data-lucide="file-text" size="20"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ $form->application_name }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded text-xs font-bold">{{ $form->session }}</span>
                            <span class="text-sm font-medium text-slate-500">
                                Assigned to <a href="{{ route('admin.institutions.show', $form->institution_id) }}" class="text-indigo-600 hover:underline">{{ $form->institution->institution_name ?? 'Institution' }}</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard Ribbon -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative overflow-hidden">
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Total Enrolled</p>
            <span class="text-3xl font-black text-slate-800">{{ $form->student_applications->count() }}</span>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative overflow-hidden">
            <p class="text-sm font-bold text-emerald-600 uppercase tracking-wider mb-1">Approved</p>
            <span class="text-3xl font-black text-slate-800">{{ $approvedCount }}</span>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative overflow-hidden">
            <p class="text-sm font-bold text-amber-600 uppercase tracking-wider mb-1">Submitted</p>
            <span class="text-3xl font-black text-slate-800">{{ $submittedCount }}</span>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative overflow-hidden">
            <p class="text-sm font-bold text-rose-600 uppercase tracking-wider mb-1">Pending EMI</p>
            <span class="text-3xl font-black text-slate-800">₹{{ number_format($totalPendingEmi, 2) }}</span>
        </div>
    </div>

    <!-- Student Applications Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="users" size="18" class="text-blue-500"></i> Student Applications
            </h3>
            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                Click a row to view complete application
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Student Name</th>
                        <th class="px-6 py-4">App Number</th>
                        <th class="px-6 py-4">Submitted At</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Payment Type</th>
                        <th class="px-6 py-4 text-right">Payment Info</th>
                        <th class="px-6 py-4 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($form->student_applications as $app)
                    <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer group" onclick="window.location='{{ route('admin.student-applications.show', $app->id) }}'">
                        <td class="px-6 py-4 font-bold text-slate-800">
                            {{ $app->application_student->full_name ?? $app->student->full_name ?? 'Unknown Student' }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-medium">
                            {{ $app->application_code ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs font-medium">
                            {{ $app->submitted_at ? $app->submitted_at->format('M d, Y h:i A') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusStyle = match($app->status) {
                                    'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'submitted' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-600 border-slate-200'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold border {{ $statusStyle }}">
                                {{ ucfirst($app->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($app->admission_student)
                                <span class="px-2 py-1 rounded text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ strtoupper($app->admission_student->payment_type) }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs font-medium">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($app->admission_student)
                                @if($app->admission_student->payment_type === 'emi')
                                    @if($app->admission_student->pending_amount > 0)
                                        <div class="flex flex-col items-end">
                                            <span class="text-xs text-slate-500 font-medium">Pending amount:</span>
                                            <span class="font-bold text-rose-600">₹{{ number_format($app->admission_student->pending_amount, 2) }}</span>
                                        </div>
                                    @else
                                        <span class="font-bold text-emerald-600 px-2 py-1 bg-emerald-50 rounded text-xs">Cleared</span>
                                    @endif
                                @elseif($app->admission_student->payment_type === 'full')
                                    <div class="flex flex-col items-end">
                                        <span class="text-xs text-slate-500 font-medium">Paid Full Amount:</span>
                                        <span class="font-bold text-emerald-600">₹{{ number_format($app->admission_student->paid_amount, 2) }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 font-medium">-</span>
                                @endif
                            @else
                                <span class="text-slate-400 font-medium">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <i data-lucide="chevron-right" size="18" class="text-slate-300 group-hover:text-blue-600 transition-colors"></i>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            <i data-lucide="users" size="32" class="mx-auto mb-3 text-slate-300"></i>
                            <p class="font-medium text-slate-600">No students have applied to this form yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
