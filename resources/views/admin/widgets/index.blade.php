@extends('layouts.app')

@section('title', 'Widget Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Widget Management</h1>
                    <p class="text-gray-600">Manage website widgets and components</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="toggleBulkActions()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-tasks mr-2"></i>
                        Bulk Actions
                    </button>
                    <a href="{{ route('admin.widgets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        New Widget
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" placeholder="Search widgets..." value="{{ request('search') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="text" {{ request('type') == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Image</option>
                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="form" {{ request('type') == 'form' ? 'selected' : '' }}>Form</option>
                        <option value="social" {{ request('type') == 'social' ? 'selected' : '' }}>Social Media</option>
                        <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                    <select name="position" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Positions</option>
                        <option value="header" {{ request('position') == 'header' ? 'selected' : '' }}>Header</option>
                        <option value="sidebar" {{ request('position') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                        <option value="content" {{ request('position') == 'content' ? 'selected' : '' }}>Content</option>
                        <option value="footer" {{ request('position') == 'footer' ? 'selected' : '' }}>Footer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulk-actions" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-blue-800 font-medium">
                        <span id="selected-count">0</span> widgets selected
                    </span>
                </div>
                <div class="flex space-x-2">
                    <button onclick="bulkActivate()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Activate
                    </button>
                    <button onclick="bulkDeactivate()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-pause mr-2"></i>
                        Deactivate
                    </button>
                    <button onclick="bulkDelete()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Delete
                    </button>
                    <button onclick="clearSelection()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>

        <!-- Widgets Grid -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($widgets ?? [] as $widget)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:border-blue-400 transition-colors">
                        <!-- Widget Header -->
                        <div class="p-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas {{ $widget->type === 'text' ? 'fa-font text-blue-600' : ($widget->type === 'image' ? 'fa-image text-green-600' : ($widget->type === 'video' ? 'fa-video text-red-600' : ($widget->type === 'form' ? 'fa-wpforms text-purple-600' : ($widget->type === 'social' ? 'fa-share-alt text-yellow-600' : 'fa-cube text-gray-600')))) }} text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $widget->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $widget->type }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" class="widget-checkbox" value="{{ $widget->id }}" 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <button onclick="toggleWidget({{ $widget->id }})" class="p-1 rounded hover:bg-gray-200">
                                        <i class="fas {{ $widget->is_active ? 'fa-eye text-blue-600' : 'fa-eye-slash text-gray-400' }}"></i>
                                    </button>
                                    <a href="{{ route('admin.widgets.edit', $widget) }}" class="text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="duplicateWidget({{ $widget->id }})" class="text-purple-600 hover:text-purple-800">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button onclick="deleteWidget({{ $widget->id }})" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Widget Content Preview -->
                        <div class="p-4">
                            @if ($widget->type === 'text')
                                <div class="text-sm text-gray-600">
                                    {{ Str::limit($widget->content ?? 'No content', 100) }}
                                </div>
                            @elseif ($widget->type === 'image')
                                <div class="text-center">
                                    @if ($widget->image_url)
                                        <img src="{{ $widget->image_url }}" alt="{{ $widget->name }}" 
                                            class="max-w-full h-32 object-cover rounded">
                                    @else
                                        <div class="h-32 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-2xl"></i>
                                        </div>
                                    @endif
                                </div>
                            @elseif ($widget->type === 'form')
                                <div class="text-center">
                                    <i class="fas fa-wpforms text-gray-400 text-3xl mb-2"></i>
                                    <p class="text-sm text-gray-600">{{ $widget->form_type ?? 'Contact' }} Form</p>
                                </div>
                            @else
                                <div class="text-center">
                                    <i class="fas fa-cube text-gray-400 text-3xl mb-2"></i>
                                    <p class="text-sm text-gray-600">Custom Widget</p>
                                </div>
                            @endif
                        </div>

                        <!-- Widget Footer -->
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-4">
                                    <span class="text-gray-500">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $widget->position }}
                                    </span>
                                    <span class="text-gray-500">
                                        <i class="fas fa-layer-group mr-1"></i>
                                        Order: {{ $widget->sort_order }}
                                    </span>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $widget->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $widget->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-cube text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No widgets found</h3>
                        <p class="text-gray-600 mb-4">Get started by creating your first widget.</p>
                        <a href="{{ route('admin.widgets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Add Widget
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if (isset($widgets) && $widgets->hasPages())
                <div class="mt-8">
                    {{ $widgets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Widget Preview Modal -->
<div id="preview-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Widget Preview</h3>
            <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="preview-content" class="border border-gray-200 rounded-lg p-4">
            <!-- Preview content will be loaded here -->
        </div>
        <div class="mt-4 flex justify-end">
            <button onclick="closePreviewModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function toggleBulkActions() {
    const bulkActions = document.getElementById('bulk-actions');
    bulkActions.classList.toggle('hidden');
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.widget-checkbox:checked');
    document.getElementById('selected-count').textContent = checkboxes.length;
}

function clearSelection() {
    document.querySelectorAll('.widget-checkbox').forEach(cb => cb.checked = false);
    updateSelectedCount();
}

function bulkActivate() {
    const selected = Array.from(document.querySelectorAll('.widget-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select widgets to activate');
        return;
    }
    
    fetch('{{ route('admin.widgets.bulk-toggle') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ widgets: selected, action: 'activate' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error activating widgets');
    });
}

function bulkDeactivate() {
    const selected = Array.from(document.querySelectorAll('.widget-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select widgets to deactivate');
        return;
    }
    
    fetch('{{ route('admin.widgets.bulk-toggle') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ widgets: selected, action: 'deactivate' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deactivating widgets');
    });
}

function bulkDelete() {
    const selected = Array.from(document.querySelectorAll('.widget-checkbox:checked')).map(cb => cb.value);
    if (selected.length === 0) {
        alert('Please select widgets to delete');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selected.length} widget(s)?`)) {
        fetch('{{ route('admin.widgets.bulk-delete') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ widgets: selected })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting widgets');
        });
    }
}

function toggleWidget(id) {
    fetch(`/admin/widgets/${id}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        alert('Error toggling widget');
    });
}

function duplicateWidget(id) {
    if (confirm('Are you sure you want to duplicate this widget?')) {
        fetch(`/admin/widgets/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            alert('Error duplicating widget');
        });
    }
}

function deleteWidget(id) {
    if (confirm('Are you sure you want to delete this widget?')) {
        fetch(`/admin/widgets/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
            alert('Error deleting widget');
        });
    }
}

function closePreviewModal() {
    document.getElementById('preview-modal').classList.add('hidden');
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.widget-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
});
</script>
@endsection
