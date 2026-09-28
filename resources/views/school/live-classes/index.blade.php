@extends('layouts.school')

@section('content')
<x-missing-session-popup />

<div class="space-y-8 shadow-sm hide-scrollbar" x-data="{ 
    createModal: false, 
    editModal: false, 
    meetingType: 'automatic',
    editMeetingType: 'automatic',
    editClass: {},
    openEditModal(data) {
        this.editClass = data;
        this.editMeetingType = (data.meeting_id && data.meeting_id.toString().startsWith('MANUAL')) ? 'manual' : (data.meeting_url ? 'manual' : 'automatic');
        
        // Format datetime-local: YYYY-MM-DDTHH:MM
        if(data.start_time) {
            let dt = new Date(data.start_time);
            let year = dt.getFullYear();
            let month = (dt.getMonth() + 1).toString().padStart(2, '0');
            let day = dt.getDate().toString().padStart(2, '0');
            let hours = dt.getHours().toString().padStart(2, '0');
            let minutes = dt.getMinutes().toString().padStart(2, '0');
            this.editClass.start_time_input = `${year}-${month}-${day}T${hours}:${minutes}`;
        }
        
        this.editModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Live Class Management</h1>
            <p class="text-slate-500 mt-1">Schedule and manage virtual classrooms with Zoom.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="createModal = true" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 transition-all text-xs uppercase tracking-widest flex items-center gap-3">
                <i data-lucide="video" size="18"></i>
                Schedule Live Class
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center gap-5 group hover:border-indigo-100 transition-all">
            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                <i data-lucide="calendar" size="24"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Scheduled</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $liveClasses->total() }}</h3>
            </div>
        </div>
        
        @php $liveNow = $liveClasses->filter(fn($c) => $c->is_live)->count(); @endphp
        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center gap-5 group hover:border-emerald-100 transition-all">
            <div class="w-14 h-14 {{ $liveNow > 0 ? 'bg-emerald-50 text-emerald-600 animate-pulse' : 'bg-slate-50 text-slate-400' }} rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
                <i data-lucide="radio" size="24"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Live Now</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $liveNow }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm flex items-center gap-5 group hover:border-rose-100 transition-all">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 group-hover:scale-110 transition-transform">
                <i data-lucide="history" size="24"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Duration (Avg)</p>
                <h3 class="text-2xl font-black text-slate-800">40m</h3>
            </div>
        </div>
    </div>

    <!-- Live Classes List -->
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Class / Subject</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Topic</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Schedule</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($liveClasses as $class)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-slate-800">{{ $class->schoolClass->name }} {{ $class->section->name ?? '' }}</p>
                            <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest mt-0.5">{{ $class->subject_name }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-medium text-slate-600 line-clamp-1">{{ $class->topic }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-black text-slate-800">{{ $class->start_time->format('d M, Y') }}</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $class->start_time->format('h:i A') }} ({{ $class->duration }}m)</p>
                        </td>
                        <td class="px-8 py-6">
                            @if($class->is_live)
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-lg uppercase tracking-widest flex items-center gap-2 w-fit">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                    Live Now
                                </span>
                            @else
                                <span class="px-3 py-1 {{ $class->status === 'scheduled' ? 'bg-indigo-50 text-indigo-700' : ($class->status === 'completed' ? 'bg-slate-100 text-slate-600' : 'bg-rose-50 text-rose-700') }} text-[10px] font-black rounded-lg uppercase tracking-widest">
                                    {{ $class->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('school.live-classes.join', $class->id) }}" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-100 flex items-center gap-2">
                                    <i data-lucide="play" size="12"></i>
                                    {{ $class->is_live ? 'Join' : 'Start' }}
                                </a>
                                @if($class->status === 'scheduled')
                                <button @click="openEditModal({{ json_encode($class->only(['id', 'topic', 'start_time', 'duration', 'meeting_id', 'meeting_url', 'password'])) }})" class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all border border-slate-200 hover:border-indigo-100">
                                    <i data-lucide="edit-3" class="p-1" size="16"></i>
                                </button>
                                <form action="{{ route('school.live-classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Cancel this live class?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all border border-slate-200 hover:border-rose-100">
                                        <i data-lucide="trash-2" class="p-1" size="16"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center justify-center opacity-30">
                                <i data-lucide="video-off" size="48" class="mb-4 text-slate-300"></i>
                                <p class="text-sm font-black text-slate-400 uppercase tracking-widest">No scheduled live classes</p>
                                <button @click="createModal = true" class="mt-4 text-xs font-black text-indigo-600 uppercase tracking-widest underline decoration-indigo-200">Schedule your first session</button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($liveClasses->hasPages())
        <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/50">
            {{ $liveClasses->links() }}
        </div>
        @endif
    </div>

    <!-- Edit Modal -->
    <div x-show="editModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        
        <div @click.away="editModal = false" 
             class="bg-white rounded-[2.5rem] w-full max-w-2xl max-h-[90vh] overflow-y-auto hide-scrollbar shadow-2xl"
             x-data="{ loading: false }">
            
            <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                        <i data-lucide="edit-3" size="24"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 leading-tight">Edit Schedule</h3>
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mt-1">Update session details</p>
                    </div>
                </div>
                <button @click="editModal = false" class="w-10 h-10 rounded-xl hover:bg-white flex items-center justify-center text-slate-400 transition-colors">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>

            <form :action="'{{ route('school.live-classes.index') }}/' + editClass.id" method="POST" class="p-8 space-y-6" @submit="loading = true">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Topic / Lesson Name</label>
                        <input type="text" name="topic" x-model="editClass.topic" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Start Time</label>
                        <input type="datetime-local" name="start_time" x-model="editClass.start_time_input" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Duration (Minutes)</label>
                        <select name="duration" x-model="editClass.duration" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer">
                            <option value="30">30 Minutes</option>
                            <option value="40">40 Minutes</option>
                            <option value="60">1 Hour</option>
                            <option value="90">1.5 Hours</option>
                        </select>
                    </div>

                    <!-- Meeting Type Toggle (ReadOnly or Switchable) -->
                    <div class="md:col-span-2 space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Meeting Type</label>
                        <div class="flex p-1.5 bg-slate-100 rounded-2xl w-fit">
                            <button type="button" @click="editMeetingType = 'automatic'" :class="editMeetingType === 'automatic' ? 'bg-white shadow-sm' : ''" class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Auto Zoom</button>
                            <button type="button" @click="editMeetingType = 'manual'" :class="editMeetingType === 'manual' ? 'bg-white shadow-sm' : ''" class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Manual Link</button>
                        </div>
                    </div>

                    <!-- Manual Fields -->
                    <template x-if="editMeetingType === 'manual'">
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 rounded-[2rem] border border-slate-200 border-dashed animate-fadeIn">
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Manual Meeting URL</label>
                                <input type="url" name="manual_meeting_url" x-model="editClass.meeting_url" placeholder="https://zoom.us/j/..." required class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all font-bold text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Meeting ID</label>
                                <input type="text" name="manual_meeting_id" x-model="editClass.meeting_id" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all font-bold text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Passcode</label>
                                <input type="text" name="manual_password" x-model="editClass.password" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all font-bold text-slate-800">
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="editModal = false" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">Cancel</button>
                    <button type="submit" 
                            :disabled="loading"
                            class="px-10 py-5 bg-amber-600 hover:bg-amber-700 text-white font-black rounded-2xl shadow-2xl shadow-amber-100 transition-all text-xs uppercase tracking-widest flex items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="!loading">
                            <div class="flex items-center gap-3">
                                <i data-lucide="save" size="18"></i>
                                <span>Save Changes</span>
                            </div>
                        </template>
                        <template x-if="loading">
                            <div class="flex items-center gap-3">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Saving...</span>
                            </div>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="createModal" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        
        <div @click.away="createModal = false" 
             class="bg-white rounded-[2.5rem] w-full max-w-2xl max-h-[90vh] overflow-y-auto hide-scrollbar shadow-2xl"
             x-data="{ loading: false }">
            
            <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                        <i data-lucide="video" size="24"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 leading-tight">Schedule New Class</h3>
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mt-1">Virtual Classroom Session</p>
                    </div>
                </div>
                <button @click="createModal = false" class="w-10 h-10 rounded-xl hover:bg-white flex items-center justify-center text-slate-400 transition-colors">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>

            <form action="{{ route('school.live-classes.store') }}" method="POST" class="p-8 space-y-6" @submit="loading = true">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Class Selection -->
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Class</label>
                        <select name="class_id" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Select Class</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject Mapping -->
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Subject</label>
                        <select name="subject_name" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj }}">{{ $subj }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Topic / Lesson Name</label>
                        <input type="text" name="topic" required placeholder="e.g. Chapter 4: Fractions Introduction" class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Start Time</label>
                        <input type="datetime-local" name="start_time" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Duration (Minutes)</label>
                        <select name="duration" required class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer">
                            <option value="30">30 Minutes</option>
                            <option value="40" selected>40 Minutes</option>
                            <option value="60">1 Hour</option>
                            <option value="90">1.5 Hours</option>
                        </select>
                    </div>

                    <!-- Meeting Type Switch -->
                    <div class="md:col-span-2 space-y-4">
                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Meeting Generation</label>
                        <div class="flex p-1.5 bg-slate-100 rounded-2xl w-fit">
                            <button type="button" @click="meetingType = 'automatic'" :class="meetingType === 'automatic' ? 'bg-white shadow-sm' : ''" class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Auto Zoom</button>
                            <button type="button" @click="meetingType = 'manual'" :class="meetingType === 'manual' ? 'bg-white shadow-sm' : ''" class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Manual Link</button>
                        </div>
                    </div>

                    <!-- Manual Fields -->
                    <template x-if="meetingType === 'manual'">
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-slate-50 rounded-[2rem] border border-slate-200 border-dashed animate-fadeIn">
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Manual Meeting URL</label>
                                <input type="url" name="manual_meeting_url" placeholder="https://zoom.us/j/..." required class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all font-bold text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Meeting ID (Optional)</label>
                                <input type="text" name="manual_meeting_id" placeholder="123 456 7890" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all font-bold text-slate-800">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Passcode (Optional)</label>
                                <input type="text" name="manual_password" placeholder="Pass123" class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all font-bold text-slate-800">
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <button type="button" @click="createModal = false" class="text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">Cancel</button>
                    <button type="submit" 
                            :disabled="loading"
                            class="px-10 py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-2xl shadow-indigo-200 transition-all text-xs uppercase tracking-widest flex items-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="!loading">
                            <div class="flex items-center gap-3">
                                <i data-lucide="check-circle" size="18"></i>
                                <span x-text="meetingType === 'automatic' ? 'Generate & Schedule' : 'Schedule Class'"></span>
                            </div>
                        </template>
                        <template x-if="loading">
                            <div class="flex items-center gap-3">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Generating...</span>
                            </div>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<style>
    [x-cloak] { display: none !important; }
    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
@endpush
@endsection
