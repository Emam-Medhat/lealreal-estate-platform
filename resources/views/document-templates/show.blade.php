@extends('layouts.app')

@section('title', 'تفاصيل القالب')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تفاصيل القالب</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('document-templates.preview', $template) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-eye ml-2"></i>معاينة
            </a>
            <a href="{{ route('document-templates.generate', $template) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-file-alt ml-2"></i>إنشاء مستند
            </a>
            <a href="{{ route('document-templates.duplicate', $template) }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600">
                <i class="fas fa-copy ml-2"></i>نسخ
            </a>
            <a href="{{ route('document-templates.edit', $template) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                <i class="fas fa-edit ml-2"></i>تعديل
            </a>
            <a href="{{ route('document-templates.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-right ml-2"></i>عودة
            </a>
        </div>
    </div>

    <!-- Template Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold mb-4">المعلومات الأساسية</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-700">اسم القالب:</dt>
                        <dd class="text-lg text-gray-900">{{ $template->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">الفئة:</dt>
                        <dd class="text-gray-900">{{ $template->category }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">الوصف:</dt>
                        <dd class="text-gray-900">{{ $template->description ?: 'غير محدد' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">الحالة:</dt>
                        <dd>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold mb-4">الإعدادات</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-700">اللغة:</dt>
                        <dd class="text-gray-900">{{ $template->language == 'ar' ? 'العربية' : 'English' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">الاتجاه:</dt>
                        <dd class="text-gray-900">{{ $template->direction == 'rtl' ? 'من اليمين إلى اليسار' : 'من اليسار إلى اليمين' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">يتطلب توقيع:</dt>
                        <dd class="text-gray-900">{{ $template->requires_signature ? 'نعم' : 'لا' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">المنشئ:</dt>
                        <dd class="text-gray-900">{{ $template->createdBy->name }}</dd>
                    </div>
                </dl>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="text-sm font-medium text-gray-700">تاريخ الإنشاء:</dt>
                    <dd class="text-gray-900">{{ $template->created_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-700">آخر تحديث:</dt>
                    <dd class="text-gray-900">{{ $template->updated_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-700">عدد الاستخدامات:</dt>
                    <dd class="text-gray-900">{{ $template->documents_count ?? 0 }}</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Variables -->
    @if($template->variables && count($template->variables) > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">متغيرات القالب</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم المتغير</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مثال</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($template->variables as $variable)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ $variable['name'] }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $variable['description'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $variable['type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $variable['example'] ?? '---' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Content Preview -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">محتوى القالب</h2>
            <div class="flex space-x-2 space-x-reverse">
                <button onclick="togglePreview()" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                    <i class="fas fa-eye ml-1"></i>معاينة
                </button>
            </div>
        </div>
        
        <div id="content-display" class="bg-gray-50 rounded-lg p-4">
            <pre class="whitespace-pre-wrap text-sm text-gray-800">{{ $template->content }}</pre>
        </div>
        
        <div id="preview-display" class="bg-gray-50 rounded-lg p-4 hidden">
            <div class="prose max-w-none">
                <div class="whitespace-pre-wrap text-sm text-gray-800">
                    {{-- Process template content for preview --}}
                    @php
                        $previewContent = $template->content;
                        // Replace variables with example values
                        if($template->variables) {
                            foreach($template->variables as $variable) {
                                $example = $variable['example'] ?? '[' . $variable['description'] . ']';
                                $previewContent = str_replace('{' . $variable['name'] . '}', $example, $previewContent);
                            }
                        }
                    @endphp
                    {!! nl2br(e($previewContent)) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Usage Statistics -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">إحصائيات الاستخدام</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">المستندات المنشأة</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $template->documents_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">آخر استخدام</p>
                        <p class="text-lg font-bold text-green-600">{{ $template->last_used_at?->format('Y-m-d') ?? 'غير مستخدم' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-purple-500 text-white rounded-full p-3 ml-3">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">عدد المتغيرات</p>
                        <p class="text-2xl font-bold text-purple-600">{{ count($template->variables ?? []) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">معدل الاستخدام</p>
                        <p class="text-lg font-bold text-yellow-600">{{ $template->usage_rate ?? 0 }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function togglePreview() {
    const contentDisplay = document.getElementById('content-display');
    const previewDisplay = document.getElementById('preview-display');
    
    if (contentDisplay.classList.contains('hidden')) {
        contentDisplay.classList.remove('hidden');
        previewDisplay.classList.add('hidden');
    } else {
        contentDisplay.classList.add('hidden');
        previewDisplay.classList.remove('hidden');
    }
}
</script>
@endsection
