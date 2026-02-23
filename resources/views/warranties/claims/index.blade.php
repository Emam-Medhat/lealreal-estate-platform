@extends('layouts.app')

@section('title', 'مطالبات الضمان')

@section('page-title', 'مطالبات الضمان')

@section('content')
<div class="container mx-auto px-4 py-8">
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full -ml-24 -mb-24"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center mb-3">
                        <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4 backdrop-blur-sm border border-white border-opacity-30">
                            <i class="fas fa-clipboard-list text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white mb-1">مطالبات الضمان</h1>
                            <p class="text-white text-lg">إدارة ومتابعة جميع مطالبات الضمان</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 mt-4">
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-30">
                            <span class="text-sm text-white">الإجمالي:</span>
                            <span class="text-sm font-bold text-white">{{ $claims->total() }}</span>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-30">
                            <span class="text-sm text-white">المعلقة:</span>
                            <span class="text-sm font-bold text-white">{{ $claims->where('status', 'pending')->count() }}</span>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-30">
                            <span class="text-sm text-white">المكتملة:</span>
                            <span class="text-sm font-bold text-white">{{ $claims->where('status', 'completed')->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-reverse space-x-3">
                    <a href="{{ route('warranties.claims.create') }}" class="inline-flex items-center px-6 py-3 bg-white text-black rounded-xl text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-plus ml-2"></i>
                        مطالبة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-black text-sm font-bold mb-2">إجمالي المطالبات</p>
                    <p class="text-4xl font-black text-black">{{ App\Models\WarrantyClaim::count() }}</p>
                    <div class="mt-2 flex items-center text-sm text-black">
                        <i class="fas fa-clipboard-list ml-1 text-blue-600"></i>
                        <span>جميع المطالبات</span>
                    </div>
                </div>
                <div class="bg-blue-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-blue-200">
                    <i class="fas fa-clipboard-list text-blue-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-amber-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-black text-sm font-bold mb-2">مطالبات معلقة</p>
                    <p class="text-4xl font-black text-black">{{ App\Models\WarrantyClaim::where('status', 'pending')->count() }}</p>
                    <div class="mt-2 flex items-center text-sm text-black">
                        <i class="fas fa-clock ml-1 text-amber-600"></i>
                        <span>في انتظار المراجعة</span>
                    </div>
                </div>
                <div class="bg-amber-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-amber-200">
                    <i class="fas fa-clock text-amber-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-black text-sm font-bold mb-2">مطالبات مقبولة</p>
                    <p class="text-4xl font-black text-black">{{ App\Models\WarrantyClaim::where('status', 'approved')->count() }}</p>
                    <div class="mt-2 flex items-center text-sm text-black">
                        <i class="fas fa-check-circle ml-1 text-green-600"></i>
                        <span>تم الموافقة</span>
                    </div>
                </div>
                <div class="bg-green-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-green-200">
                    <i class="fas fa-check-circle text-green-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-red-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-black text-sm font-bold mb-2">مطالبات مرفوضة</p>
                    <p class="text-4xl font-black text-black">{{ App\Models\WarrantyClaim::where('status', 'rejected')->count() }}</p>
                    <div class="mt-2 flex items-center text-sm text-black">
                        <i class="fas fa-times-circle ml-1 text-red-600"></i>
                        <span>تم الرفض</span>
                    </div>
                </div>
                <div class="bg-red-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-red-200">
                    <i class="fas fa-times-circle text-red-700 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Claims Table -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <h3 class="text-lg font-semibold text-black flex items-center mb-4 lg:mb-0">
                <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                    <i class="fas fa-list text-white"></i>
                </div>
                قائمة المطالبات
            </h3>
            
            <!-- Filters -->
            <form method="GET" class="flex flex-wrap gap-3">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>مقبولة</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                </select>
                
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="من تاريخ" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200">
                
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="إلى تاريخ" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200">
                
                <button type="submit" class="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition-all duration-200 transform hover:scale-105 shadow-md">
                    <i class="fas fa-filter ml-2"></i>
                    فلترة
                </button>
                
                <a href="{{ route('warranties.claims.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-200 transform hover:scale-105 shadow-md">
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
                            <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">رقم المطالبة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">الضمان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">المبلغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-black uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($claims as $claim)
                        <tr class="hover:bg-gray-50 transition-all duration-200 hover:shadow-sm">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-black">{{ $claim->claim_number }}</div>
                                <div class="text-xs text-black">{{ $claim->created_at->format('Y-m-d H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-black font-medium">{{ $claim->warranty->title }}</div>
                                <div class="text-xs text-black">{{ $claim->warranty->warranty_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                                {{ $claim->claim_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-black font-semibold">
                                {{ number_format($claim->amount, 2) }} ريال
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($claim->status == 'pending') bg-amber-100 text-black border border-amber-200
                                    @elseif($claim->status == 'approved') bg-green-100 text-black border border-green-200
                                    @elseif($claim->status == 'rejected') bg-red-100 text-black border border-red-200
                                    @elseif($claim->status == 'processing') bg-blue-100 text-black border border-blue-200
                                    @elseif($claim->status == 'completed') bg-purple-100 text-black border border-purple-200
                                    @else bg-gray-100 text-black border border-gray-200 @endif">
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
                                    <a href="{{ route('warranties.claims.show', $claim) }}" class="text-black hover:text-gray-700 transition-colors duration-200 hover:scale-110 transform" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('warranties.claims.edit', $claim) }}" class="text-black hover:text-gray-700 transition-colors duration-200 hover:scale-110 transform" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($claim->status == 'pending')
                                        <form method="POST" action="{{ route('warranties.claims.approve', $claim) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-black hover:text-gray-700 transition-colors duration-200 hover:scale-110 transform" title="قبول">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('warranties.claims.reject', $claim) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-black hover:text-gray-700 transition-colors duration-200 hover:scale-110 transform" title="رفض">
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
                <div class="mb-6">
                    <i class="fas fa-clipboard-list text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-black mb-2">لا توجد مطالبات</h3>
                    <p class="text-black mb-6">لم يتم إنشاء أي مطالبات حتى الآن</p>
                    <a href="{{ route('warranties.claims.create') }}" class="inline-flex items-center px-6 py-3 bg-slate-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-plus ml-2"></i>
                        إنشاء مطالبة جديدة
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

</div>
</div>
@endsection
