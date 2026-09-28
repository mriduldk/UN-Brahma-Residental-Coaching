@extends('layouts.administration')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">User Management</h1>
            <p class="text-slate-500 text-sm">Manage access and roles for your organization members</p>
        </div>
        <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5">
            <i data-lucide="user-plus" size="18" class="mr-2"></i> Add User
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-100 flex items-center">
        <i data-lucide="check-circle" class="mr-2"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-100 flex items-center">
        <i data-lucide="alert-circle" class="mr-2"></i> {{ session('error') }}
    </div>
    @endif
    
    @if($errors->any())
    <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-100">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                        <th class="p-4 font-semibold">User</th>
                        <th class="p-4 font-semibold">Email</th>
                        <th class="p-4 font-semibold">Role</th>
                        <th class="p-4 font-semibold">Joined Date</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold mr-3">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-medium text-slate-700">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="p-4 text-slate-600">{{ $user->email ?? 'N/A' }}</td>
                        <td class="p-4">
                            @php $role = $user->pivot->role ?? 'viewer'; @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $role }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-500 text-sm">
                            {{ $user->created_at->format('d M, Y') }}
                        </td>
                        <td class="p-4 text-right">
                           <div class="flex items-center justify-end gap-2">
                               @if(auth()->id() !== $user->id) 
                               <form action="{{ route('administration.users.update', $user->id) }}" method="POST" class="inline-block">
                                   @csrf
                                   @method('PUT')
                                   <input type="hidden" name="role" value="{{ $role === 'admin' ? 'viewer' : 'admin' }}">
                                   <button type="submit" class="text-xs font-medium px-3 py-1.5 rounded-lg border {{ $role === 'admin' ? 'border-slate-200 text-slate-600 hover:bg-slate-50' : 'border-purple-200 text-purple-600 hover:bg-purple-50' }} transition-colors" title="{{ $role === 'admin' ? 'Demote to Viewer' : 'Promote to Admin' }}">
                                       {{ $role === 'admin' ? 'Demote' : 'Promote' }}
                                   </button>
                               </form>
                               
                               <form action="{{ route('administration.users.remove', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to remove this user from the organization?');">
                                   @csrf
                                   @method('DELETE')
                                   <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded-lg transition-colors" title="Remove User">
                                       <i data-lucide="trash-2" size="16"></i>
                                   </button>
                               </form>
                               @else
                               <span class="text-xs text-slate-400 italic">Current User</span>
                               @endif
                           </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500">
                            No users found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 backdrop-blur-sm" onclick="if(event.target === this) this.classList.add('hidden')">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Add User</h3>
            <button onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" size="24"></i>
            </button>
        </div>
        
        <form action="{{ route('administration.users.add') }}" method="POST">
            @csrf
            
            <div class="mb-5">
                <label class="block text-sm font-bold text-slate-700 mb-2">Email Address (Optional)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="mail" size="18"></i>
                    </div>
                    <input type="email" name="email" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 placeholder:text-slate-400 font-medium" placeholder="colleague@example.com (Optional)">
                </div>
                <p class="text-xs text-slate-500 mt-2 ml-1 flex items-center gap-1">
                    <i data-lucide="info" size="12"></i> If user exists, they will be added. If not, a new account will be created.
                </p>
            </div>
            
            <div class="mb-5">
                 <label class="block text-sm font-bold text-slate-700 mb-2">Role</label>
                 <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="shield" size="18"></i>
                    </div>
                    <select name="role" class="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 font-medium appearance-none">
                        <option value="viewer">Viewer (Read-only)</option>
                        <option value="admin">Admin (Full Access)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="chevron-down" size="18"></i>
                    </div>
                 </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-slate-700 mb-2">Name <span class="text-slate-400 font-normal">(For new users)</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="user" size="18"></i>
                    </div>
                    <input type="text" name="name" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-slate-700 placeholder:text-slate-400 font-medium" placeholder="John Doe">
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="px-5 py-2.5 text-slate-600 hover:bg-slate-50 rounded-xl font-bold transition-colors">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5 flex items-center">
                    <i data-lucide="plus" size="18" class="mr-2"></i> Add User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
