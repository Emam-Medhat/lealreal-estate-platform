@extends('admin.layouts.admin')

@section('title', 'إنشاء استبيان جديد')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إنشاء استبيان جديد</h1>
        <a href="{{ route('surveys.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-right fa-sm text-white-50"></i> العودة للقائمة
        </a>
    </div>

    <form action="{{ route('surveys.store') }}" method="POST" id="surveyForm">
        @csrf

        <div class="row">
            <!-- Basic Info -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">تفاصيل الاستبيان</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">عنوان الاستبيان <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">وصف الاستبيان</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="starts_at">تاريخ البدء <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('starts_at') is-invalid @enderror" id="starts_at" name="starts_at" value="{{ old('starts_at', date('Y-m-d')) }}" required>
                                    @error('starts_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expires_at">تاريخ الانتهاء</label>
                                    <input type="date" class="form-control @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="target_audience">الجمهور المستهدف <span class="text-danger">*</span></label>
                            <select class="form-control @error('target_audience') is-invalid @enderror" id="target_audience" name="target_audience" required>
                                <option value="">اختر الجمهور المستهدف</option>
                                @foreach($targetAudiences as $key => $label)
                                    <option value="{{ $key }}" {{ old('target_audience') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('target_audience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">الأسئلة</h6>
                        <button type="button" class="btn btn-primary btn-sm" id="addQuestionBtn">
                            <i class="fas fa-plus fa-sm"></i> إضافة سؤال
                        </button>
                    </div>
                    <div class="card-body" id="questionsContainer">
                        <!-- Questions will be added here dynamically -->
                        <div class="text-center text-muted p-4" id="emptyQuestionsMsg">
                            لا توجد أسئلة مضافة بعد. اضغط على "إضافة سؤال" للبدء.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">إعدادات إضافية</h6>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="is_anonymous" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_anonymous">استبيان مجهول الهوية</label>
                            <small class="form-text text-muted">لن يتم تسجيل معلومات المستخدمين.</small>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="allow_multiple_responses" name="allow_multiple_responses" value="1" {{ old('allow_multiple_responses') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="allow_multiple_responses">السماح بردود متعددة</label>
                            <small class="form-text text-muted">يمكن للمستخدم المشاركة أكثر من مرة.</small>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="show_results" name="show_results" value="1" {{ old('show_results') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="show_results">إظهار النتائج للمشاركين</label>
                            <small class="form-text text-muted">عرض ملخص النتائج بعد الإجابة.</small>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save fa-sm"></i> حفظ ونشر الاستبيان
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Question Template -->
<template id="questionTemplate">
    <div class="card mb-3 border-left-primary question-item">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="font-weight-bold">سؤال رقم <span class="question-number"></span></h6>
                <button type="button" class="btn btn-danger btn-sm remove-question">
                    <i class="fas fa-trash"></i> حذف
                </button>
            </div>
            
            <div class="form-group">
                <label>نص السؤال <span class="text-danger">*</span></label>
                <input type="text" class="form-control question-text" name="questions[INDEX][question_text]" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>نوع السؤال <span class="text-danger">*</span></label>
                        <select class="form-control question-type" name="questions[INDEX][question_type]" required>
                            @foreach($questionTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group pt-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input question-required" id="req_INDEX" name="questions[INDEX][is_required]" value="1" checked>
                            <label class="custom-control-label" for="req_INDEX">مطلوب الإجابة</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Options Container (for multiple choice etc) -->
            <div class="options-container d-none p-3 bg-light rounded">
                <label>الخيارات <span class="text-danger">*</span></label>
                <div class="options-list">
                    <!-- Options will go here -->
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2 add-option">
                    <i class="fas fa-plus"></i> إضافة خيار
                </button>
            </div>
        </div>
    </div>
</template>

<!-- Option Template -->
<template id="optionTemplate">
    <div class="input-group mb-2 option-item">
        <div class="input-group-prepend">
            <span class="input-group-text"><i class="far fa-circle"></i></span>
        </div>
        <input type="text" class="form-control option-text" name="questions[Q_INDEX][options][]" placeholder="خيار الإجابة">
        <div class="input-group-append">
            <button class="btn btn-outline-danger remove-option" type="button"><i class="fas fa-times"></i></button>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let questionCount = 0;
        const questionsContainer = document.getElementById('questionsContainer');
        const emptyMsg = document.getElementById('emptyQuestionsMsg');
        const questionTemplate = document.getElementById('questionTemplate');
        const optionTemplate = document.getElementById('optionTemplate');

        // Add Question
        document.getElementById('addQuestionBtn').addEventListener('click', function() {
            if(emptyMsg) emptyMsg.classList.add('d-none');
            
            const clone = questionTemplate.content.cloneNode(true);
            const card = clone.querySelector('.question-item');
            
            // Update indexes and IDs
            card.innerHTML = card.innerHTML.replace(/INDEX/g, questionCount);
            card.querySelector('.question-number').textContent = questionCount + 1;
            
            // Handle question type change
            const typeSelect = card.querySelector('.question-type');
            const optionsContainer = card.querySelector('.options-container');
            
            typeSelect.addEventListener('change', function() {
                if(['multiple_choice', 'checkbox', 'dropdown'].includes(this.value)) {
                    optionsContainer.classList.remove('d-none');
                    // Add one option by default if empty
                    if(optionsContainer.querySelector('.options-list').children.length === 0) {
                        addOption(optionsContainer.querySelector('.options-list'), questionCount); // Use current index logic needs fix
                    }
                } else {
                    optionsContainer.classList.add('d-none');
                }
            });

            // Add Option Button
            card.querySelector('.add-option').addEventListener('click', function() {
                // Find the current index dynamically based on name attribute
                const currentName = card.querySelector('.question-text').name; // questions[0][question_text]
                const currentIndex = currentName.match(/\[(\d+)\]/)[1];
                addOption(card.querySelector('.options-list'), currentIndex);
            });

            // Remove Question
            card.querySelector('.remove-question').addEventListener('click', function() {
                card.remove();
                if(questionsContainer.children.length === 1) { // 1 because emptyMsg is hidden
                    emptyMsg.classList.remove('d-none');
                }
                updateQuestionNumbers();
            });

            questionsContainer.appendChild(card);
            
            // Trigger change to set initial state
            typeSelect.dispatchEvent(new Event('change'));
            
            questionCount++;
        });

        function addOption(container, questionIndex) {
            const clone = optionTemplate.content.cloneNode(true);
            const item = clone.querySelector('.option-item');
            item.innerHTML = item.innerHTML.replace(/Q_INDEX/g, questionIndex);
            
            item.querySelector('.remove-option').addEventListener('click', function() {
                item.remove();
            });
            
            container.appendChild(item);
        }

        function updateQuestionNumbers() {
            const questions = questionsContainer.querySelectorAll('.question-item');
            questions.forEach((q, index) => {
                q.querySelector('.question-number').textContent = index + 1;
                // Note: We are not updating the array indexes in names to simplify logic, 
                // backend should handle non-sequential indexes or we can use a counter that never decreases
            });
        }
    });
</script>
@endpush