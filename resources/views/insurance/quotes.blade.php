@extends('layouts.app')

@section('title', 'عروض أسعار التأمين')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">عروض أسعار التأمين</h1>
        <a href="{{ route('insurance.quotes.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus ml-2"></i>طلب سعر جديد
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن عرض سعر..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>مرسل</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي الصلاحية</option>
                </select>
            </div>
            <div>
                <select name="provider" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الشركات</option>
                    @foreach($providers as $id => $name)
                        <option value="{{ $id }}" {{ request('provider') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="property" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع العقارات</option>
                    @foreach($properties as $id => $title)
                        <option value="{{ $id }}" {{ request('property') == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">
                    <i class="fas fa-search ml-2"></i>بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-calculator"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">عروض أسعار نشطة</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['active_quotes'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">تنتهي صلاحيتها</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['expiring_quotes'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">مقبولة</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['accepted_quotes'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-purple-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">متوسط القسط</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['average_premium'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotes Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم العرض</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شركة التأمين</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">القسط</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التغطية</th>
th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">صلاحية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($quotes as $quote)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $quote->quote_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $quote->title }}</div>
                                <div class="text-sm text-gray-500">{{ $quote->coverage_type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $quote->provider->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $quote->provider->rating ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $quote->property->title ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $quote->property->property_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($quote->premium_amount, 2) }}
                                <div class="text-xs text-gray-500">{{ $quote->payment_frequency }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($quote->coverage_amount, 2) }}
                                <div class="text-xs text-gray-500">خصم: {{ number_format($quote->deductible, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($quote->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            معلق
                                        </span>
                                        @break
                                    @case('sent')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            مرسل
                                        </span>
                                        @break
                                    @case('accepted')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            مقبول
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            مرفوض
                                        </span>
                                        @break
                                    @case('expired')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            منتهي الصلاحية
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $quote->valid_until->format('Y-m-d') }}
                                @if($quote->is_expiring_soon)
                                    <span class="text-yellow-600 text-xs">({{ $quote->days_until_expiry }} يوم)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('insurance.quotes.show', $quote) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('insurance.quotes.edit', $quote) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($quote->status === 'pending')
                                    <a href="{{ route('insurance.quotes.send', $quote) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-paper-plane"></i>
                                    </a>
                                @endif
                                @if($quote->status === 'sent')
                                    <a href="{{ route('insurance.quotes.accept', $quote) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="{{ route('insurance.quotes.reject', $quote) }}" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                                <a href="{{ route('insurance.quotes.download', $quote) }}" class="text-purple-600 hover:text-purple-900 ml-2">
                                    <i class="fas fa-download"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                لا توجد عروض أسعار مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $quotes->links() }}
        </div>
    </div>

    <!-- Comparison Tool -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">أداة المقارنة</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-md font-medium mb-3">اختر عروض للمقارنة</h3>
                <select id="compareQuote1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">اختر العرض الأول</option>
                    @foreach($quotes as $quote)
                        <option value="{{ $quote->id }}">{{ $quote->quote_number }} - {{ $quote->provider->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <h3 class="text-md font-medium mb-3">اختر عروض للمقارنة</h3>
                <select id="compareQuote2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">اختر العرض الثاني</option>
                    @foreach($quotes as $quote)
                        <option value="{{ $quote->id }}">{{ $quote->quote_number }} - {{ $quote->provider->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button onclick="compareQuotes()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 mt-4">
            <i class="fas fa-balance-scale ml-2"></i>مقارنة العروض
        </button>
        <div id="comparisonResult" class="hidden mt-6 p-4 bg-gray-50 rounded-lg">
            <!-- Comparison results will be shown here -->
        </div>
    </div>

    <!-- Quote Requests -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">طلبات الأسعار</h2>
        <div class="space-y-4">
            @foreach($quoteRequests as $request)
                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-4">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-900">{{ $request->title }}</div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $request->status_label }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $request->property->title }} - {{ $request->coverage_type }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $request->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <a href="{{ route('insurance.quote-requests.show', $request) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            <i class="fas fa-eye ml-1"></i>عرض
                        </a>
                        @if($request->status === 'pending')
                            <a href="{{ route('insurance.quote-requests.process', $request) }}" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                <i class="fas fa-cog ml-1"></i>معالجة
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@section('scripts')
<script>
function compareQuotes() {
    const quote1Id = document.getElementById('compareQuote1').value;
    const quote2Id = document.getElementById('compareQuote2').value;
    
    if (!quote1Id || !quote2Id) {
        alert('الرجاء اختيار عرضين للمقارنة');
        return;
    }
    
    // Fetch comparison data via AJAX
    fetch(`/insurance/quotes/compare?quote1=${quote1Id}&quote2=${quote2Id}`)
        .then(response => response.json())
        .then(data => {
            displayComparison(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأء في المقارنة');
        });
}

function displayComparison(data) {
    const resultDiv = document.getElementById('comparisonResult');
    resultDiv.classList.remove('hidden');
    
    let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
    
    // Quote 1
    html += '<div class="bg-white p-4 rounded-lg border border-blue-200">';
    html += '<h3 class="text-lg font-semibold text-blue-600 mb-2">عرض 1</h3>';
    html += `<p class="text-sm text-gray-600">الرقم: ${data.quote1.quote_number}</p>`;
    html += `<p class="text-sm text-gray-600">الشركة: ${data.quote1.provider_name}</p>`;
    html += `<p class="text-sm text-gray-600">القسط: ${data.quote1.premium_amount}</p>`;
    html += `<p class="text-sm text-gray-600">التغطية: ${data.quote1.coverage_amount}</p>';
    html += `<p class="text-sm text-gray-600">الخصم: ${data.quote1.deductible}</p>`;
    html += `<p class="text-sm text-gray-600">التقييم: ${data.quote1.rating}/5</p>`;
    html += '</div>';
    
    // Quote 2
    html += '<div class="bg-white p-4 rounded-lg border border-green-200">';
    html += '<h3 class="text-lg font-semibold text-green-600 mb-2">عرض 2</h3>';
    html += `<p class="text-sm text-gray-600">الرقم: ${data.quote2.quote_number}</p>`;
    html += `<p class="text-sm text-gray-600">الشركة: ${data.quote2.provider_name}</p>`;
    html += `<p class="text-sm text-gray-600">القسط: ${data.quote2.premium_amount}</p>`;
    html += `<p class="text-sm text-gray-600">التغطية: ${data.quote2.coverage_amount}</p>`;
    html += `<p class="text-sm text-gray-600">الخصم: ${data.quote2.deductible}</p>`;
    html += `<p class="text-sm text-gray-600">التقييم: ${data.quote2.rating}/5</p>`;
    html += '</div>';
    
    html += '</div>';
    
    // Comparison Summary
    html += '<div class="col-span-full bg-gray-50 p-4 rounded-lg">';
    html += '<h3 class="text-lg font-semibold mb-2">ملخص المقارنة</h3>';
    html += '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
    html += `<div><p class="text-sm text-gray-600">فرق القسط:</p><p class="text-lg font-bold ${data.premium_difference > 0 ? 'text-red-600' : 'text-green-600'}">${data.premium_difference}</p></div>`;
    html += `<div><p class="text-sm text-gray-600">فرق التغطية:</p><p class="text-lg font-bold ${data.coverage_difference > 0 ? 'text-green-600' : 'text-red-600'}">${data.coverage_difference}</p></div>`;
    html += `<div><p class="text-sm text-gray-600">الأفضل:</p><p class="text-lg font-bold ${data.recommendation}</p></div>`;
    html += '</div>';
    html += '</div>';
    
    resultDiv.innerHTML = html;
}
</script>
@endsection
