@extends('layouts.app')

@section('title', 'BIM Models')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">BIM Models</h1>
                    <p class="text-gray-600">Manage 3D Building Information Models</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="uploadModel()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Model
                    </button>
                    <a href="{{ route('developer.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Models Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-cube text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Models</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $models->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Published</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $models->where('status', 'published')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-database text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Size</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalSize }} GB</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-eye text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Views</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalViews }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <input type="text" placeholder="Search models..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-64">
                    <select onchange="filterByProject(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Types</option>
                        <option>Architectural</option>
                        <option>Structural</option>
                        <option>Mechanical</option>
                        <option>Electrical</option>
                        <option>Plumbing</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Status</option>
                        <option>Draft</option>
                        <option>Review</option>
                        <option>Published</option>
                        <option>Archived</option>
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

        <!-- Models Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($models as $model)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Model Preview -->
                    <div class="relative h-48 bg-gray-200">
                        @if($model->preview_image)
                            <img src="{{ $model->preview_image }}" alt="{{ $model->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-cube text-gray-400 text-4xl"></i>
                            </div>
                        @endif
                        
                        <!-- Status Badge -->
                        <div class="absolute top-2 left-2">
                            <span class="bg-{{ $model->status === 'published' ? 'green' : ($model->status === 'review' ? 'yellow' : ($model->status === 'archived' ? 'gray' : 'blue')) }}-500 text-white px-2 py-1 rounded text-xs">
                                {{ ucfirst($model->status) }}
                            </span>
                        </div>
                        
                        <!-- 3D Badge -->
                        @if($model->is_3d)
                            <div class="absolute top-2 right-2">
                                <span class="bg-purple-500 text-white px-2 py-1 rounded text-xs">
                                    <i class="fas fa-cube mr-1"></i>3D
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Model Details -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $model->name }}</h3>
                        <p class="text-gray-600 mb-3">{{ Str::limit($model->description, 80) }}</p>
                        <p class="text-gray-600 text-sm mb-3">
                            <i class="fas fa-building mr-1"></i>{{ $model->project->name }}
                        </p>
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                            <span><i class="fas fa-layer-group mr-1"></i>{{ $model->model_type }}</span>
                            <span><i class="fas fa-database mr-1"></i>{{ $model->file_size }} MB</span>
                            <span><i class="fas fa-eye mr-1"></i>{{ $model->views }}</span>
                        </div>
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Completion</span>
                                <span class="font-medium text-gray-800">{{ $model->completion_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $model->completion_percentage }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Model Info -->
                        <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-4">
                            <div>
                                <i class="fas fa-calendar mr-1"></i>
                                Updated {{ $model->updated_at->format('M j') }}
                            </div>
                            <div>
                                <i class="fas fa-user mr-1"></i>
                                {{ $model->created_by->name }}
                            </div>
                        </div>
                        
                        <!-- Version Info -->
                        @if($model->latest_version)
                            <div class="mb-4 p-3 bg-gray-50 rounded">
                                <p class="text-sm font-medium text-gray-800">Latest Version</p>
                                <p class="text-sm text-gray-600">v{{ $model->latest_version }} - {{ $model->version_date }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Model Actions -->
                    <div class="p-6 border-t bg-gray-50">
                        <div class="flex space-x-2">
                            @if($model->is_3d)
                                <button onclick="view3DModel({{ $model->id }})" class="flex-1 bg-purple-600 text-white px-3 py-2 rounded hover:bg-purple-700 transition-colors text-sm">
                                    View 3D
                                </button>
                            @else
                                <button onclick="viewModel({{ $model->id }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                    View Details
                                </button>
                            @endif
                            <button onclick="downloadModel({{ $model->id }})" class="flex-1 border border-gray-300 text-gray-700 px-3 py-2 rounded hover:bg-gray-50 transition-colors text-sm">
                                Download
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-cube text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No BIM Models Yet</h3>
                    <p class="text-gray-500 mb-6">Start by uploading your first building model.</p>
                    <button onclick="uploadModel()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>
                        Upload First Model
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($models->hasPages())
            <div class="mt-6">
                {{ $models->links() }}
            </div>
        @endif

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Model Activity</h2>
            
            <div class="space-y-3">
                @forelse ($recentActivity as $activity)
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
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

<!-- Upload Model Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Upload BIM Model</h3>
        
        <form action="{{ route('developer.bim-models.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Model Name</label>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Model Type</label>
                        <select name="model_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Type</option>
                            <option value="architectural">Architectural</option>
                            <option value="structural">Structural</option>
                            <option value="mechanical">Mechanical</option>
                            <option value="electrical">Electrical</option>
                            <option value="plumbing">Plumbing</option>
                            <option value="combined">Combined</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="draft">Draft</option>
                            <option value="review">Review</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Model File</label>
                    <input type="file" name="model_file" accept=".rvt,.ifc,.dwg,.skp,.3dm" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview Image</label>
                    <input type="file" name="preview_image" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Completion Percentage</label>
                    <input type="number" name="completion_percentage" min="0" max="100" value="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Version</label>
                    <input type="text" name="version" placeholder="e.g., 1.0.0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_3d" class="mr-2">
                    <label class="text-sm text-gray-700">3D Model (Interactive)</label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeUploadModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Upload Model
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function uploadModel() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
}

function filterByProject(projectId) {
    window.location.href = '?project=' + projectId;
}

function viewModel(modelId) {
    window.location.href = '/developer/bim-models/' + modelId;
}

function view3DModel(modelId) {
    window.location.href = '/developer/bim-models/' + modelId + '/3d';
}

function downloadModel(modelId) {
    window.location.href = '/developer/bim-models/' + modelId + '/download';
}
</script>
@endsection
