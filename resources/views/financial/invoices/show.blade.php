@extends('admin.layouts.admin')

@section('title', 'تفاصيل الفاتورة #' . $invoice->id)

@section('content')
<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header">
        <div>
            <h1 class="page-title">تفاصيل الفاتورة #{{ $invoice->id }}</h1>
            <p class="page-subtitle">تاريخ الإنشاء: {{ \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d H:i') }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.financial.invoices.download', $invoice->id) }}" class="btn-primary" style="background: var(--purple); text-decoration: none; display: inline-flex; align-items: center;">
                <i class="fas fa-download ml-2"></i>
                تحميل PDF
            </a>
            <a href="{{ route('admin.financial.invoices') }}" class="btn-secondary" style="background: var(--card2); color: var(--t1); text-decoration: none; display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 8px; margin-right: 10px;">
                <i class="fas fa-arrow-right ml-2"></i>
                عودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Invoice Details -->
        <div class="col-md-8">
            <div class="card" style="background: var(--card2); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--bd); padding-bottom: 16px;">
                    <h3 style="font-size: 18px; font-weight: 700; color: var(--t1); margin: 0;">معلومات الفاتورة</h3>
                    @php
                        $statusColors = [
                            'draft' => 'background: rgba(148, 163, 184, 0.1); color: rgb(148, 163, 184); border: 1px solid rgba(148, 163, 184, 0.2);',
                            'pending' => 'background: rgba(13, 110, 253, 0.1); color: rgb(13, 110, 253); border: 1px solid rgba(13, 110, 253, 0.2);',
                            'paid' => 'background: rgba(25, 135, 84, 0.1); color: rgb(25, 135, 84); border: 1px solid rgba(25, 135, 84, 0.2);',
                            'overdue' => 'background: rgba(220, 53, 69, 0.1); color: rgb(220, 53, 69); border: 1px solid rgba(220, 53, 69, 0.2);'
                        ];
                        $statusTexts = [
                            'draft' => 'مسودة',
                            'pending' => 'مرسلة',
                            'paid' => 'مدفوعة',
                            'overdue' => 'متأخرة'
                        ];
                        $colorStyle = $statusColors[$invoice->status] ?? 'background: rgba(148, 163, 184, 0.1); color: rgb(148, 163, 184); border: 1px solid rgba(148, 163, 184, 0.2);';
                        $text = $statusTexts[$invoice->status] ?? $invoice->status;
                    @endphp
                    <span style="padding: 8px 16px; font-size: 14px; font-weight: 600; border-radius: 25px; {{ $colorStyle }}">
                        {{ $text }}
                    </span>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4 style="font-size: 14px; color: var(--t4); margin-bottom: 8px;">إلى العميل</h4>
                        <p style="font-size: 16px; font-weight: 600; color: var(--t1); margin-bottom: 4px;">{{ $invoice->user_name }}</p>
                        <p style="font-size: 14px; color: var(--t2);">{{ $invoice->user_email ?? 'لا يوجد بريد إلكتروني' }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h4 style="font-size: 14px; color: var(--t4); margin-bottom: 8px;">العقار المرتبط</h4>
                        <p style="font-size: 16px; font-weight: 600; color: var(--t1); margin-bottom: 4px;">{{ $invoice->property_title ?? 'غير محدد' }}</p>
                        <p style="font-size: 14px; color: var(--t2);">{{ $invoice->property_address ?? '' }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: rgba(148, 163, 184, 0.05);">
                                <th style="padding: 12px; text-align: right; color: var(--t2); font-weight: 600;">البند</th>
                                <th style="padding: 12px; text-align: center; color: var(--t2); font-weight: 600;">الكمية</th>
                                <th style="padding: 12px; text-align: center; color: var(--t2); font-weight: 600;">سعر الوحدة</th>
                                <th style="padding: 12px; text-align: left; color: var(--t2); font-weight: 600;">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($invoiceItems) && count($invoiceItems) > 0)
                                @foreach($invoiceItems as $item)
                                <tr style="border-bottom: 1px solid var(--bd);">
                                    <td style="padding: 16px 12px; color: var(--t1);">{{ $item->description }}</td>
                                    <td style="padding: 16px 12px; text-align: center; color: var(--t1);">{{ $item->quantity }}</td>
                                    <td style="padding: 16px 12px; text-align: center; color: var(--t1);">{{ number_format($item->unit_price, 2) }}</td>
                                    <td style="padding: 16px 12px; text-align: left; color: var(--t1); font-weight: 600;">{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr style="border-bottom: 1px solid var(--bd);">
                                    <td style="padding: 16px 12px; color: var(--t1);">{{ $invoice->description ?? 'تفاصيل الفاتورة' }}</td>
                                    <td style="padding: 16px 12px; text-align: center; color: var(--t1);">1</td>
                                    <td style="padding: 16px 12px; text-align: center; color: var(--t1);">{{ number_format($invoice->total, 2) }}</td>
                                    <td style="padding: 16px 12px; text-align: left; color: var(--t1); font-weight: 600;">{{ number_format($invoice->total, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="padding: 16px 12px; text-align: right; font-weight: 700; color: var(--t1);">المجموع الكلي</td>
                                <td style="padding: 16px 12px; text-align: left; font-weight: 800; color: var(--blue); font-size: 18px;">{{ number_format($invoice->total, 2) }} ريال</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <div class="card" style="background: var(--card2); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <h3 style="font-size: 18px; font-weight: 700; color: var(--t1); margin-bottom: 16px;">الإجراءات السريعة</h3>
                
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="{{ route('admin.financial.invoices.edit', $invoice->id) }}" class="btn-action" style="background: rgba(25, 135, 84, 0.1); color: var(--green); border: 1px solid rgba(25, 135, 84, 0.2); padding: 12px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; font-weight: 600; transition: all 0.3s ease;">
                        <i class="fas fa-edit ml-2"></i> تعديل الفاتورة
                    </a>
                    
                    <form action="{{ route('admin.financial.invoices.destroy', $invoice->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action" style="width: 100%; background: rgba(220, 53, 69, 0.1); color: var(--red); border: 1px solid rgba(220, 53, 69, 0.2); padding: 12px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 600; transition: all 0.3s ease;" onclick="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟')">
                            <i class="fas fa-trash ml-2"></i> حذف الفاتورة
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card" style="background: var(--card2); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <h3 style="font-size: 18px; font-weight: 700; color: var(--t1); margin-bottom: 16px;">التواريخ المهمة</h3>
                
                <div style="margin-bottom: 12px;">
                    <p style="font-size: 14px; color: var(--t4); margin-bottom: 4px;">تاريخ الاستحقاق</p>
                    <p style="font-size: 16px; font-weight: 600; color: var(--t1);">{{ \Carbon\Carbon::parse($invoice->due_date ?? $invoice->created_at)->format('Y-m-d') }}</p>
                </div>
                
                <div>
                    <p style="font-size: 14px; color: var(--t4); margin-bottom: 4px;">آخر تحديث</p>
                    <p style="font-size: 16px; font-weight: 600; color: var(--t1);">{{ \Carbon\Carbon::parse($invoice->updated_at)->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
