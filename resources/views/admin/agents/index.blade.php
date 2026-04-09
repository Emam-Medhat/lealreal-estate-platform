@extends('layouts.app')


@section('title', 'إدارة الوكلاء')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    <div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 via-indigo-700 to-purple-700 rounded-2xl shadow-xl p-6 md:p-8 mb-6 text-white overflow-hidden relative">
            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.35), transparent 40%), radial-gradient(circle at 80% 30%, rgba(255,255,255,0.25), transparent 45%), radial-gradient(circle at 50% 90%, rgba(255,255,255,0.20), transparent 50%);"></div>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">إدارة الوكلاء</h1>
                    <p class="text-blue-100">إدارة وكلاء العقارات في المنصة</p>
                </div>
                <div class="relative">
                    @if(\Illuminate\Support\Facades\Route::has('admin.agents.create'))
                        <a href="{{ route('admin.agents.create') }}" class="bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-xl transition-all duration-300 flex items-center shadow-sm hover:shadow-md">
                            <i class="fas fa-user-plus ml-2"></i>
                            إضافة وكيل جديد
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-r-4 border-green-500 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 ml-3"></i>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-r-4 border-red-500 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 ml-3"></i>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الوكلاء</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $agents->total() }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-blue-100 to-indigo-100 rounded-2xl p-3">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">وكلاء نشطون</p>
                        <p class="text-2xl font-bold text-green-600">{{ $agents->where('status', 'active')->count() }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-green-100 to-emerald-100 rounded-2xl p-3">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">في انتظار</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $agents->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-100 to-amber-100 rounded-2xl p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">غير نشطين</p>
                        <p class="text-2xl font-bold text-red-600">{{ $agents->where('status', 'inactive')->count() }}</p>
                    </div>
                    <div class="bg-gradient-to-r from-red-100 to-rose-100 rounded-2xl p-3">
                        <i class="fas fa-user-times text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" action="{{ route('admin.agents.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-2">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="البحث عن وكيل بالاسم، البريد الإلكتروني، أو الهاتف..." 
                                   class="w-full px-4 py-2.5 pl-10 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في انتظار</option>
                        </select>
                    </div>
                    <div class="flex space-x-2 space-x-reverse">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-4 py-2.5 rounded-xl hover:from-blue-700 hover:to-indigo-800 transition-all shadow-sm hover:shadow-md">
                            <i class="fas fa-search ml-2"></i>بحث
                        </button>
                        <a href="{{ route('admin.agents.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Agents Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @if($agents->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-slate-50 to-indigo-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوكيل</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الهاتف</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإنشاء</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($agents as $agent)
                                <tr class="hover:bg-indigo-50/40 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <div class="h-10 w-10 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold shadow-sm">
                                                    {{ strtoupper(substr($agent->name ?? $agent->first_name ?? 'A', 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="mr-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $agent->name ?? ($agent->first_name . ' ' . $agent->last_name) }}</div>
                                                <div class="text-sm text-gray-500">ID: {{ $agent->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $agent->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $agent->phone ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($agent->status == 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-circle text-green-400 ml-1.5" style="font-size: 6px;"></i>
                                                نشط
                                            </span>
                                        @elseif($agent->status == 'inactive')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-circle text-red-400 ml-1.5" style="font-size: 6px;"></i>
                                                غير نشط
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-circle text-yellow-400 ml-1.5" style="font-size: 6px;"></i>
                                                في انتظار
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $agent->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                        <div class="flex space-x-2 space-x-reverse">
                                            <a href="{{ route('admin.agents.show', $agent) }}" 
                                               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors"
                                               title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.agents.edit', $agent) }}" 
                                               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-green-50 text-green-700 hover:bg-green-100 transition-colors"
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    onclick="deleteAgent({{ $agent->id }})" 
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-700 hover:bg-red-100 transition-colors"
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gradient-to-r from-slate-50 to-indigo-50 px-6 py-3 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                            عرض {{ $agents->firstItem() }} إلى {{ $agents->lastItem() }} من {{ $agents->total() }} وكيل
                        </div>
                        <div>
                            {{ $agents->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-users text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد وكلاء</h3>
                    <p class="text-gray-500 mb-6">لم يتم العثور على أي وكلاء حالياً</p>
                    @if(\Illuminate\Support\Facades\Route::has('admin.agents.create'))
                        <a href="{{ route('admin.agents.create') }}" 
                           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl hover:from-blue-700 hover:to-indigo-800 transition-all shadow-sm hover:shadow-md">
                            <i class="fas fa-plus ml-2"></i>
                            إضافة وكيل جديد
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deleteAgent(id) {
    if(confirm('هل أنت متأكد من حذف هذا الوكيل؟')) {
        // Create form for delete
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/agents/${id}`;
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        // Add DELETE method
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
