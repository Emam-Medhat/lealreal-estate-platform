@extends('layouts.app')

@section('title', 'إضافة وثيقة جديدة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إضافة وثيقة جديدة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">العنوان *</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الفئة</label>
                                    <select name="category_id" class="form-select">
                                        <option value="">اختر الفئة</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">مستوى السرية *</label>
                                    <select name="confidentiality_level" class="form-select" required>
                                        <option value="public" {{ old('confidentiality_level') == 'public' ? 'selected' : '' }}>عام</option>
                                        <option value="internal" {{ old('confidentiality_level') == 'internal' ? 'selected' : '' }}>داخلي</option>
                                        <option value="confidential" {{ old('confidentiality_level') == 'confidential' ? 'selected' : '' }}>سري</option>
                                        <option value="restricted" {{ old('confidentiality_level') == 'restricted' ? 'selected' : '' }}>مقيد</option>
                                    </select>
                                    @error('confidentiality_level')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الملف *</label>
                            <input type="file" name="file" class="form-control" required>
                            <small class="text-muted">الصيغ المسموحة: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF</small>
                            @error('file')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">تاريخ انتهاء الصلاحية</label>
                                    <input type="date" name="expiration_date" class="form-control" value="{{ old('expiration_date') }}">
                                    @error('expiration_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الوسوم (فصل بينها بفاصلة)</label>
                                    <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="مثال: مهم، قانوني، عقد">
                                    @error('tags')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('documents.index') }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ الوثيقة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات المستوى</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>مستويات السرية:</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="badge bg-success me-2">عام</span>
                                <small>متاح للجميع</small>
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-info me-2">داخلي</span>
                                <small>للموظفين فقط</small>
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-warning me-2">سري</span>
                                <small>للمسؤولين المعتمدين</small>
                            </li>
                            <li class="mb-2">
                                <span class="badge bg-danger me-2">مقيد</span>
                                <small>للمستويات العليا فقط</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">نصائح</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            اختر عنواناً واضحاً وموجزاً
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            حدد مستوى السرية المناسب
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            استخدم الوسوم لتسهيل البحث
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            حدد تاريخ انتهاء الصلاحية للوثائق المهمة
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('input[name="file"]');
    const confidentialitySelect = document.querySelector('select[name="confidentiality_level"]');
    
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Auto-set confidentiality based on file type or content
            if (file.type.includes('pdf') && file.name.toLowerCase().includes('contract')) {
                confidentialitySelect.value = 'confidential';
            }
        }
    });
});
</script>
@endpush
