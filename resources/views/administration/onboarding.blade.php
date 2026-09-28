@extends('layouts.administration')

@php 
    $hideSidebar = true; 
    
    $businessType = session('onboarding_business_type', 'Organization');
    
    // Default labels
    $pageTitle = 'Setup Organization';
    $nameLabel = 'Organization Name';
    $shortNameLabel = 'Short Name';
    $namePlaceholder = 'e.g. Acme Educational Trust';
    $shortNamePlaceholder = 'e.g. Acme Trust';
    $showRegistrationAndGst = true;
    
    // Type Options
    $typeOptions = [
        'Govt' => 'Government',
        'Private' => 'Private',
        'NGO' => 'NGO',
        'Company' => 'Company',
        'School' => 'School/College'
    ];

    if ($businessType === 'School') {
        $pageTitle = 'Setup School Profile';
        $nameLabel = 'School Name';
        $shortNameLabel = 'School Short Name';
        $namePlaceholder = 'e.g. Acme Public School';
        $shortNamePlaceholder = 'e.g. Acme School';
        $typeOptions = [
            'Government' => 'Government',
            'Private (Unaided)' => 'Private (Unaided)',
            'Private (Aided)' => 'Private (Aided)',
            'Trust / Society' => 'Trust / Society',
            'NGO' => 'NGO'
        ];
    } elseif ($businessType === 'Coaching') {
        $pageTitle = 'Setup Coaching Institute';
        $nameLabel = 'Institute Name';
        $shortNameLabel = 'Institute Short Name';
        $namePlaceholder = 'e.g. Apex Coaching Classes';
        $shortNamePlaceholder = 'e.g. Apex Coaching';
        $typeOptions = [
            'Sole Proprietorship' => 'Sole Proprietorship',
            'Partnership' => 'Partnership',
            'Private Limited Company' => 'Private Limited Company',
            'Trust / Society' => 'Trust / Society',
            'NGO' => 'NGO'
        ];
    } elseif ($businessType === 'Tuition') {
        $pageTitle = 'Setup Tuition Center';
        $nameLabel = 'Tuition Center Name';
        $shortNameLabel = 'Center Short Name';
        $namePlaceholder = 'e.g. Bright Minds Tuition';
        $shortNamePlaceholder = 'e.g. Bright Minds';
        $showRegistrationAndGst = false;
        $typeOptions = [
            'Sole Proprietorship' => 'Sole Proprietorship',
            'Partnership' => 'Partnership',
            'Private Limited Company' => 'Private Limited Company',
            'Trust / Society' => 'Trust / Society',
            'NGO' => 'NGO',
            'Individual Freelancer' => 'Individual Freelancer'
        ];
    }
@endphp

@section('title', $pageTitle)

@section('content')
<div class="fixed inset-0 z-50 bg-white flex overflow-hidden">
    <!-- Left Panel: Branding & Info (Hidden on mobile, visible on lg) -->
    <div class="hidden lg:flex lg:w-1/2 bg-slate-900 relative flex-col justify-between p-12 overflow-hidden">
        <!-- Abstract Background Shapes -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-20">
            <div class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-blue-600 rounded-full blur-[100px]"></div>
            <div class="absolute bottom-[-10%] left-[-10%] w-[500px] h-[500px] bg-indigo-600 rounded-full blur-[100px]"></div>
        </div>

        <div class="relative z-10">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white shadow-xl shadow-blue-900/50 mb-8">
                <i data-lucide="shield-check" size="28"></i>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight mb-4">Welcome to <br>Director of Education, BTC Admin Portal</h1>
            <p class="text-slate-400 text-lg leading-relaxed max-w-md">Complete your organization profile to unlock the full power of the administration dashboard. Manage applications, forms, and finance seamlessly.</p>
        </div>

        <div class="relative z-10 text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} Director of Education, BTC Management Solutions. All rights reserved.</p>
        </div>
    </div>

    <!-- Right Panel: Form (Full width on mobile, 1/2 on lg) -->
    <div class="w-full lg:w-1/2 flex flex-col overflow-y-auto bg-white">
        <!-- Mobile Header (Visible only on small screens) -->
        <div class="lg:hidden p-6 border-b border-slate-100 flex items-center gap-3">
             <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white shadow-lg">
                <i data-lucide="shield-check" size="20"></i>
            </div>
            <span class="font-bold text-slate-900">Admin Portal</span>
        </div>

        <div class="flex-1 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-24">
            <div class="w-full max-w-xl mx-auto">
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">{{ $pageTitle }}</h2>
                    <p class="mt-2 text-slate-500">Enter your {{ strtolower(str_replace('Setup ', '', $pageTitle)) }}'s legal details below.</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm">
                        <div class="flex items-center mb-2">
                            <i data-lucide="alert-circle" class="text-red-500 mr-2" size="20"></i>
                            <h3 class="text-sm font-bold text-red-800">Please correct the following errors:</h3>
                        </div>
                        <ul class="list-disc list-inside text-xs text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form class="space-y-6" action="{{ route('administration.onboarding.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label for="organization_name" class="block text-sm font-semibold text-slate-700 mb-2">{{ $nameLabel }} <span class="text-red-500">*</span></label>
                            <input id="organization_name" name="organization_name" type="text" required placeholder="{{ $namePlaceholder }}" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-800 placeholder-slate-400">
                            @error('organization_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-semibold text-slate-700 mb-2">Type <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select id="type" name="type" required 
                                    class="w-full pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-800 appearance-none cursor-pointer">
                                    <option value="">Select Type</option>
                                    @foreach($typeOptions as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500">
                                    <i data-lucide="chevron-down" size="16"></i>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="industry" value="Education">

                        @if($showRegistrationAndGst)
                        <div>
                            <label for="registration_no" class="block text-sm font-semibold text-slate-700 mb-2">Registration No (optional)</label>
                            <input id="registration_no" name="registration_no" type="text" placeholder="Reg. No." 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-800 placeholder-slate-400">
                        </div>

                        <div>
                            <label for="gst_no" class="block text-sm font-semibold text-slate-700 mb-2">GST No (optional)</label>
                            <input id="gst_no" name="gst_no" type="text" placeholder="GSTIN" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-800 placeholder-slate-400">
                        </div>
                        @endif
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full flex items-center justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-blue-500/30 text-base font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all hover:-translate-y-0.5">
                            Create Organization <i data-lucide="arrow-right" size="18" class="ml-2"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-400 hover:text-slate-600 transition-colors">Sign out currently logged in user</button>
                    </form>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Re-initialize icons since this view might be loaded dynamically or needs refresh
    lucide.createIcons();
</script>
@endsection
