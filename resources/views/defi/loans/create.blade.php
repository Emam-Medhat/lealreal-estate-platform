@extends('admin.layouts.admin')

@section('title', 'إنشاء قرض DeFi جديد')

@section('page-title', 'إنشاء قرض DeFi جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-hand-holding-usd text-blue-500 ml-3"></i>
                    إنشاء قرض DeFi جديد
                </h1>
                <p class="text-gray-600 mt-2">إنشاء قرض جديد للتمويل اللامركزي</p>
            </div>
            <div>
                <a href="{{ route('defi.loans.index') }}" class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-right ml-2"></i>
                    عودة للقائمة
                </a>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-blue-500 text-white rounded-full font-semibold">1</div>
                    <div class="w-24 h-1 bg-blue-500"></div>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-semibold">2</div>
                    <div class="w-24 h-1 bg-gray-300"></div>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-semibold">3</div>
                </div>
            </div>
            <div class="flex items-center justify-center mt-2 text-sm text-gray-600">
                <span class="ml-8">معلومات المقترض</span>
                <span class="ml-20">تفاصيل القرض</span>
                <span class="ml-20">الضمانات</span>
            </div>
        </div>

        <!-- Main Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="{{ route('defi.loans.store') }}" method="POST">
                @csrf
                
                <!-- Section 1: Borrower Information -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-500 text-white rounded-full text-sm font-bold">1</div>
                        <h2 class="text-xl font-semibold text-gray-900 mr-3">معلومات المقترض</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-lg">
                        <div>
                            <label for="borrower_name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user ml-1"></i>
                                اسم المقترض
                            </label>
                            <input type="text" name="borrower_name" id="borrower_name" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   value="{{ old('borrower_name') }}" required>
                            @error('borrower_name')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="borrower_email" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-envelope ml-1"></i>
                                البريد الإلكتروني
                            </label>
                            <input type="email" name="borrower_email" id="borrower_email" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   value="{{ old('borrower_email') }}" required>
                            @error('borrower_email')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="borrower_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-phone ml-1"></i>
                                رقم الهاتف
                            </label>
                            <input type="tel" name="borrower_phone" id="borrower_phone" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   value="{{ old('borrower_phone') }}" required>
                            @error('borrower_phone')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Loan Details -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-500 text-white rounded-full text-sm font-bold">2</div>
                        <h2 class="text-xl font-semibold text-gray-900 mr-3">تفاصيل القرض</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-lg">
                        <div>
                            <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-home ml-1"></i>
                                العقار
                            </label>
                            <select name="property_id" id="property_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                                <option value="">اختر العقار...</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->title }}</option>
                                @endforeach
                            </select>
                            @error('property_id')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="loan_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-dollar-sign ml-1"></i>
                                مبلغ القرض (ريال)
                            </label>
                            <input type="number" name="loan_amount" id="loan_amount" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   value="{{ old('loan_amount') }}" min="10000" max="5000000" required>
                            @error('loan_amount')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="interest_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-percentage ml-1"></i>
                                سعر الفائدة (%)
                            </label>
                            <input type="number" name="interest_rate" id="interest_rate" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   value="{{ old('interest_rate') }}" min="1" max="30" step="0.1" required>
                            @error('interest_rate')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="loan_term_months" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar ml-1"></i>
                                مدة القرض (شهور)
                            </label>
                            <input type="number" name="loan_term_months" id="loan_term_months" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   value="{{ old('loan_term_months') }}" min="6" max="360" required>
                            @error('loan_term_months')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-info-circle ml-1"></i>
                                الغرض من القرض
                            </label>
                            <textarea name="purpose" id="purpose" rows="3" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                      required>{{ old('purpose') }}</textarea>
                            @error('purpose')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Collateral & Financial -->
                <div class="mb-8">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-8 h-8 bg-blue-500 text-white rounded-full text-sm font-bold">3</div>
                        <h2 class="text-xl font-semibold text-gray-900 mr-3">الضمانات والمعلومات المالية</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-lg">
                        <div>
                            <label for="collateral_value" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-shield-alt ml-1"></i>
                                قيمة الضمان (ريال)
                            </label>
                            <input type="number" name="collateral_value" id="collateral_value" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   value="{{ old('collateral_value') }}" min="0" required>
                            @error('collateral_value')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="monthly_income" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-wallet ml-1"></i>
                                الدخل الشهري (ريال)
                            </label>
                            <input type="number" name="monthly_income" id="monthly_income" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   value="{{ old('monthly_income') }}" min="1000" required>
                            @error('monthly_income')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="credit_score" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-chart-line ml-1"></i>
                                الدرجة الائتمان
                            </label>
                            <input type="number" name="credit_score" id="credit_score" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                   value="{{ old('credit_score') }}" min="300" max="850" required>
                            @error('credit_score')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Documents Upload -->
                <div class="mb-8">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <label for="documents" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-file-upload ml-1"></i>
                            المستندات المطلوبة
                        </label>
                        <input type="file" name="documents[]" id="documents" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                               multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        @error('documents')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                        <div class="text-gray-500 text-sm mt-2">
                            <i class="fas fa-info-circle ml-1"></i>
                            صيغ مدعومة: PDF, DOC, DOCX, JPG, JPEG, PNG (حجم أقصى: 10MB لكل ملف)
                        </div>
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="flex justify-between items-center">
                    <div class="text-gray-500 text-sm">
                        <i class="fas fa-lock ml-1"></i>
                        معلوماتك آمنة ومشفرة
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('defi.loans.index') }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-300">
                            إلغاء
                        </a>
                        <button type="submit" 
                                class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-plus ml-2"></i>
                            إنشاء القرض
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
