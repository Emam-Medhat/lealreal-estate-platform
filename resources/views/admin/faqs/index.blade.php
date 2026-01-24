@extends('layouts.app')

@section('title', 'FAQs Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Frequently Asked Questions</h1>
                    <p class="text-gray-600">Manage FAQ entries and categories</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.faqs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        New FAQ
                    </a>
                    <button onclick="toggleReorder()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-sort mr-2"></i>
                        Reorder
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" placeholder="Search FAQs..." value="{{ request('search') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach ($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- FAQs List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            @if (request('reorder'))
                <!-- Reorder Mode -->
                <div class="p-6 bg-yellow-50 border-b border-yellow-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                            <span class="text-yellow-800">Drag and drop FAQs to reorder them</span>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="saveOrder()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Save Order
                            </button>
                            <button onclick="cancelReorder()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
                <div id="sortable-faqs" class="divide-y divide-gray-200">
                    @forelse ($faqs ?? [] as $faq)
                        <div class="p-6 hover:bg-gray-50 cursor-move" data-id="{{ $faq->id }}">
                            <div class="flex items-center">
                                <i class="fas fa-grip-vertical text-gray-400 mr-4"></i>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $faq->question }}</h3>
                                    <p class="text-gray-600 mt-1">{{ Str::limit($faq->answer, 150) }}</p>
                                    <div class="flex items-center mt-2 text-sm text-gray-500">
                                        <span class="mr-4"><i class="fas fa-folder mr-1"></i> {{ $faq->category->name ?? 'Uncategorized' }}</span>
                                        <span class="mr-4">Order: {{ $faq->sort_order }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $faq->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($faq->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            No FAQs found.
                        </div>
                    @endforelse
                </div>
            @else
                <!-- Normal View -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($faqs ?? [] as $faq)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $faq->question }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($faq->answer, 100) }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $faq->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $faq->sort_order }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $faq->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($faq->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $faq->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.faqs.show', $faq) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="text-gray-600 hover:text-gray-900 mr-3">Edit</a>
                                        <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No FAQs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if (isset($faqs) && $faqs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $faqs->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
function toggleReorder() {
    window.location.href = '{{ request()->fullUrlWithQuery(['reorder' => 'true']) }}';
}

function cancelReorder() {
    window.location.href = '{{ request()->fullUrlWithQuery(['reorder' => null]) }}';
}

function saveOrder() {
    const faqs = document.querySelectorAll('#sortable-faqs > div');
    const order = Array.from(faqs).map((faq, index) => ({
        id: faq.dataset.id,
        order: index + 1
    }));

    fetch('{{ route('admin.faqs.reorder') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ order: order })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ request()->fullUrlWithQuery(['reorder' => null]) }}';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving order');
    });
}

// Initialize sortable if in reorder mode
@if (request('reorder'))
document.addEventListener('DOMContentLoaded', function() {
    new Sortable(document.getElementById('sortable-faqs'), {
        animation: 150,
        ghostClass: 'opacity-50'
    });
});
@endif
</script>
@endsection
