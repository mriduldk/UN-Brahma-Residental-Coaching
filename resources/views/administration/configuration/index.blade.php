@extends('layouts.administration')

@section('title', 'System Configuration')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('administration.forms') }}" class="w-10 h-10 bg-white rounded-xl border border-slate-200 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
            <i data-lucide="arrow-left" size="20"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">System Configuration</h1>
            <p class="text-slate-500 text-sm mt-1">Manage system settings and preferences.</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Quota Usage Card -->
        <div class="bg-white p-8 rounded-[2rem] border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-[100%] -mr-16 -mt-16 z-0"></div>
            
            <div class="relative z-10">
                <h3 class="text-lg font-bold text-slate-800 mb-8 flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i data-lucide="bar-chart-3" size="20"></i>
                    </div>
                    Form Creation Quota
                </h3>
                
                <div class="space-y-8">
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <div>
                                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Current Usage</div>
                                <div class="text-3xl font-black text-slate-900">{{ $used }} <span class="text-lg text-slate-400 font-medium">/ {{ $available }}</span></div>
                            </div>
                            <div class="text-right">
                                @php 
                                    $percentage = $available > 0 ? round(($used / $available) * 100) : 0;
                                    $colorClass = $percentage >= 100 ? 'text-red-600 bg-red-50' : ($percentage > 80 ? 'text-orange-600 bg-orange-50' : 'text-blue-600 bg-blue-50');
                                    $barColorClass = $percentage >= 100 ? 'bg-red-500' : ($percentage > 80 ? 'bg-orange-500' : 'bg-blue-600');
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-black {{ $colorClass }}">{{ $percentage }}%</span>
                            </div>
                        </div>
                        <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden p-1 border border-slate-50">
                            <div class="h-full {{ $barColorClass }} rounded-full transition-all duration-1000 shadow-sm" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 pt-4">
                        <div class="p-5 rounded-3xl bg-slate-50 border border-slate-100 group hover:border-blue-200 transition-colors">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Base Capacity</div>
                            <div class="text-2xl font-black text-slate-700">{{ $globalConfig->free_form_limit }}</div>
                            <p class="text-[10px] text-slate-400 mt-1">Default free forms</p>
                        </div>
                        <div class="p-5 rounded-3xl bg-slate-50 border border-slate-100 group hover:border-blue-200 transition-colors">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Current Limit</div>
                            <div class="text-2xl font-black text-slate-700">{{ $available }}</div>
                            <p class="text-[10px] text-slate-400 mt-1">Total allowed forms</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upgrade / Purchase Card -->
        <div class="bg-slate-900 p-8 rounded-[2rem] border border-slate-800 shadow-2xl relative overflow-hidden group">
            <!-- Decorative Elements -->
            <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl group-hover:bg-blue-600/20 transition-colors"></div>
            <div class="absolute top-20 left-10 w-32 h-32 bg-purple-600/10 rounded-full blur-2xl"></div>

            <div class="relative z-10 h-full flex flex-col">
                <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-orange-500/20">
                        <i data-lucide="zap" size="20"></i>
                    </div>
                    Expand Your Reach
                </h3>
                <p class="text-slate-400 text-sm mb-10 leading-relaxed">
                    Need to create more admission forms or trades? Purchase additional quota and start digitalizing more of your institution's processes today.
                </p>

                <div class="mt-auto">
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 p-6 rounded-[1.5rem] mb-8">
                        <div class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-2">Additional Form Price</div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-black text-white">₹{{ number_format($globalConfig->form_creation_charge, 0) }}</span>
                            <span class="text-slate-500 font-medium">/ per form</span>
                        </div>
                    </div>

                    <a href="{{ route('administration.configuration.quota.pay') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black text-sm transition-all flex items-center justify-center gap-3 shadow-xl shadow-blue-600/20 hover:-translate-y-1 active:scale-[0.98]">
                        Purchase Additional Quota
                        <i data-lucide="arrow-right" size="18"></i>
                    </a>
                    
                    <div class="mt-4 flex flex-col items-center gap-2">
                        <a href="{{ route('administration.configuration.quota.transactions') }}" class="text-[10px] font-bold text-slate-400 hover:text-blue-500 uppercase tracking-widest transition-colors flex items-center gap-2">
                            <i data-lucide="history" size="12"></i>
                            View Payment History
                        </a>
                        <p class="text-[10px] text-slate-500 font-medium uppercase tracking-widest">Instant Activation After Payment</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
