@extends('layouts.app')

@section('title', 'تفاصيل قالب العقد')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تفاصيل قالب العقد</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('contract-templates.preview', $template) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-eye ml-2"></i>معاينة
            </a>
            <a href="{{ route('contract-templates.create-contract', $template) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-file-contract ml-2"></i>إنشاء عقد
            </a>
            <a href="{{ route('contract-templates.edit', $template) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                <i class="fas fa-edit ml-2"></i>تعديل
            </a>
            <a href="{{ route('contract-templates.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
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
                        <dd class="text-gray-900">{{ $template->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">الوصف:</dt>
                        <dd class="text-gray-900">{{ $template->description ?: 'غير محدد' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">نوع العقد:</dt>
                        <dd class="text-gray-900">{{ $template->contract_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">الحالة:</dt>
                        <dd>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-700">يتطلب توقيع:</dt>
                        <dd>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->requires_signature ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $template->requires_signature ? 'نعم' : 'لا' }}
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
                    <dt class="text-sm font-medium text-gray-700">الاتجاه:</dt>
                        <dd class="text-gray-900">{{ $template->direction == 'rtl' ? 'من اليمين إلى اليسار' : 'من اليسار إلى اليمين' }}</dd>
                    </div>
                    <dt class="text-sm font-medium text-gray-700">المدة الافتراضية:</dt>
                        <dd class="text-gray-900">{{ $template->default_duration ?: 'غير محدد' }} يوم</dd>
                    </div>
                </dl>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="text-sm font-medium text-gray-700">المنشئ:</dt>
                    <dd class="text-gray-900">{{ $template->createdBy->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-700">تاريخ الإنشاء:</dt>
                    <dd class="text-gray-900">{{ $template->created_at->format('Y-m-d H:i') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-700">آخر تحديث:</dt>
                    <dd class="text-gray-900">{{ $template->updated_at->format('Y-m-d H:i') }}</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Terms -->
    @if($template->terms && count($template->terms) > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">بنود العقد</h2>
            
            <div class="space-y-4">
                @foreach($template->terms as $term)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $term->title }}</h3>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $term->is_required ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $term->is_required ? 'إلزامي' : 'اختياري' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                الترتيب: {{ $term->order }}
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-gray-700">{{ $term->content }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Standard Clauses -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-hidden">البنود القياسية المضمنة</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center">
                <input type="checkbox" {{ $template->include_force_majeure ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled>
                <label class="mr-2 text-sm text-gray-700">بند القوة القاهرة</label>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" {{ $template->include_confidentiality ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled>
                <label class="mr-2 text-sm text-gray-700">بند السرية</label>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" {{ $template->include_termination ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled>
                <label class="mr-2 text-sm text-gray-700">بند الإنهاء</label>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" {{ $template->include_dispute_resolution ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled>
                <label class="mr-2 text-sm text-gray-700">بند حل النزاعات</label>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" {{ $template->include_governing_law ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled>
                <label class="mr-2 text-sm text-gray-700">بند القانون الحاكم</label>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" {{ $template->include_liability ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" disabled>
                <label class="mr-2 text-sm text-gray-700">بند المسؤولية</label>
            </div>
        </div>
    </div>

    <!-- Usage Statistics -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">إحصائيات الاستخدام</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">العقود المنشأة</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $template->contracts_count ?? 0 }}</p>
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
                        <p class="text-lg font-bold text-green-600">{{ $template->last_used_at?->format('Y-m-d') ?: 'غير مستخدم' }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-purple-500 text-white rounded-full p-3 ml-3">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">عدد البنود</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $template->terms_count ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">معدد الاستخدام</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $template->usage_rate ?? 0 }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Contracts -->
    @if($contracts && $contracts->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">العقود المنشأة من هذا القالب</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم العقد</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإنشاء</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($contracts as $contract)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#{{ $contract->id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $contract->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($contract->status)
                                        @case('draft')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                مسودة
                                            </span>
                                            @break
                                        @case('active')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                نشط
                                            </span>
                                        @break>
                                        @case('signed')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                موقّع
                                            </span>
                                        @break>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $contract->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                    <a href="{{ route('contracts.show', $contract) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('contracts.edit', $contract) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">الإجراءات سريعة</h2>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('contract-templates.duplicate', $template) }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600">
                <i class="fas fa-copy ml-2"></i>نسخ القالب
            </a>
            <a href="{{ route('contract-templates.toggle', $template) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                <i class="fas fa-toggle-on ml-2"></i>تفعيل الحالة
            </a>
            <a href="{{ route('contract-templates.export', $template) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-download ml-2"></i>تصدير القالب
            </a>
            <a href="{{ route('contract-templates.analytics') }}" class="bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-600">
                <i class="fas fa-chart-bar ml-2"></i>تحليلات
            </a>
        </div>
    </div>
</div>
@endsection
