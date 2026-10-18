@extends('layouts.student')

@section('content')
<div class="space-y-6 pb-12 max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <span>Entrance Exam Admit Card</span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('student.applications.history') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white text-slate-700 hover:text-blue-600 border border-slate-200 rounded-xl text-xs font-bold shadow-xs hover:border-slate-300 transition-all">
                <i data-lucide="folder-open" size="15"></i>
                <span>My Applications</span>
            </a>
        </div>
    </div>

    <!-- Alert / Announcements -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl flex items-center gap-3 shadow-xs">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <p class="text-sm font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    @if($submittedApplications->isEmpty())
        <!-- Empty State -->
        <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center shadow-xs">
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-inner">
                <i data-lucide="id-card" size="36"></i>
            </div>
            <h3 class="text-xl font-black text-slate-800 mb-2">No Admit Cards Available Yet</h3>
            <p class="text-slate-500 text-sm max-w-md mx-auto mb-8 leading-relaxed">
                Your Admit Card is generated automatically once your application has been submitted. 
                If you have an active draft application, please submit it before the deadline to receive your official hall ticket.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('student.applications.history') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-blue-500/20">
                    <i data-lucide="folder-open" size="18"></i> Check My Applications
                </a>
                <a href="{{ route('student.applications.index') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-all border border-slate-200">
                    <i data-lucide="plus-circle" size="18"></i> Browse New Forms
                </a>
            </div>
        </div>
    @else
        <!-- List View Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-600">
                        <tr>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Application ID & Form</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Applied Stream</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Allotted Exam Center</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs">Exam Schedule</th>
                            <th class="px-6 py-4 font-bold uppercase tracking-wider text-xs text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($submittedApplications as $app)
                        @php
                            $stream = $app->applied_for_display ?? '';
                            $center = $app->examination_center_full ?? $app->examination_center_display ?? '';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- Roll No. & Form -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <span class="font-mono font-black text-blue-900 bg-blue-50 border border-blue-200/60 px-2.5 py-0.5 rounded-lg text-xs tracking-wide">
                                            {{ $app->application_code }}
                                        </span>
                                        <div class="font-bold text-slate-800 text-xs mt-1">
                                            {{ $app->application_form->application_name ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Stream / Course -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-indigo-100">
                                    <i data-lucide="compass" size="13" class="text-indigo-600"></i>
                                    {{ $stream }}
                                </span>
                            </td>

                            <!-- Allotted Center -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-slate-800 font-semibold text-xs">
                                    <i data-lucide="map-pin" size="14" class="text-rose-500 shrink-0"></i>
                                    <span>{{ $center }}</span>
                                </div>
                            </td>

                            <!-- Schedule -->
                            <td class="px-6 py-4">
                                <div class="text-xs">
                                    <div class="font-bold text-slate-800">{{ $app->exam_date }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1 font-semibold">
                                        <i data-lucide="clock" size="12" class="text-blue-500"></i>
                                        <span>{{ $app->exam_time }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Action Button -->
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('student.application.admin-card', $app->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-bold text-xs shadow-sm shadow-blue-200 hover:shadow-blue-300 transition-all transform hover:-translate-y-0.5" 
                                   title="Download Admit Card PDF">
                                    <i data-lucide="download" size="14"></i>
                                    <span>Download Admit Card</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex justify-between items-center text-xs text-slate-500">
                <span>Showing {{ $submittedApplications->count() }} eligible candidate record(s)</span>
                <span class="inline-flex items-center gap-1 font-medium text-emerald-600">
                    <i data-lucide="shield-check" size="14"></i> Official digital hall ticket
                </span>
            </div>
        </div>
    @endif
</div>
@endsection
