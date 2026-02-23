@extends('admin.layouts.admin')

@section('title', 'SEO Keywords Analysis')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">SEO Keywords Analysis</h1>
        <div>
            <a href="{{ route('admin.seo.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to SEO
            </a>
            <a href="{{ route('admin.seo.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm ml-2">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add Meta Tag
            </a>
        </div>
    </div>

    <!-- Keywords Analysis -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Keywords</h6>
                </div>
                <div class="card-body">
                    @if(count($keywords) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Keyword</th>
                                        <th>Frequency</th>
                                        <th>Usage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($keywords as $keyword => $frequency)
                                        <tr>
                                            <td>
                                                <strong>{{ $keyword }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $frequency }}</span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ ($frequency / max($keywords)) * 100 }}%"
                                                         aria-valuenow="{{ $frequency }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="{{ max($keywords) }}">
                                                        {{ round(($frequency / max($keywords)) * 100) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-key fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No Keywords Found</h5>
                            <p class="text-gray-500">Start adding meta tags with keywords to see analysis here.</p>
                            <a href="{{ route('admin.seo.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Meta Tag
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Keywords Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Keywords Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Keywords</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($keywords) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
                        </div>
                    </div>

                    @if(count($keywords) > 0)
                        <div class="mb-3">
                            <h6 class="text-muted">Most Used Keyword:</h6>
                            <p class="mb-0">
                                <strong>{{ array_key_first($keywords) }}</strong>
                                <span class="badge badge-primary ml-2">{{ array_values($keywords)[0] }} times</span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Average Usage:</h6>
                            <p class="mb-0">
                                <strong>{{ round(array_sum($keywords) / count($keywords), 1) }}</strong> times per keyword
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SEO Tips -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">SEO Tips</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb"></i> Keyword Optimization</h6>
                        <ul class="mb-0">
                            <li>Use 5-10 relevant keywords per page</li>
                            <li>Include primary keyword in title</li>
                            <li>Use keywords naturally in description</li>
                            <li>Avoid keyword stuffing</li>
                            <li>Focus on long-tail keywords</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
