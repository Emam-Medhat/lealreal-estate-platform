@extends('layouts.dashboard')

@section('title', 'محفظة الاستثمار التجزيئي')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">محفظتي الاستثمارية</h1>
                    <p class="text-gray-600">إدارة استثماراتك العقارية المجزأة والعوائد</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('blockchain.index') }}" class="btn-secondary">
                        <i class="fas fa-link ml-2"></i>
                        مركز البلوكشين
                    </a>
                    <a href="{{ route('properties.index') }}" class="btn-primary">
                        <i class="fas fa-search ml-2"></i>
                        استكشف العقارات
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card-stat border-blue-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                        <i class="fas fa-coins text-2xl"></i>
                    </div>
                </div>
                <div class="stat-label uppercase tracking-wider text-xs font-bold text-gray-400">إجمالي الاستثمار</div>
                <div class="stat-number text-3xl font-black mt-1" id="total-invested">...</div>
                <div class="stat-change text-blue-600">
                    <i class="fas fa-wallet ml-1"></i>
                    <span id="holdings-count">0</span> وحدة استثمارية
                </div>
            </div>

            <div class="card-stat border-green-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-50 rounded-xl text-green-600">
                        <i class="fas fa-hand-holding-usd text-2xl"></i>
                    </div>
                </div>
                <div class="stat-label uppercase tracking-wider text-xs font-bold text-gray-400">إجمالي الأرباح المستلمة
                </div>
                <div class="stat-number text-3xl font-black mt-1" id="total-dividends">...</div>
                <div class="stat-change text-green-600">
                    <i class="fas fa-arrow-up ml-1"></i>
                    العوائد التراكمية
                </div>
            </div>

            <div class="card-stat border-purple-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                        <i class="fas fa-percentage text-2xl"></i>
                    </div>
                </div>
                <div class="stat-label uppercase tracking-wider text-xs font-bold text-gray-400">متوسط معدل العائد (ROI)
                </div>
                <div class="stat-number text-3xl font-black mt-1" id="avg-roi">...</div>
                <div class="stat-change text-purple-600">
                    <i class="fas fa-chart-line ml-1"></i>
                    أداء المحفظة
                </div>
            </div>
        </div>

        <!-- Holdings Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-xl font-bold text-gray-900">الأصول المملوكة</h2>
                <div class="relative">
                    <i class="fas fa-filter absolute right-3 top-3 text-gray-400 text-sm"></i>
                    <select
                        class="search-input pr-10 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-none shadow-sm">
                        <option>ترتيب حسب: الأحدث</option>
                        <option>ترتيب حسب: الأعلى قيمة</option>
                        <option>ترتيب حسب: الأعلى عائداً</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right" id="holdings-table">
                    <thead>
                        <tr class="text-gray-400 text-xs uppercase tracking-widest border-b border-gray-100 italic">
                            <th class="px-6 py-4 font-bold">العقار / الأصل</th>
                            <th class="px-6 py-4 font-bold">الحصة المملوكة</th>
                            <th class="px-6 py-4 font-bold">إجمالي المدفوع</th>
                            <th class="px-6 py-4 font-bold">العوائد (Dividends)</th>
                            <th class="px-6 py-4 font-bold">العائد الحالي</th>
                            <th class="px-6 py-4 font-bold">الحالة</th>
                            <th class="px-6 py-4 font-bold">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="holdings-list">
                        <!-- Loaded via JS for extra snapiness -->
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 italic">
                                <i class="fas fa-spinner fa-spin text-2xl mb-3 block text-blue-500"></i>
                                جاري تحميل المحفظة...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                fetchHoldings();

                async function fetchHoldings() {
                    try {
                        const response = await fetch('{{ route("blockchain.fractional.holdings") }}');
                        const data = await response.json();

                        renderStats(data);
                        renderTable(data.holdings);
                    } catch (error) {
                        console.error('Error:', error);
                        document.getElementById('holdings-list').innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-red-500">
                                <i class="fas fa-exclamation-triangle text-2xl mb-3"></i>
                                فشل في استعادة البيانات. يرجى المحاولة لاحقاً.
                            </td>
                        </tr>
                    `;
                    }
                }

                function renderStats(data) {
                    document.getElementById('total-invested').textContent = formatCurrency(data.total_invested);
                    document.getElementById('total-dividends').textContent = formatCurrency(data.total_dividends);
                    document.getElementById('holdings-count').textContent = data.total_holdings;

                    const avgRoi = data.total_invested > 0 ? (data.total_dividends / data.total_invested * 100).toFixed(2) : 0;
                    document.getElementById('avg-roi').textContent = avgRoi + '%';
                }

                function renderTable(holdings) {
                    const tbody = document.getElementById('holdings-list');
                    if (holdings.length === 0) {
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                لا توجد استثمارات نشطة حالياً. ابدأ بالاستثمار الآن!
                            </td>
                        </tr>
                    `;
                        return;
                    }

                    tbody.innerHTML = holdings.map(h => `
                    <tr class="hover:bg-blue-50/50 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 mr-4 shadow-inner">
                                    <i class="fas fa-building text-lg"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-black text-gray-900">${h.property_title}</div>
                                    <div class="text-[10px] text-gray-400 font-mono tracking-tighter mt-0.5">${h.token_id || 'Blockchain Verified'}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-gray-900">${h.shares_owned} حصة</div>
                            <div class="text-xs text-blue-600 font-medium">${h.percentage}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-gray-900">${formatCurrency(h.purchase_price)}</div>
                            <div class="text-[10px] text-gray-400 italic">تم الشراء بتاريخ ${new Date(h.purchased_at).toLocaleDateString('ar-EG')}</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-green-600">+ ${formatCurrency(h.dividends_earned)}</div>
                            <div class="text-[10px] text-gray-400">متاحة للسحب: ${formatCurrency(h.dividends_available)}</div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                ${h.roi_percentage}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-left">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-widest bg-blue-600 text-white animate-pulse">
                                Verified
                            </span>
                        </td>
                        <td class="px-6 py-5 text-left">
                            <button class="bg-gray-50 border border-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-white hover:border-blue-500 hover:text-blue-600 transition shadow-sm group-hover:shadow-md">
                                التفاصيل
                            </button>
                        </td>
                    </tr>
                `).join('');
                }

                function formatCurrency(val) {
                    return new Intl.NumberFormat('ar-EG', { style: 'currency', currency: 'USD' }).format(val);
                }
            });
        </script>
    @endpush
@endsection