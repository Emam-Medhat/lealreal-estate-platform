@extends('layouts.app')

@section('title', 'تفاصيل الوثيقة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Document Details -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $document->title }}</h5>
                    <div class="btn-group">
                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-success">
                            <i class="fas fa-download"></i> تحميل
                        </a>
                        <button class="btn btn-primary" onclick="shareDocument()">
                            <i class="fas fa-share"></i> مشاركة
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>الوصف:</strong> {{ $document->description ?? '-' }}</p>
                            <p><strong>الفئة:</strong> {{ $document->category?->name ?? '-' }}</p>
                            <p><strong>نوع الملف:</strong> {{ $document->file_type }}</p>
                            <p><strong>حجم الملف:</strong> {{ formatFileSize($document->file_size) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>مستوى السرية:</strong> 
                                <span class="badge bg-{{ getConfidentialityColor($document->confidentiality_level) }}">
                                    {{ getConfidentialityLabel($document->confidentiality_level) }}
                                </span>
                            </p>
                            <p><strong>المنشئ:</strong> {{ $document->createdBy->name }}</p>
                            <p><strong>تاريخ الإنشاء:</strong> {{ $document->created_at->format('Y-m-d H:i') }}</p>
                            @if($document->expiration_date)
                                <p><strong>تاريخ انتهاء الصلاحية:</strong> 
                                    <span class="text-{{ $document->expiration_date->isPast() ? 'danger' : 'warning' }}">
                                        {{ $document->expiration_date->format('Y-m-d') }}
                                        @if($document->expiration_date->isPast())
                                            (منتهي الصلاحية)
                                        @endif
                                    </span>
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Tags -->
                    @if($document->tags)
                        <div class="mt-3">
                            <strong>الوسوم:</strong>
                            @foreach(json_decode($document->tags) as $tag)
                                <span class="badge bg-secondary me-1">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Document Preview -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معاينة الوثيقة</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        @if(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                            <img src="{{ asset('storage/' . $document->file_path) }}" class="img-fluid" alt="{{ $document->title }}">
                        @elseif($document->file_type == 'pdf')
                            <iframe src="{{ asset('storage/' . $document->file_path) }}" width="100%" height="600px"></iframe>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                لا يمكن عرض هذا النوع من الملفات. يرجى تحميل الملف لعرضه.
                                <br>
                                <a href="{{ route('documents.download', $document) }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-download"></i> تحميل الملف
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-info" onclick="requestSignature()">
                            <i class="fas fa-signature"></i> طلب توقيع
                        </button>
                        <button class="btn btn-secondary" onclick="createVersion()">
                            <i class="fas fa-code-branch"></i> إنشاء نسخة
                        </button>
                        <button class="btn btn-warning" onclick="requestApproval()">
                            <i class="fas fa-check-circle"></i> طلب موافقة
                        </button>
                        <button class="btn btn-success" onclick="sendForNotary()">
                            <i class="fas fa-certificate"></i> توثيق
                        </button>
                    </div>
                </div>
            </div>

            <!-- Versions -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">الإصدارات</h5>
                </div>
                <div class="card-body">
                    @if($document->versions->count() > 0)
                        <div class="list-group">
                            @foreach($document->versions as $version)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">الإصدار {{ $version->version_number }}</h6>
                                        <small class="text-muted">{{ $version->created_at->format('Y-m-d H:i') }} - {{ $version->createdBy->name }}</small>
                                        @if($version->changes)
                                            <p class="mb-0 small text-muted">{{ $version->changes }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        @if($version->is_current)
                                            <span class="badge bg-success">الحالي</span>
                                        @else
                                            <button class="btn btn-sm btn-outline-primary" onclick="restoreVersion({{ $version->id }})">
                                                استعادة
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">لا توجد إصدارات سابقة</p>
                    @endif
                </div>
            </div>

            <!-- Signatures -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">التوقيعات</h5>
                </div>
                <div class="card-body">
                    @if($document->signatures->count() > 0)
                        <div class="list-group">
                            @foreach($document->signatures as $signature)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $signature->user->name }}</h6>
                                            <small class="text-muted">{{ $signature->signed_at->format('Y-m-d H:i') }}</small>
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $signature->is_verified ? 'success' : 'warning' }}">
                                                {{ $signature->is_verified ? 'موثق' : 'غير موثق' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">لا توجد توقيعات</p>
                    @endif
                </div>
            </div>

            <!-- Access Log -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">سجل الوصول</h5>
                </div>
                <div class="card-body">
                    @if($document->accessLogs->count() > 0)
                        <div class="list-group">
                            @foreach($document->accessLogs->take(5) as $log)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ getActionLabel($log->action) }}</h6>
                                            <small class="text-muted">{{ $log->user->name }} - {{ $log->created_at->format('Y-m-d H:i') }}</small>
                                        </div>
                                        <div>
                                            <i class="fas fa-{{ getActionIcon($log->action) }}"></i>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($document->accessLogs->count() > 5)
                            <div class="text-center mt-2">
                                <a href="#" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">لا يوجد سجل وصول</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Request Signature Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">طلب توقيع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('documents.signature.request', $document) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني للموقع</label>
                        <input type="email" name="signer_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">اسم الموقع</label>
                        <input type="text" name="signer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رسالة</label>
                        <textarea name="message" class="form-control" rows="3">يرجى توقيع هذه الوثيقة</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function shareDocument() {
    // Implementation for sharing document
    console.log('Share document');
}

function requestSignature() {
    new bootstrap.Modal(document.getElementById('signatureModal')).show();
}

function createVersion() {
    // Implementation for creating version
    console.log('Create version');
}

function requestApproval() {
    // Implementation for requesting approval
    console.log('Request approval');
}

function sendForNotary() {
    // Implementation for notary verification
    console.log('Send for notary');
}

function restoreVersion(versionId) {
    if (confirm('هل أنت متأكد من استعادة هذا الإصدار؟')) {
        // Implementation for restoring version
        console.log('Restore version:', versionId);
    }
}

// Helper functions
function getActionLabel(action) {
    const labels = {
        'view': 'عرض',
        'download': 'تحميل',
        'edit': 'تعديل',
        'delete': 'حذف'
    };
    return labels[action] || action;
}

function getActionIcon(action) {
    const icons = {
        'view': 'eye',
        'download': 'download',
        'edit': 'edit',
        'delete': 'trash'
    };
    return icons[action] || 'file';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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
</script>
@endpush
