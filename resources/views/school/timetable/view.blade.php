@extends('layouts.school')

@section('title', 'Weekly Timetable')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="no-print">
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Weekly Timetable</h1>
            <p class="text-slate-500 text-sm mt-1">View the academic schedule for the selected class.</p>
        </div>
        
        <!-- Print-only Institution Details -->
        <div class="hidden print:block w-full border-b-2 border-slate-900 pb-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight">{{ auth()->user()->currentOrganization()->organization_name ?? 'School Institution' }}</h1>
                    <p class="text-slate-600 font-bold text-sm tracking-widest uppercase mt-1">Official Academic Timetable</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Academic Year</p>
                    <p class="text-lg font-black text-slate-900">{{ $activeYear->name ?? 'N/A' }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-8 mt-8 border-t border-slate-100 pt-6">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Class</p>
                    <p class="text-xl font-black text-indigo-600">{{ $classes->find($selectedClassId)->name ?? 'N/A' }}</p>
                </div>
                @if($selectedSectionId)
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Section</p>
                    <p class="text-xl font-black text-indigo-600">{{ $classes->find($selectedClassId)->sections->find($selectedSectionId)->name ?? 'All Sections' }}</p>
                </div>
                @else
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Section</p>
                    <p class="text-xl font-black text-indigo-600">General Schedule</p>
                </div>
                @endif
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('school.timetable.pdf', ['class_id' => $selectedClassId, 'section_id' => $selectedSectionId]) }}" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-xl shadow-sm hover:bg-indigo-700 transition-all text-xs flex items-center gap-2 uppercase tracking-widest no-print">
                <i data-lucide="printer" size="14"></i> Print Schedule
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm no-print">
        <form action="{{ route('school.timetable.view') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Select Class</label>
                <select name="class_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                    <option value="">Choose a Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($selectedClassId)
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Select Section (Optional)</label>
                <select name="section_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                    <option value="">All Sections</option>
                    @foreach($classes->find($selectedClassId)->sections as $section)
                        <option value="{{ $section->id }}" {{ $selectedSectionId == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="flex justify-end pb-1">
                 @if($selectedClassId)
                 <a href="{{ route('school.timetable.view') }}" class="text-[10px] font-black text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-widest flex items-center gap-1">
                    <i data-lucide="refresh-ccw" size="12"></i> Reset Filters
                 </a>
                 @endif
            </div>
        </form>
    </div>

    @if(!$selectedClassId)
        <div class="bg-indigo-50/50 border-2 border-dashed border-indigo-100 rounded-[3rem] p-16 text-center">
            <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-xl shadow-indigo-100/50">
                <i data-lucide="calendar" class="text-indigo-500" size="36"></i>
            </div>
            <h3 class="text-2xl font-black text-indigo-900 mb-3 tracking-tight">No Class Selected</h3>
            <p class="text-slate-500 max-w-sm mx-auto font-medium leading-relaxed">Select a class from the dropdown above to view its weekly timetable.</p>
        </div>
    @else
        <!-- Timetable Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-6">
            @foreach($days as $day)
            <div class="space-y-6">
                <!-- Day Header -->
                <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md p-4 rounded-2xl text-center shadow-sm border border-slate-200/50 mb-2 group transition-all duration-300">
                    <span class="text-[11px] font-black uppercase tracking-[0.2em] text-indigo-900 group-hover:text-indigo-600 transition-colors">{{ $day }}</span>
                    <div class="h-0.5 w-8 bg-indigo-100 mx-auto mt-2 rounded-full group-hover:w-12 group-hover:bg-indigo-500 transition-all duration-300"></div>
                </div>
                
                <div class="space-y-5">
                    @forelse(collect($timetable->get($day, []))->sortBy('start_time') as $slot)
                    <div class="group relative bg-white p-6 rounded-[1.5rem] border border-slate-200/50 transition-all duration-500 hover:shadow-[0_20px_50px_rgba(79,70,229,0.1)] hover:border-indigo-200/50 hover:-translate-y-1 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/0 via-transparent to-transparent group-hover:from-indigo-50/50 transition-colors duration-500"></div>
                        <div class="relative space-y-5">
                            <!-- Time & Room -->
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col select-none">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em] leading-none mb-1">
                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                    </span>
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.15em] leading-none">
                                        {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Subject & Teacher -->
                            <div>
                                <h4 class="font-black text-slate-900 text-[15px] leading-snug group-hover:text-indigo-600 transition-colors duration-300 tracking-tight">{{ $slot->subject_name }}</h4>
                                <div class="flex items-center gap-2 mt-2">
                                    <p class="text-[11px] text-slate-500 font-bold tracking-tight uppercase">{{ $slot->staff->full_name }}</p>
                                </div>
                            </div>
                            
                            <!-- Additional Info -->
                            <div class="flex items-center justify-between border-t border-slate-100/60 pt-3">
                                <div class="flex items-center">
                                    @if($slot->section)
                                    <span class="text-[9px] font-black text-slate-300 border border-slate-200 px-2 py-0.5 rounded-lg uppercase tracking-widest">
                                        {{ $slot->section->name }}
                                    </span>
                                    @endif
                                </div>
                                
                                @if($slot->room_number)
                                <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">
                                    Room {{ $slot->room_number }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-10 text-center opacity-20 group-hover:opacity-100 transition-opacity">
                        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">No Classes</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    @media print {
        @page {
            size: landscape;
            margin: 10mm;
        }
        .no-print, header, aside, .sidebar-overlay {
            display: none !important;
        }
        body {
            background-color: white !important;
            color: #000 !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 10pt;
        }
        .max-w-7xl {
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .grid {
            display: grid !important;
            gap: 0.5rem !important;
        }
        .lg\:grid-cols-7 {
            grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
        }
        .bg-white {
            background: #fff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
        }
        .rounded-[1.5rem], .rounded-2xl, .rounded-3xl {
            border-radius: 0.5rem !important;
        }
        .p-6 {
            padding: 0.75rem !important;
        }
        .space-y-8 {
            space-y: 1rem !important;
        }
        .text-indigo-600, .text-indigo-900, .text-indigo-400 {
            color: #1e1b4b !important; /* Dark indigo for print */
        }
        .text-slate-500, .text-slate-400, .text-slate-300 {
            color: #475569 !important; /* Darker slate for readability */
        }
        .border-slate-200\/50, .border-slate-100\/60 {
            border-color: #cbd5e1 !important;
        }
        .shadow-sm, .hover\:shadow-xl, .shadow-xl {
            box-shadow: none !important;
        }
        .sticky {
            position: relative !important;
        }
        /* Hide backgrounds for ink saving */
        .bg-indigo-50\/50, .bg-slate-50, .bg-white\/80 {
            background-color: #f8fafc !important;
        }
        .bg-gradient-to-br {
            background: none !important;
        }
    }
</style>
@endsection
