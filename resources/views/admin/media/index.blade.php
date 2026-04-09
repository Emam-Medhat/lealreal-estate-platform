@extends('admin.layouts.admin')

@section('title', 'مكتبة الوسائط')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">مكتبة الوسائط</h1>
            <p class="text-gray-600 mt-1">إدارة وتنظيم ملفات الوسائط</p>
        </div>
        <button onclick="openUploadModal()" 
                class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-upload"></i>
            <span>رفع ملفات جديدة</span>
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي الملفات</p>
                <p class="text-2xl font-bold text-gray-900">{{ $mediaFiles->total() }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-photo-video text-purple-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">الصور</p>
                <p class="text-2xl font-bold text-blue-600">{{ App\Models\MediaFile::where('type', 'image')->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-image text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">الفيديو</p>
                <p class="text-2xl font-bold text-green-600">{{ App\Models\MediaFile::where('type', 'video')->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-video text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">المستندات</p>
                <p class="text-2xl font-bold text-orange-600">{{ App\Models\MediaFile::where('type', 'document')->count() }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-file-alt text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">البحث والتصفية</h3>
    <form method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">النوع</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    <option value="">كل الأنواع</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <div class="relative">
                    <input type="text" name="search" 
                           class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" 
                           placeholder="بحث في الملفات..." 
                           value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="date_from" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                       value="{{ request('date_from') }}">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="date_to" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                       value="{{ request('date_to') }}">
            </div>
            
            <div class="flex items-end space-x-2 space-x-reverse">
                <button type="submit" class="flex-1 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2 space-x-reverse">
                    <i class="fas fa-search"></i>
                    <span>بحث</span>
                </button>
                <a href="{{ route('admin.media.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Bulk Actions -->
<div id="bulkActions" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4 space-x-reverse">
            <button onclick="bulkDelete()" 
                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-trash"></i>
                <span>حذف المحدد</span>
            </button>
            <button onclick="deselectAll()" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-times"></i>
                <span>إلغاء التحديد</span>
            </button>
        </div>
        <span class="text-yellow-800 font-medium" id="selectedCount">0 ملف محدد</span>
    </div>
</div>

<!-- Media Grid -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($mediaFiles->count() > 0)
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6" id="mediaGrid">
                @foreach($mediaFiles as $mediaFile)
                    <div class="media-item group relative">
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-200">
                            <!-- Selection Checkbox -->
                            <div class="absolute top-2 right-2 z-10">
                                <input type="checkbox" 
                                       class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500" 
                                       value="{{ $mediaFile->id }}" 
                                       onchange="updateBulkActions()">
                            </div>
                            
                            <!-- Media Preview -->
                            <div class="relative aspect-square bg-gray-100">
                                @if($mediaFile->type == 'image')
                                    <img src="{{ $mediaFile->getUrl() }}" 
                                         class="w-full h-full object-cover"
                                         alt="{{ $mediaFile->alt_text ?? $mediaFile->filename }}">
                                @elseif($mediaFile->type == 'video')
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                        <i class="fas fa-video text-4xl text-gray-400"></i>
                                    </div>
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                        <i class="fas fa-file text-4xl text-gray-400"></i>
                                    </div>
                                @endif
                                
                                <!-- Type Badge -->
                                <div class="absolute top-2 left-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $mediaFile->type }}
                                    </span>
                                </div>
                                
                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <button onclick="previewMedia({{ $mediaFile->id }})" 
                                            class="bg-white text-gray-900 p-2 rounded-full mx-1 hover:bg-gray-100 transition-colors"
                                            title="معاينة">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- File Info -->
                            <div class="p-3">
                                <h4 class="text-sm font-medium text-gray-900 truncate" title="{{ $mediaFile->filename }}">
                                    {{ $mediaFile->filename }}
                                </h4>
                                <p class="text-xs text-gray-500 mt-1">{{ $mediaFile->getFormattedFileSize() }}</p>
                                @if($mediaFile->dimensions)
                                    <p class="text-xs text-gray-500">{{ $mediaFile->dimensions }}</p>
                                @endif
                                
                                <!-- Action Buttons -->
                                <div class="flex items-center justify-between mt-3 space-x-2 space-x-reverse">
                                    <button onclick="previewMedia({{ $mediaFile->id }})" 
                                            class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50 transition-colors"
                                            title="معاينة">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <a href="{{ route('admin.media.edit', $mediaFile) }}" 
                                       class="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50 transition-colors"
                                       title="تعديل">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <a href="{{ route('admin.media.download', $mediaFile) }}" 
                                       class="text-purple-600 hover:text-purple-800 p-1 rounded hover:bg-purple-50 transition-colors"
                                       title="تحميل">
                                        <i class="fas fa-download text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.media.destroy', $mediaFile) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50 transition-colors"
                                                title="حذف"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    عرض {{ $mediaFiles->firstItem() ?? 0 }} - {{ $mediaFiles->lastItem() ?? 0 }} من {{ $mediaFiles->total() }} ملف
                </div>
                <div class="flex items-center space-x-2 space-x-reverse">
                    {{ $mediaFiles->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16">
            <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <i class="fas fa-photo-video text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-2">لا توجد ملفات</h3>
            <p class="text-gray-600 mb-8">ابدأ برفع بعض الملفات إلى مكتبة الوسائط</p>
            <button onclick="openUploadModal()" 
                    class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-upload"></i>
                <span>رفع ملفات جديدة</span>
            </button>
        </div>
    @endif
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">رفع ملفات جديدة</h3>
                <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اختر الملفات</label>
                    <input type="file" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                           name="files[]" 
                           multiple 
                           accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx" 
                           required>
                    <p class="text-sm text-gray-500 mt-1">يمكنك اختيار ملفات متعددة. الحد الأقصى للملف: 10MB</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نص بديل (للصور)</label>
                    <input type="text" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                           name="alt_text" 
                           placeholder="سيتم تطبيقه على جميع الصور المرفوعة">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">وصف</label>
                    <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                              name="caption" 
                              rows="3" 
                              placeholder="سيتم تطبيقه على جميع الملفات المرفوعة"></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                    <input type="text" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" 
                           name="category" 
                           placeholder="مثال: عام، مقالات، منتجات">
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 space-x-reverse mt-6">
                <button type="button" 
                        onclick="closeUploadModal()" 
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    إلغاء
                </button>
                <button type="submit" 
                        class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2 space-x-reverse">
                    <i class="fas fa-upload"></i>
                    <span>رفع الملفات</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">معاينة الملف</h3>
                <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div id="previewContent" class="p-6">
            <!-- Preview content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.media-item input[type="checkbox"]:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkboxes.length > 0) {
        bulkActions.classList.remove('hidden');
        selectedCount.textContent = checkboxes.length + ' ملف محدد';
    } else {
        bulkActions.classList.add('hidden');
    }
}

function deselectAll() {
    document.querySelectorAll('.media-item input[type="checkbox"]').forEach(cb => cb.checked = false);
    updateBulkActions();
}

function bulkDelete() {
    const checkboxes = document.querySelectorAll('.media-item input[type="checkbox"]:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (confirm('هل أنت متأكد من حذف ' + ids.length + ' ملف؟')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.media.bulk-delete") }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'files[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function previewMedia(id) {
    fetch(`/admin/media/${id}/preview`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('previewContent').innerHTML = html;
            document.getElementById('previewModal').classList.remove('hidden');
        });
}

function openUploadModal() {
    document.getElementById('uploadModal').classList.remove('hidden');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
}

function closePreviewModal() {
    document.getElementById('previewModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('uploadModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUploadModal();
    }
});

document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreviewModal();
    }
});
</script>
@endpush
