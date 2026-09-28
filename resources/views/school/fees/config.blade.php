@extends('layouts.school')

@section('title', 'Fee Configuration')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Fee Configuration</h1>
            <p class="text-slate-500 text-sm mt-1">Set monthly tuition fees for each class for the current academic year.</p>
        </div>
        
        <div>
            <div class="bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100">
                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest block leading-none mb-1">Academic Year</span>
                <span class="text-sm font-bold text-indigo-900">{{ $activeYear->name }}</span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <i data-lucide="check-circle-2" size="20"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ route('school.fees.config.store') }}" method="POST">
            @csrf
            <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Class Name</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Monthly Fee (₹)</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Allow Dashboard Payments</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($classes as $class)
                        <tr class="group hover:bg-slate-50/30 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 font-bold group-hover:scale-110 transition-transform">
                                        {{ substr($class->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-slate-700">{{ $class->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="relative max-w-[200px]">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">₹</span>
                                    <input type="number" 
                                           name="fees[{{ $class->id }}]" 
                                           value="{{ $configs->get($class->id)?->monthly_amount }}"
                                           placeholder="0.00"
                                           class="w-full pl-8 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <input type="checkbox" 
                                       name="allow_student_payment[{{ $class->id }}]" 
                                       value="1" 
                                       {{ $configs->get($class->id)?->allow_student_payment ? 'checked' : '' }}
                                       class="w-5 h-5 rounded-lg border-slate-200 text-indigo-600 focus:ring-indigo-50 transition-all cursor-pointer">
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-tight">
                                    {{ $configs->get($class->id)?->updated_at ? $configs->get($class->id)->updated_at->format('d M, Y') : 'Not Set' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-8 bg-slate-50/50 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 transition-all text-xs uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="save" size="16"></i> Save Configuration
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
