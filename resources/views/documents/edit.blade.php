@extends('layouts.app')

@section('title', 'تعديل الوثيقة')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تعديل الوثيقة</h1>
        <a href="{{ route('documents.show', $document) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-right ml-2"></i>عودة
        </a>
    </div>

    <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">المعلومات الأساسية</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العنوان <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ $document->title }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الفئة <span class="text-red-500">*</span></label>
                    <select name="category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">اختر الفئة</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $document->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                    <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $document->description }}</textarea>
                </div>
            </div>
        </div>

        <!-- File Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">ملف الوثيقة</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">الملف الحالي</label>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium">{{ $document->file_name }}</p>
                        <p class="text-sm text-gray-600">{{ number_format($document->file_size / 1024, 2) }} KB - {{ $document->file_type }}</p>
                    </div>
                    <a href="{{ route('documents.download', $document) }}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">استبدال الملف (اختياري)</label>
                <input type="file" name="file" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                <p class="text-sm text-gray-500 mt-1">اترك هذا الحقل فارغاً إذا كنت تريد الاحتفاظ بالملف الحالي</p>
            </div>
        </div>

        <!-- Tags and Classification -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">الوسوم والتصنيف</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الوسوم</label>
                    <input type="text" name="tags" value="{{ implode(', ', $document->tags ?? []) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل الوسوم مفصولة بفاصلة">
                    <p class="text-sm text-gray-500 mt-1">مثال: عقد، إيجار، سكني</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">مستوى السرية</label>
                    <select name="confidentiality_level" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="public" {{ $document->confidentiality_level == 'public' ? 'selected' : '' }}>عام</option>
                        <option value="internal" {{ $document->confidentiality_level == 'internal' ? 'selected' : '' }}>داخلي</option>
                        <option value="confidential" {{ $document->confidentiality_level == 'confidential' ? 'selected' : '' }}>سري</option>
                        <option value="restricted" {{ $document->confidentiality_level == 'restricted' ? 'selected' : '' }}>مقيد</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الانتهاء</label>
                    <input type="date" name="expires_at" value="{{ $document->expires_at?->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" {{ $document->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <label class="mr-2 text-sm text-gray-700">نشط</label>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 space-x-reverse">
            <a href="{{ route('documents.show', $document) }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                إلغاء
            </a>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-save ml-2"></i>حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
