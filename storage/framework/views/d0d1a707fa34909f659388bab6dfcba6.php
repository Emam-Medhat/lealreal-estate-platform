<?php $__env->startSection('title', 'قيم بيتك الآن'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">ما قيمة بيتك؟</h1>
            <p class="opacity-90">أدخل عنوانك وبعض التفاصيل لتحصل على تقدير فوري للسعر</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-8">
            <form id="valuation-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                    <input type="text" name="address" placeholder="مثال: شارع التحلية، الرياض"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                    <input type="text" name="city" placeholder="المدينة"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">المساحة (م²)</label>
                    <input type="number" name="area_sqm" placeholder="120"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">عدد الغرف</label>
                    <input type="number" name="bedrooms" placeholder="3"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">عدد الحمامات</label>
                    <input type="number" name="bathrooms" placeholder="2"
                           class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                <div class="md:col-span-2">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                        احسب القيمة
                    </button>
                </div>
            </form>
        </div>

        <div id="result" class="mt-8 hidden">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold mb-4">التقدير الفوري</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-blue-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">القيمة التقديرية</div>
                        <div id="estimate" class="text-3xl font-bold text-blue-700">-</div>
                    </div>
                    <div class="p-6 bg-indigo-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">السعر لكل متر</div>
                        <div id="price-sqm" class="text-3xl font-bold text-indigo-700">-</div>
                    </div>
                    <div class="p-6 bg-green-50 rounded-xl">
                        <div class="text-sm text-gray-600 mb-1">درجة الثقة</div>
                        <div id="confidence" class="text-3xl font-bold text-green-700">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('valuation-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const data = {
        address: form.address.value,
        city: form.city.value,
        area_sqm: form.area_sqm.value,
        bedrooms: form.bedrooms.value,
        bathrooms: form.bathrooms.value,
        property_type: form.property_type.value,
    };
    const res = await fetch('<?php echo e(route('tools.valuation.evaluate')); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    });
    const json = await res.json();
    document.getElementById('result').classList.remove('hidden');
    document.getElementById('estimate').textContent = new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' }).format(json.estimate);
    document.getElementById('price-sqm').textContent = new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' }).format(json.price_per_sqm);
    document.getElementById('confidence').textContent = Math.round((json.confidence || 0) * 100) + '%';
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/tools/valuation.blade.php ENDPATH**/ ?>