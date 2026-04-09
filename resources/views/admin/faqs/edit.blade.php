@extends('admin.layouts.admin')

@section('title', 'Edit FAQ')
@section('page-title', 'FAQ Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.faqs.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Edit FAQ</h1>
                    <p class="text-sm text-gray-500">Update question details</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.faqs.show', $faq->id) }}" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors border border-gray-300 flex items-center gap-2">
                    <i class="fas fa-eye"></i>
                    <span>View</span>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Question & Answer -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-edit text-indigo-500"></i>
                                Content
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label for="question" class="block text-sm font-medium text-gray-700 mb-2">Question <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       id="question" 
                                       name="question" 
                                       value="{{ old('question', $faq->question) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                       placeholder="Enter the question"
                                       required>
                                @error('question')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">Answer <span class="text-red-500">*</span></label>
                                <textarea id="answer" 
                                          name="answer" 
                                          rows="8"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                          placeholder="Enter the answer"
                                          required>{{ old('answer', $faq->answer) }}</textarea>
                                @error('answer')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-semibold text-gray-800">Publishing</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer select-none">Active</label>
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $faq->is_active) ? 'checked' : '' }} class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer border-gray-300 checked:right-0 checked:border-green-500"/>
                                    <label for="is_active" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer checked:bg-green-500"></label>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between border-t border-gray-50 pt-4">
                                <label for="is_featured" class="text-sm font-medium text-gray-700 cursor-pointer select-none">Featured</label>
                                <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $faq->is_featured) ? 'checked' : '' }} class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer border-gray-300 checked:right-0 checked:border-yellow-500"/>
                                    <label for="is_featured" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer checked:bg-yellow-500"></label>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-gray-50">
                                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                                <input type="number" 
                                       id="order" 
                                       name="order" 
                                       value="{{ old('order', $faq->order) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                       min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Category Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-semibold text-gray-800">Category</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Select Category</label>
                                <select id="category" 
                                        name="category" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option value="">Select Category</option>
                                    @foreach ($categories ?? [] as $category)
                                        <option value="{{ $category }}" {{ old('category', $faq->category) == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="relative flex py-2 items-center">
                                <div class="flex-grow border-t border-gray-200"></div>
                                <span class="flex-shrink-0 mx-4 text-gray-400 text-xs">OR</span>
                                <div class="flex-grow border-t border-gray-200"></div>
                            </div>

                            <div>
                                <label for="new_category" class="block text-sm font-medium text-gray-700 mb-2">New Category</label>
                                <input type="text" 
                                       id="new_category" 
                                       name="new_category" 
                                       value="{{ old('new_category') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                       placeholder="Create new category">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors font-medium shadow-sm">
                            Save Changes
                        </button>
                        <a href="{{ route('admin.faqs.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.toggle-checkbox:checked {
    right: 0;
    border-color: #68D391;
}
.toggle-checkbox:checked + .toggle-label {
    background-color: #68D391;
}
#is_featured.toggle-checkbox:checked {
    border-color: #F6E05E;
}
#is_featured.toggle-checkbox:checked + .toggle-label {
    background-color: #F6E05E;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const newCategoryInput = document.getElementById('new_category');
    
    // Initial state
    if (categorySelect.value) {
        newCategoryInput.disabled = true;
        newCategoryInput.classList.add('bg-gray-100', 'cursor-not-allowed');
    }

    categorySelect.addEventListener('change', function() {
        if (this.value) {
            newCategoryInput.value = '';
            newCategoryInput.disabled = true;
            newCategoryInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        } else {
            newCategoryInput.disabled = false;
            newCategoryInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
        }
    });
    
    newCategoryInput.addEventListener('input', function() {
        if (this.value) {
            categorySelect.value = '';
        }
    });
});
</script>
@endsection
