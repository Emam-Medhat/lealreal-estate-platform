@extends('layouts.app')

@section('title', 'توقيع العقد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Contract Details -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">{{ $contract->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>الوصف:</strong> {{ $contract->description ?? '-' }}</p>
                            <p><strong>تاريخ البدء:</strong> {{ $contract->start_date->format('Y-m-d') }}</p>
                            <p><strong>تاريخ الانتهاء:</strong> {{ $contract->end_date?->format('Y-m-d') ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>القيمة:</strong> {{ $contract->currency }} {{ number_format($contract->value ?? 0, 2) }}</p>
                            <p><strong>الحالة:</strong> 
                                <span class="badge bg-{{ getStatusColor($contract->status) }}">
                                    {{ getStatusLabel($contract->status) }}
                                </span>
                            </p>
                            <p><strong>المنشئ:</strong> {{ $contract->createdBy->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contract Terms -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">بنود العقد</h5>
                </div>
                <div class="card-body">
                    @foreach($contract->terms as $term)
                        <div class="mb-3">
                            <h6>{{ $term->title }}</h6>
                            <p>{{ $term->content }}</p>
                            <small class="text-muted">النوع: {{ getTermTypeLabel($term->type) }}</small>
                            @if($term !== $contract->terms->last())
                                <hr>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Signature Area -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">منطقة التوقيع</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>توقيع الطرف الأول</h6>
                            <div class="signature-area border rounded p-3 mb-3">
                                <canvas id="signature1" width="400" height="150"></canvas>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الاسم الكامل</label>
                                <input type="text" name="signer1_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="signer1_email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">التاريخ</label>
                                <input type="date" name="signer1_date" class="form-control" value="{{ now()->format('Y-m-d') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>توقيع الطرف الثاني</h6>
                            <div class="signature-area border rounded p-3 mb-3">
                                <canvas id="signature2" width="400" height="150"></canvas>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الاسم الكامل</label>
                                <input type="text" name="signer2_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input type="email" name="signer2_email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">التاريخ</label>
                                <input type="date" name="signer2_date" class="form-control" value="{{ now()->format('Y-m-d') }}" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-secondary" onclick="clearSignatures()">مسح التوقيعات</button>
                        <button type="button" class="btn btn-primary" onclick="submitSignatures()">تقديم التوقيعات</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Contract Parties -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">أطراف العقد</h5>
                </div>
                <div class="card-body">
                    @foreach($contract->parties as $party)
                        <div class="mb-3">
                            <h6>{{ $party->name }}</h6>
                            <p class="mb-1"><strong>البريد:</strong> {{ $party->email }}</p>
                            <p class="mb-1"><strong>الهاتف:</strong> {{ $party->phone ?? '-' }}</p>
                            <p class="mb-1"><strong>الدور:</strong> {{ getRoleLabel($party->role) }}</p>
                            @if($party->signed_at)
                                <p class="mb-1"><strong>تم التوقيع:</strong> {{ $party->signed_at->format('Y-m-d H:i') }}</p>
                            @endif
                            @if($party !== $contract->parties->last())
                                <hr>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Signature Status -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">حالة التوقيع</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar" style="width: {{ $signatureProgress }}%">
                                    {{ $signatureProgress }}% مكتمل
                                </div>
                            </div>
                        </div>
                        <p class="mb-2">
                            <i class="fas fa-signature"></i>
                            {{ $signedParties }} من {{ $totalParties }} وقعوا
                        </p>
                        @if($signatureProgress == 100)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                العقد موقع بالكامل
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-clock"></i>
                                في انتظار التوقيعات المتبقية
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">تعليمات التوقيع</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-pencil-alt text-primary"></i>
                            استخدم الماوس أو الشاشة اللمسية للتوقيع
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-user text-primary"></i>
                            أدخل اسمك الكامل والبريد الإلكتروني
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-primary"></i>
                            تحقق من جميع البود قبل التوقيع
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-save text-primary"></i>
                            سيتم حفظ التوقيع إلكترونياً
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.signature-area {
    background-color: #f8f9fa;
    cursor: crosshair;
}

.signature-area canvas {
    border: 1px solid #dee2e6;
    background-color: white;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
let signaturePad1, signaturePad2;

document.addEventListener('DOMContentLoaded', function() {
    initializeSignaturePads();
});

function initializeSignaturePads() {
    const canvas1 = document.getElementById('signature1');
    const canvas2 = document.getElementById('signature2');
    
    signaturePad1 = new SignaturePad(canvas1, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });
    
    signaturePad2 = new SignaturePad(canvas2, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });
    
    // Resize canvas
    resizeCanvas(canvas1, signaturePad1);
    resizeCanvas(canvas2, signaturePad2);
    
    window.addEventListener('resize', function() {
        resizeCanvas(canvas1, signaturePad1);
        resizeCanvas(canvas2, signaturePad2);
    });
}

function resizeCanvas(canvas, signaturePad) {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
    signaturePad.clear();
}

function clearSignatures() {
    signaturePad1.clear();
    signaturePad2.clear();
}

function submitSignatures() {
    if (signaturePad1.isEmpty() || signaturePad2.isEmpty()) {
        alert('يرجى التوقيع في كلا الحقلين');
        return;
    }
    
    const formData = new FormData();
    formData.append('contract_id', {{ $contract->id }});
    formData.append('signature1', signaturePad1.toDataURL());
    formData.append('signature2', signaturePad2.toDataURL());
    formData.append('signer1_name', document.querySelector('input[name="signer1_name"]').value);
    formData.append('signer1_email', document.querySelector('input[name="signer1_email"]').value);
    formData.append('signer2_name', document.querySelector('input[name="signer2_name"]').value);
    formData.append('signer2_email', document.querySelector('input[name="signer2_email"]').value);
    
    fetch('{{ route("contracts.sign.store", $contract) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم حفظ التوقيعات بنجاح');
            window.location.href = '{{ route("contracts.show", $contract) }}';
        } else {
            alert('حدث خطأ أثناء حفظ التوقيعات');
        }
    })
    .catch(error => {
        console.error('Error:', Combined:', error);
        alert('حدث خطأ أثناء حفظ التوقيعات');
    });
}

// Helper functions
function getStatusColor(status) {
    const colors = {
        'draft丛': 'secondary',
        'active': 'success',
        'expired': 'danger',
        'terminated': 'warning'
集': 'info'
    };
    return colors[status] || 'secondary';
}

function getStatusLabel(status) {
    const labels = {
        'draft': 'مسودة',
        'active': 'نشط',
        'expired': 'منتهي',
        'terminated': 'ملغي'
    };
    return labels[status] || status;
}

function getTermTypeLabel(type) {
    const labels = {
        'general': 'عام',
        'payment': 'دفع',
        'termination': 'إنهاء',
        'liability': 'مسؤولية'
    };
    return labels[type] || type;
}

function getRoleLabel(role) {
    const labels = {
        'buyer': 'مشتري',
        'seller': 'بائع',
        'agent': 'وكيل',
        'witness': 'شاهد'
    };
    return labels[role] || role;
}
</script>
@endpush
