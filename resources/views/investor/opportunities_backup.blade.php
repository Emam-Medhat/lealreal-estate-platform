@extends('admin.layouts.admin')

@section('title', 'فرص الاستثمار')
@section('page-title', 'فرص الاستثمار')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">فرص الاستثمار</h1>
                        <p class="mt-2 text-gray-600">اكتشف واستثمر في مشاريع استثمارية متميزة</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="createAlert()" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                            <i class="fas fa-bell ml-2"></i>
                            تنبيهات
                        </button>
                    </div>
                </div>
            </div>

            <!-- Opportunities Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($opportunities as $opportunity)
                <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4">
                        <div class="flex items-center justify-between">
                            <span class="bg-white/20 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                {{ ucfirst($opportunity->type) }}
                            </span>
                            @if($opportunity->featured)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">
                                مميز
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">{{ $opportunity->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ $opportunity->description }}</p>

                        <!-- Stats -->
                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">الحد الأدنى للاستثمار</span>
                                <span class="text-sm font-semibold text-gray-800">${{ number_format($opportunity->min_investment) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">العائد المتوقع</span>
                                <span class="text-sm font-semibold text-green-600">{{ $opportunity->expected_return }}%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">المدة</span>
                                <span class="text-sm font-semibold text-gray-800">{{ $opportunity->duration }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">مستوى المخاطرة</span>
                                <span class="text-sm font-semibold px-2 py-1 rounded-full @if($opportunity->risk_level == 'low') bg-green-100 text-green-800 @elseif($opportunity->risk_level == 'medium') bg-yellow-100 text-yellow-800 @else bg-red-100 text-red-800 @endif">
                                    {{ $opportunity->risk_level == 'low' ? 'منخفض' : ($opportunity->risk_level == 'medium' ? 'متوسط' : 'مرتفع') }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">المستثمرون</span>
                                <span class="text-sm font-semibold text-blue-600">{{ $opportunity->investors_count }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">المستثمر حتى الآن</span>
                                <span class="text-sm font-semibold text-gray-800">${{ number_format($opportunity->current_investment) }}</span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        @if($opportunity->max_investment > 0)
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-600">نسبة التمويل</span>
                                <span class="text-xs text-gray-800">{{ round(($opportunity->current_investment / $opportunity->max_investment) * 100, 1) }}%</span>
                            </div>
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($opportunity->current_investment / $opportunity->max_investment) * 100 }}%"></div>
                            </div>
                        </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors">
                                <i class="fas fa-chart-line ml-2"></i>
                                تفاصيل
                            </button>
                            <button class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                                <i class="fas fa-hand-holding-usd ml-2"></i>
                                استثمر
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Empty State (if no opportunities) -->
            @if($opportunities->isEmpty())
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">لا توجد فرص استثمارية حالياً</h3>
                <p class="text-gray-600">تحقق لاحقاً للحصول على فرص استثمارية جديدة</p>
            </div>
            @endif
        </div>
    </div>

<script>
function createAlert() {
    alert('سيتم إضافة تنبيهات الفرص الاستثمارية قريباً');
}
</script>
@endsection
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Opportunities Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-search text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Available</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['available'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">New This Week</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['new_this_week'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-percentage text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Avg. ROI</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['avg_roi'] }}%</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Featured</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['featured'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <input type="text" placeholder="Search opportunities..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-64">
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Types</option>
                        <option>Residential</option>
                        <option>Commercial</option>
                        <option>Mixed-use</option>
                        <option>Industrial</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Investment Types</option>
                        <option>Equity</option>
                        <option>Debt</option>
                        <option>Mixed</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Risk Levels</option>
                        <option>Low Risk</option>
                        <option>Medium Risk</option>
                        <option>High Risk</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>Min Investment</option>
                        <option>$1,000</option>
                        <option>$5,000</option>
                        <option>$10,000</option>
                        <option>$25,000</option>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 transition-colors text-sm">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                    <button class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                        <i class="fas fa-sort mr-1"></i>
                        Sort
                    </button>
                </div>
            </div>
        </div>

        <!-- Featured Opportunities -->
        @if($featuredOpportunities->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Featured Opportunities</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($featuredOpportunities as $opportunity)
                        <div class="border-2 border-yellow-400 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="relative">
                                <div class="h-48 bg-gray-200">
                                    @if($opportunity->property->images->isNotEmpty())
                                        <img src="{{ $opportunity->property->images->first()->url }}" alt="{{ $opportunity->property->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-building text-gray-400 text-4xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="absolute top-2 left-2">
                                    <span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                        <i class="fas fa-star mr-1"></i>Featured
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $opportunity->property->name }}</h3>
                                <p class="text-gray-600 mb-3">{{ Str::limit($opportunity->description, 80) }}</p>
                                <p class="text-gray-600 text-sm mb-3">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $opportunity->property->location }}
                                </p>
                                
                                <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-3">
                                    <div>
                                        <i class="fas fa-dollar-sign mr-1"></i>
                                        Min: ${{ number_format($opportunity->minimum_investment, 0) }}
                                    </div>
                                    <div>
                                        <i class="fas fa-percentage mr-1"></i>
                                        ROI: {{ $opportunity->expected_roi }}%
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar mr-1"></i>
                                        Term: {{ $opportunity->investment_term }}
                                    </div>
                                    <div>
                                        <i class="fas fa-shield-alt mr-1"></i>
                                        {{ ucfirst($opportunity->risk_level) }}
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Funded</span>
                                        <span class="font-medium text-gray-800">{{ $opportunity->funded_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $opportunity->funded_percentage }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button onclick="viewOpportunity({{ $opportunity->id }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                        View Details
                                    </button>
                                    <button onclick="investNow({{ $opportunity->id }})" class="flex-1 bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition-colors text-sm">
                                        Invest Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- All Opportunities -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">All Opportunities</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($opportunities as $opportunity)
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="relative h-48 bg-gray-200">
                            @if($opportunity->property->images->isNotEmpty())
                                <img src="{{ $opportunity->property->images->first()->url }}" alt="{{ $opportunity->property->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-building text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            
                            <div class="absolute top-2 left-2">
                                <span class="bg-{{ $opportunity->risk_level === 'low' ? 'green' : ($opportunity->risk_level === 'medium' ? 'yellow' : 'red') }}-500 text-white px-2 py-1 rounded text-xs">
                                    {{ ucfirst($opportunity->risk_level) }} Risk
                                </span>
                            </div>
                            
                            @if($opportunity->is_new)
                                <div class="absolute top-2 right-2">
                                    <span class="bg-blue-500 text-white px-2 py-1 rounded text-xs">
                                        New
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $opportunity->property->name }}</h3>
                            <p class="text-gray-600 mb-3">{{ Str::limit($opportunity->description, 80) }}</p>
                            <p class="text-gray-600 text-sm mb-3">
                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $opportunity->property->location }}
                            </p>
                            
                            <div class="grid grid-cols-2 gap-3 text-sm text-gray-600 mb-3">
                                <div>
                                    <i class="fas fa-dollar-sign mr-1"></i>
                                    Min: ${{ number_format($opportunity->minimum_investment, 0) }}
                                </div>
                                <div>
                                    <i class="fas fa-percentage mr-1"></i>
                                    ROI: {{ $opportunity->expected_roi }}%
                                </div>
                                <div>
                                    <i class="fas fa-calendar mr-1"></i>
                                    Term: {{ $opportunity->investment_term }}
                                </div>
                                <div>
                                    <i class="fas fa-layer-group mr-1"></i>
                                    {{ ucfirst($opportunity->investment_type) }}
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">Funded</span>
                                    <span class="font-medium text-gray-800">{{ $opportunity->funded_percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $opportunity->funded_percentage }}%"></div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center mb-4">
                                <div class="text-lg font-bold text-gray-800">
                                    ${{ number_format($opportunity->minimum_investment, 0) }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $opportunity->days_remaining }} days left
                                </div>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button onclick="viewOpportunity({{ $opportunity->id }})" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                    View Details
                                </button>
                                <button onclick="investNow({{ $opportunity->id }})" class="flex-1 bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition-colors text-sm">
                                    Invest Now
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Opportunities Available</h3>
                        <p class="text-gray-500 mb-6">Check back soon for new investment opportunities.</p>
                        <button onclick="createAlert()" class="bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-bell mr-2"></i>
                            Set Up Alerts
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($opportunities->hasPages())
            <div class="mt-6">
                {{ $opportunities->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function createAlert() {
    window.location.href = '/investor/opportunities/alerts/create';
}

function viewOpportunity(opportunityId) {
    window.location.href = '/investor/opportunities/' + opportunityId;
}

function investNow(opportunityId) {
    window.location.href = '/investor/opportunities/' + opportunityId + '/invest';
}
</script>
@endsection
