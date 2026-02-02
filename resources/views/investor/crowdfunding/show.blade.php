@extends('admin.layouts.admin')

@section('title', 'تفاصيل المشروع')
@section('page-title', 'تفاصيل المشروع')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $project['title'] }}</h1>
                        <p class="mt-2 text-gray-600">{{ $project['description'] }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="/investor/crowdfunding" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-arrow-right ml-2"></i>
                            العودة للمشاريع
                        </a>
                    </div>
                </div>
            </div>

            <!-- Project Details -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <!-- Project Header -->
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="bg-purple-100 rounded-full p-3 mr-4">
                                    <i class="fas fa-rocket text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800">{{ $project['title'] }}</h2>
                                    <p class="text-sm text-gray-600">
                                        @php
                                            $categories = [
                                                'real_estate' => 'العقارات',
                                                'technology' => 'التكنولوجيا',
                                                'agriculture' => 'الزراعة',
                                                'food_beverage' => 'المطاعم والمشروبات',
                                                'healthcare' => 'الرعاية الصحية',
                                                'manufacturing' => 'التصنيع'
                                            ];
                                            echo $categories[$project['category']] ?? $project['category'];
                                        @endphp
                                    </p>
                                </div>
                            </div>
                            @if($project['featured'])
                            <span class="bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">
                                مميز
                            </span>
                            @endif
                        </div>

                        <!-- Project Image -->
                        <div class="mb-8">
                            <img src="{{ $project['image_url'] }}" alt="{{ $project['title'] }}" class="w-full h-64 object-cover rounded-lg">
                        </div>

                        <!-- Full Description -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">وصف المشروع</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $project['description'] }}</p>
                        </div>

                        <!-- Key Stats -->
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-dollar-sign text-blue-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">الحد الأدنى للاستثمار</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">${{ number_format($project['min_investment']) }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-percentage text-green-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">العائد المتوقع</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">{{ number_format($project['expected_return'], 2) }}%</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-clock text-purple-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">المدة</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">{{ $project['duration'] }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-shield-alt text-orange-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">مستوى المخاطرة</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">
                                    {{ $project['risk_level'] == 'low' ? 'منخفض' : ($project['risk_level'] == 'medium' ? 'متوسط' : 'مرتفع') }}
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-users text-indigo-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">عدد المستثمرين</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">{{ $project['investors_count'] }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-red-600 mr-2"></i>
                                    <span class="text-sm text-gray-600">الموقع</span>
                                </div>
                                <p class="text-xl font-bold text-gray-800">{{ $project['location'] }}</p>
                            </div>
                        </div>

                        <!-- Funding Progress -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">تمويل المشروع</h3>
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <p class="text-sm text-gray-600">الممول حتى الآن</p>
                                        <p class="text-2xl font-bold text-gray-800">${{ number_format($project['current_amount']) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">هدف التمويل</p>
                                        <p class="text-2xl font-bold text-gray-800">${{ number_format($project['target_amount']) }}</p>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">نسبة التمويل</span>
                                        <span class="font-medium text-gray-800">{{ round(($project['current_amount'] / $project['target_amount']) * 100, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full transition-all duration-500" style="width: {{ ($project['current_amount'] / $project['target_amount']) * 100 }}%"></div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-4 text-sm">
                                    <span class="text-red-600 font-semibold">
                                        <i class="fas fa-clock ml-1"></i>
                                        {{ $project['days_left'] }} يوم متبقي
                                    </span>
                                    <span class="text-gray-600">
                                        الحد الأقصى: ${{ number_format($project['max_investment']) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Team Section -->
                        @if(isset($project['team']) && !empty($project['team']))
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">فريق العمل</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($project['team'] as $member)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center mb-2">
                                        <div class="bg-purple-100 rounded-full p-2 mr-3">
                                            <i class="fas fa-user text-purple-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $member['name'] }}</h4>
                                            <p class="text-sm text-gray-600">{{ $member['role'] }}</p>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $member['experience'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Timeline Section -->
                        @if(isset($project['timeline']) && !empty($project['timeline']))
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">الجدول الزمني</h3>
                            <div class="space-y-4">
                                @foreach($project['timeline'] as $event)
                                <div class="flex items-start">
                                    <div class="bg-purple-100 rounded-full p-2 mr-4 mt-1">
                                        <i class="fas fa-calendar text-purple-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-gray-800">{{ $event['title'] }}</h4>
                                            <span class="text-sm text-gray-600">{{ $event['date'] }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $event['description'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Documents Section -->
                        @if(isset($project['documents']) && !empty($project['documents']))
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">الوثائق</h3>
                            <div class="space-y-3">
                                @foreach($project['documents'] as $doc)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                                            <i class="fas fa-file text-blue-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $doc['name'] }}</h4>
                                            <p class="text-sm text-gray-600">{{ $doc['type'] }} • {{ $doc['size'] }}</p>
                                        </div>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-download"></i>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- FAQ Section -->
                        @if(isset($project['faqs']) && !empty($project['faqs']))
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">الأسئلة الشائعة</h3>
                            <div class="space-y-4">
                                @foreach($project['faqs'] as $faq)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-800 mb-2">{{ $faq['question'] }}</h4>
                                    <p class="text-gray-600">{{ $faq['answer'] }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Investment Card -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">استثمر في هذا المشروع</h3>
                        <button onclick="investInProject({{ $project['id'] }})" class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white py-3 px-4 rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all">
                            <i class="fas fa-hand-holding-usd ml-2"></i>
                            استثمر الآن
                        </button>
                        <div class="mt-4 text-sm text-gray-600">
                            <p>• الحد الأدنى: ${{ number_format($project['min_investment']) }}</p>
                            <p>• الحد الأقصى: ${{ number_format($project['max_investment']) }}</p>
                            <p>• العائد المتوقع: {{ number_format($project['expected_return'], 2) }}%</p>
                            <p>• المدة: {{ $project['duration'] }}</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">إحصائيات سريعة</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">الحالة</span>
                                <span class="text-sm font-medium text-green-600">نشط</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">الفئة</span>
                                <span class="text-sm font-medium text-gray-800">
                                    @php
                                        $categories = [
                                            'real_estate' => 'العقارات',
                                            'technology' => 'التكنولوجيا',
                                            'agriculture' => 'الزراعة',
                                            'food_beverage' => 'المطاعم والمشروبات',
                                            'healthcare' => 'الرعاية الصحية',
                                            'manufacturing' => 'التصنيع'
                                        ];
                                        echo $categories[$project['category']] ?? $project['category'];
                                    @endphp
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">مستوى المخاطرة</span>
                                <span class="text-sm font-medium px-2 py-1 rounded-full @if($project['risk_level'] == 'low') bg-green-100 text-green-800 @elseif($project['risk_level'] == 'medium') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                    {{ $project['risk_level'] == 'low' ? 'منخفض' : ($project['risk_level'] == 'medium' ? 'متوسط' : 'مرتفع') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">المستثمرون</span>
                                <span class="text-sm font-medium text-gray-800">{{ $project['investors_count'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Share Section -->
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">مشاركة المشروع</h3>
                        <div class="flex gap-2">
                            <button class="flex-1 bg-blue-500 text-white py-2 px-3 rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                <i class="fab fa-facebook"></i>
                            </button>
                            <button class="flex-1 bg-blue-400 text-white py-2 px-3 rounded-lg hover:bg-blue-500 transition-colors text-sm">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="flex-1 bg-green-500 text-white py-2 px-3 rounded-lg hover:bg-green-600 transition-colors text-sm">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="flex-1 bg-gray-500 text-white py-2 px-3 rounded-lg hover:bg-gray-600 transition-colors text-sm">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Modal -->
    <script>
        function investInProject(projectId) {
            // Show investment modal
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">استثمار في المشروع</h3>
                    <form id="investmentForm" onsubmit="processInvestment(event, ${projectId})">
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
                            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all">
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

        function processInvestment(event, projectId) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            
            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المعالجة...';
            submitBtn.disabled = true;
            
            // Send data to server
            fetch(`/api/investor/crowdfunding/${projectId}/invest`, {
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
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
@endsection
