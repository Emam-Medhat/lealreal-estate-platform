@extends('layouts.app')

@section('title', 'تفاصيل الإصدار')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تفاصيل الإصدار {{ $version->getFormattedVersionNumber() }}</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('document-versions.compare', [$document, $version, $nextVersion]) }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600">
                <i class="fas fa-exchange-alt ml-2"></i>مقارنة
            </a>
            @if($version->isLatest())
                <a href="{{ route('document-versions.edit', [$document, $version]) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                    <i class="fas fa-edit ml-2"></i>تعديل
                </a>
            @endif
            <a href="{{ route('document-versions.index', $document) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-right ml-2"></i>الإصدارات
            </a>
        </div>
    </div>

    <!-- Version Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">رقم الإصدار</label>
                <p class="text-lg font-semibold text-gray-900">{{ $version->getFormattedVersionNumber() }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">نوع الإصدار</label>
                <p class="text-gray-900">{{ $version->getVersionTypeLabel() }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">الحالة</label>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $version->isPublished() ? 'bg-green-100 text-green-800' : ($version->isDraft() ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ $version->getStatusLabel() }}
                </span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">المنشئ</label>
                <p class="text-gray-900">{{ $version->createdBy->name }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-gray-200">
            <div>
                <label class="block text-sm font-medium text-gray-700">تاريخ الإنشاء</label>
                <p class="text-gray-900">{{ $version->created_at->format('Y-m-d H:i') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">حجم المحتوى</label>
                <p class="text-gray-900">{{ $version->getContentLength() }} حرف</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">عدد الكلمات</label>
                <p class="text-gray-900">{{ $version->getContentWordCount() }} كلمة</p>
            </div>
        </div>
        
        @if($version->changes_summary)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">ملخص التغييرات</label>
                <p class="text-gray-700 bg-gray-50 rounded-lg p-3">{{ $version->changes_summary }}</p>
            </div>
        @endif
    </div>

    <!-- Navigation -->
    @if($previousVersion || $nextVersion)
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex justify-between items-center">
                @if($previousVersion)
                    <a href="{{ route('document-versions.show', [$document, $previousVersion]) }}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-chevron-right ml-2"></i>
                        الإصدار السابق (v{{ $previousVersion->version_number }})
                    </a>
                @endif
                
                <div class="text-center">
                    <span class="text-gray-500">الإصدار {{ $version->version_number }} من {{ $document->versions()->count() }}</span>
                </div>
                
                @if($nextVersion)
                    <a href="{{ route('document-versions.show', [$document, $nextVersion]) }}" class="text-blue-600 hover:text-blue-900">
                        الإصدار التالي (v{{ $nextVersion->version_number }})
                        <i class="fas fa-chevron-left ml-2"></i>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <!-- Content Display -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">محتوى الوثيقة</h2>
                <div class="flex space-x-2 space-x-reverse">
                    <button onclick="toggleViewMode()" class="bg-gray-500 text-white px-3 py-1 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-eye ml-1"></i>عرض
                    </button>
                    <button onclick="printContent()" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                        <i class="fas fa-print ml-1"></i>طباعة
                    </button>
                    <button onclick="downloadContent()" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                        <i class="fas fa-download ml-1"></i>تحميل
                    </button>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div id="content-display" class="bg-gray-50 rounded-lg p-4">
                <pre class="whitespace-pre-wrap text-sm text-gray-800">{{ $version->content }}</pre>
            </div>
            
            <div id="content-preview" class="prose max-w-none hidden">
                <div class="whitespace-pre-wrap text-sm text-gray-800">
                    {!! nl2br(e($version->content)) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Version History -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">سجل التغييرات</h2>
        
        <div class="space-y-4">
            <div class="border-l-4 border-blue-500 pl-4">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-800 rounded-full p-2 ml-3">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">إنشاء الإصدار</p>
                        <p class="text-sm text-gray-600">بواسطة {{ $version->createdBy->name }} في {{ $version->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                @if($version->changes_summary)
                    <p class="text-gray-700 mt-2">{{ $version->changes_summary }}</p>
                @endif
            </div>
            
            @if($version->updated_at && $version->updated_at != $version->created_at)
                <div class="border-l-4 border-yellow-500 pl-4">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 text-yellow-800 rounded-full p-2 ml-3">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">تعديل الإصدار</p>
                            <p class="text-sm text-gray-600">بواسطة {{ $version->updatedBy->name }} في {{ $version->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($version->published_at)
                <div class="border-l-4 border-green-500 pl-4">
                    <div class="flex items-center">
                        <div class="bg-green-100 text-green-800 rounded-full p-2 ml-3">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">نشر الإصدار</p>
                            <p class="text-sm text-gray-600">بواسطة {{ $version->publishedBy->name }} في {{ $version->published_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($version->archived_at)
                <div class="border-l-4 border-gray-500 pl-4">
                    <div class="flex items-center">
                        <div class="bg-gray-100 text-gray-800 rounded-full p-2 ml-3">
                            <i class="fas fa-archive"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">أرشفة الإصدار</p>
                            <p class="text-sm text-gray-600">بواسطة {{ $version->archivedBy->name }} في {{ $version->archived_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">الإجراءات</h2>
        
        <div class="flex flex-wrap gap-3">
            @if($version->isDraft())
                <a href="{{ route('document-versions.edit', [$document, $version]) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                    <i class="fas fa-edit ml-2"></i>تعديل
                </a>
                <a href="{{ route('document-versions.publish', $version) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                    <i class="fas fa-check ml-2"></i>نشر
                </a>
            @endif
            
            @if($version->isPublished() && !$version->isLatest())
                <a href="{{ route('document-versions.archive', $version) }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                    <i class="fas fa-archive ml-2"></i>أرشفة
                </a>
            @endif
            
            @if($version->isArchived())
                <form method="POST" action="{{ route('document-versions.destroy', $version) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإصدار؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        <i class="fas fa-trash ml-2"></i>حذف
                    </button>
                </form>
            @endif
            
            @if(!$version->isLatest())
                <a href="{{ route('document-versions.restore', $version) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-undo ml-2"></i>استعادة كإصدار حالي
                </a>
            @endif
            
            <a href="{{ route('document-versions.download', $version) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-download ml-2"></i>تحميل
            </a>
        </div>
    </div>
</div>

@section('scripts')
<script>
function toggleViewMode() {
    const contentDisplay = document.getElementById('content-display');
    const contentPreview = document.getElementById('content-preview');
    
    if (contentDisplay.classList.contains('hidden')) {
        contentDisplay.classList.remove('hidden');
        contentPreview.classList.add('hidden');
    } else {
        contentDisplay.classList.add('hidden');
        contentPreview.classList.remove('hidden');
    }
}

function printContent() {
    const content = document.getElementById('content-display').textContent;
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>طباعة الإصدار {{ $version->getFormattedVersionNumber() }}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                pre { white-space: pre-wrap; }
            </style>
        </head>
        <body>
            <h1>الإصدار {{ $version->getFormattedVersionNumber() }}</h1>
            <p>الوثيقة: {{ $document->title }}</p>
            <p>التاريخ: {{ now()->format('Y-m-d H:i') }}</p>
            <hr>
            <pre>${content}</pre>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

function downloadContent() {
    const content = document.getElementById('content-display').textContent;
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'version-{{ $version->version_number }}-{{ $document->title }}.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
