@extends('layouts.app')

@section('title', 'Create Menu')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('admin.menus.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Menus
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Create New Menu</h1>
            </div>
            <p class="text-gray-600">Create a new navigation menu for your website</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('admin.menus.store') }}" method="POST">
                @csrf
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Menu Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., Main Navigation"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., main-navigation"
                                   required>
                            <p class="mt-1 text-sm text-gray-500">Unique identifier for the menu (auto-generated from name if empty)</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                Location <span class="text-red-500">*</span>
                            </label>
                            <select id="location" 
                                    name="location" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">Select a location</option>
                                <option value="header" {{ old('location') == 'header' ? 'selected' : '' }}>Header</option>
                                <option value="footer" {{ old('location') == 'footer' ? 'selected' : '' }}>Footer</option>
                                <option value="sidebar" {{ old('location') == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                                <option value="mobile" {{ old('location') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Where this menu will be displayed</p>
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', '1') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 text-sm text-gray-700">
                                    Active
                                </label>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Enable or disable this menu</p>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Description</h3>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Optional description for this menu">{{ old('description') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Optional description to help identify this menu</p>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between">
                    <a href="{{ route('admin.menus.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Create Menu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    
    const slugField = document.getElementById('slug');
    if (!slugField.value) {
        slugField.value = slug;
    }
});
</script>
@endsection
