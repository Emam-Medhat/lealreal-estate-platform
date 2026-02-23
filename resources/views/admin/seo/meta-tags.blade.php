@extends('admin.layouts.admin')

@section('title', 'Meta Tags Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tags text-primary mr-2"></i>Meta Tags Management
            </h1>
            <p class="text-muted mb-0 mt-1">Manage SEO meta tags and Open Graph data</p>
        </div>
        <div>
            <a href="{{ route('admin.seo.create') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add Meta Tag
            </a>
            <a href="{{ route('admin.seo.index') }}" class="d-none d-sm-inline-block btn btn-secondary shadow-sm ml-2">
                <i class="fas fa-list fa-sm text-white-50"></i> All SEO
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Meta Tags</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $metaTags->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                With Titles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $metaTags->whereNotNull('meta_title')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heading fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                With Descriptions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $metaTags->whereNotNull('meta_description')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-align-left fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                With Keywords</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $metaTags->whereNotNull('meta_keywords')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meta Tags Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>Meta Tags List
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                    <a class="dropdown-item" href="{{ route('admin.seo.create') }}">
                        <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>Add New
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.seo.keywords') }}">
                        <i class="fas fa-key fa-sm fa-fw mr-2 text-gray-400"></i>Keywords Analysis
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.seo.analyze') }}">
                        <i class="fas fa-search fa-sm fa-fw mr-2 text-gray-400"></i>SEO Analysis
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($metaTags->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-top-0 border-bottom-0">
                                    <i class="fas fa-file-alt mr-2"></i>Page/Resource
                                </th>
                                <th class="border-top-0 border-bottom-0">
                                    <i class="fas fa-heading mr-2"></i>Title
                                </th>
                                <th class="border-top-0 border-bottom-0">
                                    <i class="fas fa-align-left mr-2"></i>Description
                                </th>
                                <th class="border-top-0 border-bottom-0">
                                    <i class="fas fa-key mr-2"></i>Keywords
                                </th>
                                <th class="border-top-0 border-bottom-0">
                                    <i class="fas fa-share-alt mr-2"></i>OG Title
                                </th>
                                <th class="border-top-0 border-bottom-0 text-center">
                                    <i class="fas fa-cogs mr-2"></i>Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($metaTags as $metaTag)
                                <tr>
                                    <td>
                                        @php
                                            $metaable = null;
                                            try {
                                                // Only try to load if class exists to prevent crash
                                                if (class_exists($metaTag->model_type)) {
                                                    $metaable = $metaTag->metaable;
                                                }
                                            } catch (\Exception $e) {
                                                // Ignore
                                            }
                                        @endphp

                                        @if($metaable)
                                            <div class="d-flex align-items-center">
                                                <div class="icon-circle bg-primary text-white mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-file"></i>
                                                </div>
                                                <div>
                                                    <strong class="text-gray-800">{{ class_basename($metaable) }}</strong>
                                                    <br>
                                                    <small class="text-muted">ID: {{ $metaTag->model_id }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center">
                                                <div class="icon-circle bg-secondary text-white mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-question"></i>
                                                </div>
                                                <div>
                                                    <span class="text-muted">
                                                        {{ class_basename($metaTag->model_type) }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">ID: {{ $metaTag->model_id }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($metaTag->meta_title)
                                            <div>
                                                <span class="text-gray-800">{{ Str::limit($metaTag->meta_title, 50) }}</span>
                                                <br>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ strlen($metaTag->meta_title) > 50 ? 'warning' : 'success' }}" 
                                                         style="width: {{ (strlen($metaTag->meta_title) / 60) * 100 }}%"
                                                         title="{{ strlen($metaTag->meta_title) }}/60 chars">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ strlen($metaTag->meta_title) }}/60 chars</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($metaTag->meta_description)
                                            <div>
                                                <span class="text-gray-800">{{ Str::limit($metaTag->meta_description, 80) }}</span>
                                                <br>
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ strlen($metaTag->meta_description) > 150 ? 'warning' : 'success' }}" 
                                                         style="width: {{ (strlen($metaTag->meta_description) / 160) * 100 }}%"
                                                         title="{{ strlen($metaTag->meta_description) }}/160 chars">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ strlen($metaTag->meta_description) }}/160 chars</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($metaTag->meta_keywords)
                                            <div>
                                                @foreach(explode(',', $metaTag->meta_keywords) as $keyword)
                                                    @if(trim($keyword))
                                                        <span class="badge badge-info mb-1">{{ trim($keyword) }}</span>
                                                    @endif
                                                    @if($loop->iteration >= 3)
                                                        @break
                                                    @endif
                                                @endforeach
                                                @if(count(explode(',', $metaTag->meta_keywords)) > 3)
                                                    <small class="text-muted ml-1">+{{ count(explode(',', $metaTag->meta_keywords)) - 3 }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($metaTag->og_title)
                                            <span class="text-gray-800">{{ Str::limit($metaTag->og_title, 50) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.seo.show', ['seoMeta' => $metaTag->id]) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="View Details"
                                               data-toggle="tooltip" data-placement="top">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.seo.edit', ['seoMeta' => $metaTag->id]) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Edit"
                                               data-toggle="tooltip" data-placement="top">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.seo.destroy', ['seoMeta' => $metaTag->id]) }}" 
                                                  method="POST" 
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('Are you sure you want to delete this meta tag?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-sm" 
                                                        title="Delete"
                                                        data-toggle="tooltip" data-placement="top">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Enhanced Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Showing {{ $metaTags->firstItem() }} to {{ $metaTags->lastItem() }} of {{ $metaTags->total() }} entries
                        </small>
                    </div>
                    <div>
                        {{ $metaTags->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-tags fa-4x text-gray-300"></i>
                    </div>
                    <h5 class="text-gray-600 mb-3">No Meta Tags Found</h5>
                    <p class="text-gray-500 mb-4">Start optimizing your SEO by adding meta tags to your pages.</p>
                    <a href="{{ route('admin.seo.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus mr-2"></i>Add Your First Meta Tag
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.icon-circle {
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.25rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>

<script>
// Initialize tooltips
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
