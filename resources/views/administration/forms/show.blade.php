@extends('layouts.administration')

@section('title', 'Form Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <a href="{{ route('administration.forms') }}" class="mr-4 text-slate-500 hover:text-slate-800 bg-white p-2 rounded-lg hover:shadow-sm transition-all">
                <i data-lucide="arrow-left" size="24"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">{{ $form->application_name }}</h2>
                <div class="flex items-center space-x-2 text-sm text-slate-500 mt-1">
                    <span class="font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-600">{{ $form->application_code }}</span>
                    <span>•</span>
                    <span>{{ $form->session }}</span>
                </div>
            </div>
        </div>
        <div class="flex space-x-3">
             <a href="{{ route('administration.forms.edit', $form->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 flex items-center font-medium transition-all hover:-translate-y-0.5">
                <i data-lucide="edit-3" size="18" class="mr-2"></i> Edit Form
            </a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-50 p-2 rounded-lg text-blue-600">
                <i data-lucide="info" size="20"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Current Status</p>
                <p class="font-bold text-slate-800 capitalize">{{ $form->status }}</p>
            </div>
        </div>
        <span class="px-4 py-1.5 rounded-full text-sm font-bold uppercase {{ $form->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
            {{ $form->status }}
        </span>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left Column: Institute & Description -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-full">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-3">
                    <i data-lucide="building-2" class="mr-2 text-slate-400" size="20"></i> Form Details
                </h3>
                
                <div class="mb-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Institute Name</p>
                    <p class="text-xl font-bold text-slate-800">{{ $form->institution->institution_name ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Description</p>
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-slate-600 leading-relaxed text-sm">
                        {{ $form->application_description ?? 'No description provided.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Fees & Requirements -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-3">
                    <i data-lucide="wallet" class="mr-2 text-slate-400" size="20"></i> Fees & Requirements
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <!-- Application Fee -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Application Fee</p>
                        <p class="text-2xl font-bold text-slate-800">₹ {{ number_format($form->application_fee, 2) }}</p>
                        <span class="inline-block mt-2 text-xs px-2 py-1 rounded {{ $form->is_application_fee_required ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $form->is_application_fee_required ? 'Required' : 'Optional' }}
                        </span>
                    </div>

                    <!-- Admission Fee -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Admission Fee</p>
                        <p class="text-2xl font-bold text-slate-800">₹ {{ number_format($form->admission_fee, 2) }}</p>
                        <span class="inline-block mt-2 text-xs px-2 py-1 rounded {{ $form->is_admission_required ? 'bg-indigo-100 text-indigo-700' : 'bg-green-100 text-green-700' }}">
                            {{ $form->is_admission_required ? 'Required' : 'Optional' }}
                        </span>
                    </div>
                </div>

                <div class="pt-2">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Requirements Summary</p>
                    <div class="flex gap-6">
                        <div class="flex items-center">
                            <i data-lucide="{{ $form->is_application_fee_required ? 'check-circle' : 'minus-circle' }}" class="{{ $form->is_application_fee_required ? 'text-green-500' : 'text-slate-300' }} mr-2" size="20"></i>
                            <span class="text-sm font-medium {{ $form->is_application_fee_required ? 'text-slate-700' : 'text-slate-400' }}">App Fee</span>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="{{ $form->is_admission_required ? 'check-circle' : 'minus-circle' }}" class="{{ $form->is_admission_required ? 'text-green-500' : 'text-slate-300' }} mr-2" size="20"></i>
                            <span class="text-sm font-medium {{ $form->is_admission_required ? 'text-slate-700' : 'text-slate-400' }}">Admission</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Deadline</p>
                        <p class="text-lg font-bold text-slate-800">{{ $form->deadline ? $form->deadline->format('d M Y') : 'No Deadline' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Applications</p>
                        <p class="text-2xl font-bold text-blue-600">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
            <h3 class="font-bold text-slate-800">Field Configuration (Visible to Students)</h3>
        </div>
        <div class="p-6">
            @if(empty($form->field_config))
                <p class="text-slate-500 italic">No custom configuration found (Default settings apply).</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($form->field_config as $section => $config)
                        @if(isset($config['enabled']) && $config['enabled'])
                        <div class="border border-slate-200 rounded-xl p-4">
                            <h4 class="font-bold text-slate-700 capitalize mb-3 border-b border-slate-100 pb-2">{{ str_replace('_', ' ', $section) }}</h4>
                            <ul class="space-y-2">
                                @if(isset($config['fields']) && is_array($config['fields']))
                                    @foreach($config['fields'] as $field)
                                    <li class="text-sm text-slate-600 flex items-center">
                                        <i data-lucide="check" size="14" class="text-green-500 mr-2"></i>
                                        {{ ucwords(str_replace('_', ' ', $field)) }}
                                    </li>
                                    @endforeach
                                @else
                                    <li class="text-sm text-slate-400 italic">No specific fields selected</li>
                                @endif
                            </ul>
                        </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
