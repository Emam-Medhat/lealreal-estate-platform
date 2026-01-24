@extends('layouts.app')

@section('title', 'عيوب العقارات')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">عيوب العقارات</h1>
        <a href="{{ route('defects.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus ml-2"></i>إضافة عيب جديد
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن عيب..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="severity" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الخطور</option>
                    <option value="minor" {{ request('severity') == 'minor' ? 'selected' : '' }}>طفيف</option>
                    <option value="moderate" {{ request('severity') == 'moderate' ? 'selected' : '' }}>متوسط</option>
                    <option value="major" {{ request('severity') == 'major' ? 'selected' : '' }}>كبير</option>
                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>حرج</option>
                </select>
            </div>
            <div>
                <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الفئات</option>
                    <option value="structural" {{ request('category') == 'structural' ? 'selected' : '' }}>هيكلي</option>
                    <option value="electrical" {{ request('category') == 'electrical' ? 'selected' : '' }}>كهربائي</option>
                    <option value="plumbing" {{ request('category') == 'plumbing' ? 'selected' : '' }}>سباكة</option>
                    <option value="hvac" {{ request('category') == 'hvac' ? 'selected' : '' }}>تكييف</option>
                    <option value="roofing" {{ request('category') == 'roofing' ? 'selected' : '' }}>سقف</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="identified" {{ request('status') == 'identified' ? 'selected' : '' }}>محدد</option>
                    <option value="reported" {{ request('status') == 'reported' ? 'selected' : '' }}>مبلغ عنه</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>تم الحل</option>
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
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">حرج</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $defects->where('severity', 'critical')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">كبير</p>
                    <p class="text-2xl font-bold text-red-600">{{ $defects->where('severity', 'major')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-orange-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-exclamation"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">متوسط</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $defects->where('severity', 'moderate')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">طفيف</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $defects->where('severity', 'minor')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Defects Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العيب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفئة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخطورة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التكلفة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التحديد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($defects as $defect)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $defect->property->title }}</div>
                                <div class="text-sm text-gray-500">{{ $defect->property->property_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $defect->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($defect->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($defect->category)
                                    @case('structural')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            هيكلي
                                        </span>
                                        @break
                                    @case('electrical')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            كهربائي
                                        </span>
                                        @break
                                    @case('plumbing')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            سباكة
                                        </span>
                                        @break
                                    @case('hvac')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            تكيف
                                        </span>
                                        @break
                                    @case('roofing')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            سقف
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $defect->category }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($defect->severity)
                                    @case('critical')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            حرج
                                        </span>
                                        @break
                                    @case('major')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            كبير
                                        </span>
                                        @break
                                    @case('moderate')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            متوسط
                                        </span>
                                        @break
                                    @case('minor')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            طفيف
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($defect->status)
                                    @case('identified')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            محدد
                                        </span>
                                        @break
                                    @case('reported')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            مبلغ عنه
                                        </span>
                                        @break
                                    @case('in_progress')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            قيد المعالجة
                                        </span>
                                        @break
                                    @case('resolved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            تم الحل
                                        </span>
                                        @break
                                    @case('deferred')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            مؤجل
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($defect->estimated_repair_cost)
                                    {{ number_format($defect->estimated_repair_cost, 2) }} ريال
                                @else
                                    غير محدد
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $defect->identified_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('defects.show', $defect) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('defects.edit', $defect) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($defect->status === 'identified' || $defect->status === 'reported')
                                    <a href="{{ route('defects.assign', $defect) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                @endif
                                @if($defect->status === 'in_progress')
                                    <a href="{{ route('defects.complete', $defect) }}" class="text-purple-600 hover:text-purple-900 ml-2">
                                        <i class="fas fa-check"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                لا توجد عيوب مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $defects->links() }}
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">إجراءات سريعة</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-2 ml-3">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <h3 class="font-medium text-gray-900">تصدير التقرير</h3>
                </div>
                <p class="text-sm text-gray-600 mb-3">تصدير تقرير شامل بجميع العيوب</p>
                <button onclick="exportDefects()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 w-full">
                    <i class="fas fa-download ml-2"></i>تصدير
                </button>
            </div>
            
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="bg-green-100 text-green-600 rounded-full p-2 ml-3">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="font-medium text-gray-900">إرسال تنبيهات</h3>
                </div>
                <p class="text-sm text-gray-600 mb-3">إرسال تنبيهات للعيوب الحرجة</p>
                <button onclick="sendNotifications()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 w-full">
                    <i class="fas fa-paper-plane ml-2"></i>إرسال
                </button>
            </div>
            
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="bg-purple-100 text-purple-600 rounded-full p-2 ml-3">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="font-medium text-gray-900">لوحة التحكم</h3>
                </div>
                <p class="text-sm text-gray-600 mb-3">عرض إحصائيات العيوب</p>
                <a href="{{ route('defects.dashboard') }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 w-full block text-center">
                    <i class="fas fa-chart-line ml-2"></i>عرض
                </a>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function exportDefects() {
    window.location.href = '{{ route('defects.export') }}';
}

function sendNotifications() {
    if (confirm('هل أنت متأكد من إرسال التنبيهات للعيوب الحرجة؟')) {
        fetch('{{ route('defects.notify-critical') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم إرسال التنبيهات بنجاح');
            } else {
                alert('حدث خطأء: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء أثناء إرسال التنبيهات');
        });
    }
}
</script>
@endsection
