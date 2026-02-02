@extends('admin.layouts.admin')

@section('title', 'تفاصيل حملة التمويل الجماعي')
@section('page-title', 'تفاصيل الحملة')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-users text-violet-500 ml-3"></i>
                    تفاصيل حملة التمويل الجماعي
                </h1>
                <p class="text-gray-600 mt-2">{{ $campaign->title }}</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('defi.crowdfunding.index') }}" class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للحملات
                </a>
                @if($campaign->status === 'active')
                <button onclick="investInCampaign()" class="bg-gradient-to-r from-violet-500 to-violet-600 text-white px-6 py-3 rounded-lg hover:from-violet-600 hover:to-violet-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-hand-holding-usd ml-2"></i>
                    استثمر الآن
                </button>
                @endif
            </div>
        </div>

        <!-- Campaign Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Main Info -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $campaign->title }}</h2>
                            <p class="text-gray-600 mb-4">{{ $campaign->property_title }}</p>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt ml-2"></i>
                                {{ $campaign->location }}
                            </div>
                        </div>
                        <div class="text-left">
                            <span class="bg-{{ $campaign->status === 'active' ? 'green' : ($campaign->status === 'completed' ? 'blue' : 'gray') }}-100 text-{{ $campaign->status === 'active' ? 'green' : ($campaign->status === 'completed' ? 'blue' : 'gray') }}-800 text-sm font-medium px-3 py-1 rounded-full">
                                {{ $campaign->status === 'active' ? 'نشطة' : ($campaign->status === 'completed' ? 'مكتملة' : 'منتهية') }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-3">وصف المشروع</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $campaign->property_description ?? 'لا يوجد وصف متاح' }}</p>
                    </div>

                    <!-- Progress Section -->
                    <div class="bg-gradient-to-r from-violet-50 to-violet-100 rounded-xl p-6 border border-violet-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-violet-800">تقدم التمويل</h3>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-violet-600">{{ number_format($progress) }}%</span>
                                <p class="text-sm text-violet-600">مكتمل</p>
                            </div>
                        </div>
                        
                        <div class="w-full bg-violet-200 rounded-full h-4 mb-4">
                            <div class="bg-gradient-to-r from-violet-400 to-violet-600 h-4 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-violet-600">المبلغ المجموع</p>
                                <p class="text-xl font-bold text-violet-800">{{ number_format($campaign->current_amount) }} ريال</p>
                            </div>
                            <div class="text-left">
                                <p class="text-sm text-violet-600">الهدف</p>
                                <p class="text-xl font-bold text-violet-800">{{ number_format($campaign->target_amount) }} ريال</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Investment Details -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-chart-line text-green-500 ml-2"></i>
                        تفاصيل الاستثمار
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">العائد المتوقع</span>
                            <span class="font-bold text-green-600">{{ $campaign->return_rate }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">المدة</span>
                            <span class="font-bold text-gray-800">{{ $campaign->duration_months }} شهر</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">الحد الأدنى للاستثمار</span>
                            <span class="font-bold text-gray-800">{{ number_format($campaign->min_investment) }} ريال</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">الأيام المتبقية</span>
                            <span class="font-bold text-{{ $daysLeft > 30 ? 'green' : ($daysLeft > 10 ? 'yellow' : 'red') }}-600">{{ $daysLeft }} يوم</span>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-clock text-blue-500 ml-2"></i>
                        الجدول الزمني
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-2 ml-3">
                                <i class="fas fa-play text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">بدء الحملة</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="bg-{{ $campaign->status === 'active' ? 'orange' : 'blue' }}-100 rounded-full p-2 ml-3">
                                <i class="fas fa-{{ $campaign->status === 'active' ? 'hourglass-half' : 'check' }} text-{{ $campaign->status === 'active' ? 'orange' : 'blue' }}-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">انتهاء الحملة</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') }}</p>
                            </div>
                        </div>
                        @if($campaign->status === 'completed')
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-2 ml-3">
                                <i class="fas fa-flag-checkered text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">اكتملت الحملة</p>
                                <p class="text-xs text-gray-500">تم تحقيق الهدف بنجاح</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Investors Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-users text-blue-500 ml-3"></i>
                    المستثمرون
                </h3>
                <div class="bg-blue-100 rounded-full p-2">
                    <i class="fas fa-user-friends text-blue-600"></i>
                </div>
            </div>
            
            @if($investments->count() > 0)
            <div class="space-y-4">
                @foreach($investments as $investment)
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-full p-3 ml-4">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800">{{ $investment->investor_name }}</h4>
                            <p class="text-sm text-gray-500">{{ $investment->email }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-blue-600">{{ number_format($investment->amount) }} ريال</p>
                        <p class="text-sm text-gray-500">{{ number_format($investment->shares) }} سهم</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($investment->created_at)->format('d M Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-users text-gray-400 text-3xl"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-600 mb-2">لا يوجد مستثمرون بعد</h3>
                <p class="text-gray-500">كن أول من يستثمر في هذه الحملة</p>
            </div>
            @endif
        </div>

    </div>
</div>

<!-- Investment Modal -->
<div id="investmentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">استثمر في الحملة</h3>
        <form id="investmentForm">
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2">مبلغ الاستثمار (ريال)</label>
                <input type="number" id="investmentAmount" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500" min="{{ $campaign->min_investment }}" step="1000" required>
                <p class="text-sm text-gray-500 mt-2">الحد الأدنى: {{ number_format($campaign->min_investment) }} ريال</p>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2">عدد الأسهم</label>
                <input type="number" id="investmentShares" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                <p class="text-sm text-gray-500 mt-2">سعر السهم: {{ number_format($campaign->min_investment) }} ريال</p>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <button type="button" onclick="closeInvestmentModal()" class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 bg-gradient-to-r from-violet-500 to-violet-600 text-white px-6 py-3 rounded-lg hover:from-violet-600 hover:to-violet-700 transition-all duration-300">
                    تأكيد الاستثمار
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function investInCampaign() {
    document.getElementById('investmentModal').classList.remove('hidden');
}

function closeInvestmentModal() {
    document.getElementById('investmentModal').classList.add('hidden');
    document.getElementById('investmentForm').reset();
}

// Calculate shares based on amount
document.getElementById('investmentAmount').addEventListener('input', function() {
    const amount = parseFloat(this.value) || 0;
    const sharePrice = {{ $campaign->min_investment }};
    const shares = amount / sharePrice;
    document.getElementById('investmentShares').value = shares.toFixed(2);
});

// Handle investment form submission
document.getElementById('investmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const amount = document.getElementById('investmentAmount').value;
    const shares = document.getElementById('investmentShares').value;
    const campaignId = {{ $campaign->id }};
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المعالجة...';
    submitBtn.disabled = true;
    
    // Make real API call
    fetch(`/defi/crowdfunding/${campaignId}/invest`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            amount: parseFloat(amount)
        })
    })
    .then(response => response.json())
    .then(data => {
        closeInvestmentModal();
        
        if (data.success) {
            // Show success message
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
            successDiv.innerHTML = '<i class="fas fa-check-circle ml-2"></i> ' + data.message;
            document.body.appendChild(successDiv);
            
            // Remove success message and reload after 2 seconds
            setTimeout(() => {
                successDiv.remove();
                location.reload();
            }, 2000);
        } else {
            // Show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
            errorDiv.innerHTML = '<i class="fas fa-exclamation-circle ml-2"></i> ' + data.error;
            document.body.appendChild(errorDiv);
            
            // Remove error message after 4 seconds
            setTimeout(() => {
                errorDiv.remove();
            }, 4000);
        }
    })
    .catch(error => {
        closeInvestmentModal();
        
        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
        errorDiv.innerHTML = '<i class="fas fa-exclamation-circle ml-2"></i> حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.';
        document.body.appendChild(errorDiv);
        
        // Remove error message after 4 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 4000);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Close modal when clicking outside
document.getElementById('investmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeInvestmentModal();
    }
});
</script>
@endpush
