@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex items-center gap-2 text-sm font-medium text-slate-500 mb-2">
        <a href="{{ route('admin.organizations.index') }}" class="hover:text-purple-600 transition-colors">Organizations</a>
        <i data-lucide="chevron-right" size="14"></i>
        <span class="text-slate-800">Add New</span>
    </div>
    <h1 class="text-2xl font-black tracking-tight text-slate-800">Add Organization</h1>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden w-full">
    <form action="{{ route('admin.organizations.store') }}" method="POST" class="p-6 md:p-8">
        @csrf

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
                <input type="text" name="organization_name" id="organization_name" value="{{ old('organization_name') }}" required 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('organization_name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="short_name" class="block text-sm font-bold text-slate-700 mb-2">Short Name / Abbreviation <span class="text-red-500">*</span></label>
                <input type="text" name="short_name" id="short_name" value="{{ old('short_name') }}" required 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3 uppercase" placeholder="e.g. Director of Education, BTC">
                @error('short_name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-bold text-slate-700 mb-2">Organization Type <span class="text-red-500">*</span></label>
                <select name="type" id="type" required class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                    <option value="" disabled selected>Select an entity type...</option>
                    <option value="trust" {{ old('type') == 'trust' ? 'selected' : '' }}>Trust / Foundation</option>
                    <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>Company (Pvt Ltd / Public)</option>
                    <option value="society" {{ old('type') == 'society' ? 'selected' : '' }}>Society / NGO</option>
                    <option value="proprietorship" {{ old('type') == 'proprietorship' ? 'selected' : '' }}>Proprietorship</option>
                    <option value="partnership" {{ old('type') == 'partnership' ? 'selected' : '' }}>Partnership Firm</option>
                    <option value="government" {{ old('type') == 'government' ? 'selected' : '' }}>Government Body</option>
                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <!-- Ownership Section -->
            <div class="md:col-span-2 pt-6 pb-4 border-b border-t border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
                    <i data-lucide="user-plus" size="18" class="text-blue-500"></i> Primary Administrator
                </h2>
                <p class="text-sm text-slate-500">Assign a registered user to manage this organization. They will act as the root user for this organization.</p>
            </div>

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="admin_name" class="block text-sm font-bold text-slate-700 mb-2">Admin Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="admin_name" id="admin_name" value="{{ old('admin_name') }}" required 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                    @error('admin_name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="admin_email" class="block text-sm font-bold text-slate-700 mb-2">Admin Email Address (Optional)</label>
                    <input type="email" name="admin_email" id="admin_email" value="{{ old('admin_email') }}" 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                    @error('admin_email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="admin_phone" class="block text-sm font-bold text-slate-700 mb-2">Admin Phone Number</label>
                    <input type="text" name="admin_phone" id="admin_phone" value="{{ old('admin_phone') }}" 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                    @error('admin_phone') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="admin_password" class="block text-sm font-bold text-slate-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="admin_password" id="admin_password" required 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                    <p class="text-xs text-slate-500 mt-1">Minimum 8 characters</p>
                    @error('admin_password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Legal & Identifiers Section -->
            <div class="md:col-span-2 pt-6 pb-4 border-b border-t border-slate-100">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
                    <i data-lucide="briefcase" size="18" class="text-emerald-500"></i> Registration & Compliances
                </h2>
            </div>

            <div>
                <label for="registration_no" class="block text-sm font-bold text-slate-700 mb-2">Registration/CIN No.</label>
                <input type="text" name="registration_no" id="registration_no" value="{{ old('registration_no') }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('registration_no') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="gst_no" class="block text-sm font-bold text-slate-700 mb-2">GSTIN</label>
                <input type="text" name="gst_no" id="gst_no" value="{{ old('gst_no') }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('gst_no') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="pan_no" class="block text-sm font-bold text-slate-700 mb-2">PAN Number</label>
                <input type="text" name="pan_no" id="pan_no" value="{{ old('pan_no') }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('pan_no') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="tan_no" class="block text-sm font-bold text-slate-700 mb-2">TAN Number</label>
                <input type="text" name="tan_no" id="tan_no" value="{{ old('tan_no') }}" 
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
                <input type="text" name="industry" id="industry" value="{{ old('industry') }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3" placeholder="e.g. Education, IT, Healthcare">
                @error('industry') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="category" class="block text-sm font-bold text-slate-700 mb-2">Category</label>
                <input type="text" name="category" id="category" value="{{ old('category') }}" 
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3">
                @error('category') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="website" class="block text-sm font-bold text-slate-700 mb-2">Website URL</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="link-2" size="18"></i>
                    </div>
                    <input type="url" name="website" id="website" value="{{ old('website') }}" 
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 hover:bg-white focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 shadow-sm transition-all text-slate-800 font-medium px-4 py-3 pl-11" placeholder="https://www.example.com">
                </div>
                @error('website') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.organizations.index') }}" class="px-5 py-3 text-slate-600 hover:bg-slate-100 font-bold rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-md shadow-purple-200 transition-all flex items-center gap-2">
                <i data-lucide="check" size="18"></i> Create Organization
            </button>
        </div>
    </form>
</div>
@endsection
