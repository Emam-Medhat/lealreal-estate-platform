@extends('admin.layouts.admin')

@section('title', 'SEO Analysis')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">SEO Analysis</h1>
        <div>
            <a href="{{ route('admin.seo.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to SEO
            </a>
        </div>
    </div>

    <!-- SEO Analysis Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Analyze URL</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.seo.analyze') }}">
                <div class="input-group">
                    <input type="url" name="url" class="form-control" placeholder="Enter URL to analyze (e.g., https://example.com)" value="{{ request('url') }}" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Analyze
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request('url'))
        <!-- Analysis Results -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Analysis Results for {{ request('url') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Basic SEO Elements</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Title:</strong></td>
                                        <td>
                                            {{ $analysis['title'] ?? 'Not found' }}
                                            @if(isset($analysis['title_length']))
                                                <br><small class="text-muted">{{ $analysis['title_length'] }} characters</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Description:</strong></td>
                                        <td>
                                            {{ Str::limit($analysis['description'] ?? 'Not found', 100) }}
                                            @if(isset($analysis['description_length']))
                                                <br><small class="text-muted">{{ $analysis['description_length'] }} characters</small>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>H1 Tags:</strong></td>
                                        <td>{{ $analysis['h1_count'] ?? 0 }} found</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Images with Alt:</strong></td>
                                        <td>{{ $analysis['image_alt_count'] ?? 0 }} / {{ $analysis['total_images'] ?? 0 }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Link Analysis</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Internal Links:</strong></td>
                                        <td>{{ $analysis['internal_links'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>External Links:</strong></td>
                                        <td>{{ $analysis['external_links'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Word Count:</strong></td>
                                        <td>{{ $analysis['word_count'] ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>SEO Score:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $analysis['score'] >= 80 ? 'success' : ($analysis['score'] >= 60 ? 'warning' : 'danger') }}">
                                                {{ $analysis['score'] ?? 0 }}/100
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if(isset($analysis['recommendations']) && count($analysis['recommendations']) > 0)
                            <div class="mt-4">
                                <h6>Recommendations</h6>
                                <div class="alert alert-warning">
                                    <ul class="mb-0">
                                        @foreach($analysis['recommendations'] as $recommendation)
                                            <li>{{ $recommendation }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- SEO Score -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">SEO Score</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="circular-progress" style="position: relative; display: inline-block;">
                            <svg width="120" height="120">
                                <circle cx="60" cy="60" r="50" stroke="#e9ecef" stroke-width="10" fill="none"></circle>
                                <circle cx="60" cy="60" r="50" 
                                        stroke="{{ $analysis['score'] >= 80 ? '#28a745' : ($analysis['score'] >= 60 ? '#ffc107' : '#dc3545') }}" 
                                        stroke-width="10" 
                                        fill="none"
                                        stroke-dasharray="{{ ($analysis['score'] / 100) * 314 }} 314"
                                        stroke-dashoffset="0"
                                        transform="rotate(-90 60 60)"></circle>
                            </svg>
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                <h3 class="mb-0">{{ $analysis['score'] ?? 0 }}</h3>
                                <small class="text-muted">/ 100</small>
                            </div>
                        </div>
                        <p class="mt-3 mb-0">
                            <span class="badge badge-{{ $analysis['score'] >= 80 ? 'success' : ($analysis['score'] >= 60 ? 'warning' : 'danger') }}">
                                {{ $analysis['score'] >= 80 ? 'Excellent' : ($analysis['score'] >= 60 ? 'Good' : 'Needs Improvement') }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick SEO Tips</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb"></i> On-Page SEO</h6>
                            <ul class="mb-0">
                                <li>Use unique title tags (50-60 chars)</li>
                                <li>Write compelling meta descriptions (150-160 chars)</li>
                                <li>Use one H1 tag per page</li>
                                <li>Optimize images with alt text</li>
                                <li>Create quality content (300+ words)</li>
                                <li>Use internal linking</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Getting Started -->
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">Ready to analyze your SEO?</h5>
            <p class="text-gray-500">Enter a URL above to get a comprehensive SEO analysis and recommendations.</p>
        </div>
    @endif
</div>
@endsection
