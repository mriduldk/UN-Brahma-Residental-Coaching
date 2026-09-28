@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <div class="flex items-center space-x-3 mb-6">
        <a href="{{ route('student.documents') }}" class="text-slate-500 hover:text-slate-800">
            <i data-lucide="arrow-left" size="24"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Document Tools</h2>
            <p class="text-slate-500">Optimize your files for application submission.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Tool Input -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center">
                <i data-lucide="image" class="mr-2 text-blue-600" size="20"></i> Image Compressor
            </h3>
            
            <div class="space-y-6">
                <!-- File Select -->
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:border-blue-500 hover:bg-blue-50 transition-colors cursor-pointer relative">
                    <input type="file" id="compressInput" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="handleFileSelect(event)">
                    <i data-lucide="upload" class="mx-auto text-slate-400 mb-3" size="32"></i>
                    <p class="text-sm font-bold text-slate-700">Click to Select Image</p>
                    <p class="text-xs text-slate-500">JPG or PNG formats</p>
                </div>

                <!-- Settings -->
                <div id="compressSettings" class="hidden space-y-4">
                    <div class="bg-slate-50 p-3 rounded-lg flex justify-between items-center text-sm">
                        <span class="text-slate-600">Original Size:</span>
                        <span class="font-mono font-bold text-slate-800" id="originalSize">-- KB</span>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Compression Quality</label>
                        <input type="range" id="qualityRange" min="0.1" max="1" step="0.1" value="0.7" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between text-xs text-slate-500 mt-1">
                            <span>Max Compression</span>
                            <span>High Quality</span>
                        </div>
                    </div>
                    <button onclick="compressImage()" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm">
                        Compress Image
                    </button>
                </div>
            </div>
        </div>

        <!-- Tool Output -->
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm flex flex-col items-center justify-center min-h-[400px] text-center" id="compressOutputArea">
            <!-- Initial State -->
            <div id="outputPlaceholder" class="opacity-50">
                <i data-lucide="layers" size="48" class="mx-auto text-slate-300 mb-3"></i>
                <p class="text-slate-500 font-medium">Compressed preview will appear here</p>
            </div>

            <!-- Result State (Hidden) -->
            <div id="outputResult" class="hidden w-full">
                <img id="compressedPreview" class="max-h-64 mx-auto rounded-lg shadow-sm mb-4 border border-slate-200">
                <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm font-bold mb-4 flex justify-between items-center">
                    <span>New Size:</span>
                    <span class="font-mono text-lg" id="newSize">-- KB</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <a id="downloadBtn" download="compressed-image.jpg" class="py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-bold flex items-center justify-center">
                        <i data-lucide="download" size="16" class="mr-2"></i> Download
                    </a>
                    <button onclick="alert('Saved to My Documents!')" class="py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg font-bold">
                        Save to Docs
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- Image Compressor Logic ---
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const originalSizeKB = (file.size / 1024).toFixed(2);
                document.getElementById('originalSize').innerText = originalSizeKB + " KB";
                document.getElementById('compressSettings').classList.remove('hidden');
                
                // Store image source for processing
                window.originalImageSrc = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }

    function compressImage() {
        const quality = parseFloat(document.getElementById('qualityRange').value);
        const img = new Image();
        img.src = window.originalImageSrc;
        
        img.onload = function() {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            
            // Compress
            const newDataUrl = canvas.toDataURL('image/jpeg', quality);
            
            // Show result
            const preview = document.getElementById('compressedPreview');
            preview.src = newDataUrl;
            document.getElementById('downloadBtn').href = newDataUrl;
            
            // Calculate new size
            const head = 'data:image/jpeg;base64,';
            const size = Math.round((newDataUrl.length - head.length) * 3 / 4) / 1024;
            document.getElementById('newSize').innerText = size.toFixed(2) + " KB";
            
            // Toggle UI
            document.getElementById('outputPlaceholder').classList.add('hidden');
            document.getElementById('outputResult').classList.remove('hidden');
        };
    }
</script>
@endpush
