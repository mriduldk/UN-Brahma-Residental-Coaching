@extends('layouts.administration')

@php $hideSidebar = true; @endphp

@section('title', 'Select Management Type')

@push('scripts')
<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake {
        animation: shake 0.4s ease-in-out 0s 2;
    }
    @keyframes pulse-soft {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    .animate-pulse-soft {
        animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endpush

@section('content')
<div class="fixed inset-0 z-50 bg-white flex overflow-hidden">
    <!-- Left Panel: Branding & Info -->
    <div class="hidden lg:flex lg:w-1/2 bg-slate-900 relative flex-col justify-between p-12 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-20">
            <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-blue-600 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-600 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative z-10">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-xl shadow-blue-900/50 mb-8">
                <i data-lucide="layout-grid" size="28"></i>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight mb-4">Choose Your <br>Management Solution</h1>
            <p class="text-slate-400 text-lg leading-relaxed max-w-md">Select the type of institution you want to manage. We'll tailor your experience based on your choice.</p>
        </div>

        <div class="relative z-10 text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} Director of Education, BTC Management Solutions. All rights reserved.</p>
        </div>
    </div>

    <!-- Right Panel: Selection -->
    <div class="w-full lg:w-1/2 flex flex-col overflow-y-auto bg-white">
        <div class="lg:hidden p-6 border-b border-slate-100 flex items-center gap-3">
             <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                <i data-lucide="shield-check" size="20"></i>
            </div>
            <span class="font-bold text-slate-900">Director of Education, BTC Admin</span>
        </div>

        <div class="flex-1 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-24">
            <div class="w-full max-w-xl mx-auto">
                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">How will you use Director of Education, BTC?</h2>
                    <p class="mt-2 text-slate-500 font-medium">Select one option to continue with your setup.</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm animate-shake">
                        <div class="flex items-center">
                            <i data-lucide="alert-circle" class="text-red-500 mr-2" size="20"></i>
                            <p class="text-sm font-bold text-red-800">Please select a management type to continue.</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('administration.onboarding.business-type.select') }}" method="POST" id="businessTypeForm">
                    @csrf
                    <div class="grid grid-cols-1 gap-4">
                        <x-ui.choice-card 
                            name="business_type" 
                            value="School" 
                            title="School Management" 
                            description="Complete solution for schools and colleges." 
                            icon="school" 
                        />

                        <x-ui.choice-card 
                            name="business_type" 
                            value="Coaching" 
                            title="Coaching Management" 
                            description="Optimized for training centers and institutes." 
                            icon="book-open" 
                        />

                        <x-ui.choice-card 
                            name="business_type" 
                            value="Tuition" 
                            title="Tuition Management" 
                            description="Simple tools for private tutors and small groups." 
                            icon="graduation-cap" 
                        />
                    </div>

                    <div class="mt-10">
                        <button type="submit" id="continueBtn" disabled class="w-full flex items-center justify-center py-4 px-6 border border-transparent rounded-2xl shadow-xl shadow-blue-500/30 text-lg font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 disabled:bg-slate-400 disabled:shadow-none">
                            <span id="btnText">Continue Setup</span>
                            <i data-lucide="arrow-right" class="ml-2" size="24"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-ui.button type="submit" variant="ghost" size="sm" icon="log-out">
                            Sign out
                        </x-ui.button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    
    const continueBtn = document.getElementById('continueBtn');
    const radios = document.querySelectorAll('input[name="business_type"]');
    
    // Check if any radio is already selected (e.g. old input)
    const checkSelection = () => {
        const isSelected = Array.from(radios).some(radio => radio.checked);
        if (isSelected) {
            continueBtn.disabled = false;
        }
    };
    
    // Add click event to labels to ensure radio checked
    document.querySelectorAll('label').forEach(label => {
        label.addEventListener('click', () => {
            const radio = label.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                continueBtn.disabled = false;
            }
        });
    });
    
    radios.forEach(radio => {
        radio.addEventListener('change', checkSelection);
    });
    
    // Initial check
    checkSelection();

    // Handle Form Submission Loading State
    document.getElementById('businessTypeForm').addEventListener('submit', function(e) {
        const text = document.getElementById('btnText');
        
        continueBtn.disabled = true;
        continueBtn.classList.add('opacity-80', 'cursor-not-allowed', 'animate-pulse-soft');
        
        // Find and toggle icons
        const icon = continueBtn.querySelector('[data-lucide="arrow-right"]');
        
        // Create loader if not exists
        let loader = continueBtn.querySelector('[data-lucide="loader-2"]');
        if (!loader) {
            loader = document.createElement('i');
            loader.setAttribute('data-lucide', 'loader-2');
            loader.classList.add('animate-spin', 'mr-2');
            continueBtn.prepend(loader);
            lucide.createIcons({
                attrs: {
                    size: 24
                },
                nameAttr: 'data-lucide'
            });
        }
        
        if (icon) icon.classList.add('hidden');
        text.innerText = 'Loading...';
    });
</script>
@endsection
