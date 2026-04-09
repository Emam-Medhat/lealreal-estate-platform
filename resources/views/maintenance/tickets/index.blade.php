@extends('layouts.app')

@section('title', 'تذاكر الصيانة')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تذاكر الصيانة</h1>
        <a href="{{ route('maintenance-tickets.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus ml-2"></i>إنشاء تذكرة جديدة
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن تذكرة..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوحة</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}">تم الحل</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                </select>
            </div>
            <div>
                <select name="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الأولويات</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالي</option>
                    <option value="emergency" {{ request('priority') == 'emergency' ? 'selected' : '' }}>طارئ</option>
                </select>
            </div>
            <div>
                <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الفئات</option>
                    <option value="plumbing" {{ request('category') == 'plumbing' ? 'selected' : '' }}>سباكة</option>
                    <option value="electrical" {{ request('category') == 'electrical' ? 'selected' : '' }}>كهرباء</option>
                    <option value="hvac" {{ request('category') == 'hvac' ? 'selected' : '' }}>تكييف</option>
                    <option value="structural" {{ request('category') == 'structural' ? 'selected' : '' }}>هيكلي</option>
                    <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>عام</option>
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
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">مفتوحة</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $tickets->where('status', 'open')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-cog"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">قيد التنفيذ</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $tickets->where('status', 'in_progress')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">تم الحل</p>
                    <p class="text-2xl font-bold text-green-600">{{ $tickets->where('status', 'resolved')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">طارئ</p>
                    <p class="text-2xl font-bold text-red-600">{{ $tickets->where('priority', 'emergency')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم التذكرة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأولوية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفئة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المسند إليه</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإنشاء</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $ticket->ticket_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $ticket->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($ticket->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $ticket->property->title ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $ticket->property->property_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($ticket->status)
                                    @case('open')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            مفتوحة
                                        </span>
                                        @break
                                    @case('in_progress')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            قيد التنفيذ
                                        </span>
                                        @break
                                    @case('resolved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            تم الحل
                                        </span>
                                        @break
                                    @case('closed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            مغلقة
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ملغاة
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($ticket->priority)
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
                                    @case('emergency')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            طارئ
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($ticket->category)
                                    @case('plumbing')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            سباكة
                                        </span>
                                        @break
                                    @case('electrical')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            كهرباء
                                        </span>
                                        @break
                                    @case('hvac')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            تكييف
                                        </span>
                                        @break
                                    @case('structural')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            هيكلي
                                        </span>
                                        @break
                                    @case('general')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            عام
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            {{ $ticket->category }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->assignedTo->name ?? 'غير محدد' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $ticket->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('maintenance-tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('maintenance-tickets.edit', $ticket) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($ticket->status === 'open')
                                    <a href="{{ route('maintenance-tickets.assign', $ticket) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                @endif
                                @if($ticket->status === 'in_progress')
                                    <a href="{{ route('maintenance-tickets.resolve', $ticket) }}" class="text-purple-600 hover:text-purple-900 ml-2">
                                        <i class="fas fa-check"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                لا توجد تذاكر صيانة مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $tickets->links() }}
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">إجراءات سريعة</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-2 ml-3">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="font-medium text-gray-900">إسناد التذاكر</h3>
                </div>
                <p class="text-sm text-gray-600 mb-3">إسناد التذاكر المفتوحة للفنيين</p>
                <button onclick="assignTickets()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 w-full">
                    <i class="fas fa-users ml-2"></i>إسناد
                </button>
            </div>
            
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center mb-3">
                    <div class="bg-green-100 text-green-600 rounded-full p-2 ml-3">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3 class="font-medium text-gray-900">إرسال تنبيهات</h3>
                </div>
                <p class="text-sm text-gray-600 mb-3">إرسال تنبيهات للتذاكر الطارئة</p>
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
                <p class="text-sm text-gray-600 mb-3">عرض إحصائيات التذاكر</p>
                <a href="{{ route('maintenance-tickets.dashboard') }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 w-full block text-center">
                    <i class="fas fa-chart-line ml-2"></i>عرض
                </a>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function assignTickets() {
    window.location.href = '{{ route('maintenance-tickets.bulk-assign') }}';
}

function sendNotifications() {
    if (confirm('هل أنت متأكد من إرسال التنبيهات للتذاكر الطارئة؟')) {
        fetch('{{ route('maintenance-tickets.notify-emergency') }}', {
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
