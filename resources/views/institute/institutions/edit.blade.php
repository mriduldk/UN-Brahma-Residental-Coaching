@extends('layouts.institute')

@section('title', 'Edit Institution')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Institution</h1>
            <p class="text-slate-500 mt-1">Update details for {{ $institution->institution_name }}.</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- 
            <a href="#" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 transition border border-indigo-200">
                <i data-lucide="users" size="16" class="mr-2"></i> Manage Users
            </a>
            -->
            <a href="{{ route('institute.institutions.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-700 transition">
                <i data-lucide="arrow-left" size="16" class="mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('institute.institutions.update', $institution->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- General Information -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                <i data-lucide="building" class="text-blue-600 mr-2" size="20"></i> General Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="col-span-2 md:col-span-1">
                    <label for="institution_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Institution Name <span class="text-red-500">*</span></label>
                    <input type="text" name="institution_name" id="institution_name" value="{{ old('institution_name', $institution->institution_name) }}" required 
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">
                    @error('institution_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Code -->
                <div class="col-span-2 md:col-span-1">
                    <label for="institution_code" class="block text-sm font-semibold text-slate-700 mb-1.5">Institution Code <span class="text-red-500">*</span></label>
                    <input type="text" name="institution_code" id="institution_code" value="{{ old('institution_code', $institution->institution_code) }}" required 
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">
                    @error('institution_code') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $institution->email) }}" 
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">
                    @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="contact_number" class="block text-sm font-semibold text-slate-700 mb-1.5">Contact Number</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number', $institution->contact_number) }}" 
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">
                    @error('contact_number') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Address -->
                <div class="col-span-2">
                    <label for="address" class="block text-sm font-semibold text-slate-700 mb-1.5">Address</label>
                    <textarea name="address" id="address" rows="3" 
                              class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">{{ old('address', $institution->address) }}</textarea>
                    @error('address') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4 flex items-center">
                <i data-lucide="landmark" class="text-blue-600 mr-2" size="20"></i> Bank Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Account No -->
                <div>
                    <label for="bank_account_no" class="block text-sm font-semibold text-slate-700 mb-1.5">Account Number</label>
                    <input type="text" name="bank_account_no" id="bank_account_no" value="{{ old('bank_account_no', $institution->bank_account_no) }}" 
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">
                    @error('bank_account_no') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- IFSC -->
                <div>
                    <label for="bank_ifsc_code" class="block text-sm font-semibold text-slate-700 mb-1.5">IFSC Code</label>
                    <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" value="{{ old('bank_ifsc_code', $institution->bank_ifsc_code) }}" 
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">
                    @error('bank_ifsc_code') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Bank Name</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $institution->bank_name) }}" 
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">
                    @error('bank_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                
                 <!-- Branch Name -->
                 <div>
                    <label for="bank_branch_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Branch Name</label>
                    <input type="text" name="bank_branch_name" id="bank_branch_name" value="{{ old('bank_branch_name', $institution->bank_branch_name) }}" 
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">
                    @error('bank_branch_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Holder Name -->
                <div class="col-span-2">
                    <label for="bank_account_holder_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Account Holder Name</label>
                    <input type="text" name="bank_account_holder_name" id="bank_account_holder_name" value="{{ old('bank_account_holder_name', $institution->bank_account_holder_name) }}" 
                           class="w-full rounded-lg border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-900 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-500/10 transition-all duration-200 ease-in-out placeholder:text-slate-400">
                    @error('bank_account_holder_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>



        <!-- Actions -->
        <div class="flex items-center justify-end gap-4">
             <button type="button" onclick="history.back()" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition">Cancel</button>
             <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">Update Institution</button>
        </div>
    </form>
</div>
@endsection
