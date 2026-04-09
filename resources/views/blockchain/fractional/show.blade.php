@extends('layouts.dashboard')

@section('title', 'تفاصيل الاستثمار: ' . $asset->property->title)

@section('content')
    <div class="space-y-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between">
            <a href="{{ route('blockchain.fractional.holdings') }}"
                class="flex items-center text-sm text-gray-500 hover:text-blue-600 transition">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للمحفظة
            </a>
            <div class="flex gap-2">
                <span
                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-200">
                    Blockchain Asset: {{ $asset->token_id }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Asset Info (Left/Middle) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div
                        class="relative h-64 sm:h-80 box-border p-8 flex flex-col justify-end bg-gradient-to-t from-black/80 via-black/40 to-transparent">
                        <!-- Background Image (Mock) -->
                        <div class="absolute inset-0 -z-10">
                            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                                class="w-full h-full object-cover">
                        </div>

                        <h1 class="text-4xl font-black text-white mb-2 leading-tight">{{ $asset->property->title }}</h1>
                        <div class="flex items-center text-gray-200 text-sm">
                            <i class="fas fa-map-marker-alt ml-2"></i>
                            {{ $asset->property->address }}, {{ $asset->property->city }}
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">إجمالي الحصص</div>
                                <div class="text-xl font-black text-gray-900">{{ number_format($asset->total_shares) }}
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">الحصص المتاحة</div>
                                <div class="text-xl font-black text-blue-600">{{ number_format($asset->remaining_shares) }}
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">سعر الحصة التقديري</div>
                                <div class="text-xl font-black text-gray-900">
                                    ${{ number_format($asset->property->price / $asset->total_shares, 2) }}</div>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="text-[10px] text-gray-400 font-bold uppercase mb-1">الوضع القانوني</div>
                                <div class="flex items-center text-green-600 font-black text-sm">
                                    <i class="fas fa-certificate ml-1"></i>
                                    موثق
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 prose prose-blue max-w-none text-gray-600">
                            <h3
                                class="text-xl font-bold text-gray-900 mb-4 italic underline decoration-blue-500 underline-offset-8">
                                عن هذا العقار المجزأ</h3>
                            <p>{{ $asset->property->description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Smart Contract Details -->
                <div class="bg-gray-900 rounded-3xl p-8 text-white relative overflow-hidden group shadow-2xl">
                    <div
                        class="absolute -right-10 -bottom-10 w-48 h-48 bg-blue-600/10 rounded-full blur-3xl group-hover:bg-blue-600/20 transition-all">
                    </div>

                    <h3 class="text-xl font-black mb-6 flex items-center">
                        <i class="fas fa-file-contract ml-3 text-blue-400"></i>
                        بيانات العقد الذكي (Blockchain Metadata)
                    </h3>

                    <div class="space-y-4 font-mono text-sm leading-relaxed">
                        <div class="flex justify-between border-b border-white/10 pb-2">
                            <span class="text-gray-400">العنوان البرمجي (Contract Address):</span>
                            <span class="text-blue-300 select-all">{{ $asset->smart_contract_address }}</span>
                        </div>
                        <div class="flex justify-between border-b border-white/10 pb-2">
                            <span class="text-gray-400">رمز الأصول (Token ID):</span>
                            <span class="text-indigo-300 font-black">#{{ $asset->token_id }}</span>
                        </div>
                        <div class="flex justify-between border-b border-white/10 pb-2">
                            <span class="text-gray-400">بروتوكول التحقق:</span>
                            <span class="text-green-400">SHA-256 Merkle Proof</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">آخر تحديث للسجل:</span>
                            <span class="text-gray-300">{{ $asset->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Card (Right Sidebar) -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sticky top-6">
                    <div class="text-center mb-8">
                        <div class="text-gray-400 text-xs font-bold uppercase mb-2">قيمة استثمارك المحتملة</div>
                        <div class="text-5xl font-black text-gray-900" id="investment-value">$0.00</div>
                    </div>

                    <form id="buy-form" action="{{ route('blockchain.fractional.buy', $asset->id) }}" method="POST"
                        class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-black text-gray-700 mb-3">عدد الحصص المراد شراؤها</label>
                            <div class="relative">
                                <input type="number" name="shares" id="share-input" min="1"
                                    max="{{ $asset->remaining_shares }}" value="1"
                                    class="login-input w-full bg-gray-50 border-gray-100 rounded-2xl h-14 pr-12 text-xl font-black focus:ring-4 focus:ring-blue-100 transition-all outline-none">
                                <i class="fas fa-gem absolute right-4 top-4 text-blue-500 text-lg"></i>
                            </div>
                            <div class="mt-2 text-[10px] text-gray-400 flex justify-between px-1">
                                <span>الحد الأقصى: {{ number_format($asset->remaining_shares) }} حصة</span>
                                <span id="price-per-share">السعر للحصة:
                                    ${{ number_format($asset->property->price / $asset->total_shares, 2) }}</span>
                            </div>
                        </div>

                        <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-2">
                            <div class="flex justify-between text-xs font-bold text-gray-600">
                                <span>السعر الأساسي:</span>
                                <span id="base-price">$0.00</span>
                            </div>
                            <div class="flex justify-between text-xs font-bold text-gray-600">
                                <span>رسوم المعالجة (0.5%):</span>
                                <span id="fee-price">$0.00</span>
                            </div>
                            <div
                                class="flex justify-between text-sm font-black text-blue-700 border-t border-blue-100 pt-2 mt-2">
                                <span>الإجمالي الكلي:</span>
                                <span id="total-price">$0.00</span>
                            </div>
                        </div>

                        @if($asset->isTradeable())
                            <button type="submit"
                                class="w-full h-14 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-2xl font-black text-lg shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-1 transition-all active:scale-95">
                                تأكيد شراء الحصص
                            </button>
                        @else
                            <button disabled
                                class="w-full h-14 bg-gray-200 text-gray-400 rounded-2xl font-black text-lg cursor-not-allowed italic">
                                غير متاح للتداول حالياً
                            </button>
                        @endif

                        <p class="text-[9px] text-gray-400 text-center px-4 leading-relaxed">
                            بالنقر على "تأكيد الشراء"، فإنك توافق على شروط الاستثمار العقاري التجزيئي وسيتم تسجيل المعاملة
                            فوراً في سجل البلوكشين.
                        </p>
                    </form>
                </div>

                <!-- Ownership Progress -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h4 class="text-sm font-black mb-4">تقدم التمويل الجماعي</h4>
                    <div class="space-y-4">
                        <div class="h-4 w-full bg-gray-100 rounded-full overflow-hidden flex">
                            @php $percent = (($asset->total_shares - $asset->remaining_shares) / $asset->total_shares) * 100; @endphp
                            <div class="h-full bg-gradient-to-r from-blue-400 to-indigo-600 transition-all duration-1000 shadow-inner"
                                style="width: {{ $percent }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs font-bold text-gray-500">
                            <span>مباع: {{ number_format($percent, 1) }}%</span>
                            <span>مستهدف: 100%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const input = document.getElementById('share-input');
                const pricePerShare = {{ $asset->property->price / $asset->total_shares }};

                function updateCalculations() {
                    const val = parseInt(input.value) || 0;
                    const base = val * pricePerShare;
                    const fees = base * 0.005;
                    const total = base + fees;

                    document.getElementById('investment-value').textContent = '$' + total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    document.getElementById('base-price').textContent = '$' + base.toLocaleString(undefined, { minimumFractionDigits: 2 });
                    document.getElementById('fee-price').textContent = '$' + fees.toLocaleString(undefined, { minimumFractionDigits: 2 });
                    document.getElementById('total-price').textContent = '$' + total.toLocaleString(undefined, { minimumFractionDigits: 2 });
                }

                input.addEventListener('input', updateCalculations);
                updateCalculations();
            });
        </script>
    @endpush
@endsection