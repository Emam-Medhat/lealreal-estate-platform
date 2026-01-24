@extends('layouts.app')

@section('title', 'معاينة الوثيقة')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">معاينة الوثيقة</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('documents.download', $document) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-download ml-2"></i>تحميل
            </a>
            <a href="{{ route('documents.show', $document) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-right ml-2"></i>عودة
            </a>
        </div>
    </div>

    <!-- Document Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">العنوان</label>
                <p class="text-gray-900 font-semibold">{{ $document->title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">الفئة</label>
                <p class="text-gray-900">{{ $document->category->name ?? 'غير محدد' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">الحالة</label>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $document->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $document->is_active ? 'نشط' : 'غير نشط' }}
                </span>
            </div>
        </div>
        
        @if($document->description)
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                <p class="text-gray-700">{{ $document->description }}</p>
            </div>
        @endif
    </div>

    <!-- Preview Area -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">معاينة الملف</h2>
                <div class="flex items-center space-x-4 space-x-reverse text-sm text-gray-600">
                    <span>{{ $document->file_name }}</span>
                    <span>{{ number_format($document->file_size / 1024, 2) }} KB</span>
                    <span>{{ $document->file_type }}</span>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            @if(in_array(strtolower($document->file_type), ['pdf']))
                <!-- PDF Preview -->
                <div class="bg-gray-100 rounded-lg p-4">
                    <iframe src="{{ asset('storage/' . $document->file_path) }}" 
                            width="100%" 
                            height="600px" 
                            class="border-0 rounded">
                    </iframe>
                </div>
            @elseif(in_array(strtolower($document->file_type), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                <!-- Image Preview -->
                <div class="text-center">
                    <img src="{{ asset('storage/' . $document->file_path) }}" 
                         alt="{{ $document->title }}" 
                         class="max-w-full h-auto mx-auto rounded-lg shadow-lg">
                </div>
            @elseif(in_array(strtolower($document->file_type), ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']))
                <!-- Office Document Preview -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-8 text-center">
                    <i class="fas fa-file-alt text-6xl text-blue-500 mb-4"></i>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">معاينة غير متاحة</h3>
                    <p class="text-blue-700 mb-4">نوع الملف {{ $document->file_type }} لا يدعم المعاينة المباشرة</p>
                    <a href="{{ route('documents.download', $document) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-download ml-2"></i>تحميل الملف للمعاينة
                    </a>
                </div>
            @else
                <!-- Other File Types -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-8 text-center">
                    <i class="fas fa-file text-6xl text-gray-500 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">معاينة غير متاحة</h3>
                    <p class="text-gray-700 mb-4">نوع الملف {{ $document->file_type }} لا يدعم المعاينة المباشرة</p>
                    <a href="{{ route('documents.download', $document) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        <i class="fas fa-download ml-2"></i>تحميل الملف
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Document Metadata -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">معلومات الوثيقة</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-medium text-gray-900 mb-3">المعلومات الأساسية</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">معرف الوثيقة:</dt>
                        <dd class="text-sm text-gray-900">#{{ $document->id }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">تاريخ الإنشاء:</dt>
                        <dd class="text-sm text-gray-900">{{ $document->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">آخر تحديث:</dt>
                        <dd class="text-sm text-gray-900">{{ $document->updated_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">المنشئ:</dt>
                        <dd class="text-sm text-gray-900">{{ $document->createdBy->name }}</dd>
                    </div>
                    @if($document->expires_at)
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600">تاريخ الانتهاء:</dt>
                            <dd class="text-sm text-gray-900 {{ $document->expires_at->isPast() ? 'text-red-600' : '' }}">
                                {{ $document->expires_at->format('Y-m-d') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
            
            <div>
                <h3 class="font-medium text-gray-900 mb-3">الأمان والتصنيف</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">مستوى السرية:</dt>
                        <dd class="text-sm text-gray-900">
                            @switch($document->confidentiality_level)
                                @case('public')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">عام</span>
                                    @break
                                @case('internal')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">داخلي</span>
                                    @break
                                @case('confidential')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">سري</span>
                                    @break
                                @case('restricted')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">مقيد</span>
                                    @break
                            @endswitch
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">حجم الملف:</dt>
                        <dd class="text-sm text-gray-900">{{ number_format($document->file_size / 1024, 2) }} KB</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">نوع الملف:</dt>
                        <dd class="text-sm text-gray-900">{{ $document->file_type }}</dd>
                    </div>
                    @if($document->tags && count($document->tags) > 0)
                        <div>
                            <dt class="text-sm text-gray-600">الوسوم:</dt>
                            <dd class="text-sm text-gray-900">
                                @foreach($document->tags as $tag)
                                    <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full mr-2 mb-1">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
