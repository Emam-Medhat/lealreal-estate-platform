@extends('layouts.app')

@section('title', 'Pages - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pages</h1>
            <a href="{{ route('admin.pages.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Create Page
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-lg">
            <div class="p-6">
                <!-- Search and Filter -->
                <div class="flex flex-col md:flex-row gap-4 mb-6">
                    <input type="text" placeholder="Search pages..." 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>

                <!-- Pages Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Title</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Author</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Updated</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $page->title }}</h4>
                                            <p class="text-sm text-gray-500">/{{ $page->slug }}</p>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $page->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ ucfirst($page->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center">
                                            <img src="{{ $page->author->avatar ?? asset('images/default-avatar.png') }}" 
                                                 alt="{{ $page->author->name }}" 
                                                 class="w-6 h-6 rounded-full mr-2">
                                            <span class="text-sm text-gray-700">{{ $page->author->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-500">
                                        {{ $page->updated_at->format('M d, Y') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('pages.show', $page->slug) }}" 
                                               class="text-blue-600 hover:text-blue-700" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.pages.edit', $page) }}" 
                                               class="text-gray-600 hover:text-gray-700" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="toggleStatus({{ $page->id }})" 
                                                    class="text-green-600 hover:text-green-700" title="Toggle Status">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                            <button onclick="deletePage({{ $page->id }})" 
                                                    class="text-red-600 hover:text-red-700" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        <i class="fas fa-file-alt text-4xl mb-4"></i>
                                        <p>No pages found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pages->hasPages())
                    <div class="mt-6">
                        {{ $pages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus(pageId) {
    fetch(`/admin/pages/${pageId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deletePage(pageId) {
    if (confirm('Are you sure you want to delete this page?')) {
        fetch(`/admin/pages/${pageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
