@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $project->name }}</h1>
                    <p class="text-gray-600">{{ $project->description }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="editProject({{ $project->id }})" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Project
                    </button>
                    <a href="{{ route('developer.projects') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Projects
                    </a>
                </div>
            </div>
        </div>

        <!-- Project Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Image and Info -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <!-- Project Image -->
                    <div class="h-64 bg-gray-200">
                        @if($project->image)
                            <img src="{{ $project->image }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-building text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Project Details -->
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <h3 class="font-medium text-gray-800 mb-3">Project Information</h3>
                                <div class="space-y-2">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-map-marker-alt mr-2 w-4"></i>
                                        <span>{{ $project->location }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-building mr-2 w-4"></i>
                                        <span>{{ $project->property_type }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-expand mr-2 w-4"></i>
                                        <span>{{ number_format($project->total_area) }} sqft</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-calendar mr-2 w-4"></i>
                                        <span>Started: {{ $project->start_date }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-flag-checkered mr-2 w-4"></i>
                                        <span>Completion: {{ $project->completion_date }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="font-medium text-gray-800 mb-3">Financial Information</h3>
                                <div class="space-y-2">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-dollar-sign mr-2 w-4"></i>
                                        <span>Total Value: ${{ number_format($project->total_value, 0) }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-chart-line mr-2 w-4"></i>
                                        <span>Estimated ROI: {{ $project->estimated_roi }}%</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-home mr-2 w-4"></i>
                                        <span>Total Units: {{ $project->units_count }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-dollar-sign mr-2 w-4"></i>
                                        <span>Avg Price: ${{ number_format($project->avg_price_per_unit, 0) }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-percentage mr-2 w-4"></i>
                                        <span>Commission: {{ $project->commission_rate }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Progress Overview -->
                        <div class="mb-6">
                            <h3 class="font-medium text-gray-800 mb-3">Overall Progress</h3>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Project Completion</span>
                                <span class="font-medium text-gray-800">{{ $project->progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $project->progress }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div>
                            <h3 class="font-medium text-gray-800 mb-3">Project Description</h3>
                            <p class="text-gray-600">{{ $project->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-800 mb-4">Project Status</h3>
                    <div class="text-center mb-4">
                        <span class="inline-flex px-3 py-1 text-lg font-semibold rounded-full
                            @if($project->status === 'construction')
                                bg-yellow-100 text-yellow-800
                            @elseif($project->status === 'completed')
                                bg-green-100 text-green-800
                            @elseif($project->status === 'planning')
                                bg-blue-100 text-blue-800
                            @else
                                bg-gray-100 text-gray-800
                            @endif
                        ">
                            {{ ucfirst($project->status) }}
                        </span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Units Sold</span>
                            <span class="font-medium text-gray-800">{{ $project->units_sold }}/{{ $project->units_count }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Revenue Generated</span>
                            <span class="font-medium text-gray-800">${{ number_format($project->revenue_generated, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Construction Progress</span>
                            <span class="font-medium text-gray-800">{{ $project->construction_progress }}%</span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-800 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <button onclick="viewUnits({{ $project->id }})" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            <i class="fas fa-home mr-2"></i>
                            View Units
                        </button>
                        <button onclick="viewPhases({{ $project->id }})" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                            <i class="fas fa-layer-group mr-2"></i>
                            View Phases
                        </button>
                        <button onclick="viewBimModels({{ $project->id }})" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                            <i class="fas fa-cube mr-2"></i>
                            BIM Models
                        </button>
                        <button onclick="addUpdate({{ $project->id }})" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                            <i class="fas fa-plus mr-2"></i>
                            Add Update
                        </button>
                    </div>
                </div>
                
                <!-- Team Members -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="font-medium text-gray-800 mb-4">Project Team</h3>
                    <div class="space-y-3">
                        @foreach ($project->team_members as $member)
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
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Phases -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Project Phases</h2>
                <a href="{{ route('developer.phases') }}?project={{ $project->id }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($project->phases as $phase)
                    <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-gray-800">{{ $phase->name }}</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($phase->status === 'completed')
                                    bg-green-100 text-green-800
                                @elseif($phase->status === 'in_progress')
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-gray-100 text-gray-800
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $phase->status)) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ $phase->description }}</p>
                        <div class="mb-2">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Progress</span>
                                <span class="font-medium text-gray-800">{{ $phase->progress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $phase->progress }}%"></div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $phase->start_date }} - {{ $phase->end_date }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Updates -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Updates</h2>
                <a href="{{ route('developer.construction-updates') }}?project={{ $project->id }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            
            <div class="space-y-3">
                @forelse ($recentUpdates as $update)
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                            <i class="fas fa-hard-hat text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-800 mb-1">{{ $update->title }}</h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $update->content }}</p>
                            <div class="flex items-center text-xs text-gray-500">
                                <span>{{ $update->created_at->format('M j, Y') }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $update->phase }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No recent updates</p>
                @endforelse
            </div>
        </div>

        <!-- Units Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Units Overview</h2>
                <a href="{{ route('developer.units') }}?project={{ $project->id }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-800">{{ $project->units_count }}</div>
                    <div class="text-sm text-gray-600">Total Units</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">{{ $project->units_sold }}</div>
                    <div class="text-sm text-gray-600">Sold</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600">{{ $project->units_reserved }}</div>
                    <div class="text-sm text-gray-600">Reserved</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ $project->units_available }}</div>
                    <div class="text-sm text-gray-600">Available</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editProject(projectId) {
    window.location.href = '/developer/projects/' + projectId + '/edit';
}

function viewUnits(projectId) {
    window.location.href = '/developer/units?project=' + projectId;
}

function viewPhases(projectId) {
    window.location.href = '/developer/phases?project=' + projectId;
}

function viewBimModels(projectId) {
    window.location.href = '/developer/bim-models?project=' + projectId;
}

function addUpdate(projectId) {
    window.location.href = '/developer/construction-updates/create?project=' + projectId;
}
</script>
@endsection
