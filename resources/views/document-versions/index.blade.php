@extends('layouts.app')

@section('title', 'إصدارات الوثيقة')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">إصدارات الوثيقة: {{ $document->title }}</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('document-versions.create', $document) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus ml-2"></i>إصدار جديد
            </a>
            <a href="{{ route('documents.show', $document) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-right ml-2"></i>الوثيقة
            </a>
        </div>
    </div>

    <!-- Document Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">الوثيقة</label>
                <p class="text-gray-900 font-semibold">{{ $document->title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">الفئة</label>
                <p class="text-gray-900">{{ $document->category->name ?? 'غير محدد' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">الإصدار الحالي</label>
                <p class="text-gray-900 font-semibold">v{{ $document->versions()->latest()->first()?->version_number ?? '0' }}</p>
            </div>
        </div>
    </div>

    <!-- Versions List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-semibold">جميع الإصدارات</h2>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($versions as $version)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <h3 class="text-lg font-medium text-gray-900">الإصدار {{ $version->version_number }}</h3>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $version->isPublished() ? 'bg-green-100 text-green-800' : ($version->isDraft() ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }} mr-3">
                                    {{ $version->getStatusLabel() }}
                                </span>
                                @switch($version->version_type)
                                    @case('major')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 mr-2">رئيسي</span>
                                        @break
                                    @case('minor')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">ثانوي</span>
                                        @break
                                    @case('patch')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 mr-2">تصحيح</span>
                                        @break
                                @endswitch
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 mb-4">
                                <div>
                                    <span class="font-medium">المنشئ:</span> {{ $version->createdBy->name }}
                                </div>
                                <div>
                                    <span class="font-medium">تاريخ الإنشاء:</span> {{ $version->created_at->format('Y-m-d H:i') }}
                                </div>
                                <div>
                                    <span class="font-medium">حجم المحتوى:</span> {{ $version->getContentLength() }} حرف
                                </div>
                                <div>
                                    <span class="font-medium">عدد الكلمات:</span> {{ $version->getContentWordCount() }} كلمة
                                </div>
                            </div>
                            
                            @if($version->changes_summary)
                                <div class="mb-4">
                                    <h4 class="font-medium text-gray-900 mb-2">ملخص التغييرات:</h4>
                                    <p class="text-gray-600 text-sm">{{ $version->changes_summary }}</p>
                                </div>
                            @endif
                            
                            @if($version->isLatest())
                                <div class="flex items-center text-green-600 text-sm">
                                    <i class="fas fa-star ml-2"></i>
                                    هذا هو الإصدار الحالي
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex flex-col space-y-2 mr-4">
                            <a href="{{ route('document-versions.show', $version) }}" class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($version->isLatest())
                                <a href="{{ route('document-versions.edit', $version) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            @if(!$version->isLatest())
                                <a href="{{ route('document-versions.compare', [$version->document, $version]) }}" class="text-purple-600 hover:text-purple-900">
                                    <i class="fas fa-exchange-alt"></i>
                                </a>
                                <a href="{{ route('document-versions.restore', $version) }}" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-undo"></i>
                                </a>
                            @endif
                            <a href="{{ route('document-versions.download', $version) }}" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-download"></i>
                            </a>
                            @if($version->isDraft())
                                <a href="{{ route('document-versions.publish', $version) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-check"></i>
                                </a>
                            @endif
                            @if($version->isPublished() && !$version->isLatest())
                                <a href="{{ route('document-versions.archive', $version) }}" class="text-orange-600 hover:text-orange-900">
                                    <i class="fas fa-archive"></i>
                                </a>
                            @endif
                            @if($version->isArchived())
                                <a href="{{ route('document-versions.destroy', $version) }}" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <i class="fas fa-layer-group text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد إصدارات</h3>
                    <p class="text-gray-600 mb-4">لم يتم إنشاء أي إصدارات لهذه الوثيقة بعد</p>
                    <a href="{{ route('document-versions.create', $document) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-plus ml-2"></i>إنشاء أول إصدار
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Version Statistics -->
    @if($versions->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">إحصائيات الإصدارات</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">إجمالي الإصدارات</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $versions->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">منشورة</p>
                            <p class="text-2xl font-bold text-green-600">{{ $versions->published()->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">مسودات</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $versions->draft()->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-gray-500 text-white rounded-full p-3 ml-3">
                            <i class="fas fa-archive"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">مؤرشفة</p>
                            <p class="text-2xl font-bold text-gray-600">{{ $versions->archived()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
