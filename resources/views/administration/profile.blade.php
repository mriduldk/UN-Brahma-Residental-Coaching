@extends('layouts.administration')

@section('title', 'User Profile')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
                
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-600"></div>
                <div class="px-6 pb-6 text-center relative">
                    <div class="w-24 h-24 mx-auto bg-white rounded-full p-1 -mt-12 mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin User') }}&background=0ea5e9&color=fff&size=128" class="w-full h-full rounded-full object-cover">
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">{{ auth()->user()->name ?? 'Admin User' }}</h2>
                    <p class="text-sm text-slate-500 mb-4">Principal, Director of Education, BTC</p>
                    <div class="flex justify-center gap-2 mb-6">
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full">Administrator</span>
                        <span class="px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full">Active</span>
                    </div>
                    <div class="border-t border-slate-100 pt-4 text-left space-y-3">
                        <div class="flex items-center text-sm text-slate-600">
                            <i data-lucide="mail" size="16" class="mr-3 text-slate-400"></i> {{ auth()->user()->email ?? '' }}
                        </div>
                        <div class="flex items-center text-sm text-slate-600">
                            <i data-lucide="phone" size="16" class="mr-3 text-slate-400"></i> +91 {{ auth()->user()->mobile_number ?? '' }}
                        </div>
                    </div>
                    
                    @if(auth()->user()->isAdmin(auth()->user()->currentOrganization()->id))
                    <div class="mt-6">
                        <a href="{{ route('administration.users') }}" class="block w-full py-2 px-4 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold rounded-xl border border-slate-200 transition-colors flex items-center justify-center">
                            <i data-lucide="users" size="18" class="mr-2"></i> Manage Users
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Settings & Activity -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Organization Details -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <i data-lucide="building-2" size="120" class="text-blue-600"></i>
                </div>
                <div class="flex items-center justify-between mb-6 relative z-10">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center">
                        <i data-lucide="building" size="20" class="mr-2 text-blue-600"></i> Organization Details
                    </h3>
                </div>
                
                @if(isset($organization))
                <form action="{{ route('administration.organization.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 relative z-10">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2 flex items-center gap-6 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="w-20 h-20 bg-white rounded-2xl p-2 border border-slate-200 flex items-center justify-center relative group">
                                @if($organization->logo)
                                    <img src="{{ asset('storage/' . $organization->logo) }}" class="max-w-full max-h-full object-contain">
                                @else
                                    <i data-lucide="image" class="text-slate-300" size="32"></i>
                                @endif
                                <label class="absolute inset-0 bg-black/40 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 rounded-2xl cursor-pointer transition-opacity">
                                    <i data-lucide="camera" size="24"></i>
                                    <input type="file" name="logo" class="hidden" accept="image/*">
                                </label>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800">Organization Logo</h4>
                                <p class="text-xs text-slate-500 mt-1">PNG, JPG or GIF up to 2MB. Recommended size: 512x512px.</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Organization Name</label>
                            <input type="text" name="organization_name" value="{{ old('organization_name', $organization->organization_name) }}" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm @error('organization_name') border-red-500 @enderror">
                            @error('organization_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Short Name / Initials</label>
                            <input type="text" name="short_name" value="{{ old('short_name', $organization->short_name) }}" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm @error('short_name') border-red-500 @enderror">
                            @error('short_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Organization Type</label>
                            <input type="text" name="type" value="{{ old('type', $organization->type) }}" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm @error('type') border-red-500 @enderror">
                            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Industry</label>
                            <input type="text" name="industry" value="{{ old('industry', $organization->industry) }}" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Website URL</label>
                            <input type="url" name="website" value="{{ old('website', $organization->website) }}" placeholder="https://example.com" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Registration No</label>
                            <input type="text" name="registration_no" value="{{ old('registration_no', $organization->registration_no) }}" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">GST Number</label>
                            <input type="text" name="gst_no" value="{{ old('gst_no', $organization->gst_no) }}" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">PAN Number</label>
                            <input type="text" name="pan_no" value="{{ old('pan_no', $organization->pan_no) }}" class="w-full p-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-md transition-all flex items-center">
                            <i data-lucide="save" size="18" class="mr-2"></i> Update Organization
                        </button>
                    </div>
                </form>
                @else
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 mb-3">
                        <i data-lucide="alert-triangle" size="24"></i>
                    </div>
                    <p class="text-slate-600 font-medium">Organization details not found.</p>
                </div>
                @endif
            </div>


        </div>
    </div>
</div>
@endsection
