@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black tracking-tight text-slate-800">Lead Tracking CRM</h1>
    <p class="text-sm font-medium text-slate-500 mt-1">Track onboarding calls, lead statuses, and follow-up notes for all organizations.</p>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
    <form method="GET" action="{{ route('admin.lead-tracking') }}" class="flex flex-wrap md:flex-nowrap gap-4 items-end">
        <!-- Search -->
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-slate-500 mb-1">Search</label>
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Phone, Email..." class="w-full bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl pl-10 pr-4 py-2 outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" size="16"></i>
                </div>
            </div>
        </div>

        <!-- Business Type Filter -->
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-slate-500 mb-1">Business Type</label>
            <div class="relative">
                <select name="business_type" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl pl-4 pr-10 py-2 outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-sm">
                    <option value="">All Types</option>
                    <option value="School" {{ request('business_type') == 'School' ? 'selected' : '' }}>School</option>
                    <option value="Tuition" {{ request('business_type') == 'Tuition' ? 'selected' : '' }}>Tuition</option>
                    <option value="Coaching" {{ request('business_type') == 'Coaching' ? 'selected' : '' }}>Coaching</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <i data-lucide="chevron-down" size="14"></i>
                </div>
            </div>
        </div>

        <!-- Call Status Filter -->
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-slate-500 mb-1">Call Status</label>
            <div class="relative">
                <select name="call_status" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl pl-4 pr-10 py-2 outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-sm">
                    <option value="">All Statuses</option>
                    <option value="uncontacted" {{ request('call_status') == 'uncontacted' ? 'selected' : '' }}>⭕ Uncontacted</option>
                    <option value="pending" {{ request('call_status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="picked_up" {{ request('call_status') == 'picked_up' ? 'selected' : '' }}>📞 Picked Up</option>
                    <option value="not_picked" {{ request('call_status') == 'not_picked' ? 'selected' : '' }}>📵 Not Picked</option>
                    <option value="wrong_number" {{ request('call_status') == 'wrong_number' ? 'selected' : '' }}>❌ Wrong Number</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <i data-lucide="chevron-down" size="14"></i>
                </div>
            </div>
        </div>

        <!-- Lead Temp Filter -->
        <div class="w-full md:w-48">
            <label class="block text-xs font-bold text-slate-500 mb-1">Lead Temp</label>
            <div class="relative">
                <select name="lead_temperature" class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl pl-4 pr-10 py-2 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                    <option value="">All Temps</option>
                    <option value="cold" {{ request('lead_temperature') == 'cold' ? 'selected' : '' }}>🧊 Cold</option>
                    <option value="warm" {{ request('lead_temperature') == 'warm' ? 'selected' : '' }}>🔥 Warm</option>
                    <option value="hot" {{ request('lead_temperature') == 'hot' ? 'selected' : '' }}>🌶️ Hot</option>
                    <option value="converted" {{ request('lead_temperature') == 'converted' ? 'selected' : '' }}>✅ Converted</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                    <i data-lucide="chevron-down" size="14"></i>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-2 w-full md:w-auto">
            <button type="submit" class="flex-1 md:flex-none bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl px-6 py-2 transition-colors shadow-sm flex items-center justify-center gap-2">
                <i data-lucide="filter" size="16"></i> Filter
            </button>
            @if(request()->anyFilled(['search', 'business_type', 'call_status', 'lead_temperature']))
                <a href="{{ route('admin.lead-tracking') }}" class="flex-1 md:flex-none bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl px-4 py-2 transition-colors flex items-center justify-center" title="Clear Filters">
                    <i data-lucide="x" size="16"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Lead Tracking Grid -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col" x-data="leadTracking()">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
        <div>
            <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                <i data-lucide="phone-call" size="20" class="text-purple-500"></i> Organization Contacts
            </h3>
        </div>
        <div class="flex items-center gap-3">
            <span x-show="isSaving" style="display: none;" class="text-xs font-bold text-blue-600 flex items-center gap-1">
                <i data-lucide="loader-2" class="animate-spin" size="14"></i> Saving...
            </span>
            <span x-show="saved" style="display: none;" class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                <i data-lucide="check" size="14"></i> Saved
            </span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-100/50 text-slate-500 font-bold text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">Organization</th>
                    <th class="px-6 py-4">Contact Details</th>
                    <th class="px-6 py-4">Call Status</th>
                    <th class="px-6 py-4">Lead Temp</th>
                    <th class="px-6 py-4">Remarks</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-600 font-medium">
                @forelse($organizations as $org)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 align-top">
                        <div class="flex flex-col">
                            <strong class="text-slate-800 text-base">{{ $org->organization_name }}</strong>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-[10px] font-bold uppercase">{{ $org->business_type ?? 'N/A' }}</span>
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase">{{ $org->type }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-top">
                        <div class="flex flex-col gap-1 text-xs">
                            <span class="flex items-center gap-1 text-slate-700 font-bold">
                                <i data-lucide="phone" size="12" class="text-slate-400"></i> {{ $org->user->mobile_number ?? 'N/A' }}
                            </span>
                            <span class="flex items-center gap-1 text-slate-500">
                                <i data-lucide="mail" size="12" class="text-slate-400"></i> {{ $org->user->email }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-top">
                        <div class="relative">
                            <select @change="updateLead({{ $org->id }}, 'call_status', $event.target.value)" 
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl pl-4 pr-10 py-2.5 outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 shadow-sm transition-all hover:bg-slate-100 hover:border-slate-300 cursor-pointer text-xs">
                                <option value="">-- Select Status --</option>
                                <option value="pending" {{ optional($org->leadTracking)->call_status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="picked_up" {{ optional($org->leadTracking)->call_status == 'picked_up' ? 'selected' : '' }}>📞 Picked Up</option>
                                <option value="not_picked" {{ optional($org->leadTracking)->call_status == 'not_picked' ? 'selected' : '' }}>📵 Not Picked</option>
                                <option value="wrong_number" {{ optional($org->leadTracking)->call_status == 'wrong_number' ? 'selected' : '' }}>❌ Wrong Number</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <i data-lucide="chevron-down" size="14"></i>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-top">
                        <div class="relative">
                            <select @change="updateLead({{ $org->id }}, 'lead_temperature', $event.target.value)" 
                                    class="w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-bold rounded-xl pl-4 pr-10 py-2.5 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all hover:bg-slate-100 hover:border-slate-300 cursor-pointer text-xs">
                                <option value="">-- Select Temp --</option>
                                <option value="cold" {{ optional($org->leadTracking)->lead_temperature == 'cold' ? 'selected' : '' }}>🧊 Cold</option>
                                <option value="warm" {{ optional($org->leadTracking)->lead_temperature == 'warm' ? 'selected' : '' }}>🔥 Warm</option>
                                <option value="hot" {{ optional($org->leadTracking)->lead_temperature == 'hot' ? 'selected' : '' }}>🌶️ Hot</option>
                                <option value="converted" {{ optional($org->leadTracking)->lead_temperature == 'converted' ? 'selected' : '' }}>✅ Converted</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <i data-lucide="chevron-down" size="14"></i>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 align-top whitespace-normal">
                        <textarea 
                               @change="updateLead({{ $org->id }}, 'remarks', $event.target.value)"
                               placeholder="Add conversation notes, next steps, etc..." 
                               rows="2"
                               class="text-xs border-slate-200 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 w-full min-w-[200px] resize-y p-2">{{ optional($org->leadTracking)->remarks }}</textarea>
                    </td>
                    <td class="px-6 py-4 align-top text-center">
                        <form method="POST" action="{{ route('admin.impersonate.login', $org->user->id) }}">
                            @csrf
                            <button type="submit" class="p-2 bg-slate-100 hover:bg-purple-100 text-slate-600 hover:text-purple-700 rounded-lg transition-colors inline-flex items-center justify-center shadow-sm border border-slate-200 hover:border-purple-300 group" title="Login as {{ $org->organization_name }}">
                                <i data-lucide="log-in" size="16" class="group-hover:translate-x-0.5 transition-transform"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 font-medium">No organizations registered yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($organizations->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $organizations->links() }}
    </div>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('leadTracking', () => ({
            isSaving: false,
            saved: false,
            
            updateLead(orgId, field, value) {
                this.isSaving = true;
                this.saved = false;
                
                fetch('{{ route("admin.organizations.tracking.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        organization_id: orgId,
                        [field]: value
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.isSaving = false;
                    if(data.success) {
                        this.saved = true;
                        setTimeout(() => { this.saved = false }, 2000);
                    }
                })
                .catch(err => {
                    this.isSaving = false;
                    console.error('Error updating tracking', err);
                });
            }
        }));
    });
</script>
@endsection
