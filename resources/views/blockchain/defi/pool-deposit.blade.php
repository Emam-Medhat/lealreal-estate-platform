@extends('admin.layouts.admin')

@section('title', 'إيداع في المجمع')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <button onclick="window.location.href='{{ route('blockchain.defi.pool.show', $pool->id) }}'" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-right"></i>
                </button>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">إيداع في المجمع</h1>
                    <p class="text-gray-600">إيداع أصولك لكسب العوائد</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Deposit Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">تفاصيل الإيداع</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('blockchain.defi.pool.deposit.process', $pool->id) }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Token Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اختر العملة</label>
                    <select name="token" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="ETH" {{ $pool->token_pair === 'ETH' ? 'selected' : '' }}>ETH</option>
                        <option value="USDC" {{ str_contains($pool->token_pair, 'USDC') ? 'selected' : '' }}>USDC</option>
                        <option value="USDT" {{ str_contains($pool->token_pair, 'USDT') ? 'selected' : '' }}>USDT</option>
                        <option value="DAI" {{ str_contains($pool->token_pair, 'DAI') ? 'selected' : '' }}>DAI</option>
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ</label>
                    <div class="relative">
                        <input type="number" name="amount" step="0.001" min="{{ $pool->min_deposit }}" placeholder="0.000" class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent" id="depositAmount">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">{{ explode('/', $pool->token_pair)[0] }}</span>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-sm text-gray-600">الحد الأدنى: {{ number_format($pool->min_deposit, 4) }} {{ explode('/', $pool->token_pair)[0] }}</span>
                        <button type="button" onclick="document.getElementById('depositAmount').value = '10'" class="text-green-600 hover:text-green-700 text-sm font-medium">الحد الأقصى</button>
                    </div>
                </div>

                <!-- Pool Info -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">معلومات المجمع</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">معدل العائد السنوي:</span>
                            <span class="font-semibold text-green-600">{{ number_format($pool->apy, 2) }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">السيولة الكلية:</span>
                            <span class="font-semibold">{{ number_format($pool->total_liquidity, 4) }} {{ explode('/', $pool->token_pair)[0] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">الحد الأدنى:</span>
                            <span class="font-semibold">{{ number_format($pool->min_deposit, 4) }} {{ explode('/', $pool->token_pair)[0] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">رسوم السحب:</span>
                            <span class="font-semibold">{{ number_format($pool->withdraw_fee, 1) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Estimated Returns -->
                <div class="bg-green-50 rounded-xl p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">العائد المتوقع</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-1">يومي</p>
                            <p class="text-lg font-bold text-green-600" id="dailyReturn">0.0000 {{ explode('/', $pool->token_pair)[0] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-1">شهري</p>
                            <p class="text-lg font-bold text-green-600" id="monthlyReturn">0.0000 {{ explode('/', $pool->token_pair)[0] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-1">سنوي</p>
                            <p class="text-lg font-bold text-green-600" id="yearlyReturn">0.0000 {{ explode('/', $pool->token_pair)[0] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <button type="button" onclick="window.location.href='{{ route('blockchain.defi.pool.show', $pool->id) }}'" class="flex-1 bg-gray-600 text-white py-3 rounded-xl hover:bg-gray-700 transition-colors duration-200 font-medium">
                        إلغاء
                    </button>
                    <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                        <i class="fas fa-plus ml-2"></i>
                        تأكيد الإيداع
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Important Notes -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <div class="flex items-start space-x-3 space-x-reverse">
            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
            <div>
                <h3 class="font-semibold text-yellow-800 mb-2">ملاحظات هامة</h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• سيتم قفل الأصول المودعة في المجمع</li>
                    <li>• يمكن سحب الأصول في أي وقت مع رسوم بسيطة</li>
                    <li>• العائد الفعلي قد يختلف حسب ظروف السوق</li>
                    <li>• هناك مخاطر مرتبطة بالسيولة وفقدان دائم</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const apy = {{ $pool->apy }};
const token = '{{ explode('/', $pool->token_pair)[0] }}';

function updateReturns() {
    const amount = parseFloat(document.getElementById('depositAmount').value) || 0;
    
    if (amount > 0) {
        const dailyReturn = amount * (apy / 100) / 365;
        const monthlyReturn = amount * (apy / 100) / 12;
        const yearlyReturn = amount * (apy / 100);
        
        document.getElementById('dailyReturn').textContent = dailyReturn.toFixed(4) + ' ' + token;
        document.getElementById('monthlyReturn').textContent = monthlyReturn.toFixed(4) + ' ' + token;
        document.getElementById('yearlyReturn').textContent = yearlyReturn.toFixed(4) + ' ' + token;
    } else {
        document.getElementById('dailyReturn').textContent = '0.0000 ' + token;
        document.getElementById('monthlyReturn').textContent = '0.0000 ' + token;
        document.getElementById('yearlyReturn').textContent = '0.0000 ' + token;
    }
}

document.getElementById('depositAmount').addEventListener('input', updateReturns);
document.addEventListener('DOMContentLoaded', updateReturns);
</script>
@endsection
