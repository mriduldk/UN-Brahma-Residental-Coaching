@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-2 text-sm font-medium text-slate-500 mb-2">
        <a href="{{ route('admin.organizations.index') }}" class="hover:text-purple-600 transition-colors">Organizations</a>
        <i data-lucide="chevron-right" size="14"></i>
        <span class="text-slate-800">Edit Organization</span>
    </div>
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black tracking-tight text-slate-800">Edit: {{ $organization->organization_name }}</h1>
        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-sm font-bold border border-slate-200">
            {{ $organization->organization_code }}
        </span>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden w-full">
    <form action="{{ route('admin.organizations.update', $organization->id) }}" method="POST" class="p-6 md:p-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10">
            
            <!-- Basic Info Section -->
            <div class="md:col-span-2 pb-4 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
                    <i data-lucide="building" size="18" class="text-purple-500"></i> Basic Information
                </h2>
                <p class="text-sm text-slate-500">Core details about the organization entity.</p>
            </div>

            <div class="md:col-span-2">
                <label for="organization_name" class="block text-sm font-bold text-slate-700 mb-2">Organization Name <span class="text-red-500">*</span></label>
                <input type="text" name="organization_name" id="organization_name" value="{{ old('organization_name', $organization->organization_name) }}" required 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('organization_name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="short_name" class="block text-sm font-bold text-slate-700 mb-2">Short Name / Abbreviation <span class="text-red-500">*</span></label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name', $organization->short_name) }}" required 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3 uppercase" placeholder="e.g. Director of Education, BTC">
                @error('short_name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-bold text-slate-700 mb-2">Organization Type <span class="text-red-500">*</span></label>
                <select name="type" id="type" required class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                    <option value="" disabled>Select an entity type...</option>
                    <option value="trust" {{ old('type', $organization->type) == 'trust' ? 'selected' : '' }}>Trust / Foundation</option>
                    <option value="company" {{ old('type', $organization->type) == 'company' ? 'selected' : '' }}>Company (Pvt Ltd / Public)</option>
                    <option value="society" {{ old('type', $organization->type) == 'society' ? 'selected' : '' }}>Society / NGO</option>
                    <option value="proprietorship" {{ old('type', $organization->type) == 'proprietorship' ? 'selected' : '' }}>Proprietorship</option>
                    <option value="partnership" {{ old('type', $organization->type) == 'partnership' ? 'selected' : '' }}>Partnership Firm</option>
                    <option value="government" {{ old('type', $organization->type) == 'government' ? 'selected' : '' }}>Government Body</option>
                    <option value="other" {{ old('type', $organization->type) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="organization_code" class="block text-sm font-bold text-slate-700 mb-2">Organization Code <span class="text-red-500">*</span></label>
                <input type="text" name="organization_code" id="organization_code" value="{{ old('organization_code', $organization->organization_code) }}" required 
                    class="w-full rounded-xl border border-slate-200 bg-slate-100 focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 shadow-sm transition-all text-slate-800 font-bold px-4 py-3">
                <p class="text-[10px] text-slate-400 mt-2">Unique identifier. Modifying this could affect related links.</p>
                @error('organization_code') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Ownership Section -->
            <div class="md:col-span-2 pt-6 pb-4 border-b border-t border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
                    <i data-lucide="user-check" size="18" class="text-blue-500"></i> Primary Administrator
                </h2>
                <p class="text-sm text-slate-500">Reassign the main user managing this organization.</p>
            </div>

            <div class="md:col-span-2">
                <label for="user_id" class="block text-sm font-bold text-slate-700 mb-2">Assign to User <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="user_id" id="user_id" required class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3 pl-11">
                        <option value="" disabled>Select an existing user profile...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $organization->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->role) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="user" size="18"></i>
                    </div>
                </div>
                @error('user_id') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Legal & Identifiers Section -->
            <div class="md:col-span-2 pt-6 pb-4 border-b border-t border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
                    <i data-lucide="briefcase" size="18" class="text-emerald-500"></i> Registration & Compliances
                </h2>
            </div>

            <div>
                <label for="registration_no" class="block text-sm font-bold text-slate-700 mb-2">Registration/CIN No.</label>
                <input type="text" name="registration_no" id="registration_no" value="{{ old('registration_no', $organization->registration_no) }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('registration_no') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="gst_no" class="block text-sm font-bold text-slate-700 mb-2">GSTIN</label>
                <input type="text" name="gst_no" id="gst_no" value="{{ old('gst_no', $organization->gst_no) }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('gst_no') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="pan_no" class="block text-sm font-bold text-slate-700 mb-2">PAN Number</label>
                <input type="text" name="pan_no" id="pan_no" value="{{ old('pan_no', $organization->pan_no) }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('pan_no') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="tan_no" class="block text-sm font-bold text-slate-700 mb-2">TAN Number</label>
                <input type="text" name="tan_no" id="tan_no" value="{{ old('tan_no', $organization->tan_no) }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('tan_no') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Other Details Section -->
            <div class="md:col-span-2 pt-6 pb-4 border-b border-t border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
                    <i data-lucide="globe" size="18" class="text-indigo-500"></i> Other Details
                </h2>
            </div>
            
            <div>
                <label for="industry" class="block text-sm font-bold text-slate-700 mb-2">Industry / Sector</label>
                <input type="text" name="industry" id="industry" value="{{ old('industry', $organization->industry) }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('industry') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-bold text-slate-700 mb-2">Category</label>
                <input type="text" name="category" id="category" value="{{ old('category', $organization->category) }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('category') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="website" class="block text-sm font-bold text-slate-700 mb-2">Website URL</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="link-2" size="18"></i>
                    </div>
                    <input type="url" name="website" id="website" value="{{ old('website', $organization->website) }}" 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3 pl-11">
                </div>
                @error('website') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.organizations.index') }}" class="px-5 py-3 text-slate-600 hover:bg-slate-100 font-bold rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-200 transition-all flex items-center gap-2">
                <i data-lucide="save" size="18"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
