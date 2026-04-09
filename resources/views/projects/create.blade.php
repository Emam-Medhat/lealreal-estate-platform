@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إنشاء مشروع جديد</h1>
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right"></i> العودة للمشاريع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf
                
                <!-- Basic Information -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">معلومات أساسية</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم المشروع *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="project_type" class="form-label">نوع المشروع *</label>
                            <select class="form-select @error('project_type') is-invalid @enderror" 
                                    id="project_type" name="project_type" required>
                                <option value="">اختر النوع</option>
                                <option value="residential" {{ old('project_type') == 'residential' ? 'selected' : '' }}>سكني</option>
                                <option value="commercial" {{ old('project_type') == 'commercial' ? 'selected' : '' }}>تجاري</option>
                                <option value="mixed" {{ old('project_type') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                                <option value="industrial" {{ old('project_type') == 'industrial' ? 'selected' : '' }}>صناعي</option>
                            </select>
                            @error('project_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف المشروع</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Client & Manager -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">العميل والمدير</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="client_id" class="form-label">العميل *</label>
                            <select class="form-select @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" required>
                                <option value="">اختر العميل</option>
                                @foreach(App\Models\Client::all() as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="manager_id" class="form-label">مدير المشروع *</label>
                            <select class="form-select @error('manager_id') is-invalid @enderror" 
                                    id="manager_id" name="manager_id" required>
                                <option value="">اختر المدير</option>
                                @foreach(App\Models\User::where('role', 'admin')->orWhere('role', 'manager')->get() as $user)
                                    <option value="{{ $user->id }}" {{ old('manager_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location" class="form-label">الموقع</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location') }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">الجدول الزمني</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">تاريخ البدء *</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">تاريخ الانتهاء *</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Budget & Priority -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">الميزانية والأولوية</h5>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="budget" class="form-label">الميزانية الإجمالية (ريال) *</label>
                            <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                   id="budget" name="budget" value="{{ old('budget') }}" step="0.01" min="0" required>
                            @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="priority" class="form-label">الأولوية *</label>
                            <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" name="priority" required>
                                <option value="">اختر الأولوية</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>حرجة</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">الحالة *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">اختر الحالة</option>
                                <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>التخطيط</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>معلق</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Project Details -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3">تفاصيل المشروع</h5>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="total_units" class="form-label">إجمالي الوحدات</label>
                            <input type="number" class="form-control @error('total_units') is-invalid @enderror" 
                                   id="total_units" name="total_units" value="{{ old('total_units') }}" min="0">
                            @error('total_units')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="total_area" class="form-label">المساحة الإجمالية (م²)</label>
                            <input type="number" class="form-control @error('total_area') is-invalid @enderror" 
                                   id="total_area" name="total_area" value="{{ old('total_area') }}" step="0.01" min="0">
                            @error('total_area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> إنشاء المشروع
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum end date to start date
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });
});
</script>
@endsection
