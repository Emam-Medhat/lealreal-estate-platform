@extends('layouts.app')

@section('title', 'إنشاء عقد جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إنشاء عقد جديد</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('contracts.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">عنوان العقد *</label>
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
                                    <label class="form-label">قالب العقد</label>
                                    <select name="template_id" class="form-select" id="templateSelect">
                                        <option value="">اختر قالب (اختياري)</option>
                                        @foreach($templates as $template)
                                            <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                                {{ $template->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('template_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">العملة</label>
                                    <select name="currency" class="form-select">
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو</option>
                                        <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>ريال سعودي</option>
                                        <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>درهم إماراتي</option>
                                    </select>
                                    @error('currency')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">تاريخ البدء *</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">تاريخ الانتهاء</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">قيمة العقد</label>
                                    <input type="number" name="value" class="form-control" step="0.01" value="{{ old('value') }}">
                                    @error('value')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Parties -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">أطراف العقد</h6>
                            </div>
                            <div class="card-body">
                                <div id="partiesContainer">
                                    <div class="party-row">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">الاسم *</label>
                                                    <input type="text" name="parties[0][name]" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">البريد الإلكتروني *</label>
                                                    <input type="email" name="parties[0][email]" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">الهاتف</label>
                                                    <input type="tel" name="parties[0][phone]" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">الدور *</label>
                                                    <select name="parties[0][role]" class="form-select" required>
                                                        <option value="">اختر...</option>
                                                        <option value="buyer">مشتري</option>
                                                        <option value="seller">بائع</option>
                                                        <option value="agent">وكيل</option>
                                                        <option value="witness">شاهد</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addParty()">
                                    <i class="fas fa-plus"></i> إضافة طرف
                                </button>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">بنود العقد</h6>
                            </div>
                            <div class="card-body">
                                <div id="termsContainer">
                                    <div class="term-row">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">عنوان البند *</label>
                                                    <input type="text" name="terms[0][title]" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">المحتوى *</label>
                                                    <textarea name="terms[0][content]" class="form-control" rows="2" required></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">النوع</label>
                                                    <select name="terms[0][type]" class="form-select">
                                                        <option value="general">عام</option>
                                                        <option value="payment">دفع</option>
                                                        <option value="termination">إنهاء</option>
                                                        <option value="liability">مسؤولية</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addTerm()">
                                    <i class="fas fa-plus"></i> إضافة بند
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('contracts.index') }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">إنشاء العقد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">نصائح</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            استخدم قالباً موجوداً لتوفير الوقت
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            حدد جميع أطراف العقد بدقة
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            اكتب البود بوضوح ودقة
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            حدد التواريخ والقيم بدقة
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
let partyCount = 1;
let termCount = 1;

document.addEventListener('DOMContentLoaded', function() {
    const templateSelect = document.getElementById('templateSelect');
    
    templateSelect.addEventListener('change', function() {
        if (this.value) {
            loadTemplate(this.value);
        }
    });
});

function loadTemplate(templateId) {
    fetch(`/contracts/templates/${templateId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Load template terms
                const termsContainer = document.getElementById('termsContainer');
                termsContainer.innerHTML = '';
                
                data.template.variables.forEach((variable, index) => {
                    addTermFromTemplate(variable, index);
                });
            }
        })
        .catch(error => {
            console.error('Error loading template:', error);
        });
}

function addParty() {
    const container = document.getElementById('partiesContainer');
    const partyRow = document.createElement('div');
    partyRow.className = 'party-row mt-3';
    partyRow.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">الاسم *</label>
                    <input type="text" name="parties[${partyCount}][name]" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">البريد الإلكتروني *</label>
                    <input type="email" name="parties[${partyCount}][email]" class="form-control" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">الهاتف</label>
                    <input type="tel" name="parties[${partyCount}][phone]" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">الدور *</label>
                    <select name="parties[${partyCount}][role]" class="form-select" required>
                        <option value="">اختر...</option>
                        <option value="buyer">مشتري</option>
                        <option value="seller">بائع</option>
                        <option value="agent">وكيل</option>
                        <option value="witness">شاهد</option>
                    </select>
                </div>
            </div>
        </div>
    `;
    container.appendChild(partyRow);
    partyCount++;
}

function addTerm() {
    const container = document.getElementById('termsContainer');
    const termRow = document.createElement('div');
    termRow.className = 'term-row mt-3';
    termRow.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">عنوان البند *</label>
                    <input type="text" name="terms[${termCount}][title]" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">المحتوى *</label>
                    <textarea name="terms[${termCount}][content]" class="form-control" rows="2" required></textarea>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">النوع</label>
                    <select name="terms[${termCount}][type]" class="form-select">
                        <option value="general">عام</option>
                        <option value="payment">دفع</option>
                        <option value="termination">إنهاء</option>
                        <option value="liability">مسؤولية</option>
                    </select>
                </div>
            </div>
        </div>
    `;
    container.appendChild(termRow);
    termCount++;
}

function addTermFromTemplate(variable, index) {
    const container = document.getElementById('termsContainer');
    const termRow = document.createElement('div');
    termRow.className = 'term-row mt-3';
    termRow.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">عنوان البند *</label>
                    <input type="text" name="terms[${termCount}][title]" class="form-control" value="${variable.title}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">المحتوى *</label>
                    <textarea name="terms[${termCount}][content]" class="form-control" rows="2" required>${variable.content}</textarea>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label class="form-label">النوع</label>
                    <select name="terms[${termCount}][type]" class="form-select">
                        <option value="general">عام</option>
                        <option value="payment">دفع</option>
                        <option value="termination">إنهاء</option>
                        <option value="liability">مسؤولية</option>
                    </select>
                </div>
            </div>
        </div>
    `;
    container.appendChild(termRow);
    termCount++;
}
</script>
@endpush
