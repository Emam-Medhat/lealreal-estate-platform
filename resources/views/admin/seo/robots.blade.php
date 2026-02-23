@extends('admin.layouts.admin')

@section('title', 'Robots.txt Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Robots.txt Management</h1>
        <div>
            <a href="{{ route('admin.seo.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to SEO
            </a>
        </div>
    </div>

    <!-- Robots.txt Status -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Robots.txt File</h6>
                </div>
                <div class="card-body">
                    @if($robotsExists)
                        <div class="mb-3">
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> robots.txt exists
                            </span>
                        </div>
                        
                        <div class="form-group">
                            <label for="robotsContent" class="form-label">Current robots.txt content:</label>
                            <textarea class="form-control" id="robotsContent" rows="15" readonly>{{ $robotsContent }}</textarea>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editRobotsModal">
                                <i class="fas fa-edit"></i> Edit robots.txt
                            </button>
                            <a href="/robots.txt" target="_blank" class="btn btn-info ml-2">
                                <i class="fas fa-external-link-alt"></i> View Live
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> robots.txt Not Found</h5>
                            <p class="mb-3">The robots.txt file doesn't exist in your public directory. This file is important for search engine crawling instructions.</p>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createRobotsModal">
                                <i class="fas fa-plus"></i> Create robots.txt
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">File Status</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                File Status</div>
                            <div class="h5 mb-0 font-weight-bold">
                                @if($robotsExists)
                                    <span class="text-success">Exists</span>
                                @else
                                    <span class="text-danger">Not Found</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x {{ $robotsExists ? 'text-success' : 'text-danger' }}"></i>
                        </div>
                    </div>

                    @if($robotsExists)
                        <div class="mb-2">
                            <small class="text-muted">File size: {{ strlen($robotsContent) }} bytes</small>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Lines: {{ count(explode("\n", $robotsContent)) }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Robots.txt Guide -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Robots.txt Guide</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Common Directives</h6>
                        <ul class="mb-0">
                            <li><code>User-agent: *</code> - All robots</li>
                            <li><code>Disallow: /</code> - Block all</li>
                            <li><code>Allow: /</code> - Allow all</li>
                            <li><code>Sitemap: URL</code> - Sitemap location</li>
                            <li><code>Crawl-delay: 1</code> - Delay between requests</li>
                        </ul>
                    </div>

                    <div class="mt-3">
                        <h6>Example Template:</h6>
                        <pre class="bg-light p-2 rounded" style="font-size: 12px;">User-agent: *
Disallow: /admin/
Disallow: /private/
Allow: /
Sitemap: {{ url('sitemap.xml') }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Robots Modal -->
@if($robotsExists)
<div class="modal fade" id="editRobotsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit robots.txt</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.seo.robots.update') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="robotsContentEdit" class="form-label">robots.txt content:</label>
                        <textarea class="form-control" id="robotsContentEdit" name="content" rows="15" required>{{ $robotsContent }}</textarea>
                        <small class="form-text text-muted">Enter the robots.txt content. Be careful as this affects search engine crawling.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Create Robots Modal -->
@if(!$robotsExists)
<div class="modal fade" id="createRobotsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create robots.txt</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.seo.robots.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="robotsContentCreate" class="form-label">robots.txt content:</label>
                        <textarea class="form-control" id="robotsContentCreate" name="content" rows="15" required>User-agent: *
Disallow: /admin/
Disallow: /private/
Allow: /
Sitemap: {{ url('sitemap.xml') }}</textarea>
                        <small class="form-text text-muted">Enter the robots.txt content. A basic template is provided.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create File
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
