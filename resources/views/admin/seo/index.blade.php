@extends('layouts.app')

@section('title', 'SEO Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">SEO Management</h1>
                    <p class="text-gray-600">Manage SEO settings and metadata</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="generateSitemap()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-sitemap mr-2"></i>
                        Generate Sitemap
                    </button>
                    <a href="{{ route('admin.seo.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add SEO Meta
                    </a>
                </div>
            </div>
        </div>

        <!-- SEO Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Optimized Pages</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['optimized_pages'] ?? 0 }}</p>
                        <p class="text-sm text-green-600">{{ $stats['optimization_score'] ?? 0 }}% score</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Issues Found</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['issues'] ?? 0 }}</p>
                        <p class="text-sm text-red-600">{{ $stats['critical_issues'] ?? 0 }} critical</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-search text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Keywords</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['keywords'] ?? 0 }}</p>
                        <p class="text-sm text-blue-600">{{ $stats['top_keyword'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Page Speed</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['page_speed'] ?? 0 }}</p>
                        <p class="text-sm text-purple-600">avg score</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Tools -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- SEO Analysis -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">SEO Analysis</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Analyze URL</label>
                        <div class="flex space-x-2">
                            <input type="url" id="analyze-url" placeholder="Enter URL to analyze..." 
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button onclick="analyzeUrl()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="analysis-results" class="hidden">
                        <h4 class="font-medium text-gray-800 mb-2">Analysis Results</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Title Length</span>
                                <span class="text-sm font-medium" id="title-length">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Meta Description</span>
                                <span class="text-sm font-medium" id="meta-description">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">H1 Tags</span>
                                <span class="text-sm font-medium" id="h1-tags">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Image Alt Tags</span>
                                <span class="text-sm font-medium" id="image-alt">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keyword Research -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Keyword Research</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Seed Keyword</label>
                        <div class="flex space-x-2">
                            <input type="text" id="seed-keyword" placeholder="Enter main keyword..." 
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <button onclick="researchKeywords()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div id="keyword-results" class="hidden">
                        <h4 class="font-medium text-gray-800 mb-2">Related Keywords</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <!-- Keywords will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SEO Meta Data Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            Showing {{ $seoMetas->firstItem() ?? 0 }} to {{ $seoMetas->lastItem() ?? 0 }} of {{ $seoMetas->total() ?? 0 }} entries
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <input type="text" placeholder="Search SEO metadata..." class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keywords</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($seoMetas ?? [] as $meta)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $meta->page_type }}</div>
                                    <div class="text-sm text-gray-500">{{ $meta->url }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">{{ $meta->meta_title }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 max-w-xs truncate">{{ $meta->meta_description }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach (explode(',', $meta->meta_keywords ?? '') as $keyword)
                                            @if(trim($keyword))
                                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">
                                                    {{ trim($keyword) }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="h-2 rounded-full 
                                                {{ $meta->seo_score >= 80 ? 'bg-green-500' : 
                                                   ($meta->seo_score >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                style="width: {{ $meta->seo_score ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium">{{ $meta->seo_score ?? 0 }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.seo.edit', $meta) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form action="{{ route('admin.seo.destroy', $meta) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No SEO metadata found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if (isset($seoMetas) && $seoMetas->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $seoMetas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function generateSitemap() {
    fetch('{{ route('admin.seo.generate-sitemap') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sitemap generated successfully!');
        } else {
            alert('Error generating sitemap');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating sitemap');
    });
}

function analyzeUrl() {
    const url = document.getElementById('analyze-url').value;
    if (!url) {
        alert('Please enter a URL to analyze');
        return;
    }

    fetch('{{ route('admin.seo.analyze') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ url: url })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('analysis-results').classList.remove('hidden');
        document.getElementById('title-length').textContent = data.title_length + ' chars';
        document.getElementById('meta-description').textContent = data.meta_description ? 'Present' : 'Missing';
        document.getElementById('h1-tags').textContent = data.h1_count + ' found';
        document.getElementById('image-alt').textContent = data.images_with_alt + '/' + data.total_images;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error analyzing URL');
    });
}

function researchKeywords() {
    const keyword = document.getElementById('seed-keyword').value;
    if (!keyword) {
        alert('Please enter a keyword');
        return;
    }

    // Mock keyword research - in real app, this would call an API
    const mockKeywords = [
        { keyword: keyword + ' guide', volume: '1,000', difficulty: 'Low' },
        { keyword: keyword + ' tutorial', volume: '800', difficulty: 'Low' },
        { keyword: keyword + ' tips', volume: '600', difficulty: 'Medium' },
        { keyword: keyword + ' best practices', volume: '400', difficulty: 'Medium' }
    ];

    const resultsDiv = document.getElementById('keyword-results');
    resultsDiv.classList.remove('hidden');
    
    const keywordsHtml = mockKeywords.map(k => `
        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
            <span class="text-sm font-medium">${k.keyword}</span>
            <div class="flex space-x-2 text-xs text-gray-600">
                <span>Vol: ${k.volume}</span>
                <span>Diff: ${k.difficulty}</span>
            </div>
        </div>
    `).join('');
    
    resultsDiv.querySelector('.space-y-2').innerHTML = keywordsHtml;
}
</script>
@endsection
