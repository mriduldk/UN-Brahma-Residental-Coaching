@extends('layouts.administration')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">
    <x-email-verification-alert />

    <x-ui.page-header 
        title="Dashboard Overview" 
        subtitle="Welcome back, get an overview of application submissions and candidate status." 
    />
    
    <!-- Total Applications Hero Stat Card -->
    <div class="relative overflow-hidden bg-gradient-to-br from-white via-slate-50/70 to-blue-50/50 border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs">
        {{-- Decorative ambient background elements --}}
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-32 -bottom-16 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="relative flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            {{-- Main Stat Display --}}
            <div class="flex items-center gap-5 sm:gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-blue-500 text-white flex items-center justify-center shadow-lg shadow-blue-500/25 shrink-0">
                    <i data-lucide="file-text" class="w-8 h-8 sm:w-10 sm:h-10"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-xs font-black uppercase tracking-widest text-slate-500">Total Applications</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200/70">
                            Submitted
                        </span>
                    </div>
                    <div class="flex items-baseline gap-3">
                        <span class="text-4xl sm:text-5xl font-black text-slate-900 tracking-tight">
                            {{ number_format($totalApplications) }}
                        </span>
                        <span class="text-sm font-semibold text-slate-500">
                            {{ \Illuminate\Support\Str::plural('Candidate', $totalApplications) }} Applied
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 font-medium">Verified student applications submitted across all coaching programs</p>
                </div>
            </div>

            {{-- Quick Indicators & Action --}}
            <div class="flex flex-wrap items-center gap-3 sm:gap-4 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-200/60">
                @if($todayCount > 0)
                <div class="bg-white/90 backdrop-blur-xs border border-slate-200/80 rounded-2xl px-4 py-3 shadow-2xs">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Received Today</p>
                    <p class="text-xl font-black text-blue-600 mt-0.5">+{{ $todayCount }}</p>
                </div>
                @endif
                <div class="bg-white/90 backdrop-blur-xs border border-slate-200/80 rounded-2xl px-4 py-3 shadow-2xs">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Active Programs</p>
                    <p class="text-xl font-black text-slate-800 mt-0.5">{{ $activeFormsCount }}</p>
                </div>

                <a href="{{ route('administration.applications') }}" class="inline-flex items-center gap-2 px-5 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-xs sm:text-sm font-bold shadow-md shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <span>View All Applications</span>
                    <i data-lucide="arrow-right" size="16"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Applications Table -->
    <x-ui.card>
        <x-ui.section-header 
            title="Recent Applications" 
            subtitle="Latest student applications received"
            :href="route('administration.applications')" 
            linkText="View All"
            class="!mb-6"
        />
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 text-xs uppercase tracking-wider border-b border-slate-100">
                        <th class="pb-3 font-semibold">Applicant</th>
                        <th class="pb-3 font-semibold">App Code</th>
                        <th class="pb-3 font-semibold">Applied For</th>
                        <th class="pb-3 font-semibold">Submitted</th>
                        <th class="pb-3 font-semibold text-center">Status</th>
                        <th class="pb-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-100">
                    @forelse($recentApplications as $app)
                    @php
                        $applicantName = $app->application_student->full_name ?? $app->student->full_name ?? 'Applicant';
                        $applicantPhone = $app->application_student->phone_number ?? $app->student->mobile_number ?? $app->student->phone ?? null;
                        $appliedTrade = $app->applied_for ?? $app->application_student?->applied_for ?? $app->application_form?->application_name ?? 'N/A';
                        $initials = strtoupper(substr(trim($applicantName), 0, 2));
                    @endphp
                    <tr class="group hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 pr-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs shrink-0">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-800 truncate leading-snug">{{ $applicantName }}</p>
                                    @if($applicantPhone)
                                        <p class="text-xs text-slate-400 font-medium truncate">{{ $applicantPhone }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 pr-3 font-mono text-xs font-semibold text-slate-600">
                            {{ $app->application_code ?? 'APP-'.$app->id }}
                        </td>
                        <td class="py-3.5 pr-3">
                            <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 border border-indigo-100/80 px-2.5 py-1 rounded-lg text-xs font-bold">
                                <i data-lucide="compass" size="12" class="text-indigo-500"></i>
                                {{ \Illuminate\Support\Str::limit($appliedTrade, 20) }}
                            </span>
                        </td>
                        <td class="py-3.5 pr-3 text-xs text-slate-500 font-medium whitespace-nowrap">
                            {{ $app->submitted_at ? $app->submitted_at->diffForHumans() : $app->created_at->diffForHumans() }}
                        </td>
                        <td class="py-3.5 pr-3 text-center whitespace-nowrap">
                            @if($app->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span> Approved
                                </span>
                            @elseif($app->status === 'rejected')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100">
                                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full mr-1.5"></span> Rejected
                                </span>
                            @elseif($app->status === 'submitted')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full mr-1.5"></span> Submitted
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full mr-1.5"></span> Draft
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 text-right whitespace-nowrap">
                            <a href="{{ route('administration.applications.detail', $app->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50/60 hover:bg-blue-100/80 border border-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                Review <i data-lucide="arrow-right" size="14"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i data-lucide="inbox" size="36" class="mx-auto text-slate-300 mb-2"></i>
                            <p class="text-sm font-medium">No application submissions found yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    <!-- Chart Row -->
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
        <x-ui.card padding="p-8">
            <x-ui.section-header 
                title="Applications by Trade / Course" 
                subtitle="Distribution across coaching programs"
                class="!mb-8"
            >
                <x-slot name="linkText">
                    <div class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold text-slate-600 uppercase tracking-wider">
                        Submitted Applications
                    </div>
                </x-slot>
            </x-ui.section-header>
            <div class="h-80 w-full">
                <canvas id="tradeChart"></canvas>
            </div>
        </x-ui.card>
    </div>

    <!-- Contact Support Section -->
    <div class="mt-12 space-y-6">
        <div class="bg-foreground rounded-3xl p-8 text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary rounded-full opacity-20 blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <h4 class="text-2xl font-black mb-2">Need Support</h4>
                    <p class="text-slate-400 font-medium">Contact us for any queries related to the admission process.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-6">
                    <a href="tel:+917099144957" class="flex items-center gap-4 group/item">
                        <x-ui.icon-box variant="white" size="md" class="group-hover/item:bg-white/20">
                            <i data-lucide="phone" size="20" class="text-primary"></i>
                        </x-ui.icon-box>
                        <div>
                            <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Call Us</p>
                            <p class="text-lg font-bold">7099144957</p>
                        </div>
                    </a>
                    <a href="mailto:info.jypko@gmail.com" class="flex items-center gap-4 group/item">
                        <x-ui.icon-box variant="white" size="md" class="group-hover/item:bg-white/20">
                            <i data-lucide="mail" size="20" class="text-primary"></i>
                        </x-ui.icon-box>
                        <div>
                            <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Email Us</p>
                            <p class="text-lg font-bold">info.jypko@gmail.com</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Chart (Bar Chart)
    const ctx = document.getElementById('tradeChart').getContext('2d');
    const tradeLabels = {!! json_encode($tradeStats->pluck('trade')) !!};
    const tradeCounts = {!! json_encode($tradeStats->pluck('count')) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: tradeLabels.length > 0 ? tradeLabels : ['No Applications'],
            datasets: [{
                label: 'Applications Received',
                data: tradeCounts.length > 0 ? tradeCounts : [0],
                backgroundColor: 'rgba(59, 130, 246, 0.85)',
                hoverBackgroundColor: 'rgba(37, 99, 235, 1)',
                borderRadius: 12,
                borderSkipped: false,
                barThickness: 40,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#f8fafc', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { weight: '600' } }
                },
                x: { 
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { weight: 'bold' } }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    cornerRadius: 12,
                    displayColors: false
                }
            }
        }
    });
</script>
@endpush
@endsection
