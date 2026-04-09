@extends('layouts.app')

@section('title', 'طلب إعفاء ضريبي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-100">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-20 left-1/2 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl p-8 mb-8 border border-white/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-3">
                            <i class="fas fa-shield-alt ml-3"></i>
                            طلب إعفاء ضريبي
                        </h1>
                        <p class="text-gray-600 text-lg">تقديم طلب جديد للإعفاء من الضرائب</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('taxes.exemptions.index') }}" class="bg-white/80 backdrop-blur text-gray-700 px-6 py-3 rounded-xl hover:bg-white transition-all duration-300 shadow-md border border-gray-200">
                            <i class="fas fa-arrow-left ml-2"></i>
                            العودة
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20">
                <form method="POST" action="{{ route('taxes.exemptions.store') }}" class="p-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Property Tax Selection -->
                        <div class="md:col-span-2">
                            <label for="property_tax_id" class="block text-sm font-bold text-gray-700 mb-3">
                                <i class="fas fa-home ml-2 text-purple-600"></i>
                                الضريبة العقارية
                            </label>
                            <select name="property_tax_id" id="property_tax_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                                <option value="">اختر الضريبة العقارية</option>
                                @foreach($propertyTaxes as $propertyTax)
                                    <option value="{{ $propertyTax->id }}">
                                        {{ $propertyTax->property->title }} - {{ number_format($propertyTax->tax_amount) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('property_tax_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Exemption Type -->
                        <div>
                            <label for="exemption_type" class="block text-sm font-bold text-gray-700 mb-3">
                                <i class="fas fa-tag ml-2 text-purple-600"></i>
                                نوع الإعفاء
                            </label>
                            <select name="exemption_type" id="exemption_type" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                                <option value="">اختر نوع الإعفاء</option>
                                @foreach($exemptionTypes as $type)
                                    <option value="{{ $type }}">
                                        @if($type == 'senior_citizen') مواطن كبير السن
                                        @elseif($type == 'disability') ذوي الإعاقة
                                        @elseif($type == 'veteran') محارب
                                        @elseif($type == 'primary_residence') السكن الرئيسي
                                        @elseif($type == 'agricultural') أرض زراعية
                                        @else {{ $type }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('exemption_type')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Exemption Percentage -->
                        <div>
                            <label for="exemption_percentage" class="block text-sm font-bold text-gray-700 mb-3">
                                <i class="fas fa-percentage ml-2 text-purple-600"></i>
                                نسبة الإعفاء (%)
                            </label>
                            <input type="number" name="exemption_percentage" id="exemption_percentage" 
                                min="0" max="100" step="0.01" required
                                placeholder="أدخل نسبة الإعفاء"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                            @error('exemption_percentage')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-bold text-gray-700 mb-3">
                                <i class="fas fa-calendar ml-2 text-purple-600"></i>
                                تاريخ البدء
                            </label>
                            <input type="date" name="start_date" id="start_date" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                            @error('start_date')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-bold text-gray-700 mb-3">
                                <i class="fas fa-calendar-check ml-2 text-purple-600"></i>
                                تاريخ الانتهاء
                            </label>
                            <input type="date" name="end_date" id="end_date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                            @error('end_date')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div class="md:col-span-2">
                            <label for="reason" class="block text-sm font-bold text-gray-700 mb-3">
                                <i class="fas fa-file-alt ml-2 text-purple-600"></i>
                                سبب الإعفاء
                            </label>
                            <textarea name="reason" id="reason" rows="4" required
                                placeholder="اكتب سبب طلب الإعفاء بالتفصيل"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300 resize-none"></textarea>
                            @error('reason')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-2"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Supporting Documents -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-3">
                                <i class="fas fa-paperclip ml-2 text-purple-600"></i>
                                المستندات الداعمة
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-400 transition-colors duration-300">
                                <input type="file" name="documents[]" id="documents" multiple
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    class="hidden" onchange="handleFileSelect(event)">
                                <label for="documents" class="cursor-pointer">
                                    <div class="w-16 h-16 bg-gradient-to-br from-purple-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-purple-600"></i>
                                    </div>
                                    <p class="text-gray-600 font-medium mb-2">اضغط لرفع المستندات</p>
                                    <p class="text-gray-400 text-sm">PDF, DOC, DOCX, JPG, JPEG, PNG</p>
                                </label>
                                <div id="fileList" class="mt-4 text-right"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex justify-end space-x-3 space-x-reverse">
                        <a href="{{ route('taxes.exemptions.index') }}" 
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-300 font-medium">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </a>
                        <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg font-medium">
                            <i class="fas fa-paper-plane ml-2"></i>
                            تقديم الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function handleFileSelect(event) {
    const files = event.target.files;
    const fileList = document.getElementById('fileList');
    
    fileList.innerHTML = '';
    
    if (files.length > 0) {
        const fileNames = Array.from(files).map(file => 
            `<span class="inline-block bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm ml-2 mb-2">
                <i class="fas fa-file ml-2"></i>${file.name}
            </span>`
        ).join('');
        
        fileList.innerHTML = fileNames;
    }
}

// Set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').setAttribute('min', today);
    document.getElementById('end_date').setAttribute('min', today);
});
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.backdrop-blur-lg {
    backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Custom file input styling */
input[type="file"]:hover + label {
    border-color: #9333ea;
}

/* Focus states */
select:focus,
input:focus,
textarea:focus {
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
}

/* Error states */
.is-invalid {
    border-color: #ef4444 !important;
}

.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
}
</style>
@endsection
