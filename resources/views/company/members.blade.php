@extends('layouts.app')

@section('title', 'Company Members')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Company Members</h1>
                    <p class="text-gray-600">Manage your company employees and team members</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="addMember()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add Member
                    </button>
                    <a href="{{ route('company.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Member Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Members</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $members->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Members</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $members->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-user-tie text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Team Leaders</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $members->where('is_leader', true)->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-building text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Departments</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $members->pluck('department')->unique()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <input type="text" placeholder="Search members..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-64">
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Departments</option>
                        <option>Sales</option>
                        <option>Marketing</option>
                        <option>Operations</option>
                        <option>Finance</option>
                        <option>HR</option>
                        <option>IT</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Roles</option>
                        <option>Agent</option>
                        <option>Manager</option>
                        <option>Admin</option>
                        <option>Staff</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 transition-colors text-sm">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                    <button class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition-colors text-sm">
                        <i class="fas fa-download mr-1"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Members Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($members as $member)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Member Header -->
                    <div class="p-6 border-b">
                        <div class="flex items-center space-x-4">
                            <div class="bg-gray-200 rounded-full w-16 h-16 flex items-center justify-center">
                                @if($member->avatar)
                                    <img src="{{ $member->avatar }}" alt="{{ $member->name }}" class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <i class="fas fa-user text-gray-400 text-2xl"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $member->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $member->role }}</p>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($member->status === 'active')
                                            bg-green-100 text-green-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($member->status) }}
                                    </span>
                                    @if($member->is_leader)
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">Leader</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Member Details -->
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-envelope mr-2 w-4"></i>
                                <span>{{ $member->email }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-phone mr-2 w-4"></i>
                                <span>{{ $member->phone }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-building mr-2 w-4"></i>
                                <span>{{ $member->department }}</span>
                            </div>
                            @if($member->team)
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-users mr-2 w-4"></i>
                                    <span>{{ $member->team->name }}</span>
                                </div>
                            @endif
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar mr-2 w-4"></i>
                                <span>Joined {{ $member->created_at->format('M j, Y') }}</span>
                            </div>
                        </div>
                        
                        <!-- Performance -->
                        @if($member->performance)
                            <div class="mt-4">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm text-gray-600">Performance</span>
                                    <span class="text-sm font-medium text-gray-800">{{ $member->performance }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $member->performance }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Member Actions -->
                    <div class="p-6 border-t bg-gray-50">
                        <div class="flex space-x-2">
                            <button onclick="viewMember({{ $member->id }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                View Profile
                            </button>
                            <button onclick="editMember({{ $member->id }})" class="flex-1 border border-gray-300 text-gray-700 px-3 py-2 rounded hover:bg-gray-50 transition-colors text-sm">
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Members Yet</h3>
                    <p class="text-gray-500 mb-6">Add your first team member to get started.</p>
                    <button onclick="addMember()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add First Member
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($members->hasPages())
            <div class="mt-6">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Member Modal -->
<div id="memberModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Member</h3>
        
        <form action="{{ route('company.members.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Role</option>
                            <option value="agent">Agent</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select name="department" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Department</option>
                            <option value="sales">Sales</option>
                            <option value="marketing">Marketing</option>
                            <option value="operations">Operations</option>
                            <option value="finance">Finance</option>
                            <option value="hr">Human Resources</option>
                            <option value="it">IT</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Team</label>
                    <select name="team_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Team</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                    <input type="file" name="avatar" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_leader" class="mr-2">
                    <label class="text-sm text-gray-700">Team Leader</label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeMemberModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add Member
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addMember() {
    document.getElementById('memberModal').classList.remove('hidden');
}

function closeMemberModal() {
    document.getElementById('memberModal').classList.add('hidden');
}

function viewMember(memberId) {
    window.location.href = '/company/members/' + memberId;
}

function editMember(memberId) {
    window.location.href = '/company/members/' + memberId + '/edit';
}
</script>
@endsection
