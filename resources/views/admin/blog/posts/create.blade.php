@extends('layouts.app')

@section('title', 'Create Blog Post')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('admin.blog.posts.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Create New Blog Post</h1>
                        <p class="text-gray-600">Write and publish a new blog post</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="saveDraft()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Draft
                    </button>
                    <button type="button" onclick="previewPost()" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        Preview
                    </button>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <strong>✅ Success:</strong> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>❌ Error:</strong> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>⚠️ Please fix the following errors:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Debug Info -->
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <strong>🔍 Debug Info:</strong>
            <ul class="list-disc list-inside mt-2 text-sm">
                <li>Current URL: {{ url()->current() }}</li>
                <li>Route: {{ Route::currentRouteName() }}</li>
                <li>Authenticated: @if(auth()->check()) Yes ({{ auth()->user()->email }}) @else No @endif</li>
                <li>User ID: @if(auth()->check()) {{ auth()->id() }} @else Not logged in @endif</li>
                <li>Categories available: {{ $categories->count() ?? 0 }}</li>
                <li>Tags available: {{ $tags->count() ?? 0 }}</li>
                <li>Method: {{ request()->method() }}</li>
            </ul>
        </div>

        <!-- Console Debug -->
        <div class="bg-gray-100 border border-gray-400 text-gray-700 px-4 py-3 rounded mb-6">
            <strong>🖥️ Console Debug:</strong>
            <div id="console-output" class="mt-2 text-sm font-mono bg-black text-green-400 p-2 rounded max-h-40 overflow-y-auto">
                Console output will appear here...
            </div>
        </div>

        <!-- Create Form -->
        <form action="{{ route('admin.blog.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Post Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                            class="w-full px-3 py-2 border @error('title') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                            placeholder="Enter post title..." required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Content *</label>
                        <textarea name="content" rows="20" 
                            class="w-full px-3 py-2 border @error('content') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Write your post content here..." required>{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Excerpt -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                        <textarea name="excerpt" rows="3" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Brief description of the post...">{{ old('excerpt') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from content</p>
                        @error('excerpt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Featured Image -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                        <input type="file" name="featured_image" accept="image/*" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Recommended: 1200x630px, Max 2MB</p>
                        @error('featured_image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Publish Settings -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Publish Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
                                <input type="datetime-local" name="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('published_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="featured" {{ old('featured') ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Featured Post</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Category *</h3>
                        <select name="category" class="w-full px-3 py-2 border @error('category') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select a category</option>
                            @foreach ($categories ?? [] as $category)
                                <option value="{{ $category->slug }}" {{ old('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <a href="{{ route('admin.blog.categories.create') }}" class="mt-3 text-blue-600 hover:text-blue-800 text-sm inline-block">
                            <i class="fas fa-plus mr-1"></i>
                            Add New Category
                        </a>
                    </div>

                    <!-- Tags -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tags</h3>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach ($tags ?? [] as $tag)
                                <label class="flex items-center">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                                        {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if($tags->count() === 0)
                            <p class="text-sm text-gray-500">No tags available</p>
                        @else
                            <p class="mt-2 text-sm text-gray-500">Select relevant tags for this post</p>
                        @endif
                        @error('tags')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SEO Settings -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">SEO Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                                <input type="text" name="meta_title" value="{{ old('meta_title') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="SEO title (max 60 characters)" maxlength="60">
                                @error('meta_title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                                <textarea name="meta_description" rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="SEO description (max 160 characters)" maxlength="160">{{ old('meta_description') }}</textarea>
                                @error('meta_description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Focus Keyword</label>
                                <input type="text" name="focus_keyword" value="{{ old('focus_keyword') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Main keyword for this post">
                                @error('focus_keyword')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.blog.posts.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-publish mr-2"></i>
                        Publish Post
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
logToConsole('Current page: {{ url()->current() }}', 'info');

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
    const requiredFields = ['title', 'content', 'category', 'status'];
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
            
        case 'content':
            if (isRequired && !value) {
                isValid = false;
                errorMessage = 'Content is required';
            }
            break;
            
        case 'category':
            if (isRequired && !value) {
                isValid = false;
                errorMessage = 'Category is required';
            }
            break;
            
        case 'status':
            if (isRequired && !value) {
                isValid = false;
                errorMessage = 'Status is required';
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
    const statusField = form.querySelector('select[name="status"]');
    statusField.value = 'draft';
    form.submit();
}

function previewPost() {
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
    
    const successElements = document.querySelectorAll('.text-green-700');
    if (successElements.length > 0) {
        logToConsole(`Found ${successElements.length} success messages`, 'info');
    }
});

// Network monitoring
const originalFetch = window.fetch;
window.fetch = function(...args) {
    logToConsole(`Network request: ${args[0]} ${args[1]?.method || 'GET'}`, 'info');
    return originalFetch.apply(this, args).then(response => {
        logToConsole(`Response status: ${response.status} ${response.statusText}`, response.ok ? 'info' : 'error');
        return response;
    }).catch(error => {
        logToConsole(`Network error: ${error.message}`, 'error');
        throw error;
    });
};
</script>
@endsection
