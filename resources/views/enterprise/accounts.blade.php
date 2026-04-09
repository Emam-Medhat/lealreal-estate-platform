@extends('layouts.dashboard')

@section('title', 'حسابات المؤسسات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-cyan-50">
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Animated Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-indigo-700 via-blue-700 to-cyan-700 rounded-3xl shadow-2xl p-8 mb-8 transform hover:scale-[1.02] transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/20 to-blue-600/20 animate-pulse"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <div>
                                <h1 class="text-3xl font-bold text-white tracking-tight">حسابات المؤسسات</h1>
                                <p class="text-indigo-100 text-lg font-medium">إدارة حسابات المؤسسات وحالتها</p>
                            </div>
                        </div>
                        <div class="flex space-x-3 space-x-reverse">
                            <button onclick="createAccount()" class="group bg-white/20 backdrop-blur text-white px-6 py-3 rounded-xl hover:bg-white/30 transition-all duration-300 border border-white/20 hover:border-white/40">
                                <i class="fas fa-plus ml-2 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">حساب جديد</span>
                            </button>
                            <button onclick="refreshAccounts()" class="group bg-white/20 backdrop-blur text-white px-6 py-3 rounded-xl hover:bg-white/30 transition-all duration-300 border border-white/20 hover:border-white/40">
                                <i class="fas fa-sync-alt ml-2 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">تحديث</span>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-400/30 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-400/30 rounded-full blur-3xl"></div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Accounts -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-xl p-6 border border-blue-200 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900">{{ $accounts->count() }}</p>
                            <p class="text-gray-600 text-sm font-medium">إجمالي الحسابات</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Active -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl shadow-xl p-6 border border-green-200 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900">{{ $accounts->where('status', 'active')->count() }}</p>
                            <p class="text-gray-600 text-sm font-medium">نشطة</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Pending -->
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-2xl shadow-xl p-6 border border-yellow-200 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900">{{ $accounts->where('status', 'pending')->count() }}</p>
                            <p class="text-gray-600 text-sm font-medium">في الانتظار</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-r from-yellow-500 to-amber-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Suspended/Banned -->
                <div class="bg-gradient-to-r from-red-50 to-rose-50 rounded-2xl shadow-xl p-6 border border-red-200 transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900">{{ $accounts->whereIn('status', ['suspended', 'banned'])->count() }}</p>
                            <p class="text-gray-600 text-sm font-medium">موقوفة/محظورة</p>
                        </div>
                        <div class="w-14 h-14 bg-gradient-to-r from-red-500 to-rose-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-gradient-to-r from-white to-gray-50 rounded-2xl shadow-xl p-6 mb-8 border border-gray-200">
                <form id="account-filters" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-search text-indigo-500 ml-2"></i>
                                البحث
                            </label>
                            <input type="text" id="search-accounts" placeholder="البحث في الحسابات..." 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-filter text-blue-500 ml-2"></i>
                                الحالة
                            </label>
                            <select id="filter-status" onchange="filterAccounts()" 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all duration-300">
                                <option value="">كل الحالات</option>
                                <option value="active">نشط</option>
                                <option value="pending">في الانتظار</option>
                                <option value="suspended">موقوف</option>
                                <option value="banned">محظور</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-calendar text-green-500 ml-2"></i>
                                من تاريخ
                            </label>
                            <input type="date" id="filter-date-from" onchange="filterAccounts()" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 transition-all duration-300">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-calendar-check text-purple-500 ml-2"></i>
                                إلى تاريخ
                            </label>
                            <input type="date" id="filter-date-to" onchange="filterAccounts()" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100 focus:border-purple-500 transition-all duration-300">
                        </div>
                    </div>
                </form>
            </div>

            <!-- Accounts Table -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المؤسسة</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">البريد الإلكتروني</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الهاتف</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">تاريخ الإنشاء</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($accounts as $account)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full flex items-center justify-center text-white font-bold ml-3">
                                            {{ substr($account->company_name ?? 'NA', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $account->company_name ?? 'غير محدد' }}</div>
                                            <div class="text-sm text-gray-500">{{ $account->industry ?? 'غير محدد' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $account->email ?? 'غير محدد' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $account->phone ?? 'غير محدد' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @switch($account->status)
                                        @case('active')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle ml-1"></i>
                                                نشط
                                            </span>
                                            @break
                                        @case('pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock ml-1"></i>
                                                في الانتظار
                                            </span>
                                            @break
                                        @case('suspended')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                                <i class="fas fa-pause-circle ml-1"></i>
                                                موقوف
                                            </span>
                                            @break
                                        @case('banned')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                <i class="fas fa-ban ml-1"></i>
                                                محظور
                                            </span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                غير محدد
                                            </span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $account->created_at ? $account->created_at->format('Y-m-d') : 'غير محدد' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <button onclick="viewAccount({{ $account->id }})" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editAccount({{ $account->id }})" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteAccount({{ $account->id }})" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-building text-gray-400 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-lg font-medium">لا توجد حسابات حالياً</p>
                                        <p class="text-gray-400 text-sm">قم بإنشاء حساب جديد للبدء</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function createAccount() {
    window.location.href = '/enterprise/accounts/create';
}

function refreshAccounts() {
    location.reload();
}

function viewAccount(id) {
    window.location.href = '/enterprise/accounts/' + id;
}

function editAccount(id) {
    window.location.href = '/enterprise/accounts/' + id + '/edit';
}

function deleteAccount(id) {
    if (confirm('هل أنت متأكد من حذف هذا الحساب؟')) {
        // Implement delete functionality
        console.log('Delete account:', id);
    }
}

function filterAccounts() {
    const search = document.getElementById('search-accounts').value;
    const status = document.getElementById('filter-status').value;
    const dateFrom = document.getElementById('filter-date-from').value;
    const dateTo = document.getElementById('filter-date-to').value;
    
    // Implement filter functionality
    console.log('Filter:', { search, status, dateFrom, dateTo });
}

// Search functionality
document.getElementById('search-accounts')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection
