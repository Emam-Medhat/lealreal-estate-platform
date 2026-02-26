@extends('layouts.app')

@section('title', 'تسجيل العقود على البلوكشين')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-emerald-600 to-blue-600 rounded-2xl shadow p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">توليد عقد ذكي للعقار</h1>
            <p class="opacity-90">سيتم حفظ الوثائق على IPFS وإصدار شهادة ملكية NFT لاحقًا</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-8">
            <form id="contract-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم العقار</label>
                    <input type="number" name="property_id" class="w-full border border-gray-200 rounded-lg px-4 py-2" required>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-emerald-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-emerald-700 transition-colors">
                        توليد العقد
                    </button>
                </div>
            </form>
        </div>

        <div id="contract-result" class="mt-8 hidden">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold mb-4">نتيجة</h2>
                <pre id="contract-json" class="bg-gray-100 rounded-xl p-4 text-sm overflow-x-auto"></pre>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('contract-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(e.target);
    const body = {};
    fd.forEach((v,k)=> body[k]=v);
    const res = await fetch('{{ route('contracts.generate') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(body)
    });
    const json = await res.json();
    document.getElementById('contract-result').classList.remove('hidden');
    document.getElementById('contract-json').textContent = JSON.stringify(json, null, 2);
});
</script>
@endsection
