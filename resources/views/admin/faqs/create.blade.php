@extends('layouts.app')

@section('title', 'Create FAQ')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('admin.faqs.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to FAQs
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Create New FAQ</h1>
            </div>
            <p class="text-gray-600">Create a new frequently asked question</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('admin.faqs.store') }}" method="POST">
                @csrf
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Basic Information</h3>
                    
                    <div class="space-y-6">
                        <!-- Question -->
                        <div>
                            <label for="question" class="block text-sm font-medium text-gray-700 mb-2">
                                Question <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="question" 
                                   name="question" 
                                   value="{{ old('question') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Enter the FAQ question"
                                   required>
                            @error('question')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Answer -->
                        <div>
                            <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">
                                Answer <span class="text-red-500">*</span>
                            </label>
                            <textarea id="answer" 
                                      name="answer" 
                                      rows="6"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Enter the detailed answer"
                                      required>{{ old('answer') }}</textarea>
                            @error('answer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <div class="flex space-x-2">
                                <select id="category" 
                                        name="category" 
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                    <option value="">Select a category</option>
                                    @foreach ($categories ?? [] as $category)
                                        <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="text" 
                                       id="new_category" 
                                       name="new_category" 
                                       value="{{ old('new_category') }}"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Or create new category">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Select an existing category or create a new one</p>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Additional Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Order -->
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                Display Order
                            </label>
                            <input type="number" 
                                   id="order" 
                                   name="order" 
                                   value="{{ old('order', 0) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   min="0"
                                   placeholder="0">
                            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                            @error('order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status Options -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Options</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           {{ old('is_active', '1') ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                                        Active
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="is_featured" 
                                           name="is_featured" 
                                           value="1"
                                           {{ old('is_featured') ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="is_featured" class="ml-2 text-sm text-gray-700">
                                        Featured
                                    </label>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Control visibility and highlighting</p>
                            @error('is_active')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('is_featured')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-between">
                    <a href="{{ route('admin.faqs.index') }}" 
                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    
                    <div class="flex space-x-2">
                        <button type="submit" 
                                name="save_and_continue"
                                value="1"
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Save & Continue
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Create FAQ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handle category selection
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const newCategoryInput = document.getElementById('new_category');
    
    categorySelect.addEventListener('change', function() {
        if (this.value) {
            newCategoryInput.value = '';
            newCategoryInput.disabled = true;
        } else {
            newCategoryInput.disabled = false;
        }
    });
    
    newCategoryInput.addEventListener('input', function() {
        if (this.value) {
            categorySelect.value = '';
        }
    });
    
    // Initialize state
    if (categorySelect.value) {
        newCategoryInput.disabled = true;
    }
});

// Auto-save draft functionality
let autoSaveTimer;
const form = document.querySelector('form');
const originalFormData = new FormData(form);

function autoSave() {
    const currentFormData = new FormData(form);
    const hasChanges = Array.from(currentFormData.entries()).some(([key, value]) => {
        return originalFormData.get(key) !== value;
    });
    
    if (hasChanges) {
        // Save to localStorage
        const formData = Object.fromEntries(currentFormData.entries());
        localStorage.setItem('faq_draft', JSON.stringify(formData));
        
        // Show indicator
        const indicator = document.createElement('div');
        indicator.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-3 py-1 rounded-md text-sm';
        indicator.textContent = 'Draft saved';
        document.body.appendChild(indicator);
        
        setTimeout(() => {
            indicator.remove();
        }, 2000);
    }
}

// Auto-save every 30 seconds
form.addEventListener('input', function() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(autoSave, 30000);
});

// Load draft on page load
window.addEventListener('load', function() {
    const draft = localStorage.getItem('faq_draft');
    if (draft) {
        const formData = JSON.parse(draft);
        Object.entries(formData).forEach(([key, value]) => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = value === '1';
                } else {
                    input.value = value;
                }
            }
        });
    }
});

// Clear draft on successful submission
form.addEventListener('submit', function() {
    localStorage.removeItem('faq_draft');
});
</script>
@endsection
