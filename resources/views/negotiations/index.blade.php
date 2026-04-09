@extends('layouts.app')

@section('title', 'المفاوضات')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">المفاوضات</h1>
        <div class="flex space-x-4 space-x-reverse">
            <div class="relative">
                <input type="text" id="search" placeholder="بحث في المفاوضات..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">كل الحالات</option>
                <option value="active">نشطة</option>
                <option value="paused">معلقة</option>
                <option value="completed">مكتملة</option>
                <option value="failed">فشلت</option>
                <option value="cancelled">ملغاة</option>
            </select>
            <select id="typeFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">كل الأنواع</option>
                <option value="price">السعر</option>
                <option value="terms">الشروط</option>
                <option value="contingencies">الشروط الاحتياطية</option>
                <option value="closing">الإغلاق</option>
                <option value="general">عام</option>
            </select>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-comments text-blue-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">إجمالي المفاوضات</p>
                    <p class="text-xl font-bold" id="totalNegotiations">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-play-circle text-green-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">نشطة</p>
                    <p class="text-xl font-bold" id="activeNegotiations">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-pause-circle text-yellow-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">معلقة</p>
                    <p class="text-xl font-bold" id="pausedNegotiations">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-check-circle text-purple-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">مكتملة</p>
                    <p class="text-xl font-bold" id="completedNegotiations">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول المفاوضات -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            رقم المفاوضة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            العقار
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المشتري
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            البائع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            النوع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            آخر نشاط
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody id="negotiationsTable" class="bg-white divide-y divide-gray-200">
                    <!-- سيتم تحميل البيانات هنا -->
                </tbody>
            </table>
        </div>
        
        <!-- التحميل -->
        <div id="loading" class="text-center py-8 hidden">
            <i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>
            <p class="mt-2 text-gray-600">جاري التحميل...</p>
        </div>
        
        <!-- لا توجد بيانات -->
        <div id="noData" class="text-center py-8 hidden">
            <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-600">لا توجد مفاوضات حالياً</p>
        </div>
    </div>

    <!-- ترقيم الصفحات -->
    <div id="pagination" class="mt-6 flex justify-center">
        <!-- سيتم إضافة الترقيم هنا -->
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadNegotiations();
    loadStats();
    
    // البحث
    document.getElementById('search').addEventListener('input', function() {
        loadNegotiations();
    });
    
    // فلترة الحالة
    document.getElementById('statusFilter').addEventListener('change', function() {
        loadNegotiations();
    });
    
    // فلترة النوع
    document.getElementById('typeFilter').addEventListener('change', function() {
        loadNegotiations();
    });
});

function loadNegotiations(page = 1) {
    const search = document.getElementById('search').value;
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('noData').classList.add('hidden');
    
    fetch(`/negotiations?page=${page}&search=${search}&status=${status}&type=${type}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading').classList.add('hidden');
            
            if (data.data && data.data.length > 0) {
                renderNegotiations(data.data);
                renderPagination(data);
            } else {
                document.getElementById('noData').classList.remove('hidden');
                document.getElementById('negotiationsTable').innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loading').classList.add('hidden');
        });
}

function renderNegotiations(negotiations) {
    const tbody = document.getElementById('negotiationsTable');
    tbody.innerHTML = '';
    
    negotiations.forEach(negotiation => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                #${negotiation.negotiation_number}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <a href="/properties/${negotiation.property_id}" class="text-blue-600 hover:text-blue-900">
                    ${negotiation.property?.title || 'غير محدد'}
                </a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${negotiation.buyer?.name || 'غير محدد'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${negotiation.seller?.name || 'غير محدد'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${getTypeText(negotiation.type)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${getStatusBadge(negotiation.status)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formatDate(negotiation.last_activity_at)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2 space-x-reverse">
                    <a href="/negotiations/${negotiation.id}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-eye"></i>
                    </a>
                    ${canManage(negotiation.status) ? `
                        <button onclick="pauseNegotiation(${negotiation.id})" class="text-yellow-600 hover:text-yellow-900" title="إيقاف">
                            <i class="fas fa-pause"></i>
                        </button>
                        <button onclick="resumeNegotiation(${negotiation.id})" class="text-green-600 hover:text-green-900" title="استئناف">
                            <i class="fas fa-play"></i>
                        </button>
                        <button onclick="terminateNegotiation(${negotiation.id})" class="text-red-600 hover:text-red-900" title="إنهاء">
                            <i class="fas fa-stop"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function getStatusBadge(status) {
    const badges = {
        'active': '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">نشطة</span>',
        'paused': '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">معلقة</span>',
        'completed': '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">مكتملة</span>',
        'failed': '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">فشلت</span>',
        'cancelled': '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">ملغاة</span>'
    };
    return badges[status] || status;
}

function getTypeText(type) {
    const types = {
        'price': 'السعر',
        'terms': 'الشروط',
        'contingencies': 'الشروط الاحتياطية',
        'closing': 'الإغلاق',
        'general': 'عام'
    };
    return types[type] || type;
}

function canManage(status) {
    return ['active', 'paused'].includes(status);
}

function loadStats() {
    fetch('/negotiations/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalNegotiations').textContent = data.total || 0;
            document.getElementById('activeNegotiations').textContent = data.active || 0;
            document.getElementById('pausedNegotiations').textContent = data.paused || 0;
            document.getElementById('completedNegotiations').textContent = data.completed || 0;
        });
}

function pauseNegotiation(negotiationId) {
    if (confirm('هل أنت متأكد من إيقاف هذه المفاوضات؟')) {
        fetch(`/negotiations/${negotiationId}/pause`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNegotiations();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        });
    }
}

function resumeNegotiation(negotiationId) {
    if (confirm('هل أنت متأكد من استئناف هذه المفاوضات؟')) {
        fetch(`/negotiations/${negotiationId}/resume`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNegotiations();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        });
    }
}

function terminateNegotiation(negotiationId) {
    if (confirm('هل أنت متأكد من إنهاء هذه المفاوضات؟ هذا الإجراء لا يمكن التراجع عنه.')) {
        fetch(`/negotiations/${negotiationId}/terminate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNegotiations();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        });
    }
}

function renderPagination(data) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    
    if (data.last_page > 1) {
        for (let i = 1; i <= data.last_page; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.className = i === data.current_page 
                ? 'px-3 py-1 bg-blue-500 text-white rounded mx-1' 
                : 'px-3 py-1 bg-gray-200 text-gray-700 rounded mx-1 hover:bg-gray-300';
            button.onclick = () => loadNegotiations(i);
            pagination.appendChild(button);
        }
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('ar-SA');
}
</script>
@endpush
