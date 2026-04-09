@extends('layouts.app')

@section('title', 'فحوص الامتثال')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">فحوص الامتثال</h1>
        <a href="{{ route('compliance-checks.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus ml-2"></i>إجراء فحص امتثال جديد
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن فحص امتثال..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="check_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الأنواعف</option>
                    <option value="building_code" {{ request('check_type') == 'building_code' ? 'selected' : '' }}>كود البناء</option>
                    <option value="safety" {{ request('check_type') == 'safety' ? 'selected' : '' }}>السلامة</option>
                    <option value="environmental" {{ request('check_type') == 'environmental' ? 'selected' : '' }}>بيئي</option>
                    <option value="accessibility" {{ request('check_type') == 'accessibility' ? 'selected' : '' }}>إمكانية</option>
                    <option value="fire_safety" {{ request('check_type') == 'fire_safety' ? 'selected' : '' }}>سلامة الحريق</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}">اجتاز</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                    <option value="requires_action" {{ request('status') == 'requires_action' ? 'selected' : '' }}>يتطلب إجراء</option>
                </select>
            </div>
            <div>
                <select name="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الأولويات</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالي</option>
                    <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>حرج</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">
                    <i class="fas fa-search ml-2"></i>بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">اجتاز</p>
                    <p class="text-2xl font-bold text-green-600">{{ $checks->where('status', 'passed')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">فشل</p>
                    <p class="text-2xl font-bold text-red-600">{{ $checks->where('status', 'failed')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">قيد التنفيذ</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $checks->where('status', 'in_progress')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">في الانتظار</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $checks->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance Checks Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الفحص</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع الفحص</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأولوية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الفحص</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المفتش</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النتيجة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($checks as $check)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $check->check_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $check->property->title }}</div>
                                <div class="text-sm text-gray-500">{{ $check->property->property_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($check->check_type)
                                    @case('building_code')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            كود البناء
                                        </span>
                                        @break
                                    @case('safety')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            السلامة
                                        </span>
                                        @break
                                    @case('environmental')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            بيئي
                                        </span>
                                        @break
                                    @case('accessibility')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            إمكانية
                                        </span>
                                        @break
                                    @case('fire_safety')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            سلامة الحريق
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            {{ $check->check_type }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($check->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            في الانتظار
                                        </span>
                                        @break
                                    @case('in_progress')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            قيد التنفيذ
                                        </span>
                                        @break
                                    @case('passed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            اجتاز
                                        </span>
                                        @break
                                    @case('failed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            فشل
                                        </span>
                                        @break
                                    @case('requires_action')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            يتطلب إجراء
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($check->priority)
                                    @case('low')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            منخفض
                                        </span>
                                        @break
                                    @case('medium')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            متوسط
                                        </span>
                                        @break
                                    @case('high')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            عالي
                                        </span>
                                        @break
                                    @case('critical')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            حرج
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $check->check_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $check->performedBy->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($check->is_compliant !== null)
                                    @if($check->is_compliant)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            متوافق
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            غير متوافق
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            غير محدد
                                        </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('compliance-checks.show', $check) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('compliance-checks.edit', $check) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($check->status === 'pending')
                                    <a href="{{ route('compliance-checks.start', $check) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-play"></i>
                                    </a>
                                @endif
                                @if($check->status === 'in_progress')
                                    <a href="{{ route('compliance-checks.complete', $check) }}" class="text-purple-600 hover:text-purple-900 ml-2">
                                        <i class="fas fa-check"></i>
                                    </a>
                                @endif
                                @if($check->status === 'passed')
                                    <a href="{{ route('compliance-checks.report', $check) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                    <a href="{{ route('compliance-checks.certificate', $check) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-certificate"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                لا توجد فحوص امتثال مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $checks->links() }}
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">إجراءات سريعة</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-2 ml-3">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="font-medium text-gray-900">جدولة الفحوص</h3>
                </div>
                <p class="text-sm text-gray-600 mb-3">جدولة فحوص الامتثال القادمة</p>
                <a href="{{ route('compliance-checks.dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 w-full block text-center">
                    <i class="fas fa-calendar ml-2"></i>عرض الجدولة
                </a>
            </div>
            
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="bg-green-100 text-green-600 rounded-full p-2 ml-3">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <h3 class="font-medium text-gray-900">تصدير التقارير</h3>
                </div>
                <p class="text-sm text-gray-600 mb-3">تصدير تقارير الامتثال</p>
                <button onclick="exportReports()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 w-full">
                    <i class="fas fa-download ml-2"></i>تصدير
                </button>
            </div>
            
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="bg-purple-100 text-purple-600 rounded-full p-2 ml-3">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="font-medium text-gray-900">لوحة التحكم</h3>
                </div>
                <p class="text-sm text-gray-600 mb-3">عرض إحصائيات الامتثال</p>
                <a href="{{ route('compliance-checks.dashboard') }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 w-full block text-center">
                    <i class="fas fa-chart-line ml-2"></i>عرض
                </a>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function exportReports() {
    window.location.href = '{{ route('compliance-checks.export') }}';
}
</script>
@endsection
