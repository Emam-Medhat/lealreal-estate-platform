@extends('admin.layouts.admin')

@section('title', 'تفاصيل الصندوق')
@section('page-title', 'تفاصيل الصندوق')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $fund->name }}</h1>
                        <p class="mt-2 text-gray-600">{{ $fund->description }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="/investor/funds" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-arrow-right ml-2"></i>
                            العودة للصناديق
                        </a>
                    </div>
                </div>
            </div>

            <!-- Fund Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <!-- Fund Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="bg-green-100 rounded-full p-3 mr-4">
                                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800">{{ $fund->name }}</h2>
                                    <p class="text-sm text-gray-600">{{ ucfirst($fund->type) }}</p>
                                </div>
                            </div>
                            @if($fund->featured)
                            <span class="bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">
                                مميز
                            </span>
                            @endif
                        </div>

                        <!-- Description -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">وصف الصندوق</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $fund->description }}</p>
                        </div>

                        <!-- Key Stats -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-dollar-sign text-blue-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">الحد الأدنى للاستثمار</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">${{ number_format($fund->min_investment) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-percentage text-green-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">العائد المتوقع</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">{{ number_format($fund->expected_return, 2) }}%</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-clock text-purple-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">المدة</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">{{ $fund->duration }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-shield-alt text-orange-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">مستوى المخاطرة</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">
                                    {{ $fund->risk_level == 'low' ? 'منخفض' : ($fund->risk_level == 'medium' ? 'متوسط' : 'مرتفع') }}
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-users text-indigo-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">عدد المستثمرين</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">{{ $fund->investors_count }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-building text-red-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">مدير الصندوق</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">{{ $fund->manager }}</p>
                            </div>
                        </div>

                        <!-- Funding Progress -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">تمويل الصندوق</h3>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <p class="text-sm text-gray-600">الممول حتى الآن</p>
                                        <p class="text-2xl font-bold text-gray-800">${{ number_format($fund->total_funded) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">هدف التمويل</p>
                                        <p class="text-2xl font-bold text-gray-800">${{ number_format($fund->funding_goal) }}</p>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">نسبة التمويل</span>
                                        <span class="font-medium text-gray-800">{{ round(($fund->total_funded / $fund->funding_goal) * 100, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ ($fund->total_funded / $fund->funding_goal) * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Holdings (if available) -->
                        @if($fund->holdings && !empty($fund->holdings))
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">محفظة الصندوق</h3>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($fund->holdings as $holding)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $holding['name'] ?? 'Asset' }}</p>
                                            <p class="text-sm text-gray-600">{{ $holding['sector'] ?? 'Unknown' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-gray-800">{{ $holding['weight'] ?? '0' }}%</p>
                                            <p class="text-sm text-gray-600">${{ number_format($holding['value'] ?? 0) }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Investment Card -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">استثمر الآن</h3>
                        <button onclick="investInFund({{ $fund->id }})" class="w-full bg-green-500 text-white py-3 px-4 rounded-lg hover:bg-green-600 transition-colors">
                            <i class="fas fa-hand-holding-usd ml-2"></i>
                            استثمر في هذا الصندوق
                        </button>
                        <div class="mt-4 text-sm text-gray-600">
                            <p>• الحد الأدنى: ${{ number_format($fund->min_investment) }}</p>
                            <p>• العائد المتوقع: {{ number_format($fund->expected_return, 2) }}%</p>
                            <p>• المدة: {{ $fund->duration }}</p>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات إضافية</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">الحالة</span>
                                <span class="text-sm font-medium text-green-600">نشط</span>
                            </div>
                            @if($fund->start_date)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">تاريخ البدء</span>
                                <span class="text-sm font-medium text-gray-800">{{ $fund->start_date->format('Y-m-d') }}</span>
                            </div>
                            @endif
                            @if($fund->end_date)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">تاريخ الانتهاء</span>
                                <span class="text-sm font-medium text-gray-800">{{ $fund->end_date->format('Y-m-d') }}</span>
                            </div>
                            @endif
                            @if($fund->location)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">الموقع</span>
                                <span class="text-sm font-medium text-gray-800">{{ $fund->location }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Modal (same as in funds list) -->
    <script>
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
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
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
    </script>
@endsection
