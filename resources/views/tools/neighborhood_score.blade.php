@extends('layouts.app')

@section('title', 'نقاط الحي المخصصة')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-2xl shadow p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">خصّص أوزانك واحسب التطابق</h1>
            <p class="opacity-90">اضبط الأولويات: الأمان، المدارس، التنقل، التكلفة، المساحات الخضراء</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-8">
            <form id="ns-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الأمان</label>
                    <input type="range" name="safety" min="0" max="1" step="0.05" value="0.8" class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المدارس</label>
                    <input type="range" name="schools" min="0" max="1" step="0.05" value="0.7" class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التنقل</label>
                    <input type="range" name="commute" min="0" max="1" step="0.05" value="0.6" class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التكلفة</label>
                    <input type="range" name="cost" min="0" max="1" step="0.05" value="0.5" class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الخضرة</label>
                    <input type="range" name="green" min="0" max="1" step="0.05" value="0.6" class="w-full">
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition-colors">
                        احسب النقاط
                    </button>
                </div>
            </form>
        </div>

        <div id="ns-result" class="mt-8 hidden">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold mb-4">نتيجة التطابق</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-green-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">الدرجة</div>
                        <div id="ns-score" class="text-3xl font-bold text-green-700">-</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-sm text-gray-600 mb-2">تفصيل العوامل</div>
                        <pre id="ns-breakdown" class="bg-gray-100 rounded-xl p-4 text-sm overflow-x-auto"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('ns-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const data = {
        safety: parseFloat(form.safety.value),
        schools: parseFloat(form.schools.value),
        commute: parseFloat(form.commute.value),
        cost: parseFloat(form.cost.value),
        green: parseFloat(form.green.value),
    };
    const res = await fetch('{{ route('tools.neighborhood.calculate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    });
    const json = await res.json();
    document.getElementById('ns-result').classList.remove('hidden');
    document.getElementById('ns-score').textContent = Math.round((json.score || 0) * 100) / 100;
    document.getElementById('ns-breakdown').textContent = JSON.stringify(json.breakdown, null, 2);
});
</script>
@endsection
