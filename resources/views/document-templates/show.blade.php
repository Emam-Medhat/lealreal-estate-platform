@extends('admin.layouts.admin')

@section('title', 'تفاصيل القالب')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-file-alt text-blue-500 ml-3"></i>
                    {{ $template->name }}
                </h1>
                <p class="text-gray-600 mt-2">تفاصيل وإدارة قالب المستند</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('document-templates.preview', $template) }}" 
                   class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-eye ml-2"></i>
                    معاينة
                </a>
                <a href="{{ route('document-templates.generate-form', $template) }}" 
                   class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-file-alt ml-2"></i>
                    توليد مستند
                </a>
                <form action="{{ route('document-templates.duplicate', $template) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" 
                            class="bg-purple-500 text-white px-6 py-3 rounded-lg hover:bg-purple-600 transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fas fa-copy ml-2"></i>
                        نسخ
                    </button>
                </form>
                <a href="{{ route('document-templates.edit', $template) }}" 
                   class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
                <a href="{{ route('document-templates.index') }}" 
                   class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-right ml-2"></i>
                    عودة
                </a>
            </div>
        </div>

        <!-- Template Info -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full">
                        <i class="fas fa-info-circle text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-white mr-3">معلومات القالب</h2>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">اسم القالب</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $template->name }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">الفئة</div>
                            <div class="text-lg font-semibold text-gray-900">{{ ucfirst($template->category) }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">الوصف</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $template->description ?: 'لا يوجد وصف' }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">الحالة</div>
                            <div class="text-lg font-semibold">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    @if($template->is_active) 
                                        bg-green-100 text-green-800 border border-green-300
                                    @else 
                                        bg-gray-100 text-gray-800 border border-gray-300
                                    @endif">
                                    {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">اللغة</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $template->language == 'ar' ? 'العربية' : 'English' }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">الاتجاه</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $template->direction == 'rtl' ? 'من اليمين لليسار' : 'من اليسار لليمين' }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">يتطلب توقيع</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $template->requires_signature ? 'نعم' : 'لا' }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">أنشئ بواسطة</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $template->createdBy->name }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t-2 border-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-sm text-gray-500 mb-1">تاريخ الإنشاء</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $template->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-500 mb-1">آخر تحديث</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $template->updated_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-sm text-gray-500 mb-1">عدد الاستخدامات</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $template->documents_count ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variables -->
        @if($template->variables && count($template->variables) > 0)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full">
                            <i class="fas fa-code text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-white mr-3">متغيرات القالب</h2>
                        <span class="bg-white bg-opacity-20 text-white text-xs font-medium px-3 py-1 rounded-full">
                            {{ count($template->variables) }} متغيرات
                        </span>
                    </div>
                </div>
                
                <div class="p-8">
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
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <code class="bg-gray-100 px-3 py-2 rounded-lg text-sm font-mono text-gray-800">{{ $variable['name'] }}</code>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $variable['description'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($variable['type']) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $variable['example'] ?? '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Content Preview -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full">
                            <i class="fas fa-file-code text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-white mr-3">محتوى القالب</h2>
                    </div>
                    <button onclick="togglePreview()" 
                            class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-all duration-200">
                        <i class="fas fa-eye ml-2"></i>
                        تبديل المعاينة
                    </button>
                </div>
            </div>
            
            <div class="p-8">
                <div id="content-display" class="bg-gray-50 rounded-lg p-6">
                    <pre class="whitespace-pre-wrap text-sm text-gray-800 font-mono leading-relaxed">{{ $template->content }}</pre>
                </div>
                
                <div id="preview-display" class="bg-gray-50 rounded-lg p-6 hidden">
                    <div class="prose max-w-none">
                        <div class="whitespace-pre-wrap text-sm text-gray-800 leading-relaxed">
                            @php
                                $previewContent = $template->content;
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
        </div>

        <!-- Usage Statistics -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full">
                        <i class="fas fa-chart-bar text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-white mr-3">إحصائيات الاستخدام</h2>
                </div>
            </div>
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center ml-4">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">المستندات المنشأة</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $template->documents_count ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center ml-4">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">آخر استخدام</p>
                                <p class="text-lg font-bold text-green-600">{{ $template->last_used_at?->format('Y-m-d') ?? 'لم يستخدم بعد' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center ml-4">
                                <i class="fas fa-layer-group text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">عدد المتغيرات</p>
                                <p class="text-2xl font-bold text-purple-600">{{ count($template->variables ?? []) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center ml-4">
                                <i class="fas fa-chart-line text-white"></i>
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
    </div>
</div>

@section('scripts')
<script>
function togglePreview() {
    const contentDisplay = document.getElementById('content-display');
    const previewDisplay = document.getElementById('preview-display');
    
    contentDisplay.classList.toggle('hidden');
    previewDisplay.classList.toggle('hidden');
}
</script>
@endsection
@endsection
