@extends('admin.layouts.admin')

@section('title', 'المكتبة الوسائطية')
@section('page-title', 'المكتبة الوسائطية')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32 animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 rounded-full -ml-24 -mt-24 animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="h-full w-full" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-25 rounded-2xl p-4 ml-4 backdrop-blur-sm border border-white border-opacity-40 shadow-xl transform hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-photo-video text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">المكتبة الوسائطية</h1>
                                <p class="text-white text-xl font-medium">إدارة الصور والفيديوهات والملفات بأسلوب احترافي</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 mt-6">
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">إجمالي الملفات:</span>
                                <span class="text-lg font-black text-white ml-2">1,847</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">المساحة:</span>
                                <span class="text-lg font-black text-white ml-2">8.7 GB</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <button class="inline-flex items-center px-8 py-4 bg-white text-purple-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50" onclick="document.getElementById('upload-modal').classList.remove('hidden')">
                            <i class="fas fa-upload ml-2"></i>
                            رفع ملفات
                        </button>
                        <button class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-400 to-indigo-500 text-white rounded-2xl text-sm font-bold hover:from-blue-500 hover:to-indigo-600 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-blue-500/50" onclick="document.getElementById('create-folder-modal').classList.remove('hidden')">
                            <i class="fas fa-folder-plus ml-2"></i>
                            مجلد جديد
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-2xl shadow-lg border border-purple-200 p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 font-medium mb-1">إجمالي الملفات</p>
                    <p class="text-3xl font-black text-purple-900">1,847</p>
                    <p class="text-xs text-purple-600 mt-2">جميع الملفات المسجلة</p>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-pink-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-photo-video text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl shadow-lg border border-blue-200 p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium mb-1">الصور</p>
                    <p class="text-3xl font-black text-blue-900">1,234</p>
                    <p class="text-xs text-blue-600 mt-2">ملفات الصور</p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-indigo-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-image text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-orange-100 rounded-2xl shadow-lg border border-red-200 p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-red-600 font-medium mb-1">الفيديوهات</p>
                    <p class="text-3xl font-black text-red-900">456</p>
                    <p class="text-xs text-red-600 mt-2">ملفات الفيديو</p>
                </div>
                <div class="bg-gradient-to-br from-red-400 to-orange-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-video text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl shadow-lg border border-green-200 p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium mb-1">المساحة المستخدمة</p>
                    <p class="text-3xl font-black text-green-900">8.7 GB</p>
                    <p class="text-xs text-green-600 mt-2">من مساحة التخزين</p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-emerald-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-database text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Toolbar -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse flex-wrap">
                <!-- Search -->
                <div class="relative">
                    <input type="text" placeholder="ابحث في الملفات..." class="pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 w-80 font-medium">
                    <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                </div>

                <!-- Filter -->
                <select class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-medium">
                    <option value="">كل الأنواع</option>
                    <option value="image">صور</option>
                    <option value="video">فيديوهات</option>
                    <option value="audio">صوتيات</option>
                    <option value="document">مستندات</option>
                </select>

                <!-- Date Filter -->
                <select class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-medium">
                    <option value="">كل التواريخ</option>
                    <option value="today">اليوم</option>
                    <option value="week">آخر أسبوع</option>
                    <option value="month">آخر شهر</option>
                    <option value="year">آخر سنة</option>
                </select>
            </div>

            <div class="flex items-center space-x-3 space-x-reverse">
                <!-- View Toggle -->
                <div class="flex border border-gray-300 rounded-xl overflow-hidden">
                    <button class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="px-4 py-2 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-list"></i>
                    </button>
                </div>

                <!-- Sort -->
                <select class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-medium">
                    <option value="date">الأحدث أولاً</option>
                    <option value="name">الاسم</option>
                    <option value="size">الحجم</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Enhanced Breadcrumb -->
    <div class="flex items-center text-sm font-medium mb-6 bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-3 rounded-xl border border-gray-200">
        <i class="fas fa-home ml-2 text-indigo-600"></i>
        <span class="text-gray-700">الرئيسية</span>
        <i class="fas fa-chevron-left mx-2 text-gray-400"></i>
        <span class="text-gray-700">المكتبة الوسائطية</span>
        <i class="fas fa-chevron-left mx-2 text-gray-400"></i>
        <span class="text-indigo-600 font-bold">صور العقارات</span>
    </div>

    <!-- Enhanced Media Grid -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-images ml-3 text-purple-600"></i>
                الملفات الوسائطية
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @forelse($mediaFiles as $file)
                <div class="media-item group relative">
                    <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl overflow-hidden cursor-pointer hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border border-gray-200">
                        @if($file->type === 'folder')
                            <div class="w-full h-full bg-gradient-to-br from-yellow-100 to-orange-100 flex items-center justify-center">
                                <i class="fas fa-folder text-yellow-500 text-6xl"></i>
                            </div>
                        @elseif($file->type === 'image')
                            <img src="{{ asset('storage/' . $file->path) }}" alt="{{ $file->alt_text }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                        @elseif($file->type === 'video')
                            <div class="w-full h-full bg-gradient-to-br from-red-400 to-pink-400 flex items-center justify-center">
                                <i class="fas fa-video text-white text-5xl"></i>
                            </div>
                        @elseif($file->type === 'audio')
                            <div class="w-full h-full bg-gradient-to-br from-blue-400 to-green-400 flex items-center justify-center">
                                <i class="fas fa-music text-white text-5xl"></i>
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                <i class="fas fa-file text-gray-500 text-5xl"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Enhanced Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-black via-black to-transparent bg-opacity-0 group-hover:bg-opacity-75 transition-all duration-300 rounded-2xl flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="flex space-x-2 space-x-reverse">
                            @if($file->type !== 'folder')
                            <button class="bg-white text-gray-800 p-3 rounded-xl hover:bg-gray-100 transform hover:scale-110 transition-all duration-200 shadow-lg" onclick="showPreview('{{ asset('storage/' . $file->path) }}', '{{ $file->original_name }}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="{{ route('admin.media.download', $file->id) }}" class="bg-white text-gray-800 p-3 rounded-xl hover:bg-gray-100 transform hover:scale-110 transition-all duration-200 shadow-lg">
                                <i class="fas fa-download"></i>
                            </a>
                            @endif
                            <form action="{{ route('admin.media.destroy', $file->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white p-3 rounded-xl hover:bg-red-700 transform hover:scale-110 transition-all duration-200 shadow-lg">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Enhanced File Info -->
                    <div class="mt-3">
                        <p class="text-sm font-bold text-gray-900 truncate" title="{{ $file->original_name }}">{{ $file->original_name }}</p>
                        @if($file->type !== 'folder')
                        <p class="text-xs text-gray-600 flex items-center mt-1">
                            <i class="fas fa-hdd ml-1 text-gray-400"></i>
                            {{ $file->getFormattedFileSize() }} • 
                            <i class="fas fa-calendar ml-1 mr-1 text-gray-400"></i>
                            {{ $file->created_at->format('M d, Y') }}
                        </p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-16">
                    <div class="flex flex-col items-center">
                        <div class="bg-gradient-to-br from-gray-100 to-gray-200 p-12 rounded-3xl mb-6">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-7xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-3">لا توجد ملفات حالياً</h3>
                        <p class="text-gray-500 mb-8 text-lg">قم برفع ملفات جديدة لتبدأ في إدارة المكتبة الوسائطية</p>
                        <button class="px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-2xl font-bold hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-purple-500/50" onclick="document.getElementById('upload-modal').classList.remove('hidden')">
                            <i class="fas fa-upload ml-3"></i>
                            رفع ملفات جديدة
                        </button>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Enhanced Pagination -->
            <div class="mt-12">
                {{ $mediaFiles->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="relative max-w-4xl w-full mx-4">
        <button onclick="document.getElementById('upload-modal').classList.add('hidden')" class="absolute -top-10 right-0 text-white hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" id="media-upload-form">
                @csrf
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">رفع ملفات جديدة</h3>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">المجلد (اختياري)</label>
                        <select name="category" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">عام</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-purple-500 transition-colors cursor-pointer" onclick="document.getElementById('file-input').click()">
                        <div class="mb-4">
                            <i class="fas fa-cloud-upload-alt text-6xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600">اسحب وأفلت الملفات هنا أو</p>
                            <button type="button" class="bg-purple-600 text-white px-6 py-3 rounded-xl hover:bg-purple-700 transition-colors mt-2">
                                <i class="fas fa-folder-open ml-2"></i>
                                اختر الملفات
                            </button>
                        </div>
                        <input type="file" name="files[]" multiple class="hidden" id="file-input" onchange="updateFileList(this)">
                        <div id="file-list" class="mt-4 text-sm text-gray-600"></div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-reverse space-x-4">
                    <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')" class="bg-gray-500 text-white px-6 py-3 rounded-xl hover:bg-gray-600 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300">
                        <i class="fas fa-upload ml-2"></i>
                        رفع الملفات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div id="create-folder-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="relative max-w-md w-full mx-4">
        <button onclick="document.getElementById('create-folder-modal').classList.add('hidden')" class="absolute -top-10 right-0 text-white hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <form action="{{ route('admin.media.create-folder') }}" method="POST">
                @csrf
                <h3 class="text-2xl font-bold text-gray-900 mb-6">إنشاء مجلد جديد</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم المجلد</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="أدخل اسم المجلد...">
                </div>
                
                <div class="flex justify-end space-x-reverse space-x-4">
                    <button type="button" onclick="document.getElementById('create-folder-modal').classList.add('hidden')" class="bg-gray-500 text-white px-6 py-3 rounded-xl hover:bg-gray-600 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                        <i class="fas fa-folder-plus ml-2"></i>
                        إنشاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Media Preview Modal -->
<div id="preview-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="relative max-w-4xl max-h-screen">
        <button onclick="document.getElementById('preview-modal').classList.add('hidden')" class="absolute -top-10 right-0 text-white hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <img src="/placeholder/property-800x600.jpg" alt="Preview" class="max-w-full max-h-screen rounded-lg">
        <div class="absolute bottom-4 left-4 right-4 bg-black bg-opacity-75 text-white p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-semibold">property-1.jpg</h4>
                    <p class="text-sm">1200 x 800 • 1.2 MB • JPEG</p>
                </div>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="bg-white text-gray-800 px-3 py-1 rounded hover:bg-gray-100">
                        <i class="fas fa-edit ml-1"></i>تعديل
                    </button>
                    <button class="bg-white text-gray-800 px-3 py-1 rounded hover:bg-gray-100">
                        <i class="fas fa-download ml-1"></i>تحميل
                    </button>
                    <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                        <i class="fas fa-trash ml-1"></i>حذف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize media library functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle drag and drop
    const dropZone = document.querySelector('.border-dashed');
    if (dropZone) {
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-purple-500', 'bg-purple-50');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-500', 'bg-purple-50');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-500', 'bg-purple-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('file-input');
                input.files = files;
                updateFileList(input);
            }
        });
    }
});

function updateFileList(input) {
    const fileList = document.getElementById('file-list');
    if (input.files.length > 0) {
        fileList.innerHTML = `<p>تم اختيار ${input.files.length} ملفات:</p><ul class="list-disc list-inside">` + 
            Array.from(input.files).map(file => `<li>${file.name}</li>`).join('') + 
            '</ul>';
    } else {
        fileList.innerHTML = '';
    }
}

function showPreview(url, name) {
    const modal = document.getElementById('preview-modal');
    modal.querySelector('img').src = url;
    modal.querySelector('h4').textContent = name;
    modal.classList.remove('hidden');
}
</script>
@endsection
