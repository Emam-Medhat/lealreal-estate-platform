@extends('layouts.app')

@section('title', 'طلب تحقق جديد')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">طلب تحقق جديد</h1>
        <a href="{{ route('notary.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
            <i class="fas fa-arrow-right ml-2"></i>عودة
        </a>
    </div>

    <form method="POST" action="{{ route('notary.store') }}" class="space-y-6">
        @csrf

        <!-- Contract Selection -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">اختيار العقد</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">العقد <span class="text-red-500">*</span></label>
                    <select name="contract_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">اختر العقد</option>
                        @foreach($contracts ?? [] as $contract)
                            <option value="{{ $contract->id }}">{{ $contract->title }} - {{ $contract->contract_number }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع التحقق <span class="text-red-500">*</span></label>
                    <select name="verification_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">اختر النوع</option>
                        <option value="standard">قياسي (5-7 أيام عمل)</option>
                        <option value="expedited">معجل (2-3 أيام عمل)</option>
                        <option value="priority">أولوية (24 ساعة)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Witnesses -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">الشهود</h2>
            
            <div id="witnesses-container">
                <div class="witness-item border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم الشاهد</label>
                            <input type="text" name="witnesses[0][name]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل اسم الشاهد">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهوية</label>
                            <input type="text" name="witnesses[0][national_id]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل رقم الهوية">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                            <input type="tel" name="witnesses[0][phone]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل رقم الهاتف">
                        </div>
                    </div>
                </div>
                
                <div class="witness-item border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم الشاهد</label>
                            <input type="text" name="witnesses[1][name]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل اسم الشاهد">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهوية</label>
                            <input type="text" name="witnesses[1][national_id]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل رقم الهوية">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                            <input type="tel" name="witnesses[1][phone]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل رقم الهاتف">
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add-witness" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-plus ml-2"></i>إضافة شاهد
            </button>
        </div>

        <!-- Required Documents -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">المستندات المطلوبة</h2>
            
            <div class="space-y-4">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">نسخة من العقد</h3>
                    <input type="file" name="documents[contract_copy]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" accept=".pdf,.doc,.docx">
                    <p class="text-sm text-gray-500 mt-1">نسخة مصدقة من العقد المطلوب توثيقه</p>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">بطاقات الهوية</h3>
                    <input type="file" name="documents[id_documents]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" accept=".pdf,.jpg,.jpeg,.png" multiple>
                    <p class="text-sm text-gray-500 mt-1">صور من بطاقات الهوية لجميع الأطراف والشهود</p>
                </div>
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-2">إثبات العنوان</h3>
                    <input type="file" name="documents[proof_of_address]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" accept=".pdf,.jpg,.jpeg,.png">
                    <p class="text-sm text-gray-500 mt-1">فواتير خدمات حديثة أو إثبات عناوين للأطراف</p>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">معلومات إضافية</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الموعد المفضل للتحقق</label>
                    <input type="datetime-local" name="preferred_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">مكان التحقق</label>
                    <input type="text" name="verification_location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل مكان التحقق المفضل">
                </div>
            </div>
            
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات إضافية</label>
                <textarea name="notes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أي ملاحظات إضافية أو متطلبات خاصة..."></textarea>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-4 space-x-reverse">
            <a href="{{ route('notary.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                إلغاء
            </a>
            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-paper-plane ml-2"></i>إرسال طلب التحقق
            </button>
        </div>
    </form>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let witnessCount = 2;
    
    document.getElementById('add-witness').addEventListener('click', function() {
        const container = document.getElementById('witnesses-container');
        const witnessDiv = document.createElement('div');
        witnessDiv.className = 'witness-item border border-gray-200 rounded-lg p-4 mb-4';
        witnessDiv.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-medium text-gray-900">شاهد ${witnessCount + 1}</h3>
                <button type="button" class="text-red-500 hover:text-red-700 remove-witness">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم الشاهد</label>
                    <input type="text" name="witnesses[${witnessCount}][name]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل اسم الشاهد">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهوية</label>
                    <input type="text" name="witnesses[${witnessCount}][national_id]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل رقم الهوية">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                    <input type="tel" name="witnesses[${witnessCount}][phone]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل رقم الهاتف">
                </div>
            </div>
        `;
        container.appendChild(witnessDiv);
        witnessCount++;
    });
    
    // Remove witness functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-witness')) {
            e.target.closest('.witness-item').remove();
        }
    });
});
</script>
@endsection
