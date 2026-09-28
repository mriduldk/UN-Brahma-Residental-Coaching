@extends('layouts.student')

@section('content')
<div class="w-full space-y-8">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <i data-lucide="files" size="120"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <h2 class="text-3xl font-extrabold flex items-center">
                    <i data-lucide="folder-open" class="mr-3" size="32"></i> Document Manager
                </h2>
                <p class="text-blue-100 text-lg mt-2 max-w-xl">
                    Securely upload and manage your essential documents. Keep track of your storage usage below.
                </p>
            </div>
            
             <!-- Storage Card -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-xl w-full md:w-80 shadow-lg">
                <div class="flex justify-between text-xs font-bold text-blue-100 mb-2 uppercase tracking-wide">
                    <span>Storage Usage</span>
                    <span>{{ number_format($totalSize / 1024 / 1024, 2) }} MB / {{ number_format($limit / 1024 / 1024, 0) }} MB</span>
                </div>
                <div class="w-full bg-black/20 rounded-full h-3 mb-2">
                    <div class="bg-gradient-to-r from-green-400 to-emerald-500 h-3 rounded-full transition-all duration-700 ease-out shadow-[0_0_10px_rgba(74,222,128,0.5)]" style="width: {{ $usagePercentage }}%"></div>
                </div>
                 <div class="flex justify-between items-center text-xs">
                    <span class="text-blue-200">{{ number_format($usagePercentage, 1) }}% Used</span>
                    @if($usagePercentage > 90)
                        <span class="text-red-300 font-bold flex items-center"><i data-lucide="alert-circle" size="12" class="mr-1"></i> Running Low</span>
                    @else
                        <span class="text-green-300 font-bold flex items-center"><i data-lucide="check-circle" size="12" class="mr-1"></i> Space Available</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-50 border border-green-200 flex items-center text-green-800 shadow-sm animate-fade-in-down">
            <i data-lucide="check-circle-2" class="mr-2 text-green-600"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 flex items-center text-red-800 shadow-sm animate-fade-in-down">
            <i data-lucide="alert-octagon" class="mr-2 text-red-600"></i>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 shadow-sm">
             <div class="flex items-center mb-2 text-red-800 font-bold">
                 <i data-lucide="x-circle" class="mr-2"></i> Please correct the following errors:
             </div>
            <ul class="list-disc list-inside text-sm text-red-700 ml-6 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Upload Lists -->
        @php
            $docGroups = [
                'Identity Proofs' => [
                    'icon' => 'user-check',
                    'color' => 'indigo',
                    'docs' => [
                        'photo' => 'Passport Photo',
                        'signature' => 'Signature',
                        'aadhaar_card' => 'Aadhaar Card',
                        'passport' => 'Passport',
                        'caste_certificate' => 'Caste Certificate',
                        'income_certificate' => 'Income Certificate',
                        'address_proof' => 'Address Proof',
                        'transfer_certificate' => 'Transfer Certificate',
                    ]
                ],
                'Marksheets' => [
                    'icon' => 'graduation-cap',
                    'color' => 'purple',
                    'docs' => [
                        'hslc_marksheet' => 'HSLC Marksheet',
                        'hs_marksheet' => 'HS Marksheet',
                        'graduation_marksheet' => 'Graduation Marksheet',
                        'post_graduation_marksheet' => 'PG Marksheet',
                    ]
                ],
                'Certificates & Admit Cards' => [
                    'icon' => 'award',
                    'color' => 'orange',
                    'docs' => [
                        'hslc_pass_certificate' => 'HSLC Pass Certificate',
                        'hs_pass_certificate' => 'HS Pass Certificate',
                        'graduation_pass_certificate' => 'Graduation Certificate',
                        'post_graduation_pass_certificate' => 'PG Certificate',
                        'hslc_admit_card' => 'HSLC Admit Card',
                        'hs_admit_card' => 'HS Admit Card',
                        'graduation_admit_card' => 'Graduation Admit Card',
                        'post_graduation_admit_card' => 'PG Admit Card',
                    ]
                ]
            ];
        @endphp

        @foreach($docGroups as $groupName => $groupData)
        @php $color = $groupData['color']; @endphp
        <div class="bg-white shadow-lg border border-slate-100 rounded-2xl overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col h-full">
            <div class="px-6 py-4 bg-{{ $color }}-50 border-b border-{{ $color }}-100 flex items-center gap-3">
                 <div class="bg-white p-2 rounded-lg shadow-sm text-{{ $color }}-600">
                    <i data-lucide="{{ $groupData['icon'] }}" size="20"></i>
                 </div>
                <h3 class="text-lg font-bold text-slate-800">{{ $groupName }}</h3>
            </div>
            <div class="p-6 space-y-4 flex-grow">
                @foreach($groupData['docs'] as $type => $label)
                    <div class="group relative rounded-xl border-2 transition-all duration-200 {{ isset($documents[$type]) ? 'border-green-100 bg-green-50/50 hover:bg-green-50' : 'border-dashed border-slate-200 hover:border-blue-400 hover:bg-blue-50/30' }}">
                        <div class="p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-bold text-slate-700 flex items-center">
                                    {{ $label }}
                                    @if(isset($documents[$type]))
                                        <i data-lucide="check-circle" class="ml-1.5 text-green-500" size="14"></i>
                                    @endif
                                </span>
                                @if(isset($documents[$type]))
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                        UPLOADED
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        PENDING
                                    </span>
                                @endif
                            </div>

                            @if(isset($documents[$type]))
                                <div class="flex items-center justify-between mt-3 pl-1">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">File Size</span>
                                        <span class="text-xs font-semibold text-slate-600">{{ number_format($documents[$type]['size'] / 1024, 2) }} KB</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ Storage::url($documents[$type]['path']) }}" target="_blank" 
                                           class="bg-white hover:bg-blue-50 text-blue-600 border border-blue-200 p-1.5 rounded-lg transition-colors shadow-sm"
                                           title="View Document">
                                            <i data-lucide="eye" size="16"></i>
                                        </a>
                                        <a href="{{ Storage::url($documents[$type]['path']) }}" download="{{ Str::slug($label) }}.{{ pathinfo($documents[$type]['path'], PATHINFO_EXTENSION) }}"
                                           class="bg-white hover:bg-teal-50 text-teal-600 border border-teal-200 p-1.5 rounded-lg transition-colors shadow-sm"
                                           title="Download Document">
                                            <i data-lucide="download" size="16"></i>
                                        </a>
                                        <button type="button" 
                                                onclick="confirmDelete('{{ $type }}', '{{ $label }}')"
                                                class="bg-white hover:bg-red-50 text-red-500 border border-red-200 p-1.5 rounded-lg transition-colors shadow-sm"
                                                title="Delete Document">
                                            <i data-lucide="trash-2" size="16"></i>
                                        </button>
                                        <form id="delete-form-{{ $type }}" action="{{ route('student.documents.destroy', $type) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            @else
                                <form action="{{ route('student.documents.store') }}" method="POST" enctype="multipart/form-data" class="mt-3" id="uploadForm-{{ $type }}">
                                    @csrf
                                    <input type="hidden" name="type" value="{{ $type }}">
                                    <label class="block w-full cursor-pointer">
                                        <div class="flex items-center justify-between bg-white border border-slate-200 rounded-lg p-2 shadow-sm hover:shadow hover:border-blue-300 transition-all group-hover:border-blue-300">
                                            <div class="flex items-center text-slate-500 group-hover:text-blue-600 transition-colors">
                                                <i data-lucide="upload-cloud" size="16" class="mr-2"></i>
                                                <span class="text-xs font-semibold">Choose File</span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-medium bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">Max 200KB</span>
                                        </div>
                                        <input type="file" name="file" class="hidden" data-doc-type="{{ $type }}">
                                    </label>
                                </form>
                                <!-- Loading State (Hidden by default) -->
                                <div id="loading-{{ $type }}" class="hidden mt-3 flex items-center justify-center text-blue-600 text-xs font-bold animate-pulse">
                                    <i data-lucide="loader-2" class="mr-2 animate-spin" size="14"></i> Uploading...
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Other Documents Section -->
        <div class="col-span-1 lg:col-span-3">
             <div class="bg-white shadow-lg border border-slate-100 rounded-2xl overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col h-full">
                <div class="px-6 py-4 bg-teal-50 border-b border-teal-100 flex items-center gap-3">
                     <div class="bg-white p-2 rounded-lg shadow-sm text-teal-600">
                        <i data-lucide="folder-plus" size="20"></i>
                     </div>
                    <h3 class="text-lg font-bold text-slate-800">Other Documents</h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Upload Form -->
                    <form action="{{ route('student.documents.other.store') }}" method="POST" enctype="multipart/form-data" class="bg-slate-50/50 p-6 rounded-xl border border-dashed border-slate-300" id="otherDocForm" onsubmit="return handleOtherDocSubmit(event)">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                            <div class="md:col-span-5 space-y-2">
                                <label for="doc-title" class="block text-sm font-bold text-slate-700">
                                    Document Title <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <i data-lucide="type" size="18"></i>
                                    </div>
                                    <input type="text" name="title" id="doc-title" 
                                           class="pl-10 block w-full rounded-lg border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm font-semibold placeholder:text-slate-400 py-3 transition-all text-slate-800" 
                                           placeholder="e.g. Migration Certificate" required>
                                </div>
                            </div>

                            <div class="md:col-span-5 space-y-2">
                                <label class="block text-sm font-bold text-slate-700">
                                    Select File <span class="text-red-500">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="file" name="file" id="other-file-input" class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required onchange="updateFileName(this)">
                                    <div class="flex items-center justify-between bg-white border border-slate-300 rounded-lg p-2 shadow-sm peer-focus:border-teal-500 peer-focus:ring-1 peer-focus:ring-teal-500 transition-all group-hover:border-teal-400">
                                        <div class="flex items-center text-slate-500 group-hover:text-teal-600 transition-colors truncate">
                                            <div class="bg-slate-100 p-1.5 rounded-md mr-3 group-hover:bg-teal-50">
                                                <i data-lucide="upload-cloud" size="16"></i>
                                            </div>
                                            <span id="file-name-display" class="text-sm font-medium truncate">Choose a file...</span>
                                        </div>
                                        <span class="text-[10px] bg-slate-100 text-slate-500 px-2 rounded font-bold border border-slate-200 group-hover:bg-white group-hover:text-teal-600 group-hover:border-teal-200 transition-all">BROWSE</span>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400 ml-1">Max 200KB (jpg, jpeg, png, pdf)</span>
                            </div>

                            <div class="md:col-span-2 pt-1">
                                <label class="block text-sm font-bold text-transparent select-none mb-2">&nbsp;</label>
                                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-md shadow-teal-200 hover:shadow-lg transition-all flex items-center justify-center gap-2 transform active:scale-95">
                                    <i data-lucide="plus-circle" size="18"></i> Add
                                </button>
                            </div>
                        </div>
                        <div id="other-loading" class="hidden mt-4 text-center">
                            <span class="inline-flex items-center px-4 py-2 rounded-full bg-teal-50 text-teal-700 text-xs font-bold animate-pulse border border-teal-100">
                                <i data-lucide="loader-2" class="mr-2 animate-spin" size="14"></i> Uploading document...
                            </span>
                        </div>
                    </form>

                    <!-- List of Other Documents -->
                    @if($otherDocuments->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($otherDocuments as $doc)
                                <div class="group relative rounded-xl border-2 border-teal-100 bg-teal-50/30 p-4 hover:border-teal-200 transition-all">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h4 class="font-bold text-slate-700 text-sm">{{ $doc->title }}</h4>
                                            <p class="text-xs text-slate-500 mt-1">{{ number_format($doc->file_size / 1024, 2) }} KB</p>
                                            <p class="text-[10px] text-slate-400 uppercase">{{ $doc->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" 
                                               class="bg-white hover:bg-blue-50 text-blue-600 border border-blue-200 p-1.5 rounded-lg transition-colors shadow-sm"
                                               title="View Document">
                                                <i data-lucide="eye" size="16"></i>
                                            </a>
                                            <a href="{{ Storage::url($doc->file_path) }}" download="{{ Str::slug($doc->title) }}.{{ pathinfo($doc->file_path, PATHINFO_EXTENSION) }}"
                                               class="bg-white hover:bg-teal-50 text-teal-600 border border-teal-200 p-1.5 rounded-lg transition-colors shadow-sm"
                                               title="Download Document">
                                                <i data-lucide="download" size="16"></i>
                                            </a>
                                            <button type="button" 
                                                    onclick="confirmDeleteOther('{{ $doc->id }}', '{{ $doc->title }}')"
                                                    class="bg-white hover:bg-red-50 text-red-500 border border-red-200 p-1.5 rounded-lg transition-colors shadow-sm"
                                                    title="Delete Document">
                                                <i data-lucide="trash-2" size="16"></i>
                                            </button>
                                            <form id="delete-other-form-{{ $doc->id }}" action="{{ route('student.documents.other.destroy', $doc->id) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-slate-400 border-2 border-dashed border-slate-200 rounded-xl">
                            <i data-lucide="folder-open" size="32" class="mx-auto mb-2 opacity-50"></i>
                            <p class="text-sm">No other documents uploaded.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>



<!-- Compression Modal -->
<div id="compressionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true" onclick="closeCompressionModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="sm:flex sm:items-start">
                <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                    <i data-lucide="file-warning" class="text-red-600" size="24"></i>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                        File Too Large (<span id="file-size-display" class="font-bold text-red-600"></span>)
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            The selected file exceeds the 200KB limit. 
                        </p>
                        <p id="image-options" class="hidden mt-2 text-sm text-gray-500">
                            We can attempt to compress this image for you to meet the requirement.
                        </p>
                        <p id="pdf-options" class="hidden mt-2 text-sm text-red-500">
                            Automatic compression is not available for PDF files. Please compress it manually and try again.
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" id="compress-btn" class="hidden inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-teal-600 border border-transparent rounded-md shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 sm:ml-3 sm:w-auto sm:text-sm" onclick="startCompression()">
                    Compress & Upload
                </button>
                <button type="button" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm" onclick="closeCompressionModal()">
                    Cancel
                </button>
            </div>
             <div id="compression-loading" class="hidden mt-4 text-center">
                <div class="flex items-center justify-center text-teal-600 text-sm font-bold animate-pulse">
                    <i data-lucide="loader-2" class="mr-2 animate-spin" size="18"></i> Compressing Image...
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let activeFileInput = null;
    let activeFormId = null;
    let currentFile = null;

    // Hook into all file inputs
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function(e) {
                handleFileSelection(this);
            });
        });
    });

    function handleFileSelection(input) {
        if (input.files && input.files.length > 0) {
            const file = input.files[0];
            const sizeKB = file.size / 1024;
            
            if(input.id === 'other-file-input') {
               updateFileName(input);
               return; // Skip immediate check for other-file-input
            }

            if (sizeKB > 200) {
                // Determine Form ID based on input context
                if (input.id === 'other-file-input') {
                    activeFormId = 'otherDocForm';
                } else {
                    // Standard doc input is inside the form
                    activeFormId = input.closest('form').id;
                }

                activeFileInput = input;
                currentFile = file;
                
                showCompressionModal(sizeKB, file.type);
                
                // Reset input? No, keep it for now, replace it later if compressed
                // But we might want to clear it if user cancels to enforce limit
            } else {
                // Size is OK. 
                // If it IS a standard doc (not other-file-input), we need to trigger upload
                if (input.id !== 'other-file-input') {
                     const type = input.getAttribute('data-doc-type');
                     if(type) {
                         uploadFile(type);
                     }
                }
            }
        }
    }

    // Override the inline uploadFile to check size first
    // We will modify the standard inputs to NOT call uploadFile directly on change
    // Instead we delegate to handleFileSelection -> and then upload if valid

    function showCompressionModal(sizeKB, mimeType) {
        document.getElementById('compressionModal').classList.remove('hidden');
        document.getElementById('file-size-display').textContent = sizeKB.toFixed(2) + ' KB';
        
        const isImage = mimeType.startsWith('image/');
        const compressBtn = document.getElementById('compress-btn');
        const imgOptions = document.getElementById('image-options');
        const pdfOptions = document.getElementById('pdf-options');

        if (isImage) {
            compressBtn.classList.remove('hidden');
            imgOptions.classList.remove('hidden');
            pdfOptions.classList.add('hidden');
        } else {
            compressBtn.classList.add('hidden');
            imgOptions.classList.add('hidden');
            pdfOptions.classList.remove('hidden');
            // Clear input if PDF is too large
            if(activeFileInput) activeFileInput.value = ''; 
             if(activeFileInput && activeFileInput.id === 'other-file-input') updateFileName(activeFileInput);
        }
    }

    function closeCompressionModal() {
        document.getElementById('compressionModal').classList.add('hidden');
        // Clear input if cancelled to ensure no large file upload
        if(activeFileInput && activeFileInput.files[0] && (activeFileInput.files[0].size / 1024) > 200) {
            activeFileInput.value = '';
            if(activeFileInput.id === 'other-file-input') updateFileName(activeFileInput);
        }
        activeFileInput = null;
        currentFile = null;
    }

    async function startCompression() {
        if (!currentFile) return;

        document.getElementById('compression-loading').classList.remove('hidden');
        document.getElementById('compress-btn').classList.add('disabled', 'opacity-50');
        
        try {
            const compressedFile = await compressImage(currentFile);
            
            // Replace file in input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(compressedFile);
            activeFileInput.files = dataTransfer.files;

            // Trigger UI update
            if(activeFileInput.id === 'other-file-input') updateFileName(activeFileInput);

            document.getElementById('compressionModal').classList.add('hidden');
            document.getElementById('compression-loading').classList.add('hidden');
            document.getElementById('compress-btn').classList.remove('disabled', 'opacity-50');

            // Success Message
             Swal.fire({
                icon: 'success',
                title: 'Compressed!',
                text: 'File reduced to ' + (compressedFile.size / 1024).toFixed(2) + ' KB. Uploading now...',
                timer: 1500,
                showConfirmButton: false
            });

             // Proceed with upload
             if (activeFormId === 'otherDocForm') {
                 // For other docs, we just sit there until user clicks "Add"
                 // Or should we auto upload? User said "Compress and Upload"
                 // "Add" button submits 'otherDocForm'
                  document.getElementById('otherDocForm').submit();
                 showOtherLoading();
             } else {
                 // For standard docs, upload immediately
                 const type = activeFileInput.name === 'file' ? activeFileInput.closest('form').querySelector('input[name="type"]').value : null;
                 if(type) {
                     uploadFile(type);
                 }
             }

        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Compression failed: ' + error.message, 'error');
            closeCompressionModal();
        }
    }

    function compressImage(file) {
        return new Promise((resolve, reject) => {
            const maxWidth = 1200;
            const maxHeight = 1200;
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function (event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function () {
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > maxWidth) {
                            height *= maxWidth / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width *= maxHeight / height;
                            height = maxHeight;
                        }
                    }

                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    // Iterative compression to get below 200KB
                    let quality = 0.9;
                    
                    function attemptCompression() {
                        canvas.toBlob(function (blob) {
                            if (blob.size <= 200 * 1024 || quality <= 0.1) {
                                const newFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now(),
                                });
                                resolve(newFile);
                            } else {
                                quality -= 0.1;
                                attemptCompression();
                            }
                        }, 'image/jpeg', quality);
                    }
                    
                    attemptCompression();
                };
                img.onerror = function(err) {
                    reject(new Error("Image load failed"));
                }
            };
            reader.onerror = function(err) {
                 reject(new Error("File read failed"));
            }
        });
    }

    function uploadFile(type) {
        // Show loading state
        document.getElementById('uploadForm-' + type).classList.add('hidden');
        document.getElementById('loading-' + type).classList.remove('hidden');
        // Submit form
        document.getElementById('uploadForm-' + type).submit();
    }

    function updateFileName(input) {
        const display = document.getElementById('file-name-display');
        if (input.files && input.files.length > 0) {
            display.textContent = input.files[0].name;
            display.classList.add('text-slate-800');
            display.classList.remove('text-slate-500');
        } else {
            display.textContent = 'Choose a file...';
            display.classList.add('text-slate-500');
            display.classList.remove('text-slate-800');
        }
    }

    function showOtherLoading() {
        // Ideally we should validate form first, but for now simple trigger
        const form = document.getElementById('otherDocForm');
        if(form.checkValidity()) {
             document.getElementById('other-loading').classList.remove('hidden');
        }
    }

    function confirmDelete(type, label) {
        Swal.fire({
            title: 'Delete ' + label + '?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-2xl',
                title: 'font-bold text-slate-800',
                confirmButton: 'rounded-lg font-bold px-6 py-2.5',
                cancelButton: 'rounded-lg font-bold px-6 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + type).submit();
            }
        })
    }

    function confirmDeleteOther(id, title) {
        Swal.fire({
            title: 'Delete ' + title + '?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-2xl',
                title: 'font-bold text-slate-800',
                confirmButton: 'rounded-lg font-bold px-6 py-2.5',
                cancelButton: 'rounded-lg font-bold px-6 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-other-form-' + id).submit();
            }
        })
    }
    function handleOtherDocSubmit(event) {
        event.preventDefault();
        const form = document.getElementById('otherDocForm');
        const input = document.getElementById('other-file-input');

        // Check if file selected
        if (!input.files || input.files.length === 0) {
            // Form native required validation should catch this, but just in case
            form.reportValidity();
            return false;
        }

        const file = input.files[0];
        const sizeKB = file.size / 1024;

        if (sizeKB > 200) {
            // Setup global vars for modal
            activeFormId = 'otherDocForm';
            activeFileInput = input;
            currentFile = file;
            showCompressionModal(sizeKB, file.type);
            return false;
        }

        // Proceed
        showOtherLoading();
        form.submit();
    }
</script>
<style>
    .animate-fade-in-down {
        animation: fadeInDown 0.5s ease-out;
    }
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translate3d(0, -20px, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
</style>

@endsection
