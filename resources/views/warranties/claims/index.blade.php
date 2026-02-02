@extends('admin.layouts.admin')

@section('title', 'مطالبات الضمان')

@section('page-title', 'مطالبات الضمان')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-red-600 via-red-700 to-pink-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4">
                        <i class="fas fa-clipboard-list text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">مطالبات الضمان</h1>
                        <p class="text-red-100 mt-1">إدارة ومتابعة جميع مطالبات الضمان</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-red-100">الإجمالي:</span>
                        <span class="text-sm font-semibold text-white">{{ $claims->total() }}</span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-red-100">المعلقة:</span>
                        <span class="text-sm font-semibold text-white">{{ $claims->where('status', 'pending')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('warranties.claims.create') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-plus ml-2"></i>
                    مطالبة جديدة
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-2">إجمالي المطالبات</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\WarrantyClaim::count() }}</p>
                <div class="mt-2 flex items-center text-xs text-blue-100">
                    <i class="fas fa-clipboard-list ml-1"></i>
                    <span>جميع المطالبات</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-clipboard-list text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm font-medium mb-2">مطالبات معلقة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\WarrantyClaim::where('status', 'pending')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-yellow-100">
                    <i class="fas fa-clock ml-1"></i>
                    <span>في انتظار المراجعة</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-clock text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-2">مطالبات مقبولة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\WarrantyClaim::where('status', 'approved')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-green-100">
                    <i class="fas fa-check-circle ml-1"></i>
                    <span>تم الموافقة</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-check-circle text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium mb-2">مطالبات مرفوضة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\WarrantyClaim::where('status', 'rejected')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-red-100">
                    <i class="fas fa-times-circle ml-1"></i>
                    <span>تم الرفض</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-times-circle text-white text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Claims Table -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center mb-4 lg:mb-0">
                <div class="bg-red-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-list text-white"></i>
                </div>
                قائمة المطالبات
            </h3>
            
            <!-- Filters -->
            <form method="GET" class="flex flex-wrap gap-3">
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبولة</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                </select>
                
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="من تاريخ" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="إلى تاريخ" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-filter ml-2"></i>
                    فلترة
                </button>
                
                <a href="{{ route('warranties.claims.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-redo ml-2"></i>
                    إعادة تعيين
                </a>
            </form>
        </div>
    </div>
    
    <div class="p-6">
        @if($claims->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم المطالبة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الضمان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($claims as $claim)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $claim->claim_number }}</div>
                                <div class="text-xs text-gray-500">{{ $claim->created_at->format('Y-m-d H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $claim->warranty->title }}</div>
                                <div class="text-xs text-gray-500">{{ $claim->warranty->warranty_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $claim->claim_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($claim->amount, 2) }} ريال
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($claim->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($claim->status == 'approved') bg-green-100 text-green-800
                                    @elseif($claim->status == 'rejected') bg-red-100 text-red-800
                                    @elseif($claim->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($claim->status == 'completed') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if($claim->status == 'pending') معلقة
                                    @elseif($claim->status == 'approved') مقبولة
                                    @elseif($claim->status == 'rejected') مرفوضة
                                    @elseif($claim->status == 'processing') قيد المعالجة
                                    @elseif($claim->status == 'completed') مكتملة
                                    @else {{ $claim->status }} @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-reverse space-x-2">
                                    <a href="{{ route('warranties.claims.show', $claim) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('warranties.claims.edit', $claim) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($claim->status == 'pending')
                                        <form method="POST" action="{{ route('warranties.claims.approve', $claim) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="قبول">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('warranties.claims.reject', $claim) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="رفض">
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
            <div class="mt-6">
                {{ $claims->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد مطالبات</h3>
                <p class="text-gray-500 mb-6">لم يتم إنشاء أي مطالبات حتى الآن</p>
                <a href="{{ route('warranties.claims.create') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-plus ml-2"></i>
                    إنشاء مطالبة جديدة
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
