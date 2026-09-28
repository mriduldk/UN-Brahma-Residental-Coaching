@extends('layouts.administration')

@section('title', 'Manage Institution Users')

@section('content')
<div class="space-y-6 mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
           <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('administration.institutions.index') }}" class="text-slate-500 hover:text-blue-600 transition-colors">
                    <i data-lucide="arrow-left" size="16"></i>
                </a>
                <h1 class="text-2xl font-bold text-slate-900">Manage Users</h1>
           </div>
            <p class="text-slate-500 text-sm ml-6">For {{ $institution->institution_name }} ({{ $institution->institution_code }})</p>
        </div>
        <button onclick="document.getElementById('addUserModal').showModal()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
            <i data-lucide="user-plus" size="18" class="mr-2"></i> Add User
        </button>
    </div>

    <!-- Alert -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif
    
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 p-4 rounded-md shadow-sm">
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Users List -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">User</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Added On</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($institution->users as $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff" alt="{{ $user->name }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-slate-900">{{ $user->name }}</div>
                                <div class="text-sm text-slate-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->pivot->role === 'admin')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Admin</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">Viewer</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $user->pivot->created_at->format('d M, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-3">
                            <button onclick="editRole('{{ $user->id }}', '{{ $user->pivot->role }}')" class="text-indigo-600 hover:text-indigo-900" title="Edit Role">
                                <i data-lucide="edit-2" size="18"></i>
                            </button>
                            
                            <form action="{{ route('administration.institutions.users.destroy', ['id' => $institution->id, 'userId' => $user->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this user from the institution?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Remove User">
                                    <i data-lucide="trash-2" size="18"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center">
                            <i data-lucide="users" size="48" class="text-slate-300 mb-4"></i>
                            <p class="text-lg font-medium text-slate-900">No users found</p>
                            <p class="text-sm text-slate-500">Add users to manage this institution.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<dialog id="addUserModal" class="modal rounded-2xl shadow-xl p-0 w-full max-w-md backdrop:bg-slate-900/50">
    <div class="bg-white">
        <div class="border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Add User to Institution</h3>
            <button onclick="document.getElementById('addUserModal').close()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i data-lucide="x" size="20"></i>
            </button>
        </div>
        <form action="{{ route('administration.institutions.users.store', $institution->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                <input type="email" name="email" required placeholder="Enter user's email" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                <p class="text-xs text-slate-500 mt-1">If the user doesn't exist, a new account will be created.</p>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Role</label>
                <select name="role" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all cursor-pointer">
                    <option value="viewer">Viewer (Read Only)</option>
                    <option value="admin">Admin (Full Access)</option>
                </select>
            </div>
            
            <div class="pt-2">
                <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors">Add User</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Edit Role Modal -->
<dialog id="editRoleModal" class="modal rounded-2xl shadow-xl p-0 w-full max-w-md backdrop:bg-slate-900/50">
    <div class="bg-white">
        <div class="border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Update User Role</h3>
            <button onclick="document.getElementById('editRoleModal').close()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i data-lucide="x" size="20"></i>
            </button>
        </div>
        <form id="editRoleForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Role</label>
                <select name="role" id="editRoleSelect" required class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all cursor-pointer">
                    <option value="viewer">Viewer (Read Only)</option>
                    <option value="admin">Admin (Full Access)</option>
                </select>
            </div>
            
            <div class="pt-2">
                <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors">Update Role</button>
            </div>
        </form>
    </div>
</dialog>

<script>
    function editRole(userId, currentRole) {
        const form = document.getElementById('editRoleForm');
        // Construct the URL dynamically based on the current institution ID
        // Note: JS doesn't have direct access to blade routes with wildcards easily unless passed
        // For simplicity, we'll hardcode the structure: /administration/institutions/{instId}/users/{userId}
        
        const url = "{{ route('administration.institutions.users.update', ['id' => $institution->id, 'userId' => ':userId']) }}";
        form.action = url.replace(':userId', userId);
        
        document.getElementById('editRoleSelect').value = currentRole;
        document.getElementById('editRoleModal').showModal();
    }
</script>
@endsection
