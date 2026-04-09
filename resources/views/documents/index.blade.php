@extends('layouts.app')

@section('title', 'إدارة الوثائق')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">الوثائق</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('documents.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> وثيقة جديدة
                        </a>
                        <a href="{{ route('documents.templates.index') }}" class="btn btn-info">
                            <i class="fas fa-file-alt"></i> القوالب
                        </a>
                        <button class="btn btn-success" onclick="showUploadModal()">
                            <i class="fas fa-upload"></i> رفع ملفات
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="categoryFilter">
                                <option value="">جميع الفئات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="confidentialityFilter">
                                <option value="">جميع المستويات</option>
                                <option value="public">عام</option>
                                <option value="internal">داخلي</option>
                                <option value="confidential">سري</option>
                                <option value="restricted">مقيد</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">جميع الأنواع</option>
                                <option value="pdf">PDF</option>
                                <option value="doc">Word</option>
                                <option value="xls">Excel</option>
                                <option value="img">صورة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="بحث...">
                        </div>
                    </div>

                    <!-- Documents Grid -->
                    <div class="row">
                        @foreach($documents as $document)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 document-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="file-icon">
                                                <i class="fas fa-{{ getFileIcon($document->file_type) }} fa-2x text-primary"></i>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('documents.show', $document) }}">
                                                        <i class="fas fa-eye"></i> عرض
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('documents.download', $document) }}">
                                                        <i class="fas fa-download"></i> تحميل
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="{{ route('documents.edit', $document) }}">
                                                        <i class="fas fa-edit"></i> تعديل
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteDocument({{ $document->id }})">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <h6 class="card-title">{{ $document->title }}</h6>
                                        <p class="card-text small text-muted">{{ $document->description }}</p>
                                        
                                        <div class="document-meta">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-{{ getConfidentialityColor($document->confidentiality_level) }}">
                                                    {{ getConfidentialityLabel($document->confidentiality_level) }}
                                                </span>
                                                <small class="text-muted">{{ formatFileSize($document->file_size) }}</small>
                                            </div>
                                            
                                            @if($document->category)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-folder"></i> {{ $document->category->name }}
                                                    </small>
                                                </div>
                                            @endif
                                            
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-user"></i> {{ $document->createdBy->name }}
                                                </small>
                                            </div>
                                            
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar"></i> {{ $document->created_at->format('Y-m-d') }}
                                                </small>
                                            </div>
                                            
                                            @if($document->expiration_date)
                                                <div class="mt-2">
                                                    <small class="text-{{ $document->expiration_date->isPast() ? 'danger' : 'warning' }}">
                                                        <i class="fas fa-clock"></i> 
                                                        {{ $document->expiration_date->format('Y-m-d') }}
                                                        @if($document->expiration_date->isPast())
                                                            (منتهي الصلاحية)
                                                        @endif
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Tags -->
                                        @if($document->tags)
                                            <div class="mt-2">
                                                @foreach(json_decode($document->tags) as $tag)
                                                    <span class="badge bg-secondary me-1">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رفع ملفات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('documents.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اختر الملفات</label>
                        <input type="file" name="files[]" class="form-control" multiple required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الفئة</label>
                        <select name="category_id" class="form-select">
                            <option value="">اختر الفئة</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مستوى السرية</label>
                        <select name="confidentiality_level" class="form-select">
                            <option value="public">عام</option>
                            <option value="internal">داخلي</option>
                            <option value="confidential">سري</option>
                            <option value="restricted">مقيد</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">رفع</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
});

function initializeFilters() {
    const categoryFilter = document.getElementById('categoryFilter');
    const confidentialityFilter = document.getElementById('confidentialityFilter');
    const typeFilter = document.getElementById('typeFilter');
    const searchInput = document.getElementById('searchInput');

    function filterDocuments() {
        const params = new URLSearchParams();
        if (categoryFilter.value) params.set('category', categoryFilter.value);
        if (confidentialityFilter.value) params.set('confidentiality', confidentialityFilter.value);
        if (typeFilter.value) params.set('type', typeFilter.value);
        if (searchInput.value) params.set('search', searchInput.value);
        
        const url = params.toString() ? `?${params.toString()}` : '';
        window.location.href = url;
    }

    categoryFilter.addEventListener('change', filterDocuments);
    confidentialityFilter.addEventListener('change', filterDocuments);
    typeFilter.addEventListener('change', filterDocuments);
    
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterDocuments, 500);
    });
}

function showUploadModal() {
    new bootstrap.Modal(document.getElementById('uploadModal')).show();
}

function deleteDocument(id) {
    if (confirm('هل أنت متأكد من حذف هذه الوثيقة؟')) {
        fetch(`/documents/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ أثناء حذف الوثيقة');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء حذف الوثيقة');
        });
    }
}

// Helper functions (should be defined in the controller or as global functions)
function getFileIcon(fileType) {
    const iconMap = {
        'pdf': 'file-pdf',
        'doc': 'file-word',
        'docx': 'file-word',
        'xls': 'file-excel',
        'xlsx': 'file-excel',
        'ppt': 'file-powerpoint',
        'pptx': 'file-powerpoint',
        'jpg': 'file-image',
        'jpeg': 'file-image',
        'png': 'file-image',
        'gif': 'file-image'
    };
    return iconMap[fileType.toLowerCase()] || 'file';
}

function getConfidentialityColor(level) {
    const colorMap = {
        'public': 'success',
        'internal': 'info',
        'confidential': 'warning',
        'restricted': 'danger'
    };
    return colorMap[level] || 'secondary';
}

function getConfidentialityLabel(level) {
    const labelMap = {
        'public': 'عام',
        'internal': 'داخلي',
        'confidential': 'سري',
        'restricted': 'مقيد'
    };
    return labelMap[level] || level;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush
