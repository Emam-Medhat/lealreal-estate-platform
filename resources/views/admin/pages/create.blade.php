@extends('layouts.app')

@section('title', 'Create Page')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Create New Page</h1>
                        <p class="text-gray-600">Create a new static page</p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button type="button" onclick="saveDraft()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Draft
                    </button>
                    <button type="button" onclick="previewPage()" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
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

        <!-- Create Form -->
        <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Page Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                            class="w-full px-3 py-2 border @error('title') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
                            placeholder="Enter page title..." required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL Slug *</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" 
                            class="w-full px-3 py-2 border @error('slug') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="page-url-slug" required>
                        <p class="mt-1 text-sm text-gray-500">This will be used in the URL: /pages/your-slug</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Page Content *</label>
                        <textarea name="content" rows="20" 
                            class="w-full px-3 py-2 border @error('content') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Write your page content here..." required>{{ old('content') }}</textarea>
                        @error('content')
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select name="status" class="w-full px-3 py-2 border @error('status') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template *</label>
                                <select name="template" class="w-full px-3 py-2 border @error('template') border-red-500 @enderror border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <option value="default" {{ old('template') == 'default' ? 'selected' : '' }}>Default</option>
                                    <option value="about" {{ old('template') == 'about' ? 'selected' : '' }}>About</option>
                                    <option value="contact" {{ old('template') == 'contact' ? 'selected' : '' }}>Contact</option>
                                    <option value="services" {{ old('template') == 'services' ? 'selected' : '' }}>Services</option>
                                    <option value="faq" {{ old('template') == 'faq' ? 'selected' : '' }}>FAQ</option>
                                </select>
                                @error('template')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="0" min="0">
                                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first</p>
                                @error('sort_order')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="show_in_menu" {{ old('show_in_menu', true) ? 'checked' : '' }} 
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Show in menu</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Settings -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Menu Settings</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Menu Title</label>
                                <input type="text" name="menu_title" value="{{ old('menu_title') }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Menu display name (optional)">
                                <p class="mt-1 text-sm text-gray-500">Leave empty to use page title</p>
                                @error('menu_title')
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
                    <a href="{{ route('admin.pages.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Create Page
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Form submission validation
document.querySelector('form').addEventListener('submit', function(e) {
    // Check required fields specifically
    const requiredFields = ['title', 'slug', 'content', 'status', 'template'];
    let missingFields = [];
    
    const formData = new FormData(this);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    requiredFields.forEach(field => {
        if (!data[field] || data[field].trim() === '') {
            missingFields.push(field);
        }
    });
    
    if (missingFields.length > 0) {
        e.preventDefault(); // Prevent submission
        alert(`Please fill in all required fields: ${missingFields.join(', ')}`);
        return;
    }
});

function saveDraft() {
    const form = document.querySelector('form');
    const statusField = form.querySelector('select[name="status"]');
    statusField.value = 'draft';
    form.submit();
}

function previewPage() {
    alert('Preview functionality coming soon!');
}
</script>
@endsection
