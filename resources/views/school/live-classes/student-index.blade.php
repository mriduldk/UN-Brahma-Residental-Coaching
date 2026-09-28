@extends('layouts.student')

@section('content')
<div class="space-y-8 pb-12">
    <!-- Header -->
    <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 h-full w-1/3 bg-white/5 skew-x-12 transform origin-bottom-left pointer-events-none"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-black tracking-tight mb-2">Live Virtual Classroom</h1>
            <p class="text-indigo-200 text-lg font-medium">Join your scheduled classes and interact with your teachers.</p>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i data-lucide="alert-circle" size="20"></i>
            <p class="font-bold uppercase tracking-tight text-sm">{{ $error }}</p>
        </div>
    @endif

    <!-- Live Classes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($liveClasses as $class)
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col group hover:shadow-xl transition-all hover:-translate-y-1">
                <!-- Status Header -->
                <div class="px-8 py-4 {{ $class->is_live ? 'bg-emerald-500 text-white' : 'bg-slate-50 text-slate-400' }} flex items-center justify-between">
                    <span class="text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                        @if($class->is_live)
                            <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                            Live Now
                        @else
                            <i data-lucide="clock" size="12"></i>
                            Scheduled
                        @endif
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ $class->start_time->format('h:i A') }}</span>
                </div>

                <div class="p-8 flex-1 flex flex-col">
                    <div class="mb-6">
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-2">{{ $class->subject_name }}</p>
                        <h3 class="text-xl font-black text-slate-800 leading-tight mb-2 group-hover:text-indigo-600 transition-colors">{{ $class->topic }}</h3>
                        <div class="flex items-center gap-2 text-slate-400">
                            <i data-lucide="user" size="14"></i>
                            <span class="text-xs font-bold">{{ $class->staff->full_name ?? 'Senior Faculty' }}</span>
                        </div>
                    </div>

                    <div class="mt-auto space-y-4 pt-6 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Date</span>
                                <span class="text-sm font-black text-slate-700">{{ $class->start_time->format('d M, Y') }}</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Duration</span>
                                <span class="text-sm font-black text-slate-700">{{ $class->duration }} Min</span>
                            </div>
                        </div>

                        <a href="{{ route('school.live-classes.join', $class->id) }}" 
                           target="_blank"
                           class="w-full py-4 {{ $class->is_live ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-100' : 'bg-slate-100 hover:bg-slate-200 text-slate-500 shadow-none' }} text-white font-black rounded-2xl shadow-xl transition-all text-xs uppercase tracking-widest flex items-center justify-center gap-3 group/btn hover:scale-[1.02]">
                            <i data-lucide="video" size="18" class="group-hover/btn:scale-125 transition-transform"></i>
                            {{ $class->is_live ? 'Join Class Now' : 'Enter Waiting Room' }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="lg:col-span-3 bg-white border-2 border-dashed border-slate-200 rounded-[3rem] p-24 text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                    <i data-lucide="video-off" size="40"></i>
                </div>
                <h4 class="text-xl font-black text-slate-600">No Scheduled Classes</h4>
                <p class="text-slate-400 font-medium mt-2">Check back later for your daily virtual schedule.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
