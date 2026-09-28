@extends('layouts.admin')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-sm font-medium text-slate-500 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-purple-600 transition-colors">Dashboard</a>
            <i data-lucide="chevron-right" size="14"></i>
            <span class="text-slate-800">Impersonate User</span>
        </div>
        <h1 class="text-2xl font-black tracking-tight text-slate-800">Login As User</h1>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-3">
        <form action="{{ route('admin.impersonate.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative group">
                <select name="role" class="w-full sm:w-48 rounded-xl border border-slate-200 bg-white hover:border-slate-300 focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 shadow-sm transition-all text-sm px-4 py-2.5 appearance-none" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="institute" {{ request('role') == 'institute' ? 'selected' : '' }}>Institute</option>
                    <option value="administration" {{ request('role') == 'administration' ? 'selected' : '' }}>Organization Admin</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="chevron-down" size="16"></i>
                </div>
            </div>
            <div class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or role..." 
                    class="w-full sm:w-80 rounded-xl border border-slate-200 bg-white hover:border-slate-300 focus:bg-white focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 shadow-sm transition-all text-sm px-4 py-2.5 pl-10">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-purple-500 transition-colors">
                    <i data-lucide="search" size="16"></i>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.impersonate.index', ['role' => request('role')]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-red-500 transition-colors">
                        <i data-lucide="x" size="16"></i>
                    </a>
                @endif
            </div>
            <button type="submit" class="hidden sm:block px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors">Filter</button>
        </form>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Email Address</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->role === 'student')
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold inline-flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-yellow-500"></div> Student</span>
                            @elseif($user->role === 'institute')
                                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold inline-flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div> Institute</span>
                            @elseif($user->role === 'administration')
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold inline-flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Org Admin</span>
                            @else
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold inline-flex items-center gap-1.5"><div class="w-1.5 h-1.5 rounded-full bg-slate-500"></div> {{ ucfirst($user->role) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.impersonate.login', $user->id) }}" method="POST" class="inline impersonate-form">
                                @csrf
                                <button type="button" 
                                        data-name="{{ $user->name }}"
                                    class="impersonate-btn inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 rounded-lg shadow-sm transition-colors group-hover:shadow-md">
                                    <i data-lucide="log-in" size="14"></i> Login As
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i data-lucide="users" size="48" class="text-slate-300 mb-4"></i>
                                <p class="text-sm font-medium">No users found.</p>
                                @if(request('search'))
                                    <a href="{{ route('admin.impersonate.index') }}" class="text-purple-600 hover:underline mt-2">Clear search</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const impersonateBtns = document.querySelectorAll('.impersonate-btn');
        
        impersonateBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.impersonate-form');
                const userName = this.getAttribute('data-name');
                
                Swal.fire({
                    title: 'Login As ' + userName + '?',
                    text: 'You will be logged into their account and navigated to their dashboard.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#9333ea', // purple-600
                    cancelButtonColor: '#94a3b8', // slate-400
                    confirmButtonText: 'Yes, log in!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
