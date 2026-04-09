@extends('layouts.app')

@section('title', 'محاكي الاستثمار العقاري')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">لو اشتريت في سنة سابقة، كان هيكون كام الآن؟</h1>
            <p class="opacity-90">جرّب سيناريوهات وتابع العائد السنوي وقارن مع الذهب أو الأسهم</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-8">
            <form id="sim-form" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">سعر الشراء (SAR)</label>
                    <input type="number" name="purchase_price" placeholder="500000"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">سنة الشراء</label>
                    <input type="number" name="purchase_year" placeholder="2015"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السنة الحالية</label>
                    <input type="number" name="current_year" value="{{ date('Y') }}"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المقارنة</label>
                    <select name="compare_asset" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="none">بدون مقارنة</option>
                        <option value="gold">ذهب</option>
                        <option value="stocks">أسهم</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-purple-700 transition-colors">
                        احسب العائد
                    </button>
                </div>
            </form>
        </div>

        <div id="sim-result" class="mt-8 hidden">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold mb-4">النتيجة</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="p-6 bg-pink-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">القيمة الحالية</div>
                        <div id="future-value" class="text-2xl font-bold text-pink-700">-</div>
                    </div>
                    <div class="p-6 bg-blue-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">عدد السنوات</div>
                        <div id="years" class="text-2xl font-bold text-blue-700">-</div>
                    </div>
                    <div class="p-6 bg-green-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">العائد السنوي (IRR)</div>
                        <div id="irr" class="text-2xl font-bold text-green-700">-</div>
                    </div>
                    <div class="p-6 bg-indigo-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">قيمة المقارنة</div>
                        <div id="compare" class="text-2xl font-bold text-indigo-700">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('sim-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const data = {
        purchase_price: form.purchase_price.value,
        purchase_year: form.purchase_year.value,
        current_year: form.current_year.value,
        compare_asset: form.compare_asset.value,
    };
    const res = await fetch('{{ route('tools.investment.simulate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    });
    const json = await res.json();
    document.getElementById('sim-result').classList.remove('hidden');
    const fmt = (v) => new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' }).format(v);
    document.getElementById('future-value').textContent = fmt(json.future_value);
    document.getElementById('years').textContent = json.years;
    document.getElementById('irr').textContent = Math.round((json.irr || 0) * 10000) / 100 + '%';
    document.getElementById('compare').textContent = json.compare_value ? fmt(json.compare_value) : '-';
});
</script>
@endsection
