@extends('layouts.app')

@section('title', 'Create Blog Post')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('admin.blog.posts.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Create New Blog Post</h1>
                        <p class="text-gray-600">Write and publish a new blog post</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="saveDraft()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Draft
                    </button>
                    <button type="button" onclick="previewPost()" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        Preview
                    </button>
                </div>
            </div>
        </div>

        <!-- Create Form -->
        <form action="{{ route('admin.blog.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Post Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                            placeholder="Enter post title..." required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                        <textarea name="content" rows="20" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Write your post content here..." required>{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Excerpt -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                        <textarea name="excerpt" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Brief description of the post...">{{ old('excerpt') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from content</p>
                        @error('excerpt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                        <input type="file" name="featured_image" accept="image/*" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Recommended: 1200x630px, Max 2MB</p>
                        @error('featured_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Publish Settings -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Publish Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
                                <input type="datetime-local" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('published_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="featured" {{ old('featured') ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Featured Post</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Categories</h3>
                        <div class="space-y-2">
                            @foreach ($categories ?? [] as $category)
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                        {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <a href="{{ route('admin.blog.categories.create') }}" class="mt-3 text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-plus mr-1"></i>
                            Add New Category
                        </a>
                    </div>

                    <!-- Tags -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tags</h3>
                        <input type="text" name="tags" value="{{ old('tags') }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter tags separated by commas">
                        <p class="mt-1 text-sm text-gray-500">e.g. technology, web development, tutorial</p>
                    </div>

                    <!-- SEO Settings -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">SEO Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                                <input type="text" name="meta_title" value="{{ old('meta_title') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="SEO title (max 60 characters)" maxlength="60">
                                @error('meta_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea name="meta_description" rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="SEO description (max 160 characters)" maxlength="160">{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Focus Keyword</label>
                                <input type="text" name="focus_keyword" value="{{ old('focus_keyword') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Main keyword for this post">
                                @error('focus_keyword')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.blog.posts.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-publish mr-2"></i>
                        Publish Post
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function saveDraft() {
    const form = document.querySelector('form');
    const statusField = form.querySelector('select[name="status"]');
    statusField.value = 'draft';
    form.submit();
}

function previewPost() {
    // Implement preview functionality
    alert('Preview functionality would open a new tab with post preview');
}
</script>
@endsection
