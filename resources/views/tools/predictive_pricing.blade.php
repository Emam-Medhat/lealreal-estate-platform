@extends('layouts.app')

@section('title', 'توقع أسعار العقارات')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-teal-600 to-blue-600 rounded-2xl shadow p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">توقع الأسعار خلال 1/3/5 سنوات</h1>
            <p class="opacity-90">أدخل المدينة والسعر الحالي للحصول على التوقعات والثقة والعوامل المؤثرة</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-8">
            <form id="predict-form" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                    <input type="text" name="city" placeholder="مثال: Riyadh"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">السعر الحالي (SAR)</label>
                    <input type="number" name="current_price" placeholder="750000"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع العقار</label>
                    <select name="property_type" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">اختر نوع العقار</option>
                        <option value="apartment">شقة</option>
                        <option value="villa">فيلا</option>
                        <option value="townhouse">تاون هاوس</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <button type="submit" class="bg-teal-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-teal-700 transition-colors">
                        توقّع الآن
                    </button>
                </div>
            </form>
        </div>

        <div id="predict-result" class="mt-8 hidden">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold mb-4">النتائج</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="p-6 bg-blue-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">سنة واحدة</div>
                        <div id="one-year" class="text-2xl font-bold text-blue-700">-</div>
                    </div>
                    <div class="p-6 bg-indigo-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">3 سنوات</div>
                        <div id="three-year" class="text-2xl font-bold text-indigo-700">-</div>
                    </div>
                    <div class="p-6 bg-purple-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">5 سنوات</div>
                        <div id="five-year" class="text-2xl font-bold text-purple-700">-</div>
                    </div>
                    <div class="p-6 bg-green-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">درجة الثقة</div>
                        <div id="pp-confidence" class="text-2xl font-bold text-green-700">-</div>
                    </div>
                </div>
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-2">العوامل المؤثرة</h3>
                    <pre id="factors" class="bg-gray-100 rounded-xl p-4 text-sm overflow-x-auto"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('predict-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const data = {
        city: form.city.value,
        current_price: form.current_price.value,
        property_type: form.property_type.value,
    };
    const res = await fetch('{{ route('tools.predictive.predict') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    });
    const json = await res.json();
    document.getElementById('predict-result').classList.remove('hidden');
    const fmt = (v) => new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' }).format(v);
    document.getElementById('one-year').textContent = fmt(json.one_year);
    document.getElementById('three-year').textContent = fmt(json.three_year);
    document.getElementById('five-year').textContent = fmt(json.five_year);
    document.getElementById('pp-confidence').textContent = Math.round((json.confidence || 0) * 100) + '%';
    document.getElementById('factors').textContent = JSON.stringify(json.factors, null, 2);
});
</script>
@endsection
