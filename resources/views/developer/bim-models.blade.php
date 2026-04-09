@extends('admin.layouts.admin')

@section('title', 'نماذج BIM')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">نماذج BIM</h1>
            <p class="text-gray-600 mt-2">إدارة نماذج معلومات البناء ثلاثية الأبعاد</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button onclick="openUploadModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-upload ml-2"></i>
                رفع نموذج
            </button>
            <button onclick="exportModels()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-download ml-2"></i>
                تصدير
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي النماذج</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_models'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-cube text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">النماذج النشطة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_models'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">حجم الملفات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format(($stats['total_files_size'] ?? 0) / 1024 / 1024, 1) }} MB</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-database text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">متوسط التقييم</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['avg_review_score'] ?? 0, 1) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Models Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">النماذج الحديثة</h3>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-filter ml-1"></i>فلترة
                    </button>
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-sort ml-1"></i>ترتيب
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النموذج</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المطور</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحجم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($recentModels->count() > 0)
                        @foreach($recentModels as $model)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-cube text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $model->name ?? 'نموذج بدون اسم' }}</p>
                                        <p class="text-sm text-gray-600">{{ $model->version ?? 'v1.0' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $model->company_name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ number_format(($model->file_size ?? 0) / 1024 / 1024, 1) }} MB</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($model->status == 'published') bg-green-100 text-green-800
                                    @elseif($model->status == 'draft') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $model->status ?? 'draft' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= ($model->review_score ?? 0) ? '' : 'text-gray-300' }} text-sm"></i>
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600 mr-2">{{ $model->review_score ?? 0 }}.0</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-900">{{ $model->created_at ? $model->created_at->format('Y-m-d') : 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left">
                                <button class="text-blue-600 hover:text-blue-900 ml-3" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 ml-3" title="تحرير">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-purple-600 hover:text-purple-900 ml-3" title="تحميل">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <i class="fas fa-cube text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">لا توجد نماذج حالياً</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 mb-4">رفع نموذج BIM جديد</h3>
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">اسم النموذج</label>
                        <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">الملف</label>
                        <input type="file" name="file" accept=".ifc,.rvt,.skp,.dwg" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">الوصف</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 space-x-reverse">
                        <button type="button" onclick="closeUploadModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            إلغاء
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            رفع
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer" class="fixed top-4 right-4 z-50"></div>

    <script>
        function openUploadModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            document.getElementById('uploadForm').reset();
        }

        function showMessage(message, type = 'success') {
            const messageContainer = document.getElementById('messageContainer');
            const alertClass = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const messageHtml = `
                <div class="${alertClass} text-white px-6 py-4 rounded-lg shadow-lg mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} ml-2"></i>
                        ${message}
                    </div>
                </div>
            `;
            messageContainer.innerHTML = messageHtml;
            
            setTimeout(() => {
                messageContainer.innerHTML = '';
            }, 3000);
        }

        function exportModels() {
            window.open('{{ route("developer.bim.models.export") }}', '_blank');
        }

        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الرفع...';
            
            fetch('{{ route("developer.bim.models.upload") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('تم رفع النموذج بنجاح', 'success');
                    closeUploadModal();
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showMessage(data.message || 'حدث خطأ أثناء الرفع', 'error');
                }
            })
            .catch(error => {
                showMessage('حدث خطأ في الاتصال', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'رفع';
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('uploadModal');
            if (event.target === modal) {
                closeUploadModal();
            }
        }
    </script>
</div>
@endsection
