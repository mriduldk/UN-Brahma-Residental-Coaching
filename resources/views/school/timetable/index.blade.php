@extends('layouts.school')

@section('title', 'Timetable Management')

@section('content')
<div class="space-y-8" x-data="{ 
    addModalOpen: false, 
    isEdit: false,
    editId: null,
    selectedDay: '', 
    selectedSlot: null,
    loading: false,
    roomNumber: '',
    startTimeH: '08', startTimeM: '00', startTimeP: 'AM',
    endTimeH: '09', endTimeM: '00', endTimeP: 'AM',
    get formattedStartTime() {
        let h = parseInt(this.startTimeH);
        if (this.startTimeP === 'PM' && h < 12) h += 12;
        if (this.startTimeP === 'AM' && h === 12) h = 0;
        return `${h.toString().padStart(2, '0')}:${this.startTimeM}`;
    },
    get formattedEndTime() {
        let h = parseInt(this.endTimeH);
        if (this.endTimeP === 'PM' && h < 12) h += 12;
        if (this.endTimeP === 'AM' && h === 12) h = 0;
        return `${h.toString().padStart(2, '0')}:${this.endTimeM}`;
    },
    openEditModal(slot) {
        this.isEdit = true;
        this.editId = slot.id;
        this.selectedDay = slot.day_of_week;
        this.roomNumber = slot.room_number || '';
        
        let [sh, sm] = slot.start_time.split(':');
        let sH = parseInt(sh);
        this.startTimeP = sH >= 12 ? 'PM' : 'AM';
        this.startTimeH = (sH % 12 || 12).toString().padStart(2, '0');
        this.startTimeM = sm;

        let [eh, em] = slot.end_time.split(':');
        let eH = parseInt(eh);
        this.endTimeP = eH >= 12 ? 'PM' : 'AM';
        this.endTimeH = (eH % 12 || 12).toString().padStart(2, '0');
        this.endTimeM = em;

        this.addModalOpen = true;
        this.loading = false;
        
        this.$nextTick(() => {
            const select = document.querySelector('select[name=\'mapping_index\']');
            if (select) {
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].dataset.subject === slot.subject_name && 
                        select.options[i].dataset.staff == slot.school_staff_id) {
                        select.selectedIndex = i;
                        updateMapping(select);
                        break;
                    }
                }
            }
        });
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Timetable Management</h1>
            <p class="text-slate-500 text-sm mt-1">Design and manage weekly schedules for your classes.</p>
        </div>
        
        @if($selectedClassId && $activeYear)
        <div class="flex items-center gap-3">
            <a href="{{ route('school.timetable.view', ['class_id' => $selectedClassId, 'section_id' => $selectedSectionId]) }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-black rounded-xl shadow-sm hover:bg-slate-50 transition-all text-xs text-center uppercase tracking-widest flex items-center gap-2">
                <i data-lucide="external-link" size="14"></i> Live View
            </a>
            <button @click="addModalOpen = true; selectedDay = 'Monday'" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-xl shadow-md transition-all text-xs text-center uppercase tracking-widest">
                Add Time Slot
            </button>
        </div>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <form action="{{ route('school.timetable.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
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
                 <a href="{{ route('school.timetable.index') }}" class="text-[10px] font-black text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-widest flex items-center gap-1">
                    <i data-lucide="refresh-ccw" size="12"></i> Clear Selection
                 </a>
                 @endif
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <i data-lucide="check-circle-2" size="20"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(!$selectedClassId)
        <div class="bg-indigo-50/50 border-2 border-dashed border-indigo-100 rounded-[3rem] p-16 text-center">
            <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-xl shadow-indigo-100/50">
                <i data-lucide="layout-grid" class="text-indigo-500" size="36"></i>
            </div>
            <h3 class="text-2xl font-black text-indigo-900 mb-3 tracking-tight">Ready to Design Your Schedule?</h3>
            <p class="text-slate-500 max-w-sm mx-auto font-medium leading-relaxed">Select a class from the dropdown above to start managing its weekly academic blocks and teachers.</p>
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
                    @foreach(collect($timetable->get($day, []))->sortBy('start_time') as $slot)
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
                            
                            <!-- Actions -->
                            <div class="flex items-center justify-between border-t border-slate-100/60">
                                <div class="flex items-center">
                                    @if($slot->section)
                                    <span class="text-[9px] font-black text-slate-300 border border-slate-200 px-2 py-0.5 rounded-lg uppercase tracking-widest">
                                        {{ $slot->section->name }}
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center gap-1 opacity-0 scale-95 group-hover:opacity-100 group-hover:scale-100 transition-all duration-300">
                                    <button type="button" 
                                            @click="openEditModal({{ $slot->toJson() }})" 
                                            class="w-9 h-9 rounded-xl text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all flex items-center justify-center">
                                        <i data-lucide="pencil" class="p-2" size="16"></i>
                                    </button>
                                    <button type="button" 
                                            onclick="confirmDeleteSlot({{ $slot->id }})" 
                                            class="w-9 h-9 rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-all flex items-center justify-center">
                                        <i data-lucide="trash-2" class="p-2" size="16"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Form for deletion remains hidden -->
                        <form id="delete-slot-{{ $slot->id }}" action="{{ route('school.timetable.destroy', $slot->id) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                    @endforeach

                    <!-- Premium Add Slot Option -->
                    <div class="group h-24 rounded-[1.5rem] border-2 border-dashed border-slate-100 flex flex-col items-center justify-center gap-3 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all duration-300 cursor-pointer"
                         @click="addModalOpen = true; isEdit = false; selectedDay = '{{ $day }}'; loading = false; roomNumber = '';">
                        <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300 group-hover:bg-indigo-600 group-hover:text-white group-hover:rotate-90 transition-all duration-500">
                            <i data-lucide="plus" size="20"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest group-hover:text-indigo-600 transition-all">Add Block</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <!-- Add Slot Modal -->
    <div x-show="addModalOpen" 
         x-cloak
         x-effect="if(addModalOpen) { setTimeout(() => lucide.createIcons(), 50); }"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 px-4">
        <div @click.away="addModalOpen = false" 
             x-show="addModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-[2.5rem] w-full max-w-lg overflow-hidden shadow-2xl border border-white/20">
            <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-white relative">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                <div>
                    <h3 class="text-2xl font-black text-indigo-900 tracking-tight" x-text="isEdit ? 'Edit Schedule Block' : 'New Schedule Block'"></h3>
                    <p class="text-xs text-indigo-500 mt-1 uppercase tracking-[0.2em] font-black" x-text="selectedDay"></p>
                </div>
                <button @click="addModalOpen = false" class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>
            
            <form :action="isEdit ? '/school/timetable/' + editId : '{{ route('school.timetable.store') }}'" method="POST" @submit="loading = true" class="p-8 space-y-8">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="day_of_week" :value="selectedDay">
                <input type="hidden" name="academic_year_id" value="{{ $activeYear?->id }}">
                <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">
                <input type="hidden" name="start_time" :value="formattedStartTime">
                <input type="hidden" name="end_time" :value="formattedEndTime">

                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Assignment (Subject & Teacher)</label>
                        <div class="relative">
                            <select name="mapping_index" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-5 py-4 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all appearance-none cursor-pointer" required onchange="updateMapping(this)">
                                <option value="">Select a mapping</option>
                                @foreach($mappings as $index => $mapping)
                                    <option value="{{ $index }}" data-subject="{{ $mapping->subject_name }}" data-staff="{{ $mapping->school_staff_id }}">
                                        {{ $mapping->subject_name }} - {{ $mapping->staff->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                <i data-lucide="chevron-down" size="18"></i>
                            </div>
                        </div>
                        <input type="hidden" name="subject_name" id="modal_subject_name">
                        <input type="hidden" name="school_staff_id" id="modal_staff_id">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Start Time</label>
                            <div class="grid grid-cols-3 gap-2">
                                <select x-model="startTimeH" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-2 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                                    @foreach(range(1, 12) as $h)
                                        <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endforeach
                                </select>
                                <select x-model="startTimeM" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-2 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                                <select x-model="startTimeP" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-2 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1">End Time</label>
                            <div class="grid grid-cols-3 gap-2">
                                <select x-model="endTimeH" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-2 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                                    @foreach(range(1, 12) as $h)
                                        <option value="{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endforeach
                                </select>
                                <select x-model="endTimeM" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-2 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                                    <option value="00">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                                <select x-model="endTimeP" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-2 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 outline-none transition-all cursor-pointer">
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Room Number (Optional)</label>
                        <div class="relative group">
                            <input type="text" name="room_number" x-model="roomNumber" placeholder="e.g. Lab 01" class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl px-5 py-4 pl-12 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-300 group-focus-within:text-indigo-500 transition-colors">
                                <i data-lucide="map-pin" size="18"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 pb-2">
                    <button type="submit" 
                            :disabled="loading"
                            class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 disabled:cursor-not-allowed text-white font-black uppercase tracking-[0.2em] text-xs rounded-2xl shadow-xl shadow-indigo-200 transition-all flex items-center justify-center gap-3">
                        <template x-if="!loading">
                            <span>Confirm Schedule Block</span>
                        </template>
                        <template x-if="loading">
                            <div class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Processing...</span>
                            </div>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateMapping(select) {
        const option = select.options[select.selectedIndex];
        document.getElementById('modal_subject_name').value = option.dataset.subject || '';
        document.getElementById('modal_staff_id').value = option.dataset.staff || '';
    }

    function confirmDeleteSlot(id) {
        Swal.fire({
            title: 'Delete Slot?',
            text: "This time slot will be removed from the timetable.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            borderRadius: '1.5rem',
            customClass: {
                title: 'font-black text-slate-800',
                confirmButton: 'rounded-xl font-bold uppercase tracking-widest text-xs px-6 py-3',
                cancelButton: 'rounded-xl font-bold uppercase tracking-widest text-xs px-6 py-3'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-slot-${id}`).submit();
            }
        })
    }

    // Refresh icons after Alpine model insertion
    document.addEventListener('alpine:initialized', () => {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
