@extends('layouts.app')

@section('title', 'Project Phases')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Project Phases</h1>
                    <p class="text-gray-600">Manage development phases and milestones</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="createPhase()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Phase
                    </button>
                    <a href="{{ route('developer.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Project Filter -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Filter by Project:</label>
                    <select onchange="filterByProject(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <span>Total Phases: {{ $phases->count() }}</span>
                    <span>•</span>
                    <span>Completed: {{ $phases->where('status', 'completed')->count() }}</span>
                    <span>•</span>
                    <span>In Progress: {{ $phases->where('status', 'in_progress')->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Phases Timeline -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Development Timeline</h2>
            
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-300"></div>
                
                <!-- Phases -->
                <div class="space-y-8">
                    @forelse ($phases as $phase)
                        <div class="relative flex items-start">
                            <!-- Timeline Dot -->
                            <div class="relative z-10 flex items-center justify-center w-16 h-16">
                                <div class="w-4 h-4 rounded-full
                                    @if($phase->status === 'completed')
                                        bg-green-500
                                    @elseif($phase->status === 'in_progress')
                                        bg-blue-500
                                    @else
                                        bg-gray-300
                                    @endif
                                "></div>
                            </div>
                            
                            <!-- Phase Content -->
                            <div class="flex-1 ml-6">
                                <div class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $phase->name }}</h3>
                                            <p class="text-gray-600 mb-2">{{ $phase->description }}</p>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                <span><i class="fas fa-building mr-1"></i>{{ $phase->project->name }}</span>
                                                <span><i class="fas fa-calendar mr-1"></i>{{ $phase->start_date }} - {{ $phase->end_date }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                                @if($phase->status === 'completed')
                                                    bg-green-100 text-green-800
                                                @elseif($phase->status === 'in_progress')
                                                    bg-blue-100 text-blue-800
                                                @else
                                                    bg-gray-100 text-gray-800
                                                @endif
                                            ">
                                                {{ ucfirst(str_replace('_', ' ', $phase->status)) }}
                                            </span>
                                            <div class="flex space-x-1">
                                                <button onclick="editPhase({{ $phase->id }})" class="text-gray-600 hover:text-gray-800">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deletePhase({{ $phase->id }})" class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress Bar -->
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm mb-2">
                                            <span class="text-gray-600">Phase Progress</span>
                                            <span class="font-medium text-gray-800">{{ $phase->progress }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $phase->progress }}%"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Phase Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-600">Duration:</span>
                                            <span class="font-medium text-gray-800 ml-2">{{ $phase->duration }} days</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Budget:</span>
                                            <span class="font-medium text-gray-800 ml-2">${{ number_format($phase->budget, 0) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Team Size:</span>
                                            <span class="font-medium text-gray-800 ml-2">{{ $phase->team_size }} members</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Milestones -->
                                    @if($phase->milestones->isNotEmpty())
                                        <div class="mt-4">
                                            <h4 class="font-medium text-gray-800 mb-2">Milestones</h4>
                                            <div class="space-y-2">
                                                @foreach ($phase->milestones as $milestone)
                                                    <div class="flex items-center justify-between p-2 bg-white rounded">
                                                        <div class="flex items-center">
                                                            <div class="w-2 h-2 rounded-full
                                                                @if($milestone->completed)
                                                                    bg-green-500
                                                                @else
                                                                    bg-gray-300
                                                                @endif
                                                            mr-2"></div>
                                                            <span class="text-sm text-gray-700">{{ $milestone->title }}</span>
                                                        </div>
                                                        <span class="text-xs text-gray-500">{{ $milestone->target_date }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <i class="fas fa-layer-group text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Phases Yet</h3>
                            <p class="text-gray-500 mb-6">Start by adding your first development phase.</p>
                            <button onclick="createPhase()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Add First Phase
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Phase Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-layer-group text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Phases</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_phases'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['completed_phases'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['in_progress_phases'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-percentage text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Avg. Progress</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['avg_progress'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Milestones -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Milestones</h2>
            
            <div class="space-y-3">
                @forelse ($upcomingMilestones as $milestone)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <i class="fas fa-flag text-yellow-600 text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $milestone->title }}</h4>
                                <p class="text-sm text-gray-600">{{ $milestone->phase->name }} - {{ $milestone->project->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-800">{{ $milestone->target_date }}</div>
                            <div class="text-xs text-gray-500">{{ $milestone->days_remaining }} days</div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No upcoming milestones</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Phase Modal -->
<div id="phaseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Phase</h3>
        
        <form action="{{ route('developer.phases.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phase Name</label>
                        <input type="text" name="name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select name="project_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Project</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Budget</label>
                        <input type="number" name="budget" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Team Size</label>
                        <input type="number" name="team_size"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="planning">Planning</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="on_hold">On Hold</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Initial Progress (%)</label>
                    <input type="number" name="progress" min="0" max="100" value="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closePhaseModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add Phase
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function createPhase() {
    document.getElementById('phaseModal').classList.remove('hidden');
}

function closePhaseModal() {
    document.getElementById('phaseModal').classList.add('hidden');
}

function filterByProject(projectId) {
    window.location.href = '?project=' + projectId;
}

function editPhase(phaseId) {
    window.location.href = '/developer/phases/' + phaseId + '/edit';
}

function deletePhase(phaseId) {
    if (confirm('Are you sure you want to delete this phase?')) {
        fetch('/developer/phases/' + phaseId, {
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
