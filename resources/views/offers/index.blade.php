@extends('admin.layouts.admin')

@section('title', 'العروض')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">العروض</h1>
            <p class="text-gray-600 mt-1">إدارة عروض العقارات والمفاوضات</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('properties.index') }}" 
               class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-all duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-plus"></i>
                <span>عرض جديد</span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">العروض المرسلة</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_sent'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-paper-plane text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">العروض المستلمة</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['total_received'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-inbox text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">قيد الانتظار</p>
                <p class="text-2xl font-bold text-orange-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-clock text-orange-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">مقبولة</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['accepted'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">مرفوضة</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
    <form method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع العرض</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">الكل</option>
                    <option value="sent" {{ request('type') == 'sent' ? 'selected' : '' }}>مرسلة</option>
                    <option value="received" {{ request('type') == 'received' ? 'selected' : '' }}>مستلمة</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">الكل</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>مقدمة</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبولة</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>مسحوبة</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهية الصلاحية</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأدنى للسعر</label>
                <input type="number" name="min_amount" value="{{ request('min_amount') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       placeholder="الحد الأدنى">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للسعر</label>
                <input type="number" name="max_amount" value="{{ request('max_amount') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       placeholder="الحد الأقصى">
            </div>
        </div>
        
        <div class="flex items-center justify-end">
            <button type="submit" 
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-filter ml-2"></i>
                تطبيق الفلاتر
            </button>
        </div>
    </form>
</div>

<!-- Offers Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($offers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">رقم العرض</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">العقار</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المشتري</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">البائع</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">المبلغ</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">تاريخ الانتهاء</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($offers as $offer)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-gray-900">{{ $offer->offer_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($offer->property)
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        @if($offer->property->images->count() > 0)
                                            <img src="{{ $offer->property->images->first()->image_url }}" 
                                                 alt="{{ $offer->property->title }}" 
                                                 class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-home text-gray-400 text-sm"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $offer->property->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $offer->property->location }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-500 text-sm">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($offer->buyer)
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <div class="bg-gray-200 rounded-full p-1">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $offer->buyer->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-500 text-sm">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($offer->seller)
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <div class="bg-gray-200 rounded-full p-1">
                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $offer->seller->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-500 text-sm">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($offer->offer_amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($offer->status == 'draft') bg-gray-100 text-gray-700
                                    @elseif($offer->status == 'submitted') bg-blue-100 text-blue-700
                                    @elseif($offer->status == 'under_review') bg-yellow-100 text-yellow-700
                                    @elseif($offer->status == 'accepted') bg-green-100 text-green-700
                                    @elseif($offer->status == 'rejected') bg-red-100 text-red-700
                                    @elseif($offer->status == 'withdrawn') bg-gray-100 text-gray-700
                                    @else bg-orange-100 text-orange-700
                                    @endif">
                                    @if($offer->status == 'draft') مسودة
                                    @elseif($offer->status == 'submitted') مقدمة
                                    @elseif($offer->status == 'under_review') قيد المراجعة
                                    @elseif($offer->status == 'accepted') مقبولة
                                    @elseif($offer->status == 'rejected') مرفوضة
                                    @elseif($offer->status == 'withdrawn') مسحوبة
                                    @else منتهية الصلاحية
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $offer->offer_expiration_date->format('Y-m-d') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                                    <a href="{{ route('offers.show', $offer) }}" 
                                       class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                       title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('offers.edit', $offer) }}" 
                                       class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($offer->status == 'submitted')
                                        <form action="{{ route('offers.accept', $offer) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition-colors"
                                                    title="قبول">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('offers.reject', $offer) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                    title="رفض">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    عرض {{ $offers->firstItem() ?? 0 }} - {{ $offers->lastItem() ?? 0 }} من {{ $offers->total() }} عرض
                </div>
                <div class="flex items-center space-x-2 space-x-reverse">
                    {{ $offers->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16">
            <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                <i class="fas fa-handshake text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-2">لا توجد عروض</h3>
            <p class="text-gray-600 mb-8">ابدأ بإنشاء عروض جديدة للعقارات</p>
            <a href="{{ route('properties.index') }}" 
               class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 inline-flex items-center space-x-2 space-x-reverse">
                <i class="fas fa-plus"></i>
                <span>عرض جديد</span>
            </a>
        </div>
    @endif
</div>
@endsection
