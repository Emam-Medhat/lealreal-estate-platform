@extends('admin.layouts.admin')

@section('title', 'تسجيل حركة مخزون')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تسجيل حركة مخزون</h1>
            <p class="text-gray-600 mt-1">إضافة حركة جديدة للمخزون</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('inventory.movements.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
        </div>
    </div>
</div>

<!-- Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <form method="POST" action="{{ route('inventory.movements.store') }}" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="inventory_id" class="block text-sm font-medium text-gray-700 mb-2">العنصر *</label>
                            <select id="inventory_id" name="inventory_id" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر العنصر</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" 
                                            {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }} 
                                        @if($item->sku) ({{ $item->sku }}) @endif
                                        - الكمية الحالية: {{ $item->quantity }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">نوع الحركة *</label>
                            <select id="type" name="type" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    onchange="updateQuantityLabel()">
                                <option value="">اختر النوع</option>
                                <option value="in">وارد</option>
                                <option value="out">صادر</option>
                                <option value="transfer">تحويل</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">الكمية *</label>
                            <input type="number" id="quantity" name="quantity" required min="1"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الكمية">
                        </div>
                        
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">السبب *</label>
                            <select id="reason" name="reason" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر السبب</option>
                                <optgroup label="حركات الوارد">
                                    <option value="purchase">شراء جديد</option>
                                    <option value="return">مرتجع من العميل</option>
                                    <option value="adjustment">تعديل جرد</option>
                                    <option value="production">إنتاج</option>
                                </optgroup>
                                <optgroup label="حركات الصادر">
                                    <option value="sale">بيع</option>
                                    <option value="damage">تلف</option>
                                    <option value="loss">فقدان</option>
                                    <option value="return">مرتجع للمورد</option>
                                </optgroup>
                                <optgroup label="حركات التحويل">
                                    <option value="location_transfer">نقل بين المواقع</option>
                                    <option value="department_transfer">نقل بين الأقسام</option>
                                </optgroup>
                            </select>
                        </div>
                        
                        <div>
                            <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">المرجع</label>
                            <input type="text" id="reference" name="reference"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل المرجع (أمر شراء، فاتورة، إلخ)">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات إضافية</h3>
                    
                    <div class="space-y-4">
                        <div id="location-fields" style="display: none;">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="location_from" class="block text-sm font-medium text-gray-700 mb-2">من موقع</label>
                                    <input type="text" id="location_from" name="location_from"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="الموقع الحالي">
                                </div>
                                <div>
                                    <label for="location_to" class="block text-sm font-medium text-gray-700 mb-2">إلى موقع</label>
                                    <input type="text" id="location_to" name="location_to"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="الموقع الجديد">
                                </div>
                            </div>
                        </div>
                        
                        <div id="cost-fields">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">التكلفة للوحدة</label>
                                    <input type="number" id="unit_cost" name="unit_cost" step="0.01" min="0"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="0.00"
                                           oninput="calculateTotalCost()">
                                </div>
                                <div>
                                    <label for="total_cost" class="block text-sm font-medium text-gray-700 mb-2">إجمالي التكلفة</label>
                                    <input type="number" id="total_cost" name="total_cost" step="0.01" min="0" readonly
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50"
                                           placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                            <textarea id="notes" name="notes" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="أدخل أي ملاحظات إضافية"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="mt-8 flex items-center justify-end space-x-4 space-x-reverse">
            <a href="{{ route('inventory.movements.index') }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                إلغاء
            </a>
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-save ml-2"></i>
                تسجيل الحركة
            </button>
        </div>
    </form>
</div>

<script>
function updateQuantityLabel() {
    const type = document.getElementById('type').value;
    const locationFields = document.getElementById('location-fields');
    const quantityLabel = document.querySelector('label[for="quantity"]');
    
    // Show/hide location fields based on type
    if (type === 'transfer') {
        locationFields.style.display = 'block';
    } else {
        locationFields.style.display = 'none';
        document.getElementById('location_from').value = '';
        document.getElementById('location_to').value = '';
    }
    
    // Update quantity label based on type
    if (type === 'in') {
        quantityLabel.textContent = 'الكمية الواردة *';
    } else if (type === 'out') {
        quantityLabel.textContent = 'الكمية الصادرة *';
    } else if (type === 'transfer') {
        quantityLabel.textContent = 'الكمية المنقولة *';
    } else {
        quantityLabel.textContent = 'الكمية *';
    }
}

function calculateTotalCost() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitCost = parseFloat(document.getElementById('unit_cost').value) || 0;
    const totalCost = quantity * unitCost;
    
    document.getElementById('total_cost').value = totalCost.toFixed(2);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateQuantityLabel();
    
    // Auto-calculate when quantity changes
    document.getElementById('quantity').addEventListener('input', calculateTotalCost);
});
</script>
@endsection
