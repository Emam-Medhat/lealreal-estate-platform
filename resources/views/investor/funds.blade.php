@extends('admin.layouts.admin')

@section('title', 'صناديق الاستثمار')
@section('page-title', 'صناديق الاستثمار')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">صناديق الاستثمار</h1>
                        <p class="mt-2 text-gray-600">اكتشف واستثمر في صناديق استثمارية متنوعة</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="createAlert()" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                            <i class="fas fa-bell ml-2"></i>
                            تنبيهات
                        </button>
                    </div>
                </div>
            </div>

            <!-- Funds Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($funds as $fund)
                <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 p-4">
                        <div class="flex items-center justify-between">
                            <span class="bg-white/20 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                {{ ucfirst($fund->type) }}
                            </span>
                            @if($fund->featured)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">
                                مميز
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $fund->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $fund->description }}</p>

                        <!-- Manager -->
                        <div class="flex items-center mb-4">
                            <div class="bg-gray-100 rounded-full p-2 mr-3">
                                <i class="fas fa-building text-gray-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">مدير الصندوق</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $fund->manager }}</p>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">الحد الأدنى للاستثمار</span>
                                <span class="text-sm font-semibold text-gray-800">${{ number_format($fund->min_investment) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">العائد المتوقع</span>
                                <span class="text-sm font-semibold text-green-600">{{ number_format($fund->expected_return, 2) }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">المدة</span>
                                <span class="text-sm font-semibold text-gray-800">{{ $fund->duration }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">مستوى المخاطرة</span>
                                <span class="text-sm font-semibold px-2 py-1 rounded-full @if($fund->risk_level == 'low') bg-green-100 text-green-800 @elseif($fund->risk_level == 'medium') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                    {{ $fund->risk_level == 'low' ? 'منخفض' : ($fund->risk_level == 'medium' ? 'متوسط' : 'مرتفع') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">عدد المستثمرين</span>
                                <span class="text-sm font-semibold text-blue-600">{{ $fund->investors_count }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">الممول حتى الآن</span>
                                <span class="text-sm font-semibold text-gray-800">${{ number_format($fund->total_funded) }}</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-600">نسبة التمويل</span>
                                <span class="text-xs text-gray-800">{{ round(($fund->total_funded / $fund->funding_goal) * 100, 1) }}%</span>
                            </div>
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($fund->total_funded / $fund->funding_goal) * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button onclick="viewFundDetails({{ $fund->id }})" class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors">
                                <i class="fas fa-chart-line ml-2"></i>
                                تفاصيل
                            </button>
                            <button onclick="investInFund({{ $fund->id }})" class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                                <i class="fas fa-hand-holding-usd ml-2"></i>
                                استثمر
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Empty State (if no funds) -->
            @if($funds->isEmpty())
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">لا توجد صناديق استثمارية حالياً</h3>
                <p class="text-gray-600">تحقق لاحقاً للحصول على صناديق استثمارية جديدة</p>
            </div>
            @endif
        </div>
    </div>

<script>
function createAlert() {
    // Show modal for creating alert
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">إعداد تنبيهات الصناديق</h3>
            <form id="alertForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نوع الصناديق</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">جميع الصناديق</option>
                            <option value="real_estate">العقارات</option>
                            <option value="technology">التكنولوجيا</option>
                            <option value="renewable_energy">الطاقة المتجددة</option>
                            <option value="ecommerce">التجارة الإلكترونية</option>
                            <option value="education">التعليم</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الحد الأدنى للاستثمار</label>
                        <select name="min_investment" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">أي مبلغ</option>
                            <option value="1000">$1,000</option>
                            <option value="2500">$2,500</option>
                            <option value="5000">$5,000</option>
                            <option value="10000">$10,000</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">مستوى المخاطرة</label>
                        <select name="risk_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">كل المستويات</option>
                            <option value="low">منخفض</option>
                            <option value="medium">متوسط</option>
                            <option value="high">مرتفع</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">العائد المتوقع (٪)</label>
                        <input type="number" name="expected_return" placeholder="الحد الأدنى للعائد" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">طريقة الإشعار</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="notification_email" value="1" class="ml-2" checked>
                                <span class="text-sm text-gray-700">البريد الإلكتروني</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="notification_sms" value="1" class="ml-2">
                                <span class="text-sm text-gray-700">رسالة نصية</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="notification_push" value="1" class="ml-2" checked>
                                <span class="text-sm text-gray-700">إشعارات التطبيق</span>
                            </label>
                        </div>
                    </div>
                    <!-- CSRF Token -->
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        إلغاء
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        حفظ التنبيه
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle form submission
    document.getElementById('alertForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveAlert(this);
    });
}

function closeModal() {
    const modal = document.querySelector('.fixed.inset-0');
    if (modal) {
        modal.remove();
    }
}

function saveAlert(form) {
    const formData = new FormData(form);
    
    // Send data as FormData instead of JSON to handle CSRF properly
    fetch('/api/investor/alerts', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeModal();
            showNotification('تم إعداد التنبيه بنجاح!', 'success');
        } else {
            showNotification(data.message || 'حدث خطأ، يرجى المحاولة مرة أخرى', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ في الاتصال بالخادم', 'error');
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' : 
                type === 'error' ? 'fa-exclamation-circle' : 
                'fa-info-circle'
            } ml-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Function to view fund details
function viewFundDetails(fundId) {
    window.location.href = `/investor/funds/${fundId}`;
}

// Function to invest in fund
function investInFund(fundId) {
    // Show investment modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">استثمار في الصندوق</h3>
            <form id="investmentForm" onsubmit="processInvestment(event, ${fundId})">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">مبلغ الاستثمار</label>
                        <input type="number" name="amount" required min="1000" step="100" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="أدخل مبلغ الاستثمار">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="أي ملاحظات إضافية"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="terms" required class="ml-2">
                            <span class="text-sm text-gray-700">أوافق على الشروط والأحكام</span>
                        </label>
                    </div>
                    <!-- CSRF Token -->
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="closeInvestmentModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        إلغاء
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        تأكيد الاستثمار
                    </button>
                </div>
            </form>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function closeInvestmentModal() {
    const modal = document.querySelector('.fixed.inset-0');
    if (modal) {
        modal.remove();
    }
}

function processInvestment(event, fundId) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المعالجة...';
    submitBtn.disabled = true;
    
    // Send data to server
    fetch(`/api/investor/funds/${fundId}/invest`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeInvestmentModal();
            showNotification('تم إرسال طلب الاستثمار بنجاح!', 'success');
            // Optionally redirect to investment details
            setTimeout(() => {
                window.location.href = `/investor/funds/${fundId}`;
            }, 2000);
        } else {
            showNotification(data.message || 'حدث خطأ في طلب الاستثمار', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ في الاتصال بالخادم', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
</script>
@endsection
