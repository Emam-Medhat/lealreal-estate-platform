@extends('layouts.app')

@section('title', 'Developer Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Developer Dashboard</h1>
                    <p class="text-gray-600">Manage your real estate development projects</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="createProject()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        New Project
                    </button>
                </div>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-building text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Projects</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['active_projects'] }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>{{ $stats['projects_growth'] }}% this month
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Units</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_units'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['units_sold'] }} sold</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Investment</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_investment'], 0) }}</p>
                        <p class="text-xs text-blue-600 mt-1">
                            {{ $stats['roi'] }}% ROI
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-hard-hat text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Construction</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['construction_progress'] }}%</p>
                        <p class="text-xs text-gray-500 mt-1">Avg. completion</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Projects</h2>
                <a href="{{ route('developer.projects') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($recentProjects as $project)
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gray-200">
                            @if($project->image)
                                <img src="{{ $project->image }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-building text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-800 mb-2">{{ $project->name }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($project->description, 80) }}</p>
                            
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                                <span><i class="fas fa-map-marker-alt mr-1"></i>{{ $project->location }}</span>
                                <span><i class="fas fa-home mr-1"></i>{{ $project->units_count }} units</span>
                            </div>
                            
                            <div class="mb-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Progress</span>
                                    <span class="font-medium text-gray-800">{{ $project->progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-800">${{ number_format($project->total_value, 0) }}</span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($project->status === 'active')
                                        bg-green-100 text-green-800
                                    @elseif($project->status === 'planning')
                                        bg-yellow-100 text-yellow-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Projects Yet</h3>
                        <p class="text-gray-500 mb-4">Start by creating your first development project.</p>
                        <button onclick="createProject()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Create Project
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Construction Updates -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Construction Updates</h2>
                <a href="{{ route('developer.construction-updates') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            
            <div class="space-y-3">
                @forelse ($recentUpdates as $update)
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                            <i class="fas fa-hard-hat text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">{{ $update['title'] }}</p>
                            <p class="text-xs text-gray-600">{{ $update['project'] }} - {{ $update['time'] }}</p>
                        </div>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $update['phase'] }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No recent updates</p>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 rounded-full p-3 mr-3">
                        <i class="fas fa-cube text-blue-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">BIM Models</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Manage 3D building models</p>
                <a href="{{ route('developer.bim-models') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Manage Models →
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 rounded-full p-3 mr-3">
                        <i class="fas fa-layer-group text-green-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Project Phases</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Track development phases</p>
                <a href="{{ route('developer.phases') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View Phases →
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 rounded-full p-3 mr-3">
                        <i class="fas fa-home text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Units Management</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Manage property units</p>
                <a href="{{ route('developer.units') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Manage Units →
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function createProject() {
    window.location.href = '/developer/projects/create';
}
</script>
@endsection
