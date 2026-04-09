@extends('layouts.dashboard')

@section('title', 'التحقق من التوثيق')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-purple-50 to-indigo-50">
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Animated Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-purple-700 via-indigo-600 to-purple-700 rounded-3xl shadow-2xl p-8 mb-10 transform hover:scale-[1.02] transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-indigo-600/20 animate-pulse"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                    <i class="fas fa-certificate text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-4xl font-bold text-white tracking-tight">التحقق من التوثيق</h1>
                                    <p class="text-purple-100 text-lg font-medium">إدارة طلبات التوثيق والتحقق من العقود</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3 space-x-reverse">
                            @if(Route::has('notary.dashboard'))
                                <a href="{{ route('notary.dashboard') }}" class="group bg-white/10 backdrop-blur-md text-white px-6 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20 hover:border-white/40 hover:shadow-xl">
                                    <i class="fas fa-tachometer-alt ml-2 group-hover:scale-110 transition-transform"></i>
                                    <span class="font-medium">لوحة التحكم</span>
                                </a>
                            @endif
                            @if(Route::has('notary.create'))
                                <a href="{{ route('notary.create') }}" class="group bg-white text-purple-700 px-6 py-3 rounded-xl hover:bg-purple-50 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fas fa-plus ml-2 group-hover:rotate-90 transition-transform duration-300"></i>
                                    <span class="font-semibold">طلب تحقق جديد</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-purple-400/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-400/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Enhanced Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-400/10 to-orange-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 text-white rounded-2xl p-4 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <div class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                +12% هذا الشهر
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium mb-1">في الانتظار</p>
                            <p class="text-4xl font-bold text-gray-900 group-hover:text-yellow-600 transition-colors">{{ $verifications->where('status', 'pending')->count() }}</p>
                            <div class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full animate-pulse" style="width: 65%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-400/10 to-emerald-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-green-400 to-emerald-500 text-white rounded-2xl p-4 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                +8% هذا الشهر
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium mb-1">موثق</p>
                            <p class="text-4xl font-bold text-gray-900 group-hover:text-green-600 transition-colors">{{ $verifications->where('status', 'verified')->count() }}</p>
                            <div class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full animate-pulse" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-400/10 to-pink-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-red-400 to-pink-500 text-white rounded-2xl p-4 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-times-circle text-2xl"></i>
                            </div>
                            <div class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                                -3% هذا الشهر
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium mb-1">مرفوض</p>
                            <p class="text-4xl font-bold text-gray-900 group-hover:text-red-600 transition-colors">{{ $verifications->where('status', 'rejected')->count() }}</p>
                            <div class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-red-400 to-pink-500 rounded-full animate-pulse" style="width: 15%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400/10 to-cyan-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-blue-400 to-cyan-500 text-white rounded-2xl p-4 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-info-circle text-2xl"></i>
                            </div>
                            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                +5% هذا الشهر
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium mb-1">مطلوب معلومات</p>
                            <p class="text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $verifications->where('status', 'requires_info')->count() }}</p>
                            <div class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-400 to-cyan-500 rounded-full animate-pulse" style="width: 45%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Premium Filters Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-10 border border-purple-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <div class="bg-gradient-to-br from-purple-500 to-indigo-500 text-white p-3 rounded-xl ml-3">
                            <i class="fas fa-filter"></i>
                        </div>
                        فلترة النتائج
                    </h2>
                    <button type="button" onclick="this.closest('form').reset()" class="text-gray-500 hover:text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-redo ml-2"></i>إعادة تعيين
                    </button>
                </div>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-flag text-purple-500 ml-2"></i>
                            الحالة
                        </label>
                        <select name="status" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 px-4 py-3">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>موثق</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="requires_info" {{ request('status') == 'requires_info' ? 'selected' : '' }}>مطلوب معلومات</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-tags text-purple-500 ml-2"></i>
                            نوع التحقق
                        </label>
                        <select name="verification_type" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 px-4 py-3">
                            <option value="">الكل</option>
                            <option value="standard" {{ request('verification_type') == 'standard' ? 'selected' : '' }}>قياسي</option>
                            <option value="expedited" {{ request('verification_type') == 'expedited' ? 'selected' : '' }}>معجل</option>
                            <option value="priority" {{ request('verification_type') == 'priority' ? 'selected' : '' }}>أولوية</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-user-tie text-purple-500 ml-2"></i>
                            الموثق
                        </label>
                        <select name="notary" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 px-4 py-3">
                            <option value="">الكل</option>
                            @foreach($notaries ?? [] as $notary)
                                <option value="{{ $notary->id }}" {{ request('notary') == $notary->id ? 'selected' : '' }}>
                                    {{ $notary->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105 font-semibold">
                            <i class="fas fa-search ml-2"></i>بحث متقدم
                        </button>
                    </div>
                </form>
            </div>

            <!-- Enhanced Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-purple-100">
                <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-list ml-3"></i>
                            قائمة طلبات التحقق
                        </h2>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <span class="bg-white/20 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-medium">
                                {{ $verifications->count() }} نتيجة
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-purple-50 border-b border-purple-100">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">العقد</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">كود التحقق</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">النوع</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الموثق</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاريخ الطلب</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الإكمال المتوقع</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($verifications as $verification)
                                <tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 transition-all duration-300 group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-3 space-x-reverse">
                                            <div class="bg-purple-100 text-purple-600 p-2 rounded-lg group-hover:bg-purple-200 transition-colors">
                                                <i class="fas fa-file-contract text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $verification->contract->title ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 font-mono">{{ $verification->contract->contract_number ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-700 px-3 py-2 rounded-lg font-mono text-sm font-bold border border-purple-200">
                                            {{ $verification->verification_code }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @switch($verification->verification_type)
                                            @case('standard')
                                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300">
                                                    <i class="fas fa-clock ml-2"></i>قياسي
                                                </span>
                                                @break
                                            @case('expedited')
                                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300">
                                                    <i class="fas fa-bolt ml-2"></i>معجل
                                                </span>
                                                @break
                                            @case('priority')
                                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 border border-purple-300">
                                                    <i class="fas fa-star ml-2"></i>أولوية
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4">
                                        @switch($verification->status)
                                            @case('pending')
                                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800 border border-yellow-300">
                                                    <i class="fas fa-clock ml-2 animate-pulse"></i>في الانتظار
                                                </span>
                                                @break
                                            @case('verified')
                                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-300">
                                                    <i class="fas fa-check ml-2"></i>موثق
                                                </span>
                                                @break
                                            @case('rejected')
                                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-red-100 to-pink-100 text-red-800 border border-red-300">
                                                    <i class="fas fa-times ml-2"></i>مرفوض
                                                </span>
                                                @break
                                            @case('requires_info')
                                                <span class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 border border-blue-300">
                                                    <i class="fas fa-info ml-2 animate-bounce"></i>مطلوب معلومات
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <div class="bg-gray-100 text-gray-600 p-2 rounded-full">
                                                <i class="fas fa-user-tie text-xs"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700">{{ $verification->notary->name ?? 'غير محدد' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-700">{{ $verification->requested_at->format('Y-m-d') }}</div>
                                        <div class="text-xs text-gray-500">{{ $verification->requested_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($verification->estimated_completion)
                                            <div class="space-y-1">
                                                <div class="text-sm font-medium {{ $verification->estimated_completion->isPast() ? 'text-red-600' : 'text-gray-700' }}">
                                                    {{ $verification->estimated_completion->format('Y-m-d') }}
                                                </div>
                                                @if($verification->estimated_completion->isPast())
                                                    <span class="text-xs text-red-600 font-semibold">
                                                        <i class="fas fa-exclamation-triangle ml-1"></i>متأخر
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-sm">غير محدد</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            @if(Route::has('notary.show'))
                                                <a href="{{ route('notary.show', $verification) }}" class="group/btn bg-purple-100 text-purple-700 hover:bg-purple-200 p-3 rounded-xl transition-all duration-300 hover:scale-110" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            @if($verification->status === 'verified' && Route::has('notary.certificate'))
                                                <a href="{{ route('notary.certificate', $verification) }}" class="group/btn bg-green-100 text-green-700 hover:bg-green-200 p-3 rounded-xl transition-all duration-300 hover:scale-110" title="شهادة">
                                                    <i class="fas fa-certificate"></i>
                                                </a>
                                            @endif
                                            @if($verification->status === 'requires_info' && Route::has('notary.info'))
                                                <a href="{{ route('notary.info', $verification) }}" class="group/btn bg-blue-100 text-blue-700 hover:bg-blue-200 p-3 rounded-xl transition-all duration-300 hover:scale-110" title="معلومات">
                                                    <i class="fas fa-info"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <div class="text-gray-400">
                                            <div class="bg-gray-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <i class="fas fa-inbox text-4xl"></i>
                                            </div>
                                            <p class="text-xl font-bold text-gray-600 mb-2">لا توجد طلبات تحقق</p>
                                            <p class="text-sm text-gray-500">قم بإنشاء طلب تحقق جديد للبدء</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Enhanced Pagination -->
                @if($verifications->hasPages())
                    <div class="bg-gradient-to-r from-gray-50 to-purple-50 px-8 py-6 border-t border-purple-100">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700 font-medium">
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-lg font-semibold">
                                    عرض {{ $verifications->firstItem() }} إلى {{ $verifications->lastItem() }} من {{ $verifications->total() }} نتيجة
                                </span>
                            </div>
                            {{ $verifications->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
