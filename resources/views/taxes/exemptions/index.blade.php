@extends('layouts.app')

@section('title', 'الإعفاءات الضريبية')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-100">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-20 left-1/2 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl p-8 mb-8 border border-white/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-3">
                            <i class="fas fa-shield-alt ml-3"></i>
                            الإعفاءات الضريبية
                        </h1>
                        <p class="text-gray-600 text-lg">عرض وإدارة الإعفاءات الضريبية</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('taxes.index') }}" class="bg-white/80 backdrop-blur text-gray-700 px-6 py-3 rounded-xl hover:bg-white transition-all duration-300 shadow-md border border-gray-200">
                            <i class="fas fa-arrow-left ml-2"></i>
                            العودة
                        </a>
                        <a href="{{ route('taxes.exemptions.create') }}" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-plus ml-2"></i>
                            طلب إعفاء
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-6 mb-8">
                <form method="GET" class="flex flex-wrap gap-4">
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-filter ml-2 text-purple-600"></i>
                            الحالة
                        </label>
                        <select name="status" id="status" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتم</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي الصلاحية</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="exemption_type" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-tag ml-2 text-purple-600"></i>
                            نوع الإعفاء
                        </label>
                        <select name="exemption_type" id="exemption_type" class="px-4 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                            <option value="">جميع الأنواع</option>
                            <option value="senior_citizen" {{ request('exemption_type') == 'senior_citizen' ? 'selected' : '' }}>مواطنين كبار السن</option>
                            <option value="disability" {{ request('exemption_type') == 'disability' ? 'selected' : '' }}>ذوي الإعاقة</option>
                            <option value="veteran" {{ request('exemption_type') == 'veteran' ? 'selected' : '' }}>محاربين</option>
                            <option value="primary_residence" {{ request('exemption_type') == 'primary_residence' ? 'selected' : '' }}>السكن الرئيسي</option>
                            <option value="agricultural" {{ request('exemption_type') == 'agricultural' ? 'selected' : '' }}>أراضي زراعية</option>
                            <option value="charitable" {{ request('exemption_type') == 'charitable' ? 'selected' : '' }}>منظمات خيرية</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-2 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-search ml-2"></i>
                        بحث
                    </button>
                </form>
            </div>

            <!-- Exemptions List -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20">
                @if($exemptions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الرقم</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">النوع</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الملكية</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاريخ التقديم</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($exemptions as $exemption)
                                    <tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-blue-50 transition-all duration-300">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-bold text-gray-900">#{{ $exemption->id }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                                @if($exemption->exemption_type == 'senior_citizen') bg-purple-100 text-purple-800
                                                @elseif($exemption->exemption_type == 'disability') bg-blue-100 text-blue-800
                                                @elseif($exemption->exemption_type == 'veteran') bg-green-100 text-green-800
                                                @elseif($exemption->exemption_type == 'primary_residence') bg-yellow-100 text-yellow-800
                                                @elseif($exemption->exemption_type == 'agricultural') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                @if($exemption->exemption_type == 'senior_citizen') مواطن كبير السن
                                                @elseif($exemption->exemption_type == 'disability') ذوي إعاقة
                                                @elseif($exemption->exemption_type == 'veteran') محارب
                                                @elseif($exemption->exemption_type == 'primary_residence') سكن رئيسي
                                                @elseif($exemption->exemption_type == 'agricultural') أرض زراعية
                                                @else {{ $exemption->exemption_type }} @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($exemption->propertyTax)
                                                <a href="{{ route('taxes.property.show', $exemption->propertyTax->id) }}" class="text-purple-600 hover:text-purple-800 font-medium">
                                                    {{ $exemption->propertyTax->property->title }}
                                                </a>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                                @if($exemption->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($exemption->status == 'approved') bg-green-100 text-green-800
                                                @elseif($exemption->status == 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                @if($exemption->status == 'pending') معلق
                                                @elseif($exemption->status == 'approved') معتم
                                                @elseif($exemption->status == 'rejected') مرفوض
                                                @else منتهي الصلاحية @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $exemption->created_at->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2 space-x-reverse">
                                                <a href="{{ route('taxes.exemptions.show', $exemption->id) }}" class="text-purple-600 hover:text-purple-800 transition-colors duration-300 hover:scale-110 transform" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('taxes.exemptions.edit', $exemption->id) }}" class="text-blue-600 hover:text-blue-800 transition-colors duration-300 hover:scale-110 transform" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($exemption->status == 'pending')
                                                    <form method="POST" action="{{ route('taxes.exemptions.approve', $exemption->id) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-800 transition-colors duration-300 hover:scale-110 transform" title="موافقة">
                                                            <i class="fas fa-check"></i>
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
                    <div class="mt-6">
                        {{ $exemptions->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-shield-alt text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">لا توجد إعفاءات</h3>
                        <p class="text-gray-500 mb-6">لم يتم تقديم أي طلبات إعفاء حتى الآن</p>
                        <a href="{{ route('taxes.exemptions.create') }}" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-4 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-plus ml-2"></i>
                            طلب إعفاء جديد
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.backdrop-blur-lg {
    backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection
