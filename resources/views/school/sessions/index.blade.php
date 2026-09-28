@extends('layouts.school')

@section('title', 'Manage Academic Sessions')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Academic Sessions</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your school's academic years and set the current active session.</p>
        </div>
        <div>
            <button onclick="document.getElementById('addSessionModal').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
                <i data-lucide="plus" size="18"></i>
                <span>Add New Session</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <i data-lucide="check-circle" size="20"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <i data-lucide="alert-circle" size="20"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-50">
                        <th class="px-8 py-5">Session Name</th>
                        <th class="px-8 py-5">Start Date</th>
                        <th class="px-8 py-5">End Date</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-50">
                    @forelse($sessions as $session)
                    <tr class="group hover:bg-indigo-50/30 transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 {{ $session->is_active ? 'bg-indigo-600' : 'bg-slate-100' }} rounded-xl flex items-center justify-center text-white transition-colors">
                                    <i data-lucide="calendar" size="20" class="{{ $session->is_active ? 'text-white' : 'text-slate-400' }}"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $session->name }}</div>
                                    @if($session->is_active)
                                        <div class="text-[10px] text-indigo-600 font-bold uppercase tracking-tight">Current Session</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5 font-medium text-slate-600">
                            {{ $session->start_date->format('d M, Y') }}
                        </td>
                        <td class="px-8 py-5 font-medium text-slate-600">
                            {{ $session->end_date->format('d M, Y') }}
                        </td>
                        <td class="px-8 py-5 text-center">
                            @if($session->is_active)
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest">Active</span>
                            @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest">Inactive</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-end gap-2">
                                @if(!$session->is_active)
                                    <form action="{{ route('school.sessions.set-active', $session->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg text-[10px] font-bold uppercase tracking-wider transition-all">
                                            Make Active
                                        </button>
                                    </form>
                                @endif
                                <button onclick="openEditSessionModal('{{ $session->id }}', '{{ $session->name }}', '{{ $session->start_date->format('Y-m-d') }}', '{{ $session->end_date->format('Y-m-d') }}')" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                    <i data-lucide="edit-3" size="18"></i>
                                </button>
                                <form action="{{ route('school.sessions.destroy', $session->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this session?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors {{ $session->is_active ? 'opacity-20 cursor-not-allowed' : '' }}" {{ $session->is_active ? 'disabled' : '' }}>
                                        <i data-lucide="trash-2" size="18"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                                <i data-lucide="calendar-x" size="40"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800">No Sessions Found</h3>
                            <p class="text-slate-500 mt-2 max-w-sm mx-auto">Create your first academic session to start managing classes and enrollments.</p>
                            <button onclick="document.getElementById('addSessionModal').classList.remove('hidden')" class="mt-8 px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all">
                                Create Your First Session
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Session Modal -->
<div id="addSessionModal" class="hidden fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('addSessionModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-indigo-100">
            <div class="bg-indigo-600 px-8 py-6 flex items-center justify-between text-white">
                <h3 class="text-xl font-black text-white">Add New Session</h3>
                <button onclick="document.getElementById('addSessionModal').classList.add('hidden')" class="text-indigo-200 hover:text-white transition-colors">
                    <i data-lucide="x" size="24"></i>
                </button>
            </div>
            <form action="{{ route('school.sessions.store') }}" method="POST" class="p-8 space-y-6" onsubmit="return handleFormSubmit(this)">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Session Name</label>
                    <input type="text" name="name" placeholder="e.g. 2024-25" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder:text-slate-300" required autofocus>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Start Date</label>
                        <input type="date" name="start_date" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">End Date</label>
                        <input type="date" name="end_date" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                    </div>
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="document.getElementById('addSessionModal').classList.add('hidden')" class="flex-1 py-4 text-slate-400 font-bold hover:text-slate-600 transition-colors text-sm uppercase tracking-widest">Cancel</button>
                    <button type="submit" class="flex-[2] py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 transition-all text-sm uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Create Session</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Session Modal -->
<div id="editSessionModal" class="hidden fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('editSessionModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-indigo-100">
            <div class="bg-indigo-600 px-8 py-6 flex items-center justify-between text-white">
                <h3 class="text-xl font-black text-white">Edit Session</h3>
                <button onclick="document.getElementById('editSessionModal').classList.add('hidden')" class="text-indigo-200 hover:text-white transition-colors">
                    <i data-lucide="x" size="24"></i>
                </button>
            </div>
            <form id="editSessionForm" method="POST" class="p-8 space-y-6" onsubmit="return handleFormSubmit(this)">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Session Name</label>
                    <input type="text" name="name" id="editSessionName" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder:text-slate-300" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Start Date</label>
                        <input type="date" name="start_date" id="editSessionStartDate" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">End Date</label>
                        <input type="date" name="end_date" id="editSessionEndDate" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                    </div>
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="document.getElementById('editSessionModal').classList.add('hidden')" class="flex-1 py-4 text-slate-400 font-bold hover:text-slate-600 transition-colors text-sm uppercase tracking-widest">Cancel</button>
                    <button type="submit" class="flex-[2] py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 transition-all text-sm uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openEditSessionModal(id, name, start, end) {
        document.getElementById('editSessionName').value = name;
        document.getElementById('editSessionStartDate').value = start;
        document.getElementById('editSessionEndDate').value = end;
        document.getElementById('editSessionForm').action = '/school/sessions/' + id;
        document.getElementById('editSessionModal').classList.remove('hidden');
    }

    function handleFormSubmit(form) {
        try {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn) return true;

            // Disable button
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            
            // Add spinner and text
            submitBtn.innerHTML = `
                <div class="flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="font-bold uppercase tracking-wider text-xs">Processing...</span>
                </div>
            `;
        } catch (e) {
            console.error('Loading state failed:', e);
        }
        
        return true;
    }
</script>
@endpush
@endsection
