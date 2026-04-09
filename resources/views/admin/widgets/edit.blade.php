@extends('layouts.app')

@section('title', 'Edit Widget')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('admin.widgets.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Widget</h1>
                        <p class="text-gray-600">Update widget information</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="previewWidget()" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        Preview
                    </button>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <strong>✅ Success:</strong> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>❌ Error:</strong> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>⚠️ Please fix the following errors:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Edit Form -->
        <form action="{{ route('admin.widgets.update', $widget) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Widget Title *</label>
                        <input type="text" name="title" value="{{ old('title', $widget->title) }}" 
                            class="w-full px-3 py-2 border @error('title') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                            placeholder="Enter widget title..." required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Widget Slug *</label>
                        <input type="text" name="slug" value="{{ old('slug', $widget->slug) }}" 
                            class="w-full px-3 py-2 border @error('slug') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="widget-slug" required>
                        <p class="mt-1 text-sm text-gray-500">This will be used in the URL: /widgets/your-slug</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Widget Content</label>
                        <textarea name="content" rows="10" 
                            class="w-full px-3 py-2 border @error('content') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter widget content...">{{ old('content', $widget->content) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">HTML or text content for the widget</p>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Configuration -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Widget Configuration (Optional)</label>
                        <textarea name="config" rows="6" 
                            class="w-full px-3 py-2 border @error('config') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                            placeholder='{"key": "value"}'>{{ old('config', is_array($widget->config) ? json_encode($widget->config, JSON_PRETTY_PRINT) : $widget->config) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">JSON configuration for the widget (optional). Leave empty if not needed.</p>
                        <p class="mt-1 text-xs text-gray-400">Example: {"color": "blue", "size": "large"}</p>
                        @error('config')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Widget Settings -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Widget Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Widget Type *</label>
                                <select name="type" class="w-full px-3 py-2 border @error('type') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Select widget type</option>
                                    @foreach($widgetTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('type', $widget->type) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                                <select name="location" class="w-full px-3 py-2 border @error('location') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Select location</option>
                                    @foreach($positions as $key => $value)
                                        <option value="{{ $key }}" {{ old('location', $widget->location) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                @error('location')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $widget->sort_order) }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="0" min="0">
                                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                                @error('sort_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $widget->is_active) ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                                <input type="hidden" name="is_active" value="0">
                                <p class="mt-1 text-sm text-gray-500">Uncheck to disable this widget</p>
                            </div>
                        </div>
                    </div>

                    <!-- Widget Info -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Widget Information</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">ID:</span>
                                <span class="font-medium">{{ $widget->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Created:</span>
                                <span class="font-medium">{{ $widget->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Updated:</span>
                                <span class="font-medium">{{ $widget->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.widgets.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Widget
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewWidget() {
    alert('Preview functionality coming soon!');
}
</script>
@endsection
