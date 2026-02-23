@extends('admin.layouts.admin')

@section('title', 'تسجيل دفعة')

@section('page-title', 'تسجيل دفعة')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تسجيل دفعة جديدة</h1>
            <p class="text-gray-600 mt-2">تسجيل دفعة جديدة للقرض</p>
        </div>
        <div>
            <a href="{{ route('defi.loans.show', $loan->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
                عودة لتفاصيل القرض
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('defi.loans.repay', $loan->id) }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="amount" class="form-label">مبلغ الدفعة</label>
                        <input type="number" name="amount" id="amount" class="form-control" 
                               placeholder="أدخل المبلغ" required min="1" step="0.01">
                        @error('amount')
                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="payment_method" class="form-label">طريقة الدفع</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="">اختر طريقة الدفع</option>
                            <option value="crypto">عملة رقمية</option>
                            <option value="bank">تحويل بنكي</option>
                            <option value="cash">نقدي</option>
                        </select>
                        @error('payment_method')
                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="mb-4">
                        <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" 
                                  placeholder="أي ملاحظات إضافية..."></textarea>
                        @error('notes')
                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            تسجيل الدفعة
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
