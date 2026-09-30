@extends('layouts.institute')

@section('title', 'Forms Manager')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Forms Manager</h1>
            <p class="text-slate-500 text-sm">Create and manage admission forms</p>
        </div>
        @if(auth()->user()->institutions()->wherePivot('role', 'admin')->exists())
        <a href="{{ route('institute.forms.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5">
            <i data-lucide="plus" size="18" class="mr-2"></i> Create New Form
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 flex items-center">
        <i data-lucide="check-circle" class="mr-2"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @forelse($forms as $form)
        <!-- Form Card -->
        <div class="bg-white group rounded-3xl p-6 border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all duration-300 flex flex-col h-full hover:border-blue-200 relative overflow-hidden">
            <!-- Decorative gradient line -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>

            <div class="flex justify-between items-start mb-4">
                <div class="flex gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="file-text" size="28"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-xl leading-tight mb-1 group-hover:text-blue-600 transition-colors">{{ $form->application_name }}</h3>
                        <div class="flex items-center text-slate-500 text-xs font-medium">
                            <i data-lucide="building-2" size="12" class="mr-1.5"></i>
                            {{ $form->institution->institution_name ?? 'No Institute Assigned' }}
                        </div>
                    </div>
                </div>
                <span class="shrink-0 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $form->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $form->status }}
                </span>
            </div>

            <p class="text-slate-600 text-sm mb-6 leading-relaxed bg-slate-50/50 p-3 rounded-xl border border-slate-100">
                {{ $form->application_description ?? 'No description provided.' }}
            </p>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col items-center justify-center text-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400 mb-1">Session</span>
                    <span class="font-bold text-slate-700 text-sm">{{ $form->session }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col items-center justify-center text-center">
                     <span class="text-[10px] uppercase font-bold text-slate-400 mb-1">Code</span>
                    <span class="font-bold text-slate-700 font-mono text-xs">{{ $form->application_code }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col items-center justify-center text-center">
                     <span class="text-[10px] uppercase font-bold text-slate-400 mb-1">App. Fee</span>
                     <span class="font-bold text-slate-700 text-sm">₹{{ number_format($form->application_fee, 0) }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col items-center justify-center text-center">
                     <span class="text-[10px] uppercase font-bold text-slate-400 mb-1">Adm. Fee</span>
                     <span class="font-bold text-slate-700 text-sm">₹{{ number_format($form->admission_fee, 0) }}</span>
                </div>
            </div>

            <div class="text-sm text-slate-500 mb-6 flex items-center justify-center bg-slate-50/50 py-2 rounded-lg border border-slate-100">
                <i data-lucide="calendar" size="14" class="mr-2 text-slate-400"></i>
                <span class="mr-2">Deadline:</span>
                <span class="font-bold {{ $form->isDeadlinePassed() ? 'text-red-500' : 'text-slate-700' }}">
                    {{ $form->deadline ? $form->deadline->format('d M, Y') : 'No Deadline' }}
                </span>
            </div>

            <div class="mt-auto flex items-center justify-between gap-3 pt-4 border-t border-slate-100">
                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('institute.forms.show', $form->id) }}" class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm" title="View Details">
                        <i data-lucide="eye" size="16"></i>
                    </a>
                    
                    @if(auth()->user()->isInstitutionAdmin($form->institution_id))
                    <a href="{{ route('institute.forms.edit', $form->id) }}" class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-slate-200 text-slate-400 hover:text-orange-600 hover:border-orange-200 transition-all shadow-sm" title="Edit Form">
                        <i data-lucide="edit-2" size="16"></i>
                    </a>
                    <form action="{{ route('institute.forms.destroy', $form->id) }}" method="POST" id="delete-form-{{ $form->id }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete('{{ $form->id }}')" class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all shadow-sm" title="Delete">
                            <i data-lucide="trash-2" size="16"></i>
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Main Action -->
                <a href="{{ route('institute.forms.applications', ['form_id' => $form->id]) }}" class="flex-1 px-4 py-2 bg-white hover:bg-blue-50 border border-blue-200 text-blue-600 rounded-lg font-bold text-sm shadow-sm hover:shadow hover:-translate-y-0.5 transition-all flex items-center justify-center group/btn">
                    <span>View Applications</span>
                    <i data-lucide="arrow-right" class="ml-2 group-hover/btn:translate-x-1 transition-transform" size="16"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-20">
            <div class="bg-slate-50 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 text-slate-300">
                <i data-lucide="file-plus" size="40"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800 mb-2">No Forms Created</h3>
            <p class="text-slate-500 max-w-sm mx-auto mb-8">Get started by creating your first admission form to start accepting applications.</p>
            
            @if(auth()->user()->institutions()->wherePivot('role', 'admin')->exists())
            <button onclick="window.location.href='{{ route('institute.forms.create') }}'" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 font-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-1">
                Create First Form
            </button>
            @endif
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(formId) {
            Swal.fire({
                title: 'Delete this form?',
                text: "This action cannot be undone. All associated applications might also be affected.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-2xl border border-slate-200 shadow-xl',
                    confirmButton: 'bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg font-medium',
                    cancelButton: 'bg-slate-500 hover:bg-slate-600 px-4 py-2 rounded-lg font-medium'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + formId).submit();
                }
            })
        }
    </script>
@endpush
