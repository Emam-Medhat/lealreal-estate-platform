@extends('layouts.app')

@section('title', 'Edit Guide: ' . $guide->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Guide</h1>
                    <p class="text-gray-600">Update guide information</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.guides.show', $guide) }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Guide
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('admin.guides.update', $guide) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                            <input type="text" id="title" name="title" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ old('title', $guide->title) }}">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                            <input type="text" id="slug" name="slug" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ old('slug', $guide->slug) }}">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <input type="text" id="category" name="category" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ old('category', $guide->category) }}">
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-2">Difficulty *</label>
                            <select id="difficulty" name="difficulty" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Difficulty</option>
                                <option value="مبتدئ" {{ old('difficulty', $guide->difficulty) == 'مبتدئ' ? 'selected' : '' }}>مبتدئ</option>
                                <option value="متوسط" {{ old('difficulty', $guide->difficulty) == 'متوسط' ? 'selected' : '' }}>متوسط</option>
                                <option value="متقدم" {{ old('difficulty', $guide->difficulty) == 'متقدم' ? 'selected' : '' }}>متقدم</option>
                            </select>
                            @error('difficulty')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estimated_time" class="block text-sm font-medium text-gray-700 mb-2">Estimated Time (minutes)</label>
                            <input type="number" id="estimated_time" name="estimated_time" min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ old('estimated_time', $guide->estimated_time) }}">
                            @error('estimated_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                        <textarea id="excerpt" name="excerpt" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('excerpt', $guide->excerpt) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Brief description of the guide (max 500 characters)</p>
                        @error('excerpt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                    </div>
                </div>

                <!-- Content -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Content</h3>
                    
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                        <textarea id="content" name="content" rows="15"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('content', $guide->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Additional Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="prerequisites" class="block text-sm font-medium text-gray-700 mb-2">Prerequisites</label>
                            <textarea id="prerequisites" name="prerequisites" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('prerequisites', $guide->prerequisites) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">What users should know before starting</p>
                            @error('prerequisites')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="learning_objectives" class="block text-sm font-medium text-gray-700 mb-2">Learning Objectives</label>
                            <textarea id="learning_objectives" name="learning_objectives" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('learning_objectives', $guide->learning_objectives) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">What users will learn</p>
                            @error('learning_objectives')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Media -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Media</h3>
                    
                    <div class="mb-6">
                        <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                        <input type="file" id="featured_image" name="featured_image" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500">Recommended size: 1200x630px</p>
                        @error('featured_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        
                        @if($guide->featured_image)
                            <div class="mt-4">
                                <img src="{{ asset('storage/' . $guide->featured_image) }}" 
                                     alt="{{ $guide->title }}" 
                                     class="w-full h-48 object-cover rounded-lg">
                                <div class="mt-2">
                                    <button type="button" 
                                            class="px-3 py-1 bg-red-600 text-white text-sm rounded-md hover:bg-red-700 transition-colors"
                                            onclick="confirm('Are you sure you want to remove this image?')">
                                        <i class="fas fa-trash mr-1"></i>Remove Image
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Publishing Options -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Publishing Options</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select id="status" name="status" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Status</option>
                                <option value="draft" {{ old('status', $guide->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $guide->status) == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
                            <input type="datetime-local" id="published_at" name="published_at"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ old('published_at', $guide->published_at ? $guide->published_at->format('Y-m-d\TH:i') : '') }}">
                            <p class="mt-1 text-sm text-gray-500">Leave empty to publish immediately</p>
                            @error('published_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_featured" value="1" 
                                   {{ old('is_featured', $guide->is_featured) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Feature this guide</span>
                        </label>
                    </div>
                </div>

                <!-- SEO -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">SEO</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title" maxlength="255"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ old('meta_title', $guide->meta_title) }}">
                            <p class="mt-1 text-sm text-gray-500">Recommended: 50-60 characters</p>
                            @error('meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea id="meta_description" name="meta_description" rows="3" maxlength="500"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('meta_description', $guide->meta_description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Recommended: 150-160 characters</p>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.guides.show', $guide) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Guide
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
