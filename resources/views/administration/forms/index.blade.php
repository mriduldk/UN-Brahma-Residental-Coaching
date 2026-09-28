@extends('layouts.administration')

@section('title', 'Forms Manager')

@section('content')
<div class="space-y-6" x-data="formsManager">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Forms Manager</h1>
            <p class="text-slate-500 text-sm">Create and manage admission forms</p>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <!-- Configuration Link -->
            <a href="{{ route('administration.configuration.quota.transactions') }}" class="bg-white border border-slate-200 px-5 py-2.5 rounded-2xl flex items-center gap-3 shadow-sm text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-all">
                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                    <i data-lucide="indian-rupee" size="18"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-black uppercase tracking-widest leading-none mb-0.5">Form Payments</span>
                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-tight">History & Quota</span>
                </div>
            </a>

            <!-- Quota Indicator -->
            <div class="bg-white border border-slate-200 px-4 py-2 rounded-2xl flex items-center gap-3 shadow-sm h-[42px]">
                <div class="w-8 h-8 rounded-full bg-blue-600/10 flex items-center justify-center text-blue-600">
                    <i data-lucide="activity" size="14"></i>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Quota Usage</div>
                    <div class="text-sm font-bold text-slate-700">
                        {{ $org->getUsedFormsCount() }} <span class="text-slate-400 font-medium">/</span> {{ $org->getAvailableFormsCount() }}
                    </div>
                </div>
            </div>

            @if(auth()->user()->isAdmin($org->id))
            <a href="{{ route('administration.forms.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 flex items-center shadow-lg shadow-blue-600/20 transition-all hover:-translate-y-0.5">
                <i data-lucide="plus" size="18" class="mr-2"></i> Create New Form
            </a>
            @endif
        </div>
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
                     <span class="font-bold text-slate-700 text-sm">
                        {{ $form->is_application_fee_required ? '₹' . number_format($form->application_fee, 0) : '-' }}
                     </span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col items-center justify-center text-center">
                     <span class="text-[10px] uppercase font-bold text-slate-400 mb-1">Adm. Fee</span>
                     <span class="font-bold text-slate-700 text-sm">
                        {{ $form->is_admission_required ? '₹' . number_format($form->admission_fee, 0) : '-' }}
                     </span>
                </div>
            </div>

            <div class="text-sm text-slate-500 mb-6 flex items-center justify-center bg-slate-50/50 py-2 rounded-lg border border-slate-100">
                <i data-lucide="calendar" size="14" class="mr-2 text-slate-400"></i>
                <span class="mr-2">Deadline:</span>
                <span class="font-bold {{ $form->deadline && $form->deadline->isPast() ? 'text-red-500' : 'text-slate-700' }}">
                    {{ $form->deadline ? $form->deadline->format('d M, Y') : 'No Deadline' }}
                </span>
            </div>

            <div class="mt-auto flex items-center justify-between gap-3 pt-4 border-t border-slate-100">
                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('administration.forms.show', $form->id) }}" class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm" title="View Details">
                        <i data-lucide="eye" size="16"></i>
                    </a>
                    
                    <button type="button" @click="manageLinks({{ $form->id }})" class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-slate-200 text-slate-400 hover:text-green-600 hover:border-green-200 transition-all shadow-sm" title="Manage Shareable Links">
                        <i data-lucide="share-2" size="16"></i>
                    </button>
                    
                    @if(auth()->user()->isAdmin($form->organization_id))
                    <a href="{{ route('administration.forms.edit', $form->id) }}" class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-slate-200 text-slate-400 hover:text-orange-600 hover:border-orange-200 transition-all shadow-sm" title="Edit Form">
                        <i data-lucide="edit-2" size="16"></i>
                    </a>
                    <form action="{{ route('administration.forms.destroy', $form->id) }}" method="POST" id="delete-form-{{ $form->id }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="confirmDelete('{{ $form->id }}')" class="w-9 h-9 flex items-center justify-center rounded-md bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition-all shadow-sm" title="Delete">
                            <i data-lucide="trash-2" size="16"></i>
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Main Action -->
                <a href="{{ route('administration.applications', ['form_id' => $form->id]) }}" class="flex-1 px-4 py-2 bg-white hover:bg-blue-50 border border-blue-200 text-blue-600 rounded-lg font-bold text-sm shadow-sm hover:shadow hover:-translate-y-0.5 transition-all flex items-center justify-center group/btn">
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
            
            @if(auth()->user()->isAdmin(auth()->user()->currentOrganization()->id))
            <button onclick="window.location.href='{{ route('administration.forms.create') }}'" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 font-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-1">
                Create First Form
            </button>
            @endif
        </div>
        @endforelse
    </div>
    <!-- Manage Links Modal -->
    <x-modal name="manage-links-modal" maxWidth="2xl">
        <div class="p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">Shareable Links</h2>
                    <p class="text-sm font-medium text-slate-500 mt-1" x-text="modalFormName"></p>
                </div>
                <button x-on:click="$dispatch('close-modal', 'manage-links-modal')" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <div class="space-y-4 max-h-[400px] overflow-y-auto px-1 custom-scrollbar">
                <template x-if="loading">
                    <div class="py-10 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
                        <p class="mt-2 text-slate-500 font-medium">Loading links...</p>
                    </div>
                </template>

                <template x-for="link in modalLinks" :key="link.id">
                    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 transition-all hover:border-blue-100 hover:shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="font-bold text-slate-800 text-sm" x-text="link.description"></p>
                                <p class="text-[10px] font-mono text-slate-400 uppercase tracking-wider" x-text="link.link_code"></p>
                            </div>
                            <div class="flex items-center gap-3">
                                 <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :checked="link.is_active" class="sr-only peer" @change="toggleLink(link.id)">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                                </label>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" readonly :value="'{{ url('/f') }}/' + link.link_code" class="flex-1 bg-white border border-slate-200 px-3 py-2 rounded-xl text-[11px] font-mono text-slate-500 focus:outline-none">
                            <button @click="copyToClipboard('{{ url('/f') }}/' + link.link_code)" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-400 hover:text-blue-600 transition-all shadow-sm" title="Copy Link">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                            </button>
                            <button @click="showQrModal(link.id, link.link_code)" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-400 hover:text-indigo-600 transition-all shadow-sm" title="Show QR Code">
                                <i data-lucide="qr-code" class="w-4 h-4"></i>
                            </button>
                            <button @click="deleteLink(link.id, modalFormId)" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-400 hover:text-red-600 transition-all shadow-sm" title="Delete Link">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                     Create New Campaign Link
                </h4>
                <div class="flex gap-2">
                    <input type="text" id="new-link-desc" placeholder="Link label (e.g. Facebook Campaign)" class="flex-1 bg-slate-50 border border-slate-200 px-4 py-3 rounded-2xl text-sm focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition-all">
                    <button @click="addLink(modalFormId)" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center gap-2">
                        <i data-lucide="plus" class="w-5 h-5"></i> Create
                    </button>
                </div>
            </div>
        </div>
    </x-modal>

    <!-- QR Code Modal -->
    <x-modal name="qr-code-modal" maxWidth="md">
        <div class="p-8 text-center">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-slate-900">QR Code</h3>
                <button x-on:click="$dispatch('close-modal', 'qr-code-modal')" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm mb-6 inline-block mx-auto">
                <img :src="qrUrl" alt="QR Code" class="w-48 h-48 mx-auto" x-show="qrUrl">
            </div>
            
            <p class="text-sm text-slate-500 mb-8 font-medium">Scan this code to open the public admission form link.</p>
            
            <div class="flex gap-3">
                <a :href="qrUrl" :download="'QR_' + qrCode + '.png'" class="flex-1 bg-slate-900 text-white py-4 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-slate-800 transition-all shadow-xl shadow-slate-200">
                    <i data-lucide="download" class="w-5 h-5"></i> Download PNG
                </a>
            </div>
        </div>
    </x-modal>
</div>
@endsection

@push('scripts')
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formsManager', () => ({
                modalLinks: [],
                modalFormName: '',
                modalFormId: null,
                loading: false,
                qrUrl: '',
                qrCode: '',

                async manageLinks(formId) {
                    this.loading = true;
                    this.modalFormId = formId;
                    this.$dispatch('open-modal', 'manage-links-modal');

                    try {
                        const res = await fetch(`{{ route('administration.forms.links.get', ['id' => ':id']) }}`.replace(':id', formId));
                        const data = await res.json();
                        this.modalLinks = data.links;
                        this.modalFormName = data.form_name;
                        this.loading = false;
                        this.$nextTick(() => lucide.createIcons());
                    } catch (e) {
                        this.loading = false;
                        Swal.fire('Error', 'Failed to load links', 'error');
                    }
                },

                async addLink() {
                    const desc = document.getElementById('new-link-desc').value;
                    if (!desc) return Swal.fire('Error', 'Label is required', 'error');

                    try {
                        const res = await fetch(`{{ route('administration.forms.links.add') }}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ application_form_id: this.modalFormId, description: desc })
                        });
                        const data = await res.json();
                        if (data.success) {
                            document.getElementById('new-link-desc').value = '';
                            this.manageLinks(this.modalFormId);
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'Failed to create link', 'error');
                    }
                },

                toggleLink(linkId) {
                    fetch(`{{ route('administration.forms.links.toggle', ['id' => ':id']) }}`.replace(':id', linkId), {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                },

                deleteLink(linkId) {
                    Swal.fire({
                        title: 'Delete link?',
                        text: 'This link will no longer work.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Yes, delete it'
                    }).then(async result => {
                        if (result.isConfirmed) {
                            try {
                                const res = await fetch(`{{ route('administration.forms.links.delete', ['id' => ':id']) }}`.replace(':id', linkId), {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                });
                                const data = await res.json();
                                if (data.success) {
                                    this.manageLinks(this.modalFormId);
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            } catch (e) {
                                Swal.fire('Error', 'Failed to delete link', 'error');
                            }
                        }
                    });
                },

                showQrModal(linkId, linkCode) {
                    this.qrUrl = `{{ route('administration.forms.links.qrcode', ['id' => ':id']) }}`.replace(':id', linkId);
                    this.qrCode = linkCode;
                    this.$dispatch('open-modal', 'qr-code-modal');
                    this.$nextTick(() => lucide.createIcons());
                },

                copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(() => {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Link copied!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    });
                },

                confirmDelete(formId) {
                    Swal.fire({
                        title: 'Delete this form?',
                        text: 'This action cannot be undone. All associated applications might also be affected.',
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
            }));
        });
    </script>
@endpush
