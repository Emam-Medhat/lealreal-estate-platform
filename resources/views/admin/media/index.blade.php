@extends('layouts.admin')

@section('title', 'مكتبة الوسائط')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">مكتبة الوسائط</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fas fa-upload"></i> رفع ملفات جديدة
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-2">
                                <select name="type" class="form-select">
                                    <option value="">كل الأنواع</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="بحث في الملفات..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_from" class="form-control" 
                                       value="{{ request('date_from') }}" placeholder="من تاريخ">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="date_to" class="form-control" 
                                       value="{{ request('date_to') }}" placeholder="إلى تاريخ">
                            </div>
                            <div class="col-md-3">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-search"></i> بحث
                                    </button>
                                    <a href="{{ route('admin.media.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo"></i> إعادة تعيين
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    <div class="mb-3" id="bulkActions" style="display: none;">
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-danger" onclick="bulkDelete()">
                                <i class="fas fa-trash"></i> حذف المحدد
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="deselectAll()">
                                <i class="fas fa-times"></i> إلغاء التحديد
                            </button>
                        </div>
                        <span class="ms-3 text-muted" id="selectedCount">0 ملف محدد</span>
                    </div>

                    <!-- Media Grid -->
                    <div class="row" id="mediaGrid">
                        @foreach($mediaFiles as $mediaFile)
                            <div class="col-md-3 col-sm-4 col-6 mb-4 media-item">
                                <div class="card h-100">
                                    <div class="card-body p-2">
                                        <div class="position-relative">
                                            <input type="checkbox" class="form-check-input position-absolute top-0 start-0 m-2" 
                                                   value="{{ $mediaFile->id }}" onchange="updateBulkActions()">
                                            
                                            @if($mediaFile->type == 'image')
                                                <img src="{{ asset('storage/' . $mediaFile->path) }}" 
                                                     class="img-fluid rounded" alt="{{ $mediaFile->alt_text ?? $mediaFile->filename }}"
                                                     style="height: 150px; width: 100%; object-fit: cover;">
                                            @elseif($mediaFile->type == 'video')
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="height: 150px;">
                                                    <i class="fas fa-video fa-3x text-muted"></i>
                                                </div>
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                                     style="height: 150px;">
                                                    <i class="fas fa-file fa-3x text-muted"></i>
                                                </div>
                                            @endif
                                            
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-secondary">{{ $mediaFile->type }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <small class="text-muted d-block">{{ $mediaFile->filename }}</small>
                                            <small class="text-muted d-block">{{ formatBytes($mediaFile->size) }}</small>
                                            @if($mediaFile->dimensions)
                                                <small class="text-muted d-block">{{ $mediaFile->dimensions }}</small>
                                            @endif
                                        </div>
                                        
                                        <div class="btn-group w-100 mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    onclick="previewMedia({{ $mediaFile->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('admin.media.edit', $mediaFile) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.media.download', $mediaFile) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <form action="{{ route('admin.media.destroy', $mediaFile) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($mediaFiles->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-images fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد ملفات</h5>
                            <p class="text-muted">ابدأ برفع بعض الملفات إلى مكتبة الوسائط</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="fas fa-upload"></i> رفع ملفات جديدة
                            </button>
                        </div>
                    @endif

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            عرض {{ $mediaFiles->firstItem() }} - {{ $mediaFiles->lastItem() }} من {{ $mediaFiles->total() }} ملف
                        </div>
                        {{ $mediaFiles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رفع ملفات جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="files" class="form-label">اختر الملفات</label>
                        <input type="file" class="form-control" id="files" name="files[]" multiple 
                               accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" required>
                        <small class="form-text text-muted">يمكنك اختيار ملفات متعددة. الحد الأقصى للملف: 10MB</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="alt_text" class="form-label">نص بديل (للصور)</label>
                        <input type="text" class="form-control" id="alt_text" name="alt_text" 
                               placeholder="سيتم تطبيقه على جميع الصور المرفوعة">
                    </div>
                    
                    <div class="mb-3">
                        <label for="caption" class="form-label">وصف</label>
                        <textarea class="form-control" id="caption" name="caption" rows="2" 
                                  placeholder="سيتم تطبيقه على جميع الملفات المرفوعة"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">الفئة</label>
                        <input type="text" class="form-control" id="category" name="category" 
                               placeholder="مثال: عام، مقالات، منتجات">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> رفع الملفات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">معاينة الملف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.media-item input[type="checkbox"]:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkboxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = checkboxes.length + ' ملف محدد';
    } else {
        bulkActions.style.display = 'none';
    }
}

function deselectAll() {
    document.querySelectorAll('.media-item input[type="checkbox"]').forEach(cb => cb.checked = false);
    updateBulkActions();
}

function bulkDelete() {
    const checkboxes = document.querySelectorAll('.media-item input[type="checkbox"]:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (confirm('هل أنت متأكد من حذف ' + ids.length + ' ملف؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.media.bulk-delete") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'files[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function previewMedia(id) {
    fetch(`/admin/media/${id}/preview`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('previewContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        });
}

// Helper function to format bytes
function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush
