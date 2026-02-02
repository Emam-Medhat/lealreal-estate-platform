@extends('admin.layouts.admin')

@section('title', 'المكتبة الوسائطية')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">المكتبة الوسائطية</h1>
            <p class="text-gray-600 mt-2">إدارة الصور والفيديوهات والملفات</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors" onclick="document.getElementById('upload-modal').classList.remove('hidden')">
                <i class="fas fa-upload ml-2"></i>
                رفع ملفات
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-folder-plus ml-2"></i>
                مجلد جديد
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي الملفات</p>
                    <p class="text-2xl font-bold text-gray-900">1,847</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-photo-video text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">الصور</p>
                    <p class="text-2xl font-bold text-gray-900">1,234</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-image text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">الفيديوهات</p>
                    <p class="text-2xl font-bold text-gray-900">456</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <i class="fas fa-video text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">المساحة المستخدمة</p>
                    <p class="text-2xl font-bold text-gray-900">8.7 GB</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-database text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <!-- Search -->
                <div class="relative">
                    <input type="text" placeholder="ابحث في الملفات..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 w-64">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>

                <!-- Filter -->
                <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">كل الأنواع</option>
                    <option value="image">صور</option>
                    <option value="video">فيديوهات</option>
                    <option value="audio">صوتيات</option>
                    <option value="document">مستندات</option>
                </select>

                <!-- Date Filter -->
                <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">كل التواريخ</option>
                    <option value="today">اليوم</option>
                    <option value="week">آخر أسبوع</option>
                    <option value="month">آخر شهر</option>
                    <option value="year">آخر سنة</option>
                </select>
            </div>

            <div class="flex items-center space-x-2 space-x-reverse">
                <!-- View Toggle -->
                <div class="flex border border-gray-300 rounded-lg">
                    <button class="px-3 py-1 bg-purple-600 text-white rounded-r-lg">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="px-3 py-1 hover:bg-gray-50">
                        <i class="fas fa-list"></i>
                    </button>
                </div>

                <!-- Sort -->
                <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="date">الأحدث أولاً</option>
                    <option value="name">الاسم</option>
                    <option value="size">الحجم</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Breadcrumb -->
    <div class="flex items-center text-sm text-gray-600 mb-6">
        <i class="fas fa-home ml-2"></i>
        <span>الرئيسية</span>
        <i class="fas fa-chevron-left mx-2"></i>
        <span>المكتبة الوسائطية</span>
        <i class="fas fa-chevron-left mx-2"></i>
        <span class="text-gray-900">صور العقارات</span>
    </div>

    <!-- Media Grid -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @for($i = 1; $i <= 12; $i++)
                <div class="media-item group relative">
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:shadow-lg transition-shadow">
                        @if($i % 3 == 0)
                            <img src="https://via.placeholder.com/300x300" alt="Property Image" class="w-full h-full object-cover">
                        @elseif($i % 3 == 1)
                            <div class="w-full h-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center">
                                <i class="fas fa-image text-white text-4xl"></i>
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-400 to-green-400 flex items-center justify-center">
                                <i class="fas fa-video text-white text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="flex space-x-2 space-x-reverse">
                            <button class="bg-white text-gray-800 p-2 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="bg-white text-gray-800 p-2 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="bg-white text-gray-800 p-2 rounded-lg hover:bg-gray-100">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="bg-red-600 text-white p-2 rounded-lg hover:bg-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Selection Checkbox -->
                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <input type="checkbox" class="rounded border-gray-300 bg-white">
                    </div>

                    <!-- File Info -->
                    <div class="mt-2">
                        <p class="text-sm font-medium text-gray-900 truncate">property-{{ $i }}.jpg</p>
                        <p class="text-xs text-gray-600">{{ rand(100, 2000) }} KB • {{ now()->subDays(rand(1, 30))->format('M d, Y') }}</p>
                    </div>
                </div>
                @endfor
            </div>

            <!-- Load More -->
            <div class="mt-8 text-center">
                <button class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus ml-2"></i>
                    تحميل المزيد
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">رفع ملفات جديدة</h3>
            <button onclick="document.getElementById('upload-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Drop Zone -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-purple-500 transition-colors">
            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 mb-2">اسحب وأفلت الملفات هنا أو</p>
            <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                اختر الملفات
            </button>
            <p class="text-sm text-gray-500 mt-2">PNG, JPG, GIF, MP4, PDF (حتى 10MB)</p>
        </div>

        <!-- Upload Progress -->
        <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex items-center">
                    <i class="fas fa-image text-purple-600 ml-2"></i>
                    <span class="text-sm text-gray-700">image-1.jpg</span>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 ml-2">75%</span>
                    <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
            <button onclick="document.getElementById('upload-modal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                إلغاء
            </button>
            <button class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-upload ml-2"></i>
                بدء الرفع
            </button>
        </div>
    </div>
</div>

<!-- Media Preview Modal -->
<div id="preview-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
    <div class="relative max-w-4xl max-h-screen">
        <button onclick="document.getElementById('preview-modal').classList.add('hidden')" class="absolute -top-10 right-0 text-white hover:text-gray-300">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <img src="https://via.placeholder.com/800x600" alt="Preview" class="max-w-full max-h-screen rounded-lg">
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
    // Handle file selection
    const mediaItems = document.querySelectorAll('.media-item');
    mediaItems.forEach(item => {
        const img = item.querySelector('img');
        if (img) {
            img.addEventListener('click', function() {
                document.getElementById('preview-modal').classList.remove('hidden');
            });
        }
    });

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
            // Handle file upload
        });
    }
});
</script>
@endsection
