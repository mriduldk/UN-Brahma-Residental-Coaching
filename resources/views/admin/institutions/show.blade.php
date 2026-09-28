@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.organizations.show', $institution->organization_id) }}" class="p-2 bg-white rounded-lg shadow-sm border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition-colors">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                        <i data-lucide="building" size="20"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ $institution->institution_name }}</h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded text-xs font-bold">{{ $institution->institution_code }}</span>
                            <span class="text-sm font-medium text-slate-500">
                                Part of <a href="{{ route('admin.organizations.show', $institution->organization_id) }}" class="text-indigo-600 hover:underline">{{ $institution->organization->name ?? 'Organization' }}</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard Ribbon -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Forms Metric -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Total Forms</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-800">{{ number_format($totalForms) }}</span>
                </div>
            </div>
            <div class="absolute right-[-10px] bottom-[-10px] text-slate-100 group-hover:text-amber-50 group-hover:scale-110 transition-all duration-300">
                <i data-lucide="folder-open" size="100"></i>
            </div>
        </div>

        <!-- Student Applications Metric -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Total Students Enrolled</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-800">{{ number_format($totalStudents) }}</span>
                </div>
            </div>
            <div class="absolute right-[-10px] bottom-[-10px] text-slate-100 group-hover:text-emerald-50 group-hover:scale-110 transition-all duration-300">
                <i data-lucide="users" size="100"></i>
            </div>
        </div>

        <!-- Revenue Metric -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1">Total Admission Fees</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-800">₹{{ number_format($totalRevenue, 2) }}</span>
                </div>
            </div>
            <div class="absolute right-[-10px] bottom-[-10px] text-slate-100 group-hover:text-blue-50 group-hover:scale-110 transition-all duration-300">
                <i data-lucide="indian-rupee" size="100"></i>
            </div>
        </div>
    </div>

    <!-- Application Forms Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="folder-open" size="18" class="text-amber-500"></i> Active Application Forms
            </h3>
            <span class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                Click a row to view students
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Form Title</th>
                        <th class="px-6 py-4">Session</th>
                        <th class="px-6 py-4 text-right">Reg. Fee</th>
                        <th class="px-6 py-4 text-right">Admission Fee</th>
                        <th class="px-6 py-4 text-right">Total Amount</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Students</th>
                        <th class="px-6 py-4 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($institution->application_forms as $form)
                    <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer group" onclick="window.location='{{ route('admin.forms.show', $form->id) }}'">
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-800 block">{{ $form->application_name }}</span>
                            <span class="text-xs text-slate-500 block mt-0.5">Code: {{ $form->application_code }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 font-medium">
                            {{ $form->session }}
                        </td>
                        <td class="px-6 py-4 text-right text-slate-600 font-medium">₹{{ number_format($form->application_fee, 2) }}</td>
                        <td class="px-6 py-4 text-right text-slate-600 font-medium">₹{{ number_format($form->admission_fee, 2) }}</td>
                        <td class="px-6 py-4 text-right text-indigo-600 font-black">₹{{ number_format($form->application_fee + $form->admission_fee, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $form->status == 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst($form->status ?? 'Draft') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-slate-600">
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-100 border border-slate-200">
                                {{ $form->student_applications()->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <i data-lucide="chevron-right" size="18" class="text-slate-300 group-hover:text-amber-600 transition-colors"></i>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                            <i data-lucide="folder" size="32" class="mx-auto mb-3 text-slate-300"></i>
                            <p class="font-medium text-slate-600">No application forms assigned to this institution.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
