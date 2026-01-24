@extends('layouts.app')

@section('title', 'Menu Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Menu Management</h1>
                    <p class="text-gray-600">Manage navigation menus</p>
                </div>
                <a href="{{ route('admin.menus.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    New Menu
                </a>
            </div>
        </div>

        <!-- Menus Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($menus ?? [] as $menu)
                <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $menu->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $menu->location }} menu</p>
                        </div>
                        <div class="flex space-x-1">
                            <a href="{{ route('admin.menus.builder', $menu) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if ($menu->description)
                        <p class="text-gray-600 text-sm mb-4">{{ $menu->description }}</p>
                    @endif

                    <div class="flex items-center justify-between text-sm mb-4">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-500">
                                <i class="fas fa-list mr-1"></i>
                                {{ $menu->items_count ?? 0 }} items
                            </span>
                            @if ($menu->is_active)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Menu Items Preview -->
                    @if ($menu->items && $menu->items->isNotEmpty())
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-xs font-medium text-gray-500 mb-2">Menu Items:</p>
                            <div class="space-y-1">
                                @foreach ($menu->items->take(3) as $item)
                                    <div class="flex items-center text-sm text-gray-600">
                                        @if ($item->depth > 0)
                                            @for ($i = 0; $i < $item->depth; $i++)
                                                <span class="mr-2">—</span>
                                            @endfor
                                        @endif
                                        <span>{{ $item->label }}</span>
                                    </div>
                                @endforeach
                                @if ($menu->items->count() > 3)
                                    <p class="text-xs text-gray-500">...and {{ $menu->items->count() - 3 }} more</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between">
                            <a href="{{ route('admin.menus.show', $menu) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-eye mr-1"></i>
                                View Details
                            </a>
                            <a href="{{ route('admin.menus.builder', $menu) }}" class="text-purple-600 hover:text-purple-800 text-sm">
                                <i class="fas fa-tools mr-1"></i>
                                Edit Menu
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-bars text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No menus found</h3>
                    <p class="text-gray-600 mb-4">Get started by creating your first navigation menu.</p>
                    <a href="{{ route('admin.menus.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Menu
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Menu Locations Info -->
        <div class="mt-8 bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-4">Available Menu Locations</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Header Navigation</h4>
                    <p class="text-sm text-gray-600">Main navigation menu displayed in the header</p>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded mt-2">header</code>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Footer Navigation</h4>
                    <p class="text-sm text-gray-600">Navigation links displayed in the footer</p>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded mt-2">footer</code>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 mb-2">Sidebar Navigation</h4>
                    <p class="text-sm text-gray-600">Secondary navigation for dashboard areas</p>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded mt-2">sidebar</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
