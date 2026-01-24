@extends('layouts.app')

@section('title', 'العروض المضادة')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">العروض المضادة</h1>
        <div class="flex space-x-4 space-x-reverse">
            <div class="relative">
                <input type="text" id="search" placeholder="بحث في العروض المضادة..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">كل الحالات</option>
                <option value="pending">في انتظار الرد</option>
                <option value="accepted">مقبول</option>
                <option value="rejected">مرفوض</option>
                <option value="expired">منتهي</option>
                <option value="withdrawn">مسحوب</option>
            </select>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-exchange-alt text-blue-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">إجمالي العروض المضادة</p>
                    <p class="text-xl font-bold" id="totalCounterOffers">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">في انتظار الرد</p>
                    <p class="text-xl font-bold" id="pendingCounterOffers">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">مقبولة</p>
                    <p class="text-xl font-bold" id="acceptedCounterOffers">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">مرفوضة</p>
                    <p class="text-xl font-bold" id="rejectedCounterOffers">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول العروض المضادة -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            العرض الأصلي
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            العقار
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            من قبل
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            إلى
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المبلغ الأصلي
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المبلغ المقترح
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            تاريخ الانتهاء
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody id="counterOffersTable" class="bg-white divide-y divide-gray-200">
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
            <i class="fas fa-exchange-alt text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-600">لا توجد عروض مضادة حالياً</p>
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
var currentUserId = window.currentUserId;

document.addEventListener('DOMContentLoaded', function() {
    loadCounterOffers();
    loadStats();
    
    // البحث
    document.getElementById('search').addEventListener('input', function() {
        loadCounterOffers();
    });
    
    // فلترة الحالة
    document.getElementById('statusFilter').addEventListener('change', function() {
        loadCounterOffers();
    });
});

function loadCounterOffers(page = 1) {
    const search = document.getElementById('search').value;
    const status = document.getElementById('statusFilter').value;
    
    document.getElementById('loading').classList.remove('hidden');
    document.getElementById('noData').classList.add('hidden');
    
    fetch(`/counter-offers?page=${page}&search=${search}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading').classList.add('hidden');
            
            if (data.data && data.data.length > 0) {
                renderCounterOffers(data.data);
                renderPagination(data);
            } else {
                document.getElementById('noData').classList.remove('hidden');
                document.getElementById('counterOffersTable').innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loading').classList.add('hidden');
        });
}

function renderCounterOffers(counterOffers) {
    const tbody = document.getElementById('counterOffersTable');
    tbody.innerHTML = '';
    
    counterOffers.forEach(counterOffer => {
        const row = document.createElement('tr');
        const isExpired = new Date(counterOffer.expiration_date) < new Date();
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                <a href="/offers/${counterOffer.offer_id}" class="text-blue-600 hover:text-blue-900">
                    #${counterOffer.offer?.offer_number || counterOffer.offer_id}
                </a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <a href="/properties/${counterOffer.offer?.property_id}" class="text-blue-600 hover:text-blue-900">
                    ${counterOffer.offer?.property?.title || 'غير محدد'}
                </a>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${counterOffer.countered_by?.name || 'غير محدد'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${counterOffer.countered_to?.name || 'غير محدد'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formatCurrency(counterOffer.offer?.offer_amount || 0)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold ${getPriceColor(counterOffer.counter_amount, counterOffer.offer?.offer_amount)}">
                ${formatCurrency(counterOffer.counter_amount)}
                ${getPriceChange(counterOffer.counter_amount, counterOffer.offer?.offer_amount)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${getStatusBadge(counterOffer.status, isExpired)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formatDate(counterOffer.expiration_date)}
                ${isExpired ? '<span class="text-red-500 text-xs block">(منتهي)</span>' : ''}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2 space-x-reverse">
                    <a href="/counter-offers/${counterOffer.id}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-eye"></i>
                    </a>
                    ${canAccept(counterOffer.status, counterOffer.countered_to_id) && !isExpired ? `
                        <button onclick="acceptCounterOffer(${counterOffer.id})" class="text-green-600 hover:text-green-900" title="قبول">
                            <i class="fas fa-check"></i>
                        </button>
                    ` : ''}
                    ${canReject(counterOffer.status, counterOffer.countered_to_id) && !isExpired ? `
                        <button onclick="rejectCounterOffer(${counterOffer.id})" class="text-red-600 hover:text-red-900" title="رفض">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : ''}
                    ${canWithdraw(counterOffer.status, counterOffer.countered_by_id) && !isExpired ? `
                        <button onclick="withdrawCounterOffer(${counterOffer.id})" class="text-yellow-600 hover:text-yellow-900" title="سحب">
                            <i class="fas fa-undo"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function getStatusBadge(status, isExpired) {
    if (isExpired) {
        return '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">منتهي</span>';
    }
    
    const badges = {
        'pending': '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">في انتظار الرد</span>',
        'accepted': '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">مقبول</span>',
        'rejected': '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">مرفوض</span>',
        'withdrawn': '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">مسحوب</span>'
    };
    return badges[status] || status;
}

function getPriceColor(counterAmount, originalAmount) {
    if (!originalAmount) return 'text-gray-900';
    
    if (counterAmount > originalAmount) return 'text-green-600';
    if (counterAmount < originalAmount) return 'text-red-600';
    return 'text-gray-900';
}

function getPriceChange(counterAmount, originalAmount) {
    if (!originalAmount) return '';
    
    var change = ((counterAmount - originalAmount) / originalAmount * 100).toFixed(1);
    var sign = change > 0 ? '+' : '';
    return '<span class="text-xs">(' + sign + change + '%)</span>';
}

function canAccept(status, counteredToId) {
    return status === 'pending' && counteredToId == currentUserId;
}

function canReject(status, counteredToId) {
    return status === 'pending' && counteredToId == currentUserId;
}

function canWithdraw(status, counteredById) {
    return status === 'pending' && counteredById == currentUserId;
}

function loadStats() {
    fetch('/counter-offers/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalCounterOffers').textContent = data.total || 0;
            document.getElementById('pendingCounterOffers').textContent = data.pending || 0;
            document.getElementById('acceptedCounterOffers').textContent = data.accepted || 0;
            document.getElementById('rejectedCounterOffers').textContent = data.rejected || 0;
        });
}

function acceptCounterOffer(counterOfferId) {
    if (confirm('هل أنت متأكد من قبول هذا العرض المضاد؟')) {
        fetch(`/counter-offers/${counterOfferId}/accept`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCounterOffers();
                loadStats();
            } else {
                alert(data.message || 'حدث خطأ ما');
            }
        });
    }
}

function rejectCounterOffer(counterOfferId) {
    const reason = prompt('يرجى إدخال سبب الرفض (اختياري):');
    
    fetch(`/counter-offers/${counterOfferId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ reason: reason })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadCounterOffers();
            loadStats();
        } else {
            alert(data.message || 'حدث خطأ ما');
        }
    });
}

function withdrawCounterOffer(counterOfferId) {
    if (confirm('هل أنت متأكد من سحب هذا العرض المضاد؟')) {
        fetch(`/counter-offers/${counterOfferId}/withdraw`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCounterOffers();
                loadStats();
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
            button.onclick = () => loadCounterOffers(i);
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

@endsection

<div id="js-data" data-user-id="{{ auth()->user()->id }}" style="display: none;"></div>

<script>
window.currentUserId = parseInt(document.getElementById('js-data').getAttribute('data-user-id'));
</script>
