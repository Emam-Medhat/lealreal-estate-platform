@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">إنشاء مهمة جديدة</h1>
                <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة للمهام
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.tasks.store', $project) }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Basic Information -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">عنوان المهمة *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">وصف المهمة</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="priority" class="form-label">الأولوية *</label>
                                            <select class="form-select @error('priority') is-invalid @enderror" 
                                                    id="priority" name="priority" required>
                                                <option value="">اختر الأولوية</option>
                                                <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>حرجة</option>
                                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">الحالة *</label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="">اختر الحالة</option>
                                                <option value="todo" {{ old('status') == 'todo' ? 'selected' : '' }}>قيد الانتظار</option>
                                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                                <option value="review" {{ old('status') == 'review' ? 'selected' : '' }}>قيد المراجعة</option>
                                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="due_date" class="form-label">تاريخ الاستحقاق *</label>
                                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                                   id="due_date" name="due_date" value="{{ old('due_date') }}" required>
                                            @error('due_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="progress" class="form-label">نسبة التقدم (%)</label>
                                            <input type="number" class="form-control @error('progress') is-invalid @enderror" 
                                                   id="progress" name="progress" value="{{ old('progress', 0) }}" 
                                                   min="0" max="100" step="1">
                                            @error('progress')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="estimated_hours" class="form-label">الساعات التقديرية</label>
                                            <input type="number" class="form-control @error('estimated_hours') is-invalid @enderror" 
                                                   id="estimated_hours" name="estimated_hours" value="{{ old('estimated_hours') }}" 
                                                   min="0" step="0.5">
                                            @error('estimated_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="actual_hours" class="form-label">الساعات الفعلية</label>
                                            <input type="number" class="form-control @error('actual_hours') is-invalid @enderror" 
                                                   id="actual_hours" name="actual_hours" value="{{ old('actual_hours') }}" 
                                                   min="0" step="0.5">
                                            @error('actual_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Assignment -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">الإسناد</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="assigned_to" class="form-label">الموظف المسند إليه</label>
                                            <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                                    id="assigned_to" name="assigned_to">
                                                <option value="">اختر الموظف</option>
                                                @foreach(\App\Models\User::where('account_status', 'active')->get() as $user)
                                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('assigned_to')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Dependencies -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">الاعتماديات</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="dependencies" class="form-label">مهام معتمدة</label>
                                            <select class="form-select" id="dependencies" name="dependencies[]" multiple>
                                                @if($tasks->count() > 0)
                                                    @foreach($tasks as $task)
                                                        <option value="{{ $task->id }}" {{ in_array($task->id, old('dependencies', [])) ? 'selected' : '' }}>
                                                            {{ $task->title }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="">لا توجد مهام متاحة</option>
                                                @endif
                                            </select>
                                            <small class="form-text text-muted">اختر المهام التي يجب إكمالها قبل هذه المهمة</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tags -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">الوسوم</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="tags" class="form-label">وسوم (مفصولة بفاصلة)</label>
                                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                                   id="tags" name="tags" value="{{ old('tags') }}" 
                                                   placeholder="مثال: مهم, عاجل, عميل">
                                            <small class="form-text text-muted">أدخل وسوم مفصولة بفاصلة</small>
                                            @error('tags')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('projects.tasks.index', $project) }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> حفظ المهمة
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Initialize select2 for better UX
$(document).ready(function() {
    $('#dependencies').select2({
        placeholder: 'اختر المهام المعتمدة',
        allowClear: true
    });
    
    $('#assigned_to').select2({
        placeholder: 'اختر الموظف',
        allowClear: true
    });
});
</script>
@endpush
