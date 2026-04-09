@extends('admin.layouts.admin')

@section('title', 'تم تسجيل الدفعة')

@section('page-title', 'تم تسجيل الدفعة')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="d-flex justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="text-center">
            <div class="mb-4">
                <div class="text-success">
                    <i class="fas fa-check-circle fa-3x mb-3"></i>
                    <h3 class="mb-3">تم تسجيل الدفعة بنجاح!</h3>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-muted">شكراً لك. تم تسجيل دفعتك بنجاح وسيتم معالجتها.</p>
            </div>
            
            <div class="mb-4">
                <p class="text-muted">سيتم توجيهك تلقائياً إلى صفحة تفاصيل القرض.</p>
            </div>
            
            <div>
                <a href="{{ route('defi.loans.show', $loanId ?? 1) }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    العودة لتفاصيل القرض
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
