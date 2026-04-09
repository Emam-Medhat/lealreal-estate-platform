@extends('admin.layouts.admin')

@section('title', 'إعدادات النظام')
@section('page-title', 'إعدادات النظام')

@push('styles')
<style>
    .settings-card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.5rem;
    }
    .settings-card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,.125);
        padding: 1.5rem;
    }
    .section-icon {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-left: 10px;
    }
    .nav-pills .nav-link {
        color: #6c757d;
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        margin-left: 0.5rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }
    .nav-pills .nav-link i {
        margin-left: 0.5rem;
    }
    .file-type-item {
        cursor: pointer;
        transition: all 0.2s;
    }
    .file-type-item:hover {
        background-color: #f8f9fa;
    }
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        margin-left: -3.5em; /* Adjust for RTL */
        float: left; /* Adjust for RTL */
    }
    .form-switch {
        padding-left: 3.5em; /* Adjust for RTL */
        padding-right: 0;
    }
    /* RTL Specific adjustments */
    [dir="rtl"] .form-switch .form-check-input {
        float: left;
        margin-left: 0;
        margin-right: -3.5em;
    }
    [dir="rtl"] .form-switch {
        padding-right: 3.5em;
        padding-left: 0;
    }
    [dir="rtl"] .me-2 {
        margin-left: 0.5rem !important;
        margin-right: 0 !important;
    }
    [dir="rtl"] .ms-2 {
        margin-right: 0.5rem !important;
        margin-left: 0 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 fw-bold text-dark">إعدادات النظام</h2>
            <p class="text-muted">إدارة إعدادات وتكوينات النظام الأساسية</p>
        </div>
    </div>

    <!-- Settings Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-2">
                    <ul class="nav nav-pills" id="settingsTab" role="tablist">
                        @foreach($settingsGroups as $key => $group)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $key === $activeTab ? 'active' : '' }}" 
                                        id="{{ $key }}-tab" 
                                        data-bs-toggle="pill" 
                                        data-bs-target="#{{ $key }}" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="{{ $key }}" 
                                        aria-selected="{{ $key === $activeTab ? 'true' : 'false' }}">
                                    <i class="{{ $group['icon'] }}"></i>
                                    {{ $group['name'] }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="settingsTabContent">
        @foreach($settingsGroups as $tabKey => $group)
            <div class="tab-pane fade {{ $tabKey === $activeTab ? 'show active' : '' }}" 
                 id="{{ $tabKey }}" 
                 role="tabpanel" 
                 aria-labelledby="{{ $tabKey }}-tab">
                
                <form action="{{ route('admin.settings.update', $tabKey) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card settings-card">
                        <div class="card-header settings-card-header bg-white">
                            <h5 class="mb-0 d-flex align-items-center">
                                <span class="section-icon bg-light text-primary">
                                    <i class="{{ $group['icon'] }}"></i>
                                </span>
                                {{ $group['name'] }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @foreach($group['fields'] as $fieldKey => $field)
                                <div class="mb-4">
                                    <!-- Label -->
                                    @if($field['type'] !== 'toggle')
                                        <label class="form-label fw-bold">
                                            {{ $field['label'] }}
                                            @if(str_contains($field['rules'], 'required'))
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                    @endif

                                    <!-- Description -->
                                    @if(isset($field['description']) && $field['type'] !== 'toggle')
                                        <div class="form-text text-muted mb-2 mt-0">{{ $field['description'] }}</div>
                                    @endif

                                    <!-- Input Types -->
                                    @if(in_array($field['type'], ['text', 'email', 'number']))
                                        <input type="{{ $field['type'] }}" 
                                               name="{{ $fieldKey }}" 
                                               value="{{ old($fieldKey, $settings[$fieldKey] ?? $field['default'] ?? '') }}"
                                               class="form-control"
                                               @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif
                                               @if(isset($field['readonly']) && $field['readonly']) readonly @endif
                                               @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                               @if(isset($field['max'])) max="{{ $field['max'] }}" @endif>

                                    @elseif($field['type'] === 'textarea')
                                        <textarea name="{{ $fieldKey }}" 
                                                  class="form-control" 
                                                  rows="{{ $field['rows'] ?? 3 }}"
                                                  @if(isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif>{{ old($fieldKey, $settings[$fieldKey] ?? $field['default'] ?? '') }}</textarea>

                                    @elseif($field['type'] === 'select')
                                        <select name="{{ $fieldKey }}" class="form-select">
                                            @php
                                                $options = is_string($field['options']) && method_exists($controller, $field['options']) 
                                                         ? $controller->{$field['options']}() 
                                                         : $field['options'];
                                            @endphp
                                            @foreach($options as $value => $label)
                                                <option value="{{ $value }}" {{ (string)($settings[$fieldKey] ?? $field['default'] ?? '') === (string)$value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>

                                    @elseif($field['type'] === 'toggle')
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="{{ $fieldKey }}" 
                                                   value="1" 
                                                   id="{{ $fieldKey }}"
                                                   {{ (bool)($settings[$fieldKey] ?? $field['default'] ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="{{ $fieldKey }}">
                                                {{ $field['label'] }}
                                            </label>
                                        </div>
                                        @if(isset($field['description']))
                                            <div class="form-text text-muted">{{ $field['description'] }}</div>
                                        @endif

                                    @elseif($field['type'] === 'file_types')
                                        <div class="row g-3">
                                            @foreach($field['options'] as $category => $formats)
                                                <div class="col-md-12 mb-2">
                                                    <h6 class="fw-bold text-muted border-bottom pb-2">
                                                        <i class="fas fa-{{ $category === 'images' ? 'image' : ($category === 'documents' ? 'file-alt' : ($category === 'media' ? 'film' : 'file-archive')) }} me-2"></i>
                                                        {{ $category === 'images' ? 'الصور' : ($category === 'documents' ? 'المستندات' : ($category === 'media' ? 'الوسائط' : 'الأرشيفات')) }}
                                                    </h6>
                                                    <div class="row g-2">
                                                        @foreach($formats as $format => $label)
                                                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                                                <div class="form-check border rounded p-2 file-type-item">
                                                                    <input class="form-check-input float-end ms-2" 
                                                                           type="checkbox" 
                                                                           name="{{ $fieldKey }}[]" 
                                                                           value="{{ $format }}"
                                                                           id="format_{{ $format }}"
                                                                           {{ in_array($format, (array)($settings[$fieldKey] ?? $field['default'] ?? [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label w-100 d-block" for="format_{{ $format }}">
                                                                        {{ $label }}
                                                                        <small class="text-muted d-block">.{{ $format }}</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @error($fieldKey)
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer bg-light p-3 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tabs based on URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'general';
        const triggerEl = document.querySelector(`#${activeTab}-tab`);
        
        if (triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }

        // Update URL on tab change
        const tabEls = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabEls.forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                const newTabId = event.target.getAttribute('aria-controls');
                const url = new URL(window.location);
                url.searchParams.set('tab', newTabId);
                window.history.pushState({}, '', url);
            })
        });
    });
</script>
@endpush
@endsection
