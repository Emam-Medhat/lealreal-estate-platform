<?php $__env->startSection('title', 'Create Widget'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="<?php echo e(route('admin.widgets.index')); ?>" class="text-gray-600 hover:text-gray-800 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Create New Widget</h1>
                        <p class="text-gray-600">Create a new widget for your website</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="saveDraft()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Draft
                    </button>
                    <button type="button" onclick="previewWidget()" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        Preview
                    </button>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <strong>✅ Success:</strong> <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>❌ Error:</strong> <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>⚠️ Please fix the following errors:</strong>
                <ul class="list-disc list-inside mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <!-- Create Form -->
        <form action="<?php echo e(route('admin.widgets.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Widget Title *</label>
                        <input type="text" name="title" value="<?php echo e(old('title')); ?>" 
                            class="w-full px-3 py-2 border <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                            placeholder="Enter widget title..." required>
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Slug -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Widget Slug *</label>
                        <input type="text" name="slug" value="<?php echo e(old('slug')); ?>" 
                            class="w-full px-3 py-2 border <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="widget-slug" required>
                        <p class="mt-1 text-sm text-gray-500">Unique identifier for this widget</p>
                        <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Content -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Widget Content</label>
                        <textarea name="content" rows="10" 
                            class="w-full px-3 py-2 border <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter widget content..."><?php echo e(old('content')); ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">HTML or text content for the widget</p>
                        <?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Configuration -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Widget Configuration (Optional)</label>
                        <textarea name="config" rows="6" 
                            class="w-full px-3 py-2 border <?php $__errorArgs = ['config'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                            placeholder='{"key": "value"}'><?php echo e(old('config')); ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">JSON configuration for the widget (optional). Leave empty if not needed.</p>
                        <p class="mt-1 text-xs text-gray-400">Example: {"color": "blue", "size": "large"}</p>
                        <?php $__errorArgs = ['config'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Widget Settings -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Widget Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Widget Type *</label>
                                <select name="type" class="w-full px-3 py-2 border <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Select widget type</option>
                                    <?php $__currentLoopData = $widgetTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(old('type') == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                                <select name="location" class="w-full px-3 py-2 border <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="">Select location</option>
                                    <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(old('location') == $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                                <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="0" min="0">
                                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                                <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', '1') ? 'checked' : ''); ?> 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                                <input type="hidden" name="is_active" value="0">
                                <p class="mt-1 text-sm text-gray-500">Uncheck to disable this widget</p>
                            </div>
                        </div>
                    </div>

                    <!-- Widget Preview -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Preview</h3>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center text-gray-500">
                            <i class="fas fa-cube text-4xl mb-2"></i>
                            <p>Widget preview will appear here</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-end space-x-4">
                    <a href="<?php echo e(route('admin.widgets.index')); ?>" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Widget
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Console Debug Function
function logToConsole(message, type = 'info') {
    const consoleOutput = document.getElementById('console-output');
    const timestamp = new Date().toLocaleTimeString();
    const color = type === 'error' ? '#ff6b6b' : type === 'warning' ? '#feca57' : '#48dbfb';
    
    consoleOutput.innerHTML += `<div style="color: ${color}">[${timestamp}] ${message}</div>`;
    consoleOutput.scrollTop = consoleOutput.scrollHeight;
    
    // Also log to browser console
    console.log(`[${type.toUpperCase()}] ${message}`);
}

// Initialize console
logToConsole('Debug console initialized', 'info');
logToConsole('Current page: <?php echo e(url()->current()); ?>', 'info');

// Form submission debugging
document.querySelector('form').addEventListener('submit', function(e) {
    logToConsole('Form submission started...', 'info');
    
    const formData = new FormData(this);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
        logToConsole(`${key}: ${value}`, 'info');
    }
    
    // Check required fields specifically
    const requiredFields = ['title', 'slug', 'type', 'location'];
    let missingFields = [];
    
    requiredFields.forEach(field => {
        if (!data[field] || data[field].trim() === '') {
            missingFields.push(field);
            logToConsole(`❌ MISSING REQUIRED FIELD: ${field}`, 'error');
        } else {
            logToConsole(`✅ Field OK: ${field} = "${data[field]}"`, 'info');
        }
    });
    
    if (missingFields.length > 0) {
        logToConsole(`🚨 FORM WILL FAIL - Missing: ${missingFields.join(', ')}`, 'error');
        e.preventDefault(); // Prevent submission
        alert(`Please fill in all required fields: ${missingFields.join(', ')}`);
        return;
    }
    
    logToConsole(`Form data: ${JSON.stringify(data, null, 2)}`, 'info');
    logToConsole('✅ Form looks good, sending to server...', 'info');
});

// Track field changes with real-time validation
document.querySelectorAll('input, textarea, select').forEach(field => {
    field.addEventListener('change', function() {
        logToConsole(`Field changed: ${this.name} = ${this.value}`, 'info');
        validateField(this);
    });
    
    field.addEventListener('blur', function() {
        validateField(this);
    });
    
    // Real-time validation for text fields
    if (field.tagName === 'INPUT' || field.tagName === 'TEXTAREA') {
        field.addEventListener('input', function() {
            validateField(this);
        });
    }
});

// Real-time field validation
function validateField(field) {
    const fieldName = field.name;
    const value = field.value.trim();
    const isRequired = field.hasAttribute('required');
    
    // Reset field styling
    field.style.borderColor = '';
    field.style.borderWidth = '';
    field.style.boxShadow = '';
    
    // Remove previous error indicator
    const label = field.closest('div').querySelector('label');
    if (label) {
        label.innerHTML = label.innerHTML.replace(' <span style="color: #ef4444;">❌</span>', '');
    }
    
    let isValid = true;
    let errorMessage = '';
    
    // Check specific field validations
    switch(fieldName) {
        case 'title':
            if (isRequired && !value) {
                isValid = false;
                errorMessage = 'Title is required';
            } else if (value.length > 255) {
                isValid = false;
                errorMessage = 'Title too long (max 255 characters)';
            }
            break;
            
        case 'slug':
            if (isRequired && !value) {
                isValid = false;
                errorMessage = 'Slug is required';
            }
            break;
            
        case 'type':
        case 'location':
            if (isRequired && !value) {
                isValid = false;
                errorMessage = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} is required`;
            }
            break;
    }
    
    // Update field styling based on validation
    if (!isValid) {
        field.style.borderColor = '#ef4444';
        field.style.borderWidth = '2px';
        field.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
        
        if (label) {
            label.innerHTML += ' <span style="color: #ef4444;">❌</span>';
        }
        
        logToConsole(`🔴 Field validation failed: ${fieldName} - ${errorMessage}`, 'error');
    } else if (value) {
        field.style.borderColor = '#10b981';
        field.style.borderWidth = '2px';
        field.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
        
        if (label) {
            label.innerHTML += ' <span style="color: #10b981;">✅</span>';
        }
        
        logToConsole(`✅ Field validation passed: ${fieldName}`, 'info');
    }
}

function saveDraft() {
    logToConsole('Saving draft...', 'info');
    const form = document.querySelector('form');
    const activeField = form.querySelector('input[name="is_active"]');
    activeField.checked = false;
    form.submit();
}

function previewWidget() {
    logToConsole('Preview requested...', 'info');
    // You can implement preview functionality here
    alert('Preview functionality coming soon!');
}

// Check for existing errors on page load
document.addEventListener('DOMContentLoaded', function() {
    const errorElements = document.querySelectorAll('.text-red-600');
    if (errorElements.length > 0) {
        logToConsole(`Found ${errorElements.length} validation errors`, 'error');
        errorElements.forEach((elem, index) => {
            logToConsole(`Error ${index + 1}: ${elem.textContent}`, 'error');
            
            // Find the associated input field and highlight it
            const errorContainer = elem.closest('div');
            if (errorContainer) {
                const inputField = errorContainer.querySelector('input, textarea, select');
                if (inputField) {
                    inputField.style.borderColor = '#ef4444';
                    inputField.style.borderWidth = '2px';
                    inputField.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
                    
                    // Add error indicator
                    const errorLabel = errorContainer.querySelector('label');
                    if (errorLabel) {
                        errorLabel.innerHTML += ' <span style="color: #ef4444;">❌</span>';
                    }
                    
                    logToConsole(`🔴 Highlighted problematic field: ${inputField.name || inputField.type}`, 'error');
                }
            }
        });
        
        // Scroll to first error
        const firstError = errorElements[0];
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/admin/widgets/create.blade.php ENDPATH**/ ?>