@extends('layouts.app')

@section('title', 'Content Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Content Management</h1>
                    <p class="text-gray-600">Manage all website content</p>
                </div>
                <button onclick="refreshData()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-blog text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Blog Posts</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['blog_posts'] ?? 0 }}</p>
                        <p class="text-sm text-green-600">+{{ $stats['new_posts_today'] ?? 0 }} today</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-file-alt text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pages</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['pages'] ?? 0 }}</p>
                        <p class="text-sm text-green-600">+{{ $stats['new_pages_today'] ?? 0 }} today</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-newspaper text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">News</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['news'] ?? 0 }}</p>
                        <p class="text-sm text-green-600">+{{ $stats['new_news_today'] ?? 0 }} today</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-images text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Media Files</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['media_files'] ?? 0 }}</p>
                        <p class="text-sm text-green-600">+{{ $stats['new_media_today'] ?? 0 }} today</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.blog.posts.create') }}" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition-colors text-center">
                    <i class="fas fa-plus text-blue-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">New Blog Post</p>
                </a>
                
                <a href="{{ route('admin.pages.create') }}" class="bg-green-50 border border-green-200 rounded-lg p-4 hover:bg-green-100 transition-colors text-center">
                    <i class="fas fa-file-plus text-green-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">New Page</p>
                </a>
                
                <a href="{{ route('admin.news.create') }}" class="bg-purple-50 border border-purple-200 rounded-lg p-4 hover:bg-purple-100 transition-colors text-center">
                    <i class="fas fa-newspaper-plus text-purple-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">New Article</p>
                </a>
                
                <a href="{{ route('admin.media.index') }}" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hover:bg-yellow-100 transition-colors text-center">
                    <i class="fas fa-upload text-yellow-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Upload Media</p>
                </a>
            </div>
        </div>

        <!-- Recent Content & Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Blog Posts -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Blog Posts</h3>
                    <a href="{{ route('admin.blog.posts.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse ($recent_posts ?? [] as $post)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $post->title }}</p>
                                <p class="text-sm text-gray-500">by {{ $post->author ?? 'Admin' }} • {{ $post->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No recent posts</p>
                    @endforelse
                </div>
            </div>

            <!-- Content Analytics -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Content Analytics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Views</span>
                        <span class="text-lg font-semibold text-gray-800">{{ number_format($analytics['total_views'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Published Posts</span>
                        <span class="text-lg font-semibold text-gray-800">{{ $analytics['published_posts'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Draft Posts</span>
                        <span class="text-lg font-semibold text-gray-800">{{ $analytics['draft_posts'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Scheduled Posts</span>
                        <span class="text-lg font-semibold text-gray-800">{{ $analytics['scheduled_posts'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshData() {
    location.reload();
}
</script>
@endsection
