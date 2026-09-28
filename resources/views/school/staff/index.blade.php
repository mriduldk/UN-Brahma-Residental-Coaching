@extends('layouts.school')

@section('title', 'Staff Management')

@section('content')
    <div x-data="{ 
        assignModalOpen: false, 
        staffId: null, 
        staffName: '', 
        assignAction: '', 
        isSubmitting: false,
        selectedClassSections: [],
        updateSections(event) {
            const option = event.target.options[event.target.selectedIndex];
            this.selectedClassSections = option.dataset.sections ? JSON.parse(option.dataset.sections) : [];
        }
    }" class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-indigo-900 tracking-tight">Staff Management</h1>
                <p class="text-slate-500 text-sm mt-1">Manage teachers, administrators, and assign them to classes.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('school.staff.create') }}"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200/50 transition-all flex items-center gap-2 text-sm text-center">
                    <i data-lucide="plus" size="18"></i>
                    Add New Staff
                </a>
            </div>
        </div>

        @if(session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                <i data-lucide="check-circle-2" size="20"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <form method="GET" action="{{ route('school.staff.index') }}"
            class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Search</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name, ID or phone..."
                            class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pl-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                            <i data-lucide="search" size="16"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Department</label>
                    <div class="relative group">
                        <select name="department"
                            class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm"
                            onchange="this.form.submit()">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                            <i data-lucide="chevron-down" size="16"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Designation</label>
                    <div class="relative group">
                        <select name="designation"
                            class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm"
                            onchange="this.form.submit()">
                            <option value="">All Designations</option>
                            @foreach($designations as $desig)
                                <option value="{{ $desig }}" {{ request('designation') == $desig ? 'selected' : '' }}>{{ $desig }}
                                </option>
                            @endforeach
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                            <i data-lucide="chevron-down" size="16"></i>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-4 flex justify-end gap-3">
                    @if(request()->anyFilled(['search', 'department', 'designation']))
                        <a href="{{ route('school.staff.index') }}"
                            class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                            Clear Filters
                        </a>
                    @endif
                    <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200/50 transition-all flex items-center gap-2 text-sm">
                        <i data-lucide="filter" size="16"></i> Filter Results
                    </button>
                </div>
            </div>
        </form>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="text-slate-500 text-[10px] uppercase font-bold tracking-widest border-b border-slate-50 bg-slate-50/50">
                            <th class="px-6 py-4">Staff Member</th>
                            <th class="px-6 py-4">Employee ID</th>
                            <th class="px-6 py-4">Role & Dept</th>
                            <th class="px-6 py-4">Contact</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-50">
                        @forelse($staffs as $staff)
                            <tr class="group hover:bg-indigo-50/30 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('school.staff.show', $staff->id) }}'">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 font-bold text-sm uppercase shadow-sm">
                                            {{ substr($staff->full_name, 0, 2) }}
                                        </div>
                                        <div class="font-bold text-slate-800 leading-tight">{{ $staff->full_name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="font-medium text-slate-600 bg-slate-100 px-3 py-1 rounded-lg text-xs">{{ $staff->employee_id ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-700">{{ $staff->designation ?? 'No Designation' }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">
                                        {{ $staff->department ?? 'No Dept' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium tracking-wide">
                                            <i data-lucide="phone" class="w-3 h-3 opacity-70"></i>
                                            {{ $staff->phone_number ?? '-' }}
                                        </div>
                                        <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium tracking-wide">
                                            <i data-lucide="mail" class="w-3 h-3 opacity-70"></i> {{ $staff->email ?? '-' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                            @click="event.stopPropagation(); assignModalOpen = true; staffId = {{ $staff->id }}; staffName = '{{ addslashes($staff->full_name) }}'; assignAction = '{{ route('school.staff.map', $staff->id) }}'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-purple-100 transition-all border border-purple-100/50"
                                            title="Assign Class & Subject">
                                            <i data-lucide="book-plus" size="14"></i>
                                            Assign Class & Subject
                                        </button>
                                        <a href="{{ route('school.staff.show', $staff->id) }}"
                                            class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center border border-indigo-100"
                                            title="View Profile" onclick="event.stopPropagation()">
                                            <i data-lucide="eye" size="14"></i>
                                        </a>
                                        <a href="{{ route('school.staff.edit', $staff->id) }}"
                                            class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-all flex items-center justify-center border border-amber-100"
                                            title="Edit Staff" onclick="event.stopPropagation()">
                                            <i data-lucide="pencil" size="14"></i>
                                        </a>
                                        <button type="button"
                                            onclick="event.stopPropagation(); confirmDelete({{ $staff->id }}, '{{ addslashes($staff->full_name) }}')"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all flex items-center justify-center border border-red-100"
                                            title="Remove Staff">
                                            <i data-lucide="trash-2" size="14"></i>
                                        </button>

                                        <form id="delete-form-{{ $staff->id }}"
                                            action="{{ route('school.staff.destroy', $staff->id) }}" method="POST"
                                            class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center text-slate-400">
                                    <div
                                        class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                        <i data-lucide="users" size="28" class="text-slate-300"></i>
                                    </div>
                                    <p class="font-bold text-slate-600 text-lg">No Staff Members Found</p>
                                    <p class="text-sm mt-1 max-w-sm mx-auto">Click 'Add New Staff' to start building your
                                        directory.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($staffs->hasPages())
                <div class="p-6 border-t border-slate-100">
                    {{ $staffs->links() }}
                </div>
            @endif
        </div>

        <!-- Quick Assignment Modal -->
        <div x-show="assignModalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            style="display: none;" @keydown.escape.window="assignModalOpen = false">

            <div @click.away="assignModalOpen = false"
                class="bg-white rounded-[2rem] w-full max-w-lg overflow-hidden shadow-2xl animate-in zoom-in-95 duration-300">

                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-white relative">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 border border-purple-100 font-black">
                            <i data-lucide="book-check" size="20"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-800 tracking-tight">Assign Duties</h2>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider" x-text="staffName"></p>
                        </div>
                    </div>
                    <button @click="assignModalOpen = false"
                        class="text-slate-400 hover:text-rose-500 transition-colors p-2 rounded-xl hover:bg-rose-50">
                        <i data-lucide="x" size="20"></i>
                    </button>
                </div>

                <form :action="assignAction" method="POST" class="p-8 space-y-6" @submit="isSubmitting = true">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Academic
                                Session</label>
                            <div class="relative group">
                                <select name="academic_year_id"
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                                    required>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                                            {{ $year->name }}</option>
                                    @endforeach
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                    <i data-lucide="chevron-down" size="16"></i>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2 text-wrap">Class</label>
                                <div class="relative group">
                                    <select name="class_id"
                                        class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                                        required @change="updateSections($event)">
                                        <option value="" disabled selected>Select</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}"
                                                data-sections='{{ json_encode($class->sections) }}'>{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                        <i data-lucide="chevron-down" size="16"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2 text-wrap">Section</label>
                                <div class="relative group">
                                    <select name="section_id"
                                        class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm">
                                        <option value="">All</option>
                                        <template x-for="section in selectedClassSections" :key="section.id">
                                            <option :value="section.id" x-text="section.name"></option>
                                        </template>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                                        <i data-lucide="chevron-down" size="16"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Subject
                                Name</label>
                            <input type="text" name="subject_name"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-700 font-medium focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all shadow-sm"
                                placeholder="e.g. Mathematics, Science">
                            <p class="text-[9px] text-slate-400 mt-1 font-bold italic">Leave blank if mapping as Class
                                Teacher only.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="assignModalOpen = false"
                            class="flex-1 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black uppercase tracking-widest text-xs rounded-xl transition-all">
                            Cancel
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                            :class="{'opacity-75 cursor-not-allowed': isSubmitting}"
                            class="flex-[2] px-6 py-3 bg-slate-900 hover:bg-slate-950 text-white font-black uppercase tracking-widest text-xs rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <i data-lucide="plus-circle" size="14" x-show="!isSubmitting"></i>
                            <i data-lucide="loader-2" size="14" class="animate-spin" x-show="isSubmitting"
                                style="display: none;"></i>
                            <span x-text="isSubmitting ? 'Assigning...' : 'Assign Duty'">Assign Duty</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Remove Staff?',
                    html: `Are you sure you want to remove <b class="text-indigo-600">${name}</b>? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Remove Staff',
                    cancelButtonText: 'Cancel',
                    padding: '2rem',
                    borderRadius: '1.5rem',
                    customClass: {
                        title: 'text-2xl font-black text-slate-800',
                        htmlContainer: 'text-slate-500 font-medium mt-2',
                        confirmButton: 'px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-xs shadow-lg shadow-red-200',
                        cancelButton: 'px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-xs shadow-lg shadow-slate-200'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`delete-form-${id}`).submit();
                    }
                });
            }
        </script>
    @endpush
@endsection