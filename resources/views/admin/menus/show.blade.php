@extends('layouts.app')

@section('title', 'Menu Details - ' . $menu->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <a href="{{ route('admin.menus.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Menus
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $menu->name }}</h1>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.menus.builder', $menu) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Menu
                    </a>
                    <a href="{{ route('admin.menus.edit', $menu) }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                        <i class="fas fa-cog mr-2"></i>
                        Settings
                    </a>
                </div>
            </div>
            
            <div class="flex items-center space-x-4 text-sm text-gray-600">
                <span class="flex items-center">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    {{ ucfirst($menu->location) }} Menu
                </span>
                <span class="flex items-center">
                    <i class="fas fa-circle mr-2 {{ $menu->is_active ? 'text-green-500' : 'text-red-500' }}"></i>
                    {{ $menu->is_active ? 'Active' : 'Inactive' }}
                </span>
                <span class="flex items-center">
                    <i class="fas fa-list mr-2"></i>
                    {{ $menu->menuItems->count() }} items
                </span>
            </div>
        </div>

        <!-- Menu Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Basic Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Menu Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Name</label>
                            <p class="text-gray-800">{{ $menu->name }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Slug</label>
                            <p class="text-gray-800 font-mono text-sm">{{ $menu->slug }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Location</label>
                            <p class="text-gray-800">{{ ucfirst($menu->location) }}</p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $menu->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        
                        @if ($menu->description)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Description</label>
                            <p class="text-gray-800">{{ $menu->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">Menu Items</h3>
                        <a href="{{ route('admin.menus.builder', $menu) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Add Items
                        </a>
                    </div>
                    
                    @if ($menu->menuItems->count() > 0)
                        <div class="space-y-2">
                            @foreach ($menu->menuItems as $item)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        @if ($item->icon)
                                            <i class="{{ $item->icon }} text-gray-600"></i>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $item->title }}</p>
                                            <p class="text-sm text-gray-500">{{ $item->url }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500">Order: {{ $item->sort_order }}</span>
                                        @if ($item->target == '_blank')
                                            <i class="fas fa-external-link-alt text-gray-400"></i>
                                        @endif
                                        @if (!$item->is_active)
                                            <i class="fas fa-eye-slash text-red-400"></i>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-list-ul text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 mb-4">No menu items found</p>
                            <a href="{{ route('admin.menus.builder', $menu) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Add First Item
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between">
                <div class="flex space-x-2">
                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this menu and all its items? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Menu
                        </button>
                    </form>
                </div>
                
                <div class="flex space-x-2">
                    <a href="{{ route('admin.menus.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Menus
                    </a>
                    <a href="{{ route('admin.menus.builder', $menu) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Menu Items
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
