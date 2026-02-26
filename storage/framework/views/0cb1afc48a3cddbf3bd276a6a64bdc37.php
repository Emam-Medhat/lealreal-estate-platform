<?php $__env->startSection('title', 'إعدادات النظام'); ?>
<?php $__env->startSection('page-title', 'إعدادات النظام'); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
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
                        <?php $__currentLoopData = $settingsGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo e($key === $activeTab ? 'active' : ''); ?>" 
                                        id="<?php echo e($key); ?>-tab" 
                                        data-bs-toggle="pill" 
                                        data-bs-target="#<?php echo e($key); ?>" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="<?php echo e($key); ?>" 
                                        aria-selected="<?php echo e($key === $activeTab ? 'true' : 'false'); ?>">
                                    <i class="<?php echo e($group['icon']); ?>"></i>
                                    <?php echo e($group['name']); ?>

                                </button>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content" id="settingsTabContent">
        <?php $__currentLoopData = $settingsGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tabKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="tab-pane fade <?php echo e($tabKey === $activeTab ? 'show active' : ''); ?>" 
                 id="<?php echo e($tabKey); ?>" 
                 role="tabpanel" 
                 aria-labelledby="<?php echo e($tabKey); ?>-tab">
                
                <form action="<?php echo e(route('admin.settings.update', $tabKey)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="card settings-card">
                        <div class="card-header settings-card-header bg-white">
                            <h5 class="mb-0 d-flex align-items-center">
                                <span class="section-icon bg-light text-primary">
                                    <i class="<?php echo e($group['icon']); ?>"></i>
                                </span>
                                <?php echo e($group['name']); ?>

                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <?php $__currentLoopData = $group['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldKey => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-4">
                                    <!-- Label -->
                                    <?php if($field['type'] !== 'toggle'): ?>
                                        <label class="form-label fw-bold">
                                            <?php echo e($field['label']); ?>

                                            <?php if(str_contains($field['rules'], 'required')): ?>
                                                <span class="text-danger">*</span>
                                            <?php endif; ?>
                                        </label>
                                    <?php endif; ?>

                                    <!-- Description -->
                                    <?php if(isset($field['description']) && $field['type'] !== 'toggle'): ?>
                                        <div class="form-text text-muted mb-2 mt-0"><?php echo e($field['description']); ?></div>
                                    <?php endif; ?>

                                    <!-- Input Types -->
                                    <?php if(in_array($field['type'], ['text', 'email', 'number'])): ?>
                                        <input type="<?php echo e($field['type']); ?>" 
                                               name="<?php echo e($fieldKey); ?>" 
                                               value="<?php echo e(old($fieldKey, $settings[$fieldKey] ?? $field['default'] ?? '')); ?>"
                                               class="form-control"
                                               <?php if(isset($field['placeholder'])): ?> placeholder="<?php echo e($field['placeholder']); ?>" <?php endif; ?>
                                               <?php if(isset($field['readonly']) && $field['readonly']): ?> readonly <?php endif; ?>
                                               <?php if(isset($field['min'])): ?> min="<?php echo e($field['min']); ?>" <?php endif; ?>
                                               <?php if(isset($field['max'])): ?> max="<?php echo e($field['max']); ?>" <?php endif; ?>>

                                    <?php elseif($field['type'] === 'textarea'): ?>
                                        <textarea name="<?php echo e($fieldKey); ?>" 
                                                  class="form-control" 
                                                  rows="<?php echo e($field['rows'] ?? 3); ?>"
                                                  <?php if(isset($field['placeholder'])): ?> placeholder="<?php echo e($field['placeholder']); ?>" <?php endif; ?>><?php echo e(old($fieldKey, $settings[$fieldKey] ?? $field['default'] ?? '')); ?></textarea>

                                    <?php elseif($field['type'] === 'select'): ?>
                                        <select name="<?php echo e($fieldKey); ?>" class="form-select">
                                            <?php
                                                $options = is_string($field['options']) && method_exists($controller, $field['options']) 
                                                         ? $controller->{$field['options']}() 
                                                         : $field['options'];
                                            ?>
                                            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($value); ?>" <?php echo e((string)($settings[$fieldKey] ?? $field['default'] ?? '') === (string)$value ? 'selected' : ''); ?>>
                                                    <?php echo e($label); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>

                                    <?php elseif($field['type'] === 'toggle'): ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="<?php echo e($fieldKey); ?>" 
                                                   value="1" 
                                                   id="<?php echo e($fieldKey); ?>"
                                                   <?php echo e((bool)($settings[$fieldKey] ?? $field['default'] ?? false) ? 'checked' : ''); ?>>
                                            <label class="form-check-label fw-bold" for="<?php echo e($fieldKey); ?>">
                                                <?php echo e($field['label']); ?>

                                            </label>
                                        </div>
                                        <?php if(isset($field['description'])): ?>
                                            <div class="form-text text-muted"><?php echo e($field['description']); ?></div>
                                        <?php endif; ?>

                                    <?php elseif($field['type'] === 'file_types'): ?>
                                        <div class="row g-3">
                                            <?php $__currentLoopData = $field['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $formats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-md-12 mb-2">
                                                    <h6 class="fw-bold text-muted border-bottom pb-2">
                                                        <i class="fas fa-<?php echo e($category === 'images' ? 'image' : ($category === 'documents' ? 'file-alt' : ($category === 'media' ? 'film' : 'file-archive'))); ?> me-2"></i>
                                                        <?php echo e($category === 'images' ? 'الصور' : ($category === 'documents' ? 'المستندات' : ($category === 'media' ? 'الوسائط' : 'الأرشيفات'))); ?>

                                                    </h6>
                                                    <div class="row g-2">
                                                        <?php $__currentLoopData = $formats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $format => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                                                <div class="form-check border rounded p-2 file-type-item">
                                                                    <input class="form-check-input float-end ms-2" 
                                                                           type="checkbox" 
                                                                           name="<?php echo e($fieldKey); ?>[]" 
                                                                           value="<?php echo e($format); ?>"
                                                                           id="format_<?php echo e($format); ?>"
                                                                           <?php echo e(in_array($format, (array)($settings[$fieldKey] ?? $field['default'] ?? [])) ? 'checked' : ''); ?>>
                                                                    <label class="form-check-label w-100 d-block" for="format_<?php echo e($format); ?>">
                                                                        <?php echo e($label); ?>

                                                                        <small class="text-muted d-block">.<?php echo e($format); ?></small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php $__errorArgs = [$fieldKey];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>