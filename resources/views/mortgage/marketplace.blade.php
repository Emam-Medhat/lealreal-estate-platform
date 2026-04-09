@extends('layouts.app')

@section('title', 'سوق التمويل العقاري')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-blue-600 to-violet-600 rounded-2xl shadow p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">قدم طلب تمويل واحد لتحصل على عروض من عدة بنوك</h1>
            <p class="opacity-90">المنصة تعرض العروض في الوقت الفعلي مع مقارنة الأقساط والفوائد</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-8">
            <form id="mortgage-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل</label>
                    <input type="text" name="full_name" class="w-full border border-gray-200 rounded-lg px-4 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" class="w-full border border-gray-200 rounded-lg px-4 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">سعر العقار (SAR)</label>
                    <input type="number" name="property_price" class="w-full border border-gray-200 rounded-lg px-4 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الدفعة الأولى (SAR)</label>
                    <input type="number" name="down_payment" class="w-full border border-gray-200 rounded-lg px-4 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">مدة القرض (بالسنوات)</label>
                    <input type="number" name="loan_term_years" class="w-full border border-gray-200 rounded-lg px-4 py-2" required>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                        احصل على عروض
                    </button>
                </div>
            </form>
        </div>

        <div id="offers" class="mt-8 hidden">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold mb-4">العروض</h2>
                <div id="offers-list" class="grid grid-cols-1 md:grid-cols-3 gap-6"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('mortgage-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    const jsonBody = {};
    fd.forEach((v, k) => jsonBody[k] = v);
    const res = await fetch('{{ route('mortgage.apply') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(jsonBody)
    });
    const data = await res.json();
    if (data.success) {
        document.getElementById('offers').classList.remove('hidden');
        const list = data.offers.map(o => `
            <div class="p-6 bg-gray-50 rounded-xl">
                <div class="text-lg font-bold mb-2">${o.bank}</div>
                <div class="text-sm text-gray-600">الفائدة: ${o.rate}%</div>
                <div class="text-sm text-gray-600">القسط الشهري: ${new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' }).format(o.monthly)}</div>
                <button class="mt-3 bg-indigo-600 text-white px-4 py-2 rounded-lg">تقديم</button>
            </div>
        `).join('');
        document.getElementById('offers-list').innerHTML = list;
    }
});
</script>
@endsection
