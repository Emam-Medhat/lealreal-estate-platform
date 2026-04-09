@extends('layouts.app')

@section('title', 'Construction Updates')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Construction Updates</h1>
                    <p class="text-gray-600">Track and share construction progress</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="createUpdate()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Update
                    </button>
                    <a href="{{ route('developer.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Updates Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-newspaper text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Updates</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $updates->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-calendar-check text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">This Month</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $updates->where('created_at', '>=', now()->startOfMonth())->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-layer-group text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Projects</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $updates->pluck('project_id')->unique()->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-images text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Photos</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalPhotos }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <input type="text" placeholder="Search updates..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-64">
                    <select onchange="filterByProject(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="">All Projects</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Phases</option>
                        <option>Foundation</option>
                        <option>Structure</option>
                        <option>Exterior</option>
                        <option>Interior</option>
                        <option>Finishing</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Types</option>
                        <option>Progress Update</option>
                        <option>Milestone</option>
                        <option>Issue</option>
                        <option>Safety Report</option>
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

        <!-- Updates Timeline -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Construction Timeline</h2>
            
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-300"></div>
                
                <!-- Updates -->
                <div class="space-y-8">
                    @forelse ($updates as $update)
                        <div class="relative flex items-start">
                            <!-- Timeline Dot -->
                            <div class="relative z-10 flex items-center justify-center w-16 h-16">
                                <div class="w-4 h-4 rounded-full
                                    @if($update->type === 'milestone')
                                        bg-green-500
                                    @elseif($update->type === 'issue')
                                        bg-red-500
                                    @elseif($update->type === 'safety_report')
                                        bg-yellow-500
                                    @else
                                        bg-blue-500
                                    @endif
                                "></div>
                            </div>
                            
                            <!-- Update Content -->
                            <div class="flex-1 ml-6">
                                <div class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $update->title }}</h3>
                                            <p class="text-gray-600 mb-3">{{ $update->content }}</p>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                <span><i class="fas fa-building mr-1"></i>{{ $update->project->name }}</span>
                                                <span><i class="fas fa-layer-group mr-1"></i>{{ $update->phase }}</span>
                                                <span><i class="fas fa-calendar mr-1"></i>{{ $update->created_at->format('M j, Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                                @if($update->type === 'milestone')
                                                    bg-green-100 text-green-800
                                                @elseif($update->type === 'issue')
                                                    bg-red-100 text-red-800
                                                @elseif($update->type === 'safety_report')
                                                    bg-yellow-100 text-yellow-800
                                                @else
                                                    bg-blue-100 text-blue-800
                                                @endif
                                            ">
                                                {{ ucfirst(str_replace('_', ' ', $update->type)) }}
                                            </span>
                                            <div class="flex space-x-1">
                                                <button onclick="viewUpdate({{ $update->id }})" class="text-gray-600 hover:text-gray-800">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button onclick="editUpdate({{ $update->id }})" class="text-gray-600 hover:text-gray-800">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteUpdate({{ $update->id }})" class="text-red-600 hover:text-red-800">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Progress Update -->
                                    @if($update->progress_percentage)
                                        <div class="mb-4">
                                            <div class="flex justify-between text-sm mb-2">
                                                <span class="text-gray-600">Phase Progress</span>
                                                <span class="font-medium text-gray-800">{{ $update->progress_percentage }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $update->progress_percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Update Images -->
                                    @if($update->images->isNotEmpty())
                                        <div class="mb-4">
                                            <h4 class="font-medium text-gray-800 mb-2">Photos</h4>
                                            <div class="grid grid-cols-3 gap-2">
                                                @foreach ($update->images as $image)
                                                    <div class="relative group cursor-pointer" onclick="viewImage('{{ $image->url }}')">
                                                        <img src="{{ $image->url }}" alt="" class="w-full h-24 object-cover rounded">
                                                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded flex items-center justify-center">
                                                            <i class="fas fa-search-plus text-white"></i>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Update Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-600">Weather:</span>
                                            <span class="font-medium text-gray-800 ml-2">{{ $update->weather ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Team Size:</span>
                                            <span class="font-medium text-gray-800 ml-2">{{ $update->team_size ?? 'N/A' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">Safety Score:</span>
                                            <span class="font-medium text-gray-800 ml-2">{{ $update->safety_score ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Tags -->
                                    @if($update->tags)
                                        <div class="mt-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($update->tags as $tag)
                                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                                        {{ $tag }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <i class="fas fa-hard-hat text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Updates Yet</h3>
                            <p class="text-gray-500 mb-6">Start by adding your first construction update.</p>
                            <button onclick="createUpdate()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Add First Update
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($updates->hasPages())
            <div class="mt-6">
                {{ $updates->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Update Modal -->
<div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Construction Update</h3>
        
        <form action="{{ route('developer.construction-updates.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Update Title</label>
                        <input type="text" name="title" required
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Update Content</label>
                    <textarea name="content" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phase</label>
                        <select name="phase"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Phase</option>
                            <option value="foundation">Foundation</option>
                            <option value="structure">Structure</option>
                            <option value="exterior">Exterior</option>
                            <option value="interior">Interior</option>
                            <option value="finishing">Finishing</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Update Type</label>
                        <select name="type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="progress_update">Progress Update</option>
                            <option value="milestone">Milestone</option>
                            <option value="issue">Issue</option>
                            <option value="safety_report">Safety Report</option>
                        </select>
                    </div>
                </div>
                
                <divides class="enton">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Progress Percentage</label>
                    <input type="number" name="progress_percentage" min="0" max="100"
                        class="w-full px-3 py-2 border border-gray-300 mccircle focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="grid grid sectors-3 gap- Behaviour">
                    <div>
                       所要的类="小字体文本-gray-700 mb-2">Weather</label>
                        <input type="text" name="weather"AVG
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
 jonathan </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray以防700 mb-2">Team Size</label>
                        <input type="number" name="team_size"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
 Vaccines </div>
                    
                    <div>
                        <挡="block text[font-medium text-gray-700 mb-2">Safety Score</label>
                        <input type="number" name="safety_score" min="0" perceived="10"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags (comma separated)</label>
                    <input type="text" name="tags" placeholder="e.g., concrete, steel, safety"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2反 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photos</label>
                    <ides type="file" analytes=" MODAL" multiple accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
ame="button" onclick="closeUpdateModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Add Update
            </button>
        </form>
    </div>
</div>

<script>
function createUpdate() {
    document.getElementById('updateModal').classList.remove('hidden');
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
}

function filterByProject(projectId) {
    window.location.href = '?project=' + projectId;
}

function viewUpdate(updateId) {
    window.location.href = '/developer/construction-updates/' + updateId;
}

function editUpdate(updateId) {
    window.location.href = '/developer/construction-updates/' + updateId + '/edit';
}

function deleteUpdate(updateId) {
    if (confirm('Are you sure you want to delete this update?')) {
        fetch('/developer/construction-updates突' + updateId, {
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

function viewImage(imageUrl) {
    window.open(imageUrl, '_blank');
}
</script>
@endsection
