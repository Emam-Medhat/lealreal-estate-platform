@extends('admin.layouts.admin')

@section('title', 'SEO Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-search text-primary mr-2"></i>SEO Management
            </h1>
            <p class="text-muted mb-0 mt-1">Manage SEO settings and metadata</p>
        </div>
        <div>
            <a href="{{ route('admin.seo.generate-sitemap') }}" class="btn btn-success shadow-sm mr-2">
                <i class="fas fa-sitemap fa-sm text-white-50"></i> Generate Sitemap
            </a>
            <a href="{{ route('admin.seo.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add SEO Meta
            </a>
        </div>
    </div>

    <!-- SEO Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                <i class="fas fa-chart-line mr-1"></i>Optimized Pages
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['optimized_pages'] ?? 0 }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up mr-1"></i>{{ $stats['optimization_score'] ?? 0 }}% score
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-primary rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i>Issues Found
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['issues'] ?? 0 }}</div>
                            <div class="text-xs text-danger">
                                <i class="fas fa-arrow-down mr-1"></i>{{ $stats['critical_issues'] ?? 0 }} critical
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-warning rounded-circle p-3">
                                <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                <i class="fas fa-key mr-1"></i>Keywords
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['keywords'] ?? 0 }}</div>
                            <div class="text-xs text-info">
                                <i class="fas fa-star mr-1"></i>{{ $stats['top_keyword'] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-info rounded-circle p-3">
                                <i class="fas fa-key fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-3">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                <i class="fas fa-tachometer-alt mr-1"></i>Page Speed
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $stats['page_speed'] ?? 0 }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-check mr-1"></i>avg score
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="bg-success rounded-circle p-3">
                                <i class="fas fa-tachometer-alt fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Tools -->
    <div class="row mb-4">
        <!-- SEO Analysis -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-left-primary">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-search mr-2"></i>SEO Analysis
                    </h6>
                    <div class="badge badge-primary">Live</div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="analyze-url" class="form-label font-weight-bold">
                            <i class="fas fa-link mr-1"></i>Analyze URL
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="url" id="analyze-url" placeholder="https://example.com/page" 
                                class="form-control border-primary">
                            <button onclick="analyzeUrl()" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i>Analyze
                            </button>
                        </div>
                        <small class="text-muted">Enter a URL to analyze its SEO performance</small>
                    </div>
                    
                    <div id="analysis-results" class="alert alert-info" style="display: none;">
                        <h6 class="alert-heading">
                            <i class="fas fa-chart-bar mr-1"></i>Analysis Results
                        </h6>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle p-2 mr-3">
                                        <i class="fas fa-heading text-white"></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-muted">Title Length</small>
                                        <strong id="title-length" class="text-primary">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle p-2 mr-3">
                                        <i class="fas fa-align-left text-white"></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-muted">Meta Description</small>
                                        <strong id="meta-description" class="text-success">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle p-2 mr-3">
                                        <i class="fas fa-h1 text-white"></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-muted">H1 Tags</small>
                                        <strong id="h1-tags" class="text-warning">-</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info rounded-circle p-2 mr-3">
                                        <i class="fas fa-image text-white"></i>
                                    </div>
                                    <div>
                                        <small class="d-block text-muted">Image Alt Tags</small>
                                        <strong id="image-alt" class="text-info">-</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Keyword Research -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-left-success">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-key mr-2"></i>Keyword Research
                    </h6>
                    <div class="badge badge-success">Beta</div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="seed-keyword" class="form-label font-weight-bold">
                            <i class="fas fa-search mr-1"></i>Seed Keyword
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="text" id="seed-keyword" placeholder="Enter main keyword..." 
                                class="form-control border-success">
                            <button onclick="researchKeywords()" class="btn btn-success">
                                <i class="fas fa-search mr-1"></i>Research
                            </button>
                        </div>
                        <small class="text-muted">Discover related keywords and search volumes</small>
                    </div>
                    
                    <div id="keyword-results" style="display: none;">
                        <h6 class="font-weight-bold mb-3">
                            <i class="fas fa-list mr-1"></i>Related Keywords
                        </h6>
                        <div class="list-group" style="max-height: 250px; overflow-y: auto;">
                            <!-- Keywords will be populated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Meta Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>SEO Meta Data
                <span class="badge badge-info ml-2">{{ $seoMetas->total() ?? 0 }} Total</span>
            </h6>
            <div class="d-flex align-items-center">
                <div class="input-group" style="width: 250px;">
                    <input type="text" id="table-search" placeholder="Search SEO metadata..." 
                           class="form-control border-primary">
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div class="btn-group btn-group-sm ml-2">
                    <button class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-top-0">
                                <i class="fas fa-file-alt mr-1"></i>Page
                            </th>
                            <th class="border-top-0">
                                <i class="fas fa-heading mr-1"></i>Title
                            </th>
                            <th class="border-top-0">
                                <i class="fas fa-align-left mr-1"></i>Description
                            </th>
                            <th class="border-top-0">
                                <i class="fas fa-key mr-1"></i>Keywords
                            </th>
                            <th class="border-top-0 text-center">
                                <i class="fas fa-chart-line mr-1"></i>Score
                            </th>
                            <th class="border-top-0 text-center">
                                <i class="fas fa-cogs mr-1"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($seoMetas ?? [] as $meta)
                            <tr class="hover:bg-light">
                                <td>
                                    <div class="d-flex align-items-start">
                                        <div>
                                            <div class="font-weight-bold text-primary">{{ class_basename($meta->model_type) }}</div>
                                            <small class="text-muted d-block">ID: {{ $meta->model_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $meta->meta_title }}">
                                        {{ $meta->meta_title ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $meta->meta_description }}">
                                        {{ $meta->meta_description ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    @if($meta->meta_keywords)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach(explode(',', $meta->meta_keywords) as $keyword)
                                                @if(trim($keyword))
                                                    <span class="badge badge-info mr-1 mb-1">{{ trim($keyword) }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center">
                                        <div class="progress" style="height: 8px; width: 60px;">
                                            <div class="progress-bar 
                                                {{ ($meta->seo_score ?? 0) >= 80 ? 'bg-success' : 
                                                   (($meta->seo_score ?? 0) >= 60 ? 'bg-warning' : 'bg-danger') }}"
                                                role="progressbar" 
                                                style="width: {{ $meta->seo_score ?? 0 }}%">
                                            </div>
                                        </div>
                                        <span class="ml-2 font-weight-bold 
                                            {{ ($meta->seo_score ?? 0) >= 80 ? 'text-success' : 
                                                   (($meta->seo_score ?? 0) >= 60 ? 'text-warning' : 'text-danger') }}">
                                            {{ $meta->seo_score ?? 0 }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.seo.show', ['seoMeta' => $meta->id]) }}" 
                                           class="btn btn-info btn-sm" 
                                           title="View Details"
                                           data-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.seo.edit', ['seoMeta' => $meta->id]) }}" 
                                           class="btn btn-warning btn-sm" 
                                           title="Edit"
                                           data-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.seo.destroy', ['seoMeta' => $meta->id]) }}" 
                                              method="POST" 
                                              style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm" 
                                                    title="Delete"
                                                    data-toggle="tooltip"
                                                    onclick="return confirm('Are you sure you want to delete this SEO meta data?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <div class="text-center">
                                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No SEO metadata found</h5>
                                        <p class="text-muted mb-4">Start by adding SEO meta data to your pages</p>
                                        <a href="{{ route('admin.seo.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus mr-1"></i>Add SEO Meta
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            @if (isset($seoMetas) && $seoMetas->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Showing {{ $seoMetas->firstItem() ?? 0 }} to {{ $seoMetas->lastItem() ?? 0 }} 
                            of {{ $seoMetas->total() ?? 0 }} entries
                        </small>
                    </div>
                    <div>
                        {{ $seoMetas->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
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
        document.getElementById('analysis-results').style.display = 'block';
        document.getElementById('title-length').textContent = (data.title || '').length + ' chars';
        document.getElementById('meta-description').textContent = data.description ? 'Present' : 'Missing';
        document.getElementById('h1-tags').textContent = (data.h1_count || 0) + ' found';
        document.getElementById('image-alt').textContent = (data.image_alt_count || 0) + '/' + (data.total_images || 0);
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
    resultsDiv.style.display = 'block';
    
    const keywordsHtml = mockKeywords.map(k => `
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <div class="font-weight-bold">${k.keyword}</div>
                <small class="text-muted">Volume: ${k.volume} | Difficulty: ${k.difficulty}</small>
            </div>
            <button class="btn btn-sm btn-outline-primary">Use</button>
        </div>
    `).join('');
    
    resultsDiv.querySelector('.list-group').innerHTML = keywordsHtml;
}
</script>
@endsection
