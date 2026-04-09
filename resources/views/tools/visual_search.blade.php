@extends('layouts.app')

@section('title', 'البحث الصوري عن العقارات')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-cyan-600 to-blue-600 rounded-2xl shadow p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">ابحث بصورة</h1>
            <p class="opacity-90">ارفع صورة لواجهة أو ديكور تحبه لنقترح عليك عقارات مشابهة</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-8">
            <form id="visual-form" class="grid grid-cols-1 gap-6" enctype="multipart/form-data">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الصورة</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <button type="submit" class="bg-cyan-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-cyan-700 transition-colors">
                        ابحث
                    </button>
                </div>
            </form>
        </div>

        <div id="visual-result" class="mt-8 hidden">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold mb-4">النتائج</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-blue-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">الصورة المرفوعة</div>
                        <img id="preview" class="rounded-xl shadow" src="" alt="preview">
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-sm text-gray-600 mb-2">قريبًا: اقتراحات العقارات المشابهة</div>
                        <pre id="vs-json" class="bg-gray-100 rounded-xl p-4 text-sm overflow-x-auto"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('visual-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const fd = new FormData(form);
    const res = await fetch('{{ route('tools.visual.search') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: fd
    });
    const json = await res.json();
    document.getElementById('visual-result').classList.remove('hidden');
    document.getElementById('preview').src = json.preview_url;
    document.getElementById('vs-json').textContent = JSON.stringify(json.results, null, 2);
});
</script>
@endsection
