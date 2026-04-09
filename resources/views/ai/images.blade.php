@extends('layouts.app')

@section('title', 'تحليل الصور بالذكاء الاصطناعي')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-images me-2"></i>
            تحليل الصور بالذكاء الاصطناعي
        </h1>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newAnalysisModal">
                <i class="fas fa-plus me-2"></i>تحليل جديد
            </button>
        </div>
    </div>

    @if(isset($images) && count($images) > 0)
    <div class="row">
        @foreach($images as $image)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="{{ asset($image->image_path ?? 'images/placeholder.jpg') }}" class="card-img-top" alt="Property Image" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title">{{ $image->property_title ?? 'عقار غير معروف' }}</h5>
                    <p class="card-text">
                        <small class="text-muted">نوع التحليل: {{ $image->analysis_type ?? 'عام' }}</small>
                    </p>
                    <div class="mb-2">
                        <strong>النتيجة:</strong>
                        <span class="badge bg-{{ ($image->quality_score ?? 0) > 80 ? 'success' : 'warning' }}">
                            {{ $image->quality_score ?? 'N/A' }}
                        </span>
                    </div>
                    <p class="card-text small text-muted">
                        {{ Str::limit($image->analysis_metadata ?? '', 100) }}
                    </p>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="#" class="btn btn-sm btn-outline-primary w-100">عرض التفاصيل</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $images->links() }}
    </div>
    @else
    <div class="alert alert-info text-center py-5">
        <i class="fas fa-info-circle fa-3x mb-3"></i>
        <h4>لا توجد تحليلات صور متاحة حالياً</h4>
        <p>قم ببدء تحليل جديد للصور لعرض النتائج هنا.</p>
        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#newAnalysisModal">
            تحليل جديد
        </button>
    </div>
    @endif
</div>
@endsection
