@extends('layouts.app')

@section('title', 'Developer Projects')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Development Projects</h1>
                    <p class="text-gray-600">Manage your real estate development portfolio</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="createProject()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        New Project
                    </button>
                    <a href="{{ route('developer.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Project Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-building text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Projects</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $projects->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-hard-hat text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Under Construction</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $projects->where('status', 'construction')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $projects->where('status', 'completed')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Value</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($totalValue, 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <input type="text" placeholder="Search projects..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-64">
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Status</option>
                        <option>Planning</option>
                        <option>Pre-construction</option>
                        <option>Construction</option>
                        <option>Completed</option>
                        <option>On Hold</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Types</option>
                        <option>Residential</option>
                        <option>Commercial</option>
                        <option>Mixed-use</option>
                        <option>Industrial</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Locations</option>
                        <option>Downtown</option>
                        <option>Suburbs</option>
                        <option>Rural</option>
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

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($projects as $project)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Project Image -->
                    <div class="relative h-48 bg-gray-200">
                        @if($project->image)
                            <img src="{{ $project->image }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-building text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 left-2">
                            <span class="bg-{{ $project->status === 'construction' ? 'yellow' : ($project->status === 'completed' ? 'green' : ($project->status === 'planning' ? 'blue' : 'gray')) }}-500 text-white px-2 py-1 rounded text-xs">
                                {{ ucfirst($project->status) }}
                            </span>
                        </div>
                        
                        <!-- Featured Badge -->
                        @if($project->featured)
                            <div class="absolute top-2 right-2">
                                <span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Project Details -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            <a href="{{ route('developer.project-details', $project) }}" class="hover:text-blue-600">
                                {{ $project->name }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-3">{{ Str::limit($project->description, 80) }}</p>
                        <p class="text-gray-600 text-sm mb-3">
                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $project->location }}
                        </p>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                            <span><i class="fas fa-home mr-1"></i>{{ $project->units_count }} units</span>
                            <span><i class="fas fa-expand mr-1"></i>{{ number_format($project->total_area) }} sqft</span>
                            <span><i class="fas fa-calendar mr-1"></i>{{ $project->completion_date }}</span>
                        </div>
                        
                        <!-- Progress -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Progress</span>
                                <span class="font-medium text-gray-800">{{ $project->progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-2xl font-bold text-gray-800">
                                ${{ number_format($project->total_value, 0) }}
                            </div>
                            <div class="text-sm text-gray-600">
                                ROI: {{ $project->estimated_roi }}%
                            </div>
                        </div>
                        
                        <!-- Project Stats -->
                        <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-home mr-1"></i>
                                <span>{{ $project->units_sold }}/{{ $project->units_count }} sold</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-dollar-sign mr-1"></i>
                                <span>${{ number_format($project->avg_price_per_unit, 0) }}/unit</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Project Actions -->
                    <div class="p-6 border-t bg-gray-50">
                        <div class="flex space-x-2">
                            <button onclick="viewProject({{ $project->id }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                View Details
                            </button>
                            <button onclick="editProject({{ $project->id }})" class="flex-1 border border-gray-300 text-gray-700 px-3 py-2 rounded hover:bg-gray-50 transition-colors text-sm">
                                Edit
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Projects Yet</h3>
                    <p class="text-gray-500 mb-6">Start by creating your first development project.</p>
                    <button onclick="createProject()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Project
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($projects->hasPages())
            <div class="mt-6">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function createProject() {
    window.location.href = '/developer/projects/create';
}

function viewProject(projectId) {
    window.location.href = '/developer/projects/' + projectId;
}

function editProject(projectId) {
    window.location.href = '/developer/projects/' + projectId + '/edit';
}
</script>
@endsection
