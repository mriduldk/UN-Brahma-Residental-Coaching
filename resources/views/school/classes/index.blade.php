@extends('layouts.school')

@section('title', 'Manage Classes & Sections')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Classes & Sections</h1>
            <p class="text-slate-500 text-sm mt-1">Define your school's academic structure by creating classes and sections.</p>
        </div>
        <div>
            <button onclick="document.getElementById('addClassModal').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all flex items-center gap-2">
                <i data-lucide="plus" size="18"></i>
                <span>Add New Class</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <i data-lucide="check-circle" size="20"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Classes List -->
        <div class="lg:col-span-3 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($classes as $class)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden group hover:border-indigo-300 transition-all">
                    <!-- Class Header -->
                    <div class="p-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-bold">
                                {{ $class->code ?: substr($class->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 leading-tight">{{ $class->name }}</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $class->sections_count ?? $class->sections->count() }} Sections</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="openEditClassModal('{{ $class->id }}', '{{ $class->name }}', '{{ $class->code }}')" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit Class">
                                <i data-lucide="edit-3" size="18"></i>
                            </button>
                            <button onclick="openAddSectionModal('{{ $class->id }}', '{{ $class->name }}')" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Add Section">
                                <i data-lucide="plus-square" size="18"></i>
                            </button>
                            <form action="{{ route('school.classes.destroy', $class->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this class and all its sections?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" size="18"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Sections List -->
                    <div class="p-4 bg-white">
                        <div class="grid grid-cols-2 gap-3">
                            @forelse($class->sections as $section)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100 group/section hover:border-indigo-200 transition-all">
                                <div>
                                    <div class="text-sm font-bold text-slate-700 uppercase">{{ $section->name }}</div>
                                    @if($section->room_number)
                                    <div class="text-[10px] text-slate-400 font-medium tracking-tight">Room: {{ $section->room_number }}</div>
                                    @endif
                                </div>
                                <form action="{{ route('school.sections.destroy', $section->id) }}" method="POST" onsubmit="return confirm('Remove this section?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="opacity-100 p-1.5 text-slate-300 hover:text-rose-500 transition-all">
                                        <i data-lucide="x-circle" size="14"></i>
                                    </button>
                                </form>
                            </div>
                            @empty
                            <div class="col-span-2 py-4 text-center text-slate-300 text-xs font-medium italic">
                                No sections added yet.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center bg-white rounded-[2.5rem] border border-dashed border-slate-300">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                        <i data-lucide="layers" size="40"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">No Classes Defined</h3>
                    <p class="text-slate-500 mt-2 max-w-sm mx-auto">Get started by creating your first academic class and adding sections to it.</p>
                    <button onclick="document.getElementById('addClassModal').classList.remove('hidden')" class="mt-8 px-6 py-3 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transition-all">
                        Create Your First Class
                    </button>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Class Modal -->
<div id="addClassModal" class="hidden fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('addClassModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-indigo-100">
            <div class="bg-indigo-600 px-8 py-6 flex items-center justify-between text-white">
                <h3 class="text-xl font-black">Add New Class</h3>
                <button onclick="document.getElementById('addClassModal').classList.add('hidden')" class="text-indigo-200 hover:text-white transition-colors">
                    <i data-lucide="x" size="24"></i>
                </button>
            </div>
            <form action="{{ route('school.classes.store') }}" method="POST" class="p-8 space-y-6" onsubmit="return handleFormSubmit(this)">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Class Name</label>
                    <input type="text" name="name" placeholder="e.g. Class 10" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder:text-slate-300" required autofocus>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Short Code (Optional)</label>
                    <input type="text" name="code" placeholder="e.g. C10" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder:text-slate-300">
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="document.getElementById('addClassModal').classList.add('hidden')" class="flex-1 py-4 text-slate-400 font-bold hover:text-slate-600 transition-colors">Cancel</button>
                    <button type="submit" class="flex-[2] py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                        <span>Create Class</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Class Modal -->
<div id="editClassModal" class="hidden fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('editClassModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-indigo-100">
            <div class="bg-indigo-600 px-8 py-6 flex items-center justify-between text-white">
                <h3 class="text-xl font-black">Edit Class</h3>
                <button onclick="document.getElementById('editClassModal').classList.add('hidden')" class="text-indigo-200 hover:text-white transition-colors">
                    <i data-lucide="x" size="24"></i>
                </button>
            </div>
            <form id="editClassForm" method="POST" class="p-8 space-y-6" onsubmit="return handleFormSubmit(this)">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Class Name</label>
                    <input type="text" name="name" id="editClassName" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder:text-slate-300" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Short Code (Optional)</label>
                    <input type="text" name="code" id="editClassCode" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder:text-slate-300">
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="document.getElementById('editClassModal').classList.add('hidden')" class="flex-1 py-4 text-slate-400 font-bold hover:text-slate-600 transition-colors">Cancel</button>
                    <button type="submit" class="flex-[2] py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="addSectionModal" class="hidden fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('addSectionModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-indigo-100">
            <div class="bg-indigo-600 px-8 py-6 flex items-center justify-between text-white">
                <div>
                    <h3 class="text-xl font-black">Add Section</h3>
                    <p class="text-xs text-indigo-200 font-medium" id="modalClassNameLabel"></p>
                </div>
                <button onclick="document.getElementById('addSectionModal').classList.add('hidden')" class="text-indigo-200 hover:text-white transition-colors">
                    <i data-lucide="x" size="24"></i>
                </button>
            </div>
            <form action="{{ route('school.sections.store') }}" method="POST" class="p-8 space-y-6" onsubmit="return handleFormSubmit(this)">
                @csrf
                <input type="hidden" name="class_id" id="modalClassId">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Section Name</label>
                    <input type="text" name="name" placeholder="e.g. Section A" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder:text-slate-300" required autofocus>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Room Number (Optional)</label>
                    <input type="text" name="room_number" placeholder="e.g. 101" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all placeholder:text-slate-300">
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="document.getElementById('addSectionModal').classList.add('hidden')" class="flex-1 py-4 text-slate-400 font-bold hover:text-slate-600 transition-colors">Cancel</button>
                    <button type="submit" class="flex-[2] py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-100 transition-all text-sm uppercase tracking-widest flex items-center justify-center gap-2">
                        <span>Add Section</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openAddSectionModal(classId, className) {
        document.getElementById('modalClassId').value = classId;
        document.getElementById('modalClassNameLabel').textContent = 'Adding to ' + className;
        document.getElementById('addSectionModal').classList.remove('hidden');
    }

    function openEditClassModal(id, name, code) {
        document.getElementById('editClassName').value = name;
        document.getElementById('editClassCode').value = code;
        document.getElementById('editClassForm').action = '/school/classes/' + id;
        document.getElementById('editClassModal').classList.remove('hidden');
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
