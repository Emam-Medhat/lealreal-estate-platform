@extends('layouts.app')

@section('title', 'Edit Media File')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Media File</h1>
                    <p class="text-gray-600">Update media file information</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.media.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Media Library
                    </a>
                </div>
            </div>
        </div>

        <!-- Media Preview -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">File Preview</h3>
            
            <div class="flex justify-center">
                @if($mediaFile->type === 'image')
                    <img src="{{ asset('storage/' . $mediaFile->file_path) }}" 
                         alt="{{ $mediaFile->alt_text ?? $mediaFile->original_name }}" 
                         class="max-w-full h-auto max-h-96 rounded-lg shadow-md">
                @else
                    <div class="bg-gray-100 rounded-lg p-8 text-center">
                        <i class="fas fa-file text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 font-medium">{{ $mediaFile->original_name }}</p>
                        <p class="text-sm text-gray-500 mt-2">{{ $mediaFile->mime_type }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('admin.media.update', $mediaFile) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- File Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">File Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Original Name</label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700">
                                {{ $mediaFile->original_name }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">File Name</label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700">
                                {{ $mediaFile->filename }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">File Type</label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700">
                                {{ $mediaFile->mime_type }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">File Size</label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700">
                                {{ number_format($mediaFile->file_size / 1024, 2) }} KB
                            </div>
                        </div>

                        @if($mediaFile->dimensions)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dimensions</label>
                                <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700">
                                    {{ $mediaFile->dimensions }}
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700">
                                {{ $mediaFile->category ?? 'general' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO & Accessibility -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">SEO & Accessibility</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="alt_text" class="block text-sm font-medium text-gray-700 mb-2">Alt Text</label>
                            <input type="text" id="alt_text" name="alt_text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   value="{{ old('alt_text', $mediaFile->alt_text) }}"
                                   placeholder="Describe the image for accessibility">
                            <p class="mt-1 text-sm text-gray-500">Important for SEO and screen readers</p>
                            @error('alt_text')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="caption" class="block text-sm font-medium text-gray-700 mb-2">Caption</label>
                            <textarea id="caption" name="caption" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Optional caption for the image">{{ old('caption', $mediaFile->caption) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Display caption when the image is used</p>
                            @error('caption')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Additional Information</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Detailed description of the media file">{{ old('description', $mediaFile->description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Internal notes or detailed description</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select id="category" name="category"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="general" {{ old('category', $mediaFile->category) == 'general' ? 'selected' : '' }}>General</option>
                                <option value="property" {{ old('category', $mediaFile->category) == 'property' ? 'selected' : '' }}>Property</option>
                                <option value="blog" {{ old('category', $mediaFile->category) == 'blog' ? 'selected' : '' }}>Blog</option>
                                <option value="agent" {{ old('category', $mediaFile->category) == 'agent' ? 'selected' : '' }}>Agent</option>
                                <option value="document" {{ old('category', $mediaFile->category) == 'document' ? 'selected' : '' }}>Document</option>
                                <option value="logo" {{ old('category', $mediaFile->category) == 'logo' ? 'selected' : '' }}>Logo</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Organize files by category</p>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- File Actions -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">File Actions</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.content.media.preview', $mediaFile) }}" 
                               target="_blank"
                               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>Preview File
                            </a>
                            
                            <a href="{{ asset('storage/' . $mediaFile->file_path) }}" 
                               download="{{ $mediaFile->original_name }}"
                               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between">
                    <form action="{{ route('admin.media.destroy', $mediaFile) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this file? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete File
                        </button>
                    </form>
                    
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.media.index') }}" 
                           class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Update File
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
