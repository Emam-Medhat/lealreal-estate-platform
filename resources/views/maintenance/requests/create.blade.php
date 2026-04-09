@extends('layouts.app')

@section('title', 'إنشاء طلب صيانة جديد')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">إنشاء طلب صيانة جديد</h1>
                <a href="{{ route('maintenance.requests.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات الطلب</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('maintenance.requests.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_id">العقار <span class="text-danger">*</span></label>
                                    <select name="property_id" id="property_id" class="form-control" required>
                                        <option value="">اختر العقار</option>
                                        @foreach($properties as $property)
                                        <option value="{{ $property->id }}">{{ $property->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('property_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">الأولوية <span class="text-danger">*</span></label>
                                    <select name="priority" id="priority" class="form-control" required>
                                        <option value="">اختر الأولوية</option>
                                        <option value="low">منخفض</option>
                                        <option value="medium">متوسط</option>
                                        <option value="high">عالي</option>
                                        <option value="emergency">طوارئ</option>
                                    </select>
                                    @error('priority')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">الفئة <span class="text-danger">*</span></label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">اختر الفئة</option>
                                        <option value="plumbing">سباكة</option>
                                        <option value="electrical">كهرباء</option>
                                        <option value="hvac">تكييف</option>
                                        <option value="structural">إنشائي</option>
                                        <option value="general">عام</option>
                                        <option value="cosmetic">تجميلي</option>
                                        <option value="safety">سلامة</option>
                                        <option value="other">أخرى</option>
                                    </select>
                                    @error('category')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_date">تاريخ الاستحقاق</label>
                                    <input type="datetime-local" name="due_date" id="due_date" class="form-control">
                                    @error('due_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title">عنوان الطلب <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">الوصف <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="4" class="form-control" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estimated_cost">التكلفة التقديرية</label>
                                    <input type="number" name="estimated_cost" id="estimated_cost" 
                                           class="form-control" step="0.01" min="0" value="{{ old('estimated_cost') }}">
                                    @error('estimated_cost')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location">الموقع</label>
                                    <input type="text" name="location" id="location" 
                                           class="form-control" value="{{ old('location') }}">
                                    @error('location')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">ملاحظات إضافية</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="attachments">المرفقات</label>
                            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                            <small class="text-muted">يمكنك تحميل ملفات PDF، Word، أو الصور</small>
                            @error('attachments')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الطلب
                            </button>
                            <a href="{{ route('maintenance.requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> نصائح هامة</h6>
                        <ul class="mb-0">
                            <li>كن دقيقاً في وصف المشكلة</li>
                            <li>ارفع صوراً للمشكلة إن أمكن</li>
                            <li>حدد الأولوية المناسبة للطلب</li>
                            <li>اذكر الموقع الدقيق للمشكلة</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> أولويات الطوارئ</h6>
                        <p class="mb-0">استخدم أولوية "طوارئ" فقط للمشاكل التي:</p>
                        <ul class="mb-0">
                            <li>تسبب ضرراً للممتلكات</li>
                            <li>تشكل خطراً على السلامة</li>
                            <li>تتطلب إصلاحاً فورياً</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-set due date based on priority
    document.getElementById('priority').addEventListener('change', function() {
        const priority = this.value;
        const dueDateInput = document.getElementById('due_date');
        
        if (priority && !dueDateInput.value) {
            const now = new Date();
            let dueDate = new Date();
            
            switch(priority) {
                case 'emergency':
                    dueDate.setHours(now.getHours() + 4);
                    break;
                case 'high':
                    dueDate.setHours(now.getHours() + 24);
                    break;
                case 'medium':
                    dueDate.setDate(now.getDate() + 3);
                    break;
                case 'low':
                    dueDate.setDate(now.getDate() + 7);
                    break;
            }
            
            dueDateInput.value = dueDate.toISOString().slice(0, 16);
        }
    });
</script>
@endpush
