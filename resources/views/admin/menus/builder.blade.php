@extends('layouts.app')

@section('title', 'Menu Builder - ' . $menu->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <a href="{{ route('admin.menus.show', $menu) }}" class="text-gray-600 hover:text-gray-800 mr-4">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Menu
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">Menu Builder</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-list mr-2"></i>
                        {{ $menu->menuItems->count() }} items
                    </span>
                    <button type="button" 
                            onclick="document.getElementById('addItemForm').classList.toggle('hidden')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Item
                    </button>
                </div>
            </div>
            
            <div class="text-sm text-gray-600">
                Building menu: <strong>{{ $menu->name }}</strong> ({{ ucfirst($menu->location) }})
            </div>
        </div>

        <!-- Add Item Form -->
        <div id="addItemForm" class="bg-white rounded-lg shadow-sm p-6 mb-6 hidden">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Add Menu Item</h3>
            
            <form action="{{ route('admin.menus.add_item', $menu) }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Menu item title"
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- URL -->
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
                            URL <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="url" 
                               name="url" 
                               value="{{ old('url') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="/page-url or https://example.com"
                               required>
                        @error('url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Parent -->
                    <div>
                        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Parent Item
                        </label>
                        <select id="parent_id" 
                                name="parent_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">None (Root Level)</option>
                            @foreach ($menu->menuItems as $item)
                                <option value="{{ $item->id }}" {{ old('parent_id') == $item->id ? 'selected' : '' }}>
                                    {{ str_repeat('— ', $item->depth ?? 0) }} {{ $item->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Order
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', $menu->menuItems->max('sort_order') + 1) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               min="0">
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target -->
                    <div>
                        <label for="target" class="block text-sm font-medium text-gray-700 mb-2">
                            Target
                        </label>
                        <select id="target" 
                                name="target" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="_self" {{ old('target', '_self') == '_self' ? 'selected' : '' }}>Same window</option>
                            <option value="_blank" {{ old('target') == '_blank' ? 'selected' : '' }}>New window</option>
                            <option value="_parent" {{ old('target') == '_parent' ? 'selected' : '' }}>Parent frame</option>
                            <option value="_top" {{ old('target') == '_top' ? 'selected' : '' }}>Top frame</option>
                        </select>
                        @error('target')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Icon -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                            Icon Class
                        </label>
                        <input type="text" 
                               id="icon" 
                               name="icon" 
                               value="{{ old('icon') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="fas fa-home">
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               {{ old('is_active', '1') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                            Active
                        </label>
                    </label>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-2">
                    <button type="button" 
                            onclick="document.getElementById('addItemForm').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Add Item
                    </button>
                </div>
            </form>
        </div>

        <!-- Menu Items List -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Menu Items</h3>
                <div class="text-sm text-gray-500">
                    Drag and drop to reorder
                </div>
            </div>
            
            @if ($menu->menuItems->count() > 0)
                <div id="menuItemsList" class="space-y-2">
                    @foreach ($menu->menuItems as $item)
                        <div class="menu-item flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-move"
                             data-id="{{ $item->id }}"
                             data-parent-id="{{ $item->parent_id }}"
                             data-order="{{ $item->sort_order }}">
                            
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-grip-vertical text-gray-400"></i>
                                
                                @if ($item->icon)
                                    <i class="{{ $item->icon }} text-gray-600"></i>
                                @endif
                                
                                <div>
                                    <p class="font-medium text-gray-800">{{ $item->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->url }}</p>
                                    
                                    @if ($item->parent_id)
                                        <p class="text-xs text-blue-600">Child of: {{ $menu->menuItems->where('id', $item->parent_id)->first()->title ?? 'Unknown' }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500">Order: {{ $item->sort_order }}</span>
                                
                                @if ($item->target == '_blank')
                                    <i class="fas fa-external-link-alt text-gray-400" title="Opens in new window"></i>
                                @endif
                                
                                @if (!$item->is_active)
                                    <i class="fas fa-eye-slash text-red-400" title="Inactive"></i>
                                @endif
                                
                                <form action="{{ route('admin.menus.delete_item', $item) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800"
                                            onclick="return confirm('Are you sure you want to delete this menu item?')"
                                            title="Delete item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <button type="button" 
                            onclick="saveOrder()"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Order
                    </button>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-list-ul text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 mb-4">No menu items found</p>
                    <button type="button" 
                            onclick="document.getElementById('addItemForm').classList.remove('hidden')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add First Item
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Drag and drop functionality
let draggedElement = null;

document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
        item.addEventListener('dragover', handleDragOver);
        item.addEventListener('drop', handleDrop);
    });
});

function handleDragStart(e) {
    draggedElement = this;
    this.style.opacity = '0.4';
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);
}

function handleDragEnd(e) {
    this.style.opacity = '1';
    
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.style.backgroundColor = '';
    });
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    
    e.dataTransfer.dropEffect = 'move';
    this.style.backgroundColor = '#f3f4f6';
    
    return false;
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    if (draggedElement !== this) {
        const container = document.getElementById('menuItemsList');
        const allItems = Array.from(container.children);
        const draggedIndex = allItems.indexOf(draggedElement);
        const targetIndex = allItems.indexOf(this);
        
        if (draggedIndex < targetIndex) {
            this.parentNode.insertBefore(draggedElement, this.nextSibling);
        } else {
            this.parentNode.insertBefore(draggedElement, this);
        }
    }
    
    this.style.backgroundColor = '';
    return false;
}

function saveOrder() {
    const items = document.querySelectorAll('.menu-item');
    const orderData = [];
    
    items.forEach((item, index) => {
        orderData.push({
            id: item.dataset.id,
            parent_id: item.dataset.parent_id || null,
            sort_order: index
        });
    });
    
    fetch('{{ route('admin.menus.reorder_items', $menu) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            items: orderData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error saving order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving order');
    });
}
</script>
@endsection
