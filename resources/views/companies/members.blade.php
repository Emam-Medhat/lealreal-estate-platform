@extends('layouts.app')

@section('title', 'Company Members - ' . $company->name)

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Company Members</h1>
                    <p class="mt-2 text-gray-600">Manage your team and their roles within {{ $company->name }}.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('companies.show', $company) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                        View Profile
                    </a>
                    @can('update', $company)
                        <button onclick="toggleAddMemberModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>Add Member
                        </button>
                    @endcan
                </div>
            </div>

            <!-- Members List -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Member</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($members as $member)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                            {{ substr($member->user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $member->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $member->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($member->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $member->joined_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($member->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('update', $company)
                                        @if($member->user_id !== auth()->id())
                                            <form action="{{ route('companies.members.remove', [$company, $member->user]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to remove this member?')">
                                                    Remove
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="text-gray-400 mb-2"><i class="fas fa-users text-4xl"></i></div>
                                    <h3 class="text-lg font-medium text-gray-900">No members found</h3>
                                    <p class="text-gray-500">You haven't added any members to your company yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal (Simplified) -->
<div id="addMemberModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Add New Member</h2>
        <form action="{{ route('companies.members.add', $company) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="user_email" class="block text-sm font-semibold text-gray-700 mb-2">User Email</label>
                    <input type="email" name="email" id="user_email" required placeholder="user@example.com"
                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="member_role" class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                    <select name="role" id="member_role" class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                        <option value="member">Member</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="toggleAddMemberModal()" class="px-6 py-3 text-sm font-semibold text-gray-700 hover:text-gray-900">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all">
                    Add Member
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAddMemberModal() {
        const modal = document.getElementById('addMemberModal');
        modal.classList.toggle('hidden');
    }
</script>
@endsection
