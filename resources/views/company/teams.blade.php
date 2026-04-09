@extends('layouts.app')

@section('title', 'Company Teams')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Company Teams</h1>
                    <p class="text-gray-600">Manage your teams and team members</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="createTeam()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Team
                    </button>
                    <a href="{{ route('company.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Team Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Teams</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $teams->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-user-friends text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Team Members</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalMembers }}</p>
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
                        <p class="text-2xl font-bold text-gray-800">{{ $teamLeaders }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Avg. Performance</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $avgPerformance }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teams Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($teams as $team)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Team Header -->
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $team->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $team->description }}</p>
                            </div>
                            <div class="flex space-x-1">
                                <button onclick="editTeam({{ $team->id }})" class="text-gray-600 hover:text-gray-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteTeam({{ $team->id }})" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span><i class="fas fa-users mr-1"></i>{{ $team->members_count }}</span>
                                <span><i class="fas fa-tasks mr-1"></i>{{ $team->active_projects }}</span>
                                <span><i class="fas fa-chart-line mr-1"></i>{{ $team->performance }}%</span>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($team->status === 'active')
                                    bg-green-100 text-green-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst($team->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Team Members -->
                    <div class="p-6">
                        <h4 class="font-medium text-gray-800 mb-3">Team Members</h4>
                        <div class="space-y-2">
                            @forelse ($team->members->take(4) as $member)
                                <div class="flex items-center">
                                    <div class="bg-gray-200 rounded-full w-8 h-8 mr-3 flex items-center justify-center">
                                        @if($member->avatar)
                                            <img src="{{ $member->avatar }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                        @else
                                            <i class="fas fa-user text-gray-400 text-xs"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-600">{{ $member->role }}</p>
                                    </div>
                                    @if($member->is_leader)
                                        <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">Leader</span>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">No members assigned</p>
                            @endforelse
                        </div>
                        
                        @if($team->members->count() > 4)
                            <div class="mt-3 text-center">
                                <button onclick="viewTeamMembers({{ $team->id }})" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View All {{ $team->members->count() }} Members →
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Team Actions -->
                    <div class="p-6 border-t bg-gray-50">
                        <div class="flex space-x-3">
                            <button onclick="viewTeam({{ $team->id }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                View Details
                            </button>
                            <button onclick="manageTeam({{ $team->id }})" class="flex-1 border border-gray-300 text-gray-700 px-3 py-2 rounded hover:bg-gray-50 transition-colors text-sm">
                                Manage
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Teams Yet</h3>
                    <p class="text-gray-500 mb-6">Create your first team to start organizing your workforce.</p>
                    <button onclick="createTeam()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Team
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Team Activity</h2>
            <div class="space-y-3">
                @forelse ($recentActivities as $activity)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                            <i class="fas fa-{{ $activity['icon'] }} text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Create Team Modal -->
<div id="teamModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Create New Team</h3>
        
        <form action="{{ route('company.teams.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Team Name</label>
                    <input type="text" name="name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Team Leader</label>
                    <select name="leader_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Team Leader</option>
                        @foreach ($availableMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} - {{ $member->role }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Team Members</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        @foreach ($availableMembers as $member)
                            <label class="flex items-center">
                                <input type="checkbox" name="members[]" value="{{ $member->id }}" class="mr-2">
                                <span>{{ $member->name }} - {{ $member->role }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <select name="department"
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
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeTeamModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Create Team
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function createTeam() {
    document.getElementById('teamModal').classList.remove('hidden');
}

function closeTeamModal() {
    document.getElementById('teamModal').classList.add('hidden');
}

function viewTeam(teamId) {
    window.location.href = '/company/teams/' + teamId;
}

function editTeam(teamId) {
    window.location.href = '/company/teams/' + teamId + '/edit';
}

function manageTeam(teamId) {
    window.location.href = '/company/teams/' + teamId + '/manage';
}

function viewTeamMembers(teamId) {
    window.location.href = '/company/teams/' + teamId + '/members';
}

function deleteTeam(teamId) {
    if (confirm('Are you sure you want to delete this team?')) {
        fetch('/company/teams/' + teamId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>
@endsection
