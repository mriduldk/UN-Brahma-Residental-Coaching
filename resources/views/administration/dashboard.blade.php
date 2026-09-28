@extends('layouts.administration')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">
    <x-email-verification-alert />

    <x-ui.page-header 
        title="Dashboard Overview" 
        subtitle="Welcome back, get an overview of your admission status." 
    />
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <x-ui.stats-card 
            title="Total Students" 
            :value="number_format($totalApplications)" 
            icon="users" 
            variant="primary" 
        />

        <x-ui.stats-card 
            title="Enrolled Students" 
            :value="number_format($enrolledStudentsCount)" 
            icon="user-check" 
            variant="teal" 
        />

        <x-ui.stats-card 
            title="Total Revenue" 
            value="₹{{ number_format($totalRevenue) }}" 
            icon="indian-rupee" 
            variant="emerald"
            subtitle="(Registration + Admission)"
        />

        <x-ui.stats-card 
            title="Admission Revenue" 
            value="₹{{ number_format($admRevenue) }}" 
            icon="banknote" 
            variant="green" 
        />

        <x-ui.stats-card 
            title="Overall Pending Bal" 
            value="₹{{ number_format($overallPendingBalance) }}" 
            icon="credit-card" 
            variant="orange" 
        />

        <x-ui.stats-card 
            title="Pending EMI Balance" 
            value="₹{{ number_format($totalPendingEmi) }}" 
            icon="wallet-2" 
            variant="indigo" 
        />

        <x-ui.stats-card 
            title="Cash to Settle" 
            value="₹{{ number_format($unsettledCash) }}" 
            icon="zap" 
            variant="amber" 
        />

        <x-ui.stats-card 
            title="EMI Dues (7 Days)" 
            :value="$upcomingDuesCount" 
            icon="calendar-clock" 
            variant="rose" 
        >
            <span class="text-xs text-rose-500 uppercase font-bold">Students</span>
        </x-ui.stats-card>
    </div>

    <!-- Alert Section for Urgent Attention -->
    @if($unsettledCash > 0 || $upcomingDuesCount > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        @if($unsettledCash > 0)
        <x-ui.alert-banner 
            title="Unsettled Cash Alert" 
            icon="zap" 
            variant="amber" 
            :href="route('administration.finance')"
        >
            You have ₹{{ number_format($unsettledCash) }} collected in cash that needs to be settled online across your institutions.
        </x-ui.alert-banner>
        @endif

        @if($upcomingDuesCount > 0)
        <x-ui.alert-banner 
            title="Upcoming EMI Dues" 
            icon="clock" 
            variant="rose" 
            :href="route('administration.applications')"
        >
            {{ $upcomingDuesCount }} students have payments due in the next 7 days across organization.
        </x-ui.alert-banner>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Institutions Overview -->
        <x-ui.card class="lg:col-span-2">
            <x-ui.section-header 
                title="My Institutions" 
                :href="route('administration.institutions.index')" 
                class="!mb-6"
            />
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 text-sm border-b border-slate-100">
                            <th class="pb-3 font-medium">Institution Name</th>
                            <th class="pb-3 font-medium">Code</th>
                            <th class="pb-3 font-medium text-center">Forms</th>
                            <th class="pb-3 font-medium text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @forelse($institutions as $inst)
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 pr-4">
                                <div class="font-bold text-slate-800 capitalize leading-tight">{{ $inst->institution_name }}</div>
                            </td>
                            <td class="py-4 text-slate-500 font-medium uppercase">{{ $inst->institution_code }}</td>
                            <td class="py-4 text-center">
                                <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-xs font-bold">{{ $inst->application_forms_count ?? 0 }} Forms</span>
                            </td>
                            <td class="py-4 text-right">
                                <a href="{{ route('administration.institutions.index') }}" class="inline-flex items-center gap-1.5 text-slate-600 hover:text-blue-600 font-semibold group/btn">
                                    Manage <i data-lucide="arrow-right-circle" size="16" class="transition-transform group-hover/btn:translate-x-0.5"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-slate-400">
                                <p class="text-sm">No institutions found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <!-- Recent Activity -->
        <x-ui.card class="flex flex-col">
            <x-ui.section-header 
                title="Recent Payments" 
                subtitle="Latest fee transactions"
                class="!mb-6"
            />
            
            <div class="space-y-4 flex-1">
                @forelse($recentTransactions as $transaction)
                <div class="flex items-center justify-between p-3 hover:bg-background rounded-xl transition-colors cursor-pointer group" onclick="window.location.href='{{ route('administration.finance.show', ['type' => strtolower($transaction->payment_type), 'id' => $transaction->id]) }}'">
                    <div class="flex items-center space-x-4">
                        <x-ui.icon-box variant="primary" size="sm" class="font-bold text-xs">
                            {{ substr($transaction->student_name, 0, 2) }}
                        </x-ui.icon-box>
                        <div class="overflow-hidden">
                            <p class="text-sm font-bold text-foreground truncate">{{ $transaction->student_name }}</p>
                            <p class="text-xs text-muted truncate">{{ $transaction->trade }} • {{ $transaction->payment_type }}</p>
                        </div>
                    </div>
                    <x-ui.badge variant="success">+₹{{ number_format($transaction->amount, 0) }}</x-ui.badge>
                </div>
                @empty
                <div class="py-10 text-center text-muted">
                    <p class="text-sm">No recent successful payments.</p>
                </div>
                @endforelse
            </div>
            <x-ui.button variant="ghost" :href="route('administration.finance')" class="w-full mt-6" icon="arrow-right">
                View All Transactions
            </x-ui.button>
        </x-ui.card>
    </div>

    <!-- Chart Row -->
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
        <x-ui.card padding="p-8">
            <x-ui.section-header 
                title="Applications by Trade" 
                subtitle="Distribution across your organization's courses"
                class="!mb-8"
            >
                <x-slot name="linkText">
                    <div class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold text-slate-600 uppercase tracking-wider">
                        All Sessions
                    </div>
                </x-slot>
            </x-ui.section-header>
            <div class="h-80 w-full">
                <canvas id="tradeChart"></canvas>
            </div>
        </x-ui.card>
    </div>
    <!-- Contact Section -->
    <div class="mt-12 space-y-6">
        <div class="bg-foreground rounded-3xl p-8 text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary rounded-full opacity-20 blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <h4 class="text-2xl font-black mb-2">Need Support</h4>
                    <p class="text-slate-400 font-medium">Contact us for any queries related to the admission process.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-6">
                    <a href="tel:+919101672624" class="flex items-center gap-4 group/item">
                        <x-ui.icon-box variant="white" size="md" class="group-hover/item:bg-white/20">
                            <i data-lucide="phone" size="20" class="text-primary"></i>
                        </x-ui.icon-box>
                        <div>
                            <p class="text-[10px] font-bold text-muted uppercase tracking-widest">Call Us</p>
                            <p class="text-lg font-bold">9101672624</p>
                        </div>
                    </a>
                    <a href="mailto:info.onlinesetu@gmail.com" class="flex items-center gap-4 group/item">
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
    // Initialize Chart (Simple Bar Chart)
    const ctx = document.getElementById('tradeChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($tradeStats->pluck('trade')) !!},
            datasets: [{
                label: 'Applications Received',
                data: {!! json_encode($tradeStats->pluck('count')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                hoverBackgroundColor: 'rgba(59, 130, 246, 1)',
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
