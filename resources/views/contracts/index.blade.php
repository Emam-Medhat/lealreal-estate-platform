@extends('layouts.app')

@section('title', 'العقود')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">العقود</h1>
        <div class="flex space-x-4 space-x-reverse">
            <div class="relative">
                <input type="text" id="search" placeholder="بحث في العقود..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">كل الحالات</option>
                <option value="draft">مسودة</option>
                <option value="pending_review">قيد المراجعة</option>
                <option value="awaiting_signature">في انتظار التوقيع</option>
                <option value="signed">موقع</option>
                <option value="executed">منفذ</option>
                <option value="completed">مكتمل</option>
                <option value="terminated">ملغي</option>
                <option value="cancelled">ملغى</option>
                <option value="expired">منتهي</option>
            </select>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-file-contract text-blue-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">إجمالي العقود</p>
                    <p class="text-xl font-bold" id="totalContracts">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">قيد الانتظار</p>
                    <p class="text-xl font-bold" id="pendingContracts">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">موقع</p>
                    <p class="text-xl font-bold" id="signedContracts">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-dollar-sign text-purple-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">إجمالي القيمة</p>
                    <p class="text-xl font-bold" id="totalValue">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول العقود -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            رقم العقد
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
                            القيمة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            تاريخ الإغلاق
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody id="contractsTable" class="bg-white divide-y divide-gray-200">
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
            <i class="fas fa-file-contract text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-600">لا توجد عقود حالياً</p>
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
    loadContracts();
    loadStats();
    
    // البحث
    document.getElementById('search').addEventListener('input', function() {
        loadContracts();
    });
    
    // فلترة الحالة
    document.getElementById('statusFilter').addEventListener('change', function() {
        loadContracts();
    });
});

function loadContracts(page = 1) {
    const search = document.getElementById('search').value;
    const status = document.getElementById('statusFilter').value;
    
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('noData').classList.add('hidden');
    
    fetch(`/contracts?page=${page}&search=${search}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading').classList.add('hidden');
            
            if (data.data && data.data.length > 0) {
                renderContracts(data.data);
                renderPagination(data);
            } else {
                document.getElementById('noData').classList.remove('hidden');
                document.getElementById('contractsTable').innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loading').classList.add('hidden');
        });
}

function renderContracts(contracts) {
    const tbody = document.getElementById('contractsTable');
    tbody.innerHTML = '';
    
    contracts.forEach(contract => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                ${contract.contract_number}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <a href="/properties/${contract.property_id}" class="text-blue-600 hover:text-blue-900">
                    ${contract.property?.title || 'غير محدد'}
                </a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${contract.buyer?.name || 'غير محدد'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${contract.seller?.name || 'غير محدد'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formatCurrency(contract.purchase_price)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${getStatusBadge(contract.status)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formatDate(contract.closing_date)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2 space-x-reverse">
                    <a href="/contracts/${contract.id}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-eye"></i>
                    </a>
                    ${canEdit(contract.status) ? `
                        <a href="/contracts/${contract.id}/edit" class="text-yellow-600 hover:text-yellow-900">
                            <i class="fas fa-edit"></i>
                        </a>
                    ` : ''}
                    ${canSign(contract.status) ? `
                        <button onclick="signContract(${contract.id})" class="text-green-600 hover:text-green-900">
                            <i class="fas fa-signature"></i>
                        </button>
                    ` : ''}
                    <a href="/contracts/${contract.id}/download" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function getStatusBadge(status) {
    const badges = {
        'draft': '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">مسودة</span>',
        'pending_review': '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">قيد المراجعة</span>',
        'awaiting_signature': '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">في انتظار التوقيع</span>',
        'signed': '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">موقع</span>',
        'executed': '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">منفذ</span>',
        'completed': '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">مكتمل</span>',
        'terminated': '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">ملغي</span>',
        'cancelled': '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">ملغى</span>',
        'expired': '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">منتهي</span>'
    };
    return badges[status] || status;
}

function canEdit(status) {
    return ['draft', 'pending_review'].includes(status);
}

function canSign(status) {
    return ['awaiting_signature'].includes(status);
}

function loadStats() {
    fetch('/contracts/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalContracts').textContent = data.total || 0;
            document.getElementById('pendingContracts').textContent = data.pending || 0;
            document.getElementById('signedContracts').textContent = data.signed || 0;
            document.getElementById('totalValue').textContent = formatCurrency(data.totalValue || 0);
        });
}

function signContract(contractId) {
    if (confirm('هل أنت متأكد من توقيع هذا العقد؟')) {
        fetch(`/contracts/${contractId}/sign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
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
            button.onclick = () => loadContracts(i);
            pagination.appendChild(button);
        }
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR'
    }).format(amount);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('ar-SA');
}
</script>
@endpush
