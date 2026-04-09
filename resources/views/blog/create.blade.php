@extends('layouts.app')

@section('title', 'Create Blog Post')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Create Blog Post</h1>
                <a href="{{ route('admin.blog.posts.index') }}" class="text-blue-600 hover:text-blue-700">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Posts
                </a>
            </div>

            <div class="bg-white rounded-xl shadow-lg">
                <form action="{{ route('admin.blog.posts.store') }}" method="POST" class="p-8">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Content -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                                <input type="text" name="title" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Enter post title">
                            </div>

                            <!-- Slug -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">URL Slug *</label>
                                <input type="text" name="slug" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="post-url-slug">
                                <p class="text-sm text-gray-500 mt-1">This will be used in the URL: /blog/{{ '--slug--' }}</p>
                            </div>

                            <!-- Excerpt -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                                <textarea name="excerpt" rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Brief description of the post (optional)"></textarea>
                            </div>

                            <!-- Content -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                                <textarea name="content" rows="15" required
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Write your post content here..."></textarea>
                                <p class="text-sm text-gray-500 mt-1">You can use HTML formatting</p>
                            </div>

                            <!-- Featured Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                                <input type="text" name="featured_image"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Image path or URL">
                                <p class="text-sm text-gray-500 mt-1">Enter the path to the featured image</p>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="space-y-6">
                            <!-- Publish Settings -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Publish Settings</h3>
                                
                                <!-- Status -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="draft">Draft</option>
                                        <option value="published">Published</option>
                                        <option value="scheduled">Scheduled</option>
                                        <option value="archived">Archived</option>
                                    </select>
                                </div>

                                <!-- Published At -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
                                    <input type="datetime-local" name="published_at"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Category -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                    <select name="category_id" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Tags -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                    <div class="space-y-2">
                                        @foreach($tags as $tag)
                                            <label class="flex items-center">
                                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                                                       class="mr-2 text-blue-600 focus:ring-blue-500">
                                                <span class="text-sm text-gray-700">{{ $tag->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Options -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Options</h3>
                                
                                <!-- Featured -->
                                <div class="mb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_featured" value="1"
                                               class="mr-2 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">Mark as featured</span>
                                    </label>
                                </div>

                                <!-- Allow Comments -->
                                <div class="mb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="allow_comments" value="1" checked
                                               class="mr-2 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">Allow comments</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="space-y-3">
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-save mr-2"></i>Save Post
                                </button>
                                <button type="button" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                    <i class="fas fa-eye mr-2"></i>Preview
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
