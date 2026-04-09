@extends('admin.layouts.admin')

@section('title', 'منشئ القوائم')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">منشئ القوائم</h1>
            <p class="text-gray-600 mt-2">إنشاء وإدارة قوائم الموقع</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                قائمة جديدة
            </button>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-save ml-2"></i>
                حفظ التغييرات
            </button>
        </div>
    </div>

    <!-- Menu Selection -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <label class="text-sm font-medium text-gray-700">اختر القائمة:</label>
                <select class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="main">القائمة الرئيسية</option>
                    <option value="footer">تذييل الصفحة</option>
                    <option value="user">قائمة المستخدم</option>
                    <option value="admin">قائمة الإدارة</option>
                </select>
                <button class="text-blue-600 hover:text-blue-900">
                    <i class="fas fa-edit ml-1"></i>تعديل القائمة
                </button>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-undo ml-1"></i>تراجع
                </button>
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-redo ml-1"></i>إعادة
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Menu Structure -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">هيكل القائمة</h3>
                </div>
                <div class="p-6">
                    <div id="menu-builder" class="space-y-2">
                        <!-- Root Level Items -->
                        <div class="menu-item" data-level="0">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center flex-1">
                                    <i class="fas fa-grip-vertical text-gray-400 ml-3 cursor-move"></i>
                                    <div class="bg-blue-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-home text-blue-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" value="الرئيسية" class="font-medium text-gray-900 bg-transparent border-none focus:outline-none">
                                        <p class="text-sm text-gray-600">/</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <button class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="menu-item" data-level="0">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center flex-1">
                                    <i class="fas fa-grip-vertical text-gray-400 ml-3 cursor-move"></i>
                                    <div class="bg-purple-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-building text-purple-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" value="العقارات" class="font-medium text-gray-900 bg-transparent border-none focus:outline-none">
                                        <p class="text-sm text-gray-600">/properties</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <button class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Sub-items -->
                            <div class="mr-8 mt-2 space-y-2">
                                <div class="menu-item" data-level="1">
                                    <div class="flex items-center p-2 bg-white rounded-lg border border-gray-200">
                                        <i class="fas fa-grip-vertical text-gray-400 ml-3 cursor-move"></i>
                                        <div class="bg-gray-100 p-1 rounded ml-3">
                                            <i class="fas fa-home text-gray-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <input type="text" value="شقق" class="text-sm text-gray-900 bg-transparent border-none focus:outline-none">
                                            <p class="text-xs text-gray-600">/properties/apartments</p>
                                        </div>
                                        <div class="flex items-center space-x-1 space-x-reverse">
                                            <button class="text-blue-600 hover:text-blue-900 text-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900 text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="menu-item" data-level="1">
                                    <div class="flex items-center p-2 bg-white rounded-lg border border-gray-200">
                                        <i class="fas fa-grip-vertical text-gray-400 ml-3 cursor-move"></i>
                                        <div class="bg-gray-100 p-1 rounded ml-3">
                                            <i class="fas fa-home text-gray-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <input type="text" value="فلل" class="text-sm text-gray-900 bg-transparent border-none focus:outline-none">
                                            <p class="text-xs text-gray-600">/properties/villas</p>
                                        </div>
                                        <div class="flex items-center space-x-1 space-x-reverse">
                                            <button class="text-blue-600 hover:text-blue-900 text-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="text-red-600 hover:text-red-900 text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="menu-item" data-level="0">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center flex-1">
                                    <i class="fas fa-grip-vertical text-gray-400 ml-3 cursor-move"></i>
                                    <div class="bg-green-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-users text-green-600"></i>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" value="الوكلاء" class="font-medium text-gray-900 bg-transparent border-none focus:outline-none">
                                        <p class="text-sm text-gray-600">/agents</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <button class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add New Item Button -->
                    <div class="mt-4">
                        <button class="w-full p-3 border-2 border-dashed border-gray-300 rounded-lg hover:border-gray-400 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-plus text-gray-400 ml-2"></i>
                            <span class="text-gray-600">إضافة عنصر جديد</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Details Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">تفاصيل العنصر</h3>
                </div>
                <div class="p-6">
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">النص</label>
                            <input type="text" value="العقارات" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الرابط</label>
                            <input type="text" value="/properties" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الأيقونة</label>
                            <div class="grid grid-cols-6 gap-2">
                                <button type="button" class="p-2 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-home text-gray-600"></i>
                                </button>
                                <button type="button" class="p-2 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-building text-gray-600"></i>
                                </button>
                                <button type="button" class="p-2 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-users text-gray-600"></i>
                                </button>
                                <button type="button" class="p-2 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-newspaper text-gray-600"></i>
                                </button>
                                <button type="button" class="p-2 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-phone text-gray-600"></i>
                                </button>
                                <button type="button" class="p-2 border border-gray-300 rounded hover:bg-gray-50">
                                    <i class="fas fa-envelope text-gray-600"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الهدف</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="_self">نفس النافذة</option>
                                <option value="_blank">نافذة جديدة</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الفئات</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 ml-2" checked>
                                    <span class="text-sm text-gray-700">رئيسي</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 ml-2">
                                    <span class="text-sm text-gray-700">تذييل</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 ml-2">
                                    <span class="text-sm text-gray-700">مستخدم</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الترتيب</label>
                            <input type="number" value="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 ml-2" checked>
                            <label class="text-sm text-gray-700">نشط</label>
                        </div>

                        <div class="pt-4 space-y-2">
                            <button type="button" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-save ml-2"></i>حفظ التغييرات
                            </button>
                            <button type="button" class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                                <i class="fas fa-times ml-2"></i>إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Locations -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mt-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">مواقع القوائم</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">القائمة الرئيسية</h4>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">نشط</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">القائمة الرئيسية في رأس الصفحة</p>
                    <div class="flex items-center justify-between">
                        <select class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option>القائمة الرئيسية</option>
                            <option>قائمة بديلة</option>
                        </select>
                        <button class="text-blue-600 hover:text-blue-900 text-sm">
                            <i class="fas fa-cog ml-1"></i>إعدادات
                        </button>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">تذييل الصفحة</h4>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">نشط</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">الروابط في تذييل الصفحة</p>
                    <div class="flex items-center justify-between">
                        <select class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option>تذييل الصفحة</option>
                            <option>قائمة بديلة</option>
                        </select>
                        <button class="text-blue-600 hover:text-blue-900 text-sm">
                            <i class="fas fa-cog ml-1"></i>إعدادات
                        </button>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">قائمة المستخدم</h4>
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">معطل</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">قائمة خاصة بالمستخدمين المسجلين</p>
                    <div class="flex items-center justify-between">
                        <select class="text-sm border border-gray-300 rounded px-2 py-1" disabled>
                            <option>غير محدد</option>
                        </select>
                        <button class="text-blue-600 hover:text-blue-900 text-sm">
                            <i class="fas fa-cog ml-1"></i>إعدادات
                        </button>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">قائمة الإدارة</h4>
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">نشط</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-3">قائمة لوحة التحكم</p>
                    <div class="flex items-center justify-between">
                        <select class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option>قائمة الإدارة</option>
                            <option>قائمة بديلة</option>
                        </select>
                        <button class="text-blue-600 hover:text-blue-900 text-sm">
                            <i class="fas fa-cog ml-1"></i>إعدادات
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.menu-item {
    transition: all 0.2s ease;
}

.menu-item:hover {
    transform: translateX(2px);
}

.sortable-ghost {
    opacity: 0.4;
}

.sortable-drag {
    opacity: 0.9;
}
</style>

<script>
// Simple drag and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuBuilder = document.getElementById('menu-builder');
    let draggedElement = null;

    menuBuilder.addEventListener('dragstart', function(e) {
        if (e.target.classList.contains('menu-item')) {
            draggedElement = e.target;
            e.target.style.opacity = '0.5';
        }
    });

    menuBuilder.addEventListener('dragend', function(e) {
        if (e.target.classList.contains('menu-item')) {
            e.target.style.opacity = '';
        }
    });

    menuBuilder.addEventListener('dragover', function(e) {
        e.preventDefault();
        const afterElement = getDragAfterElement(menuBuilder, e.clientY);
        if (afterElement == null) {
            menuBuilder.appendChild(draggedElement);
        } else {
            menuBuilder.insertBefore(draggedElement, afterElement);
        }
    });

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.menu-item:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
});
</script>
@endsection
