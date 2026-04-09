@extends('layouts.app')

@section('title', 'Blog Categories Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Blog Categories</h1>
                    <p class="text-gray-600">Manage blog post categories</p>
                </div>
                <a href="{{ route('admin.blog.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    New Category
                </a>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($categories ?? [] as $category)
                <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            @if ($category->icon)
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="{{ $category->icon }} text-blue-600 text-xl"></i>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-folder text-gray-600 text-xl"></i>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $category->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $category->slug }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-1">
                            <a href="{{ route('admin.blog.categories.edit', $category) }}" class="text-gray-600 hover:text-gray-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.blog.categories.destroy', $category) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if ($category->description)
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($category->description, 100) }}</p>
                    @endif

                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-500">
                                <i class="fas fa-file-alt mr-1"></i>
                                {{ $category->posts_count ?? 0 }} posts
                            </span>
                            @if ($category->parent)
                                <span class="text-gray-500">
                                    <i class="fas fa-level-up-alt mr-1"></i>
                                    {{ $category->parent->name }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center">
                            @if ($category->is_active)
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

                    @if ($category->children && $category->children->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs font-medium text-gray-500 mb-2">Subcategories:</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($category->children as $child)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                        {{ $child->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-folder text-gray-400 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
                    <p class="text-gray-600 mb-4">Get started by creating your first category.</p>
                    <a href="{{ route('admin.blog.categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Category
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Categories Table (Alternative View) -->
        @if (false) <!-- Set to true to enable table view -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mt-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posts</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($categories ?? [] as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if ($category->icon)
                                                <i class="{{ $category->icon }} text-blue-600 mr-2"></i>
                                            @endif
                                            <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->slug }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->posts_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.blog.categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        <form action="{{ route('admin.blog.categories.destroy', $category) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No categories found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
