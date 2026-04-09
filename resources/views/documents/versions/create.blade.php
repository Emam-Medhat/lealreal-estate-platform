@extends('layouts.app')

@section('title', 'إنشاء إصدار جديد')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">إنشاء إصدار جديد</h1>
        <a href="{{ route('document-versions.index', $document) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-right ml-2"></i>عودة
        </a>
    </div>

    <!-- Document Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">الوثيقة</label>
                <p class="text-gray-900 font-semibold">{{ $document->title }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">الإصدار الحالي</label>
                <p class="text-gray-900">v{{ $document->versions()->latest()->first()?->version_number ?? '0' }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('document-versions.store', $document) }}" class="space-y-6">
        @csrf

        <!-- Version Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">معلومات الإصدار</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع الإصدار <span class="text-red-500">*</span></label>
                    <select name="version_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">اختر نوع الإصدار</option>
                        <option value="major">رئيسي - تغييرات كبيرة</option>
                        <option value="minor">ثانوي - تحسينات وإضافات</option>
                        <option value="patch">تصحيح - إصلاح أخطاء</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الإصدار المقترح</label>
                    <input type="text" value="{{ $document->versions()->latest()->first()?->version_number + 0.1 }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" readonly>
                    <p class="text-sm text-gray-500 mt-1">سيتم تحديده تلقائياً</p>
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">ملخص التغييرات <span class="text-red-500">*</span></label>
                <textarea name="changes_summary" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required placeholder="صف التغييرات التي تم إجراؤها في هذا الإصدار..."></textarea>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">المحتوى</h2>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">محتوى الوثيقة <span class="text-red-500">*</span></label>
                <textarea name="content" rows="15" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required placeholder="أدخل محتوى الوثيقة هنا...">{{ $document->versions()->latest()->first()?->content ?? '' }}</textarea>
                <p class="text-sm text-gray-500 mt-1">يمكنك نسخ المحتوى من الإصدار الحالي وتعديله</p>
            </div>
        </div>

        <!-- Settings -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">الإعدادات</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة الأولية</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="draft">مسودة</option>
                        <option value="published">منشور</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">النشر التلقائي</label>
                    <select name="auto_publish" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="0">لا</option>
                        <option value="1">نعم</option>
                    </select>
                </div>
            </div>
            
            @if($document->versions()->count() > 0)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">الإصدار المرجع (اختياري)</label>
                    <select name="restore_from" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">لا شيء</option>
                        @foreach($document->versions()->latest()->take(5) as $version)
                            <option value="{{ $version->id }}">v{{ $version->version_number }} - {{ $version->created_at->format('Y-m-d') }}</option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">استعادة المحتوى من إصدار سابق</p>
                </div>
            @endif
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 space-x-reverse">
            <a href="{{ route('document-versions.index', $document) }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                إلغاء
            </a>
            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-save ml-2"></i>إنشاء الإصدار
            </button>
        </div>
    </form>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const versionTypeSelect = document.querySelector('select[name="version_type"]');
    const versionNumberInput = document.querySelector('input[name="version_number"]');
    const autoPublishSelect = document.querySelector('select[name="auto_publish"]');
    
    function updateVersionNumber() {
        const currentVersion = {{ $document->versions()->latest()->first()?->version_number ?? 0 }};
        const versionType = versionTypeSelect.value;
        let newVersion = currentVersion;
        
        switch(versionType) {
            case 'major':
                newVersion = Math.floor(currentVersion) + 1;
                break;
            case 'minor':
                newVersion = Math.floor(currentVersion) + 0.1;
                break;
            case 'patch':
                newVersion = currentVersion + 0.01;
                break;
        }
        
        versionNumberInput.value = newVersion.toFixed(2);
        
        // Auto-publish for patches
        if (versionType === 'patch') {
            autoPublishSelect.value = '1';
        }
    }
    
    versionTypeSelect.addEventListener('change', updateVersionNumber);
});
</script>
@endsection
