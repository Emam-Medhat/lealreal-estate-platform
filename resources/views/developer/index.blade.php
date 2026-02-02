@extends('layouts.dashboard')

@section('title', 'المطورين')

@section('page-title', 'المطورين')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">المطورون العقاريون</h1>
                <p class="text-blue-100 text-lg">إدارة ومراقبة المطورين العقاريين في المنصة</p>
                <div class="mt-4 flex items-center space-x-6 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-building ml-2"></i>
                        <span>{{ $developers->total() }} مطور</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle ml-2"></i>
                        <span>{{ $developers->where('is_verified', true)->count() }} موثق</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-star ml-2"></i>
                        <span>{{ $developers->where('is_featured', true)->count() }} مميز</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('developer.create') }}" class="bg-white text-blue-600 px-6 py-3 rounded-xl hover:bg-blue-50 transition-colors flex items-center font-semibold shadow-lg">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة مطور جديد
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">البحث والتصفية</h3>
        <form method="GET" action="{{ route('developer.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث عن مطور</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم الشركة، الإيميل، الهاتف..." 
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">كل الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع المطور</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">كل الأنواع</option>
                    <option value="residential" {{ request('type') == 'residential' ? 'selected' : '' }}>سكني</option>
                    <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>تجاري</option>
                    <option value="mixed" {{ request('type') == 'mixed' ? 'selected' : '' }}>مختلط</option>
                    <option value="industrial" {{ request('type') == 'industrial' ? 'selected' : '' }}>صناعي</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                    <i class="fas fa-search ml-2"></i>
                    بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">إجمالي المطورين</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $developers->total() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-building text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">المطورون النشطون</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $developers->where('status', 'active')->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">المطورون الموثقون</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $developers->where('is_verified', true)->count() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">المطورون المميزون</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $developers->where('is_featured', true)->count() }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Developers List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المطور</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشاريع</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييم</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($developers as $developer)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg">
                                            {{ substr($developer->company_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $developer->company_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $developer->email }}</div>
                                        @if($developer->is_verified)
                                            <div class="flex items-center mt-1">
                                                <i class="fas fa-check-circle text-green-500 text-xs ml-1"></i>
                                                <span class="text-xs text-green-600">موثق</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $developer->getTypeLabelAttribute() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-sm font-medium rounded-full 
                                    @if($developer->status == 'active') bg-green-100 text-green-800
                                    @elseif($developer->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($developer->status == 'suspended') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif>
                                    {{ $developer->getStatusLabelAttribute() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-semibold text-gray-900">{{ $developer->total_projects }}</span>
                                    <span class="text-xs text-gray-500 mr-2">مشروع</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($developer->rating, 1) }}</span>
                                    <div class="mr-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-sm {{ $i <= $developer->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500">({{ $developer->review_count }})</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('developer.show', $developer->id) }}" class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('developer.edit', $developer->id) }}" class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($developer->is_verified)
                                        <button class="text-yellow-600 hover:text-yellow-900 p-2 rounded-lg hover:bg-yellow-50 transition-colors" title="إلغاء التحقق">
                                            <i class="fas fa-shield-alt"></i>
                                        </button>
                                    @else
                                        <button class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors" title="تحقق">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-building text-6xl mb-4"></i>
                                    <p class="text-xl font-semibold mb-2">لا يوجد مطورين</p>
                                    <p class="text-sm mb-4">ابدأ بإضافة أول مطور عقاري</p>
                                    <a href="{{ route('developer.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                                        <i class="fas fa-plus ml-2"></i>
                                        إضافة مطور جديد
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if ($developers->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $developers->links() }}
        </div>
    @endif
</div>
@endsection
