@extends('admin.layouts.admin')

@section('title', 'الفواتير')

@section('content')
<div class="content-wrapper">
    <!-- Header Section -->
    <div class="page-header">
        <div>
            <h1 class="page-title">الفواتير</h1>
            <p class="page-subtitle">إدارة ومراقبة جميع الفواتير والمدفوعات</p>
        </div>
        <div class="page-actions">
            <button class="btn-primary">
                <i class="fas fa-download ml-2"></i>
                تصدير الفواتير
            </button>
            <a href="{{ route('admin.financial.invoices.create') }}" class="btn-primary" style="background: var(--green-g); text-decoration: none; display: inline-flex; align-items: center;">
                <i class="fas fa-plus ml-2"></i>
                فاتورة جديدة
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid" style="gap: 16px;">
        <!-- Total Invoices -->
        <div class="stat-card blue" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 20px; position: relative; overflow: hidden; border-radius: 16px; box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
            <div style="position: absolute; top: -15px; right: -15px; width: 60px; height: 60px; background: rgba(255, 255, 255, 0.15); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -10px; left: -10px; width: 40px; height: 40px; background: rgba(255, 255, 255, 0.08); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-left: 10px;">
                        <i class="fas fa-file-invoice" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div class="stat-value" style="color: white; font-size: 28px; font-weight: 800;">{{ $stats['total_invoices'] }}</div>
                </div>
                <div class="stat-label" style="color: rgba(255, 255, 255, 0.9); font-size: 13px; font-weight: 500;">إجمالي الفواتير</div>
            </div>
        </div>

        <!-- Paid Invoices -->
        <div class="stat-card green" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; padding: 20px; position: relative; overflow: hidden; border-radius: 16px; box-shadow: 0 8px 32px rgba(17, 153, 142, 0.3); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
            <div style="position: absolute; top: -15px; right: -15px; width: 60px; height: 60px; background: rgba(255, 255, 255, 0.15); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -10px; left: -10px; width: 40px; height: 40px; background: rgba(255, 255, 255, 0.08); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-left: 10px;">
                        <i class="fas fa-check-circle" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div class="stat-value" style="color: white; font-size: 28px; font-weight: 800;">{{ $stats['paid_invoices'] }}</div>
                </div>
                <div class="stat-label" style="color: rgba(255, 255, 255, 0.9); font-size: 13px; font-weight: 500;">الفواتير المدفوعة</div>
            </div>
        </div>

        <!-- Outstanding Amount -->
        <div class="stat-card orange" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; padding: 20px; position: relative; overflow: hidden; border-radius: 16px; box-shadow: 0 8px 32px rgba(240, 147, 251, 0.3); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
            <div style="position: absolute; top: -15px; right: -15px; width: 60px; height: 60px; background: rgba(255, 255, 255, 0.15); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -10px; left: -10px; width: 40px; height: 40px; background: rgba(255, 255, 255, 0.08); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-left: 10px;">
                        <i class="fas fa-hourglass-half" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div class="stat-value" style="color: white; font-size: 24px; font-weight: 800;">{{ number_format($stats['outstanding_amount'], 0) }}</div>
                </div>
                <div class="stat-label" style="color: rgba(255, 255, 255, 0.9); font-size: 13px; font-weight: 500;">المبلغ المستحق (ريال)</div>
            </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="stat-card red" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); border: none; padding: 20px; position: relative; overflow: hidden; border-radius: 16px; box-shadow: 0 8px 32px rgba(235, 51, 73, 0.3); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
            <div style="position: absolute; top: -15px; right: -15px; width: 60px; height: 60px; background: rgba(255, 255, 255, 0.15); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -10px; left: -10px; width: 40px; height: 40px; background: rgba(255, 255, 255, 0.08); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="width: 36px; height: 36px; background: rgba(255, 255, 255, 0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-left: 10px;">
                        <i class="fas fa-exclamation-triangle" style="color: white; font-size: 16px;"></i>
                    </div>
                    <div class="stat-value" style="color: white; font-size: 28px; font-weight: 800;">{{ $stats['overdue_invoices'] }}</div>
                </div>
                <div class="stat-label" style="color: rgba(255, 255, 255, 0.9); font-size: 13px; font-weight: 500;">الفواتير المتأخرة</div>
            </div>
        </div>
    </div>

    <!-- Invoice Status Overview -->
    <div class="stats-grid">
        <!-- Status Distribution -->
        <div class="content-wrapper" style="margin-bottom: 0;">
            <h3 style="font-size: 18px; font-weight: 700; color: var(--t1); margin-bottom: 20px;">توزيع حالة الفواتير</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: rgba(148, 163, 184, 0.05); border-radius: 8px; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 12px; height: 12px; background: var(--t4); border-radius: 50%;"></div>
                        <span style="color: var(--t2); font-weight: 500;">مسودة</span>
                    </div>
                    <span style="font-weight: 600; color: var(--t1);">{{ $stats['draft_invoices'] }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: rgba(13, 110, 253, 0.05); border-radius: 8px; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 12px; height: 12px; background: var(--blue); border-radius: 50%;"></div>
                        <span style="color: var(--t2); font-weight: 500;">مرسلة</span>
                    </div>
                    <span style="font-weight: 600; color: var(--t1);">{{ $stats['sent_invoices'] }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: rgba(25, 135, 84, 0.05); border-radius: 8px; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 12px; height: 12px; background: var(--green); border-radius: 50%;"></div>
                        <span style="color: var(--t2); font-weight: 500;">مدفوعة</span>
                    </div>
                    <span style="font-weight: 600; color: var(--t1);">{{ $stats['paid_invoices'] }}</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: rgba(220, 53, 69, 0.05); border-radius: 8px; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 12px; height: 12px; background: var(--red); border-radius: 50%;"></div>
                        <span style="color: var(--t2); font-weight: 500;">متأخرة</span>
                    </div>
                    <span style="font-weight: 600; color: var(--t1);">{{ $stats['overdue_invoices'] }}</span>
                </div>
            </div>
        </div>

        <!-- Total Amount Summary -->
        <div class="content-wrapper" style="margin-bottom: 0;">
            <h3 style="font-size: 18px; font-weight: 700; color: var(--t1); margin-bottom: 20px;">ملخص المبالغ</h3>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: linear-gradient(135deg, rgba(13, 110, 253, 0.08) 0%, rgba(13, 110, 253, 0.02) 100%); border-radius: 16px; border: 1px solid rgba(13, 110, 253, 0.1); transition: all 0.3s ease;">
                    <div>
                        <p style="color: var(--blue); font-size: 14px; font-weight: 600; margin-bottom: 8px;">إجمالي المبلغ</p>
                        <p style="font-size: 28px; font-weight: 800; color: var(--blue);">{{ number_format($stats['total_amount'], 2) }} ريال</p>
                    </div>
                    <div style="width: 56px; height: 56px; background: rgba(13, 110, 253, 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-sack-dollar" style="color: var(--blue); font-size: 24px;"></i>
                    </div>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: linear-gradient(135deg, rgba(253, 126, 20, 0.08) 0%, rgba(253, 126, 20, 0.02) 100%); border-radius: 16px; border: 1px solid rgba(253, 126, 20, 0.1); transition: all 0.3s ease;">
                    <div>
                        <p style="color: var(--orange); font-size: 14px; font-weight: 600; margin-bottom: 8px;">المبلغ المستحق</p>
                        <p style="font-size: 28px; font-weight: 800; color: var(--orange);">{{ number_format($stats['outstanding_amount'], 2) }} ريال</p>
                    </div>
                    <div style="width: 56px; height: 56px; background: rgba(253, 126, 20, 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-hand-holding-dollar" style="color: var(--orange); font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="table-container">
        <div class="table-header">
            <h3 class="table-title">جميع الفواتير</h3>
            <div class="table-actions">
                <div style="position: relative;">
                    <input type="text" placeholder="البحث عن فاتورة..." style="padding: 10px 16px 10px 44px; border: 1.5px solid var(--bd); border-radius: 10px; font-size: 14px; width: 220px; transition: all 0.3s ease;">
                    <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--t4); font-size: 14px;"></i>
                </div>
                <select style="padding: 10px 16px; border: 1.5px solid var(--bd); border-radius: 10px; font-size: 14px; transition: all 0.3s ease;">
                    <option value="">جميع الحالات</option>
                    <option value="draft">مسودة</option>
                    <option value="pending">مرسلة</option>
                    <option value="paid">مدفوعة</option>
                    <option value="overdue">متأخرة</option>
                </select>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>العميل</th>
                    <th>العقار</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr style="transition: all 0.3s ease;">
                        <td>
                            <span style="font-size: 14px; font-weight: 700; color: var(--t1);">#{{ $invoice->id }}</span>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div style="width: 36px; height: 36px; background: var(--card2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 12px; border: 2px solid rgba(13, 110, 253, 0.1);">
                                    <i class="fas fa-user" style="color: var(--t4); font-size: 14px;"></i>
                                </div>
                                <div>
                                    <p style="font-size: 14px; font-weight: 600; color: var(--t1);">{{ $invoice->user_name ?? 'N/A' }}</p>
                                    <p style="font-size: 12px; color: var(--t4);">العميل</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p style="font-size: 14px; color: var(--t1); font-weight: 500;">{{ $invoice->property_title ?? 'N/A' }}</p>
                        </td>
                        <td>
                            <p style="font-size: 16px; font-weight: 700; color: var(--t1);">{{ number_format($invoice->total, 2) }} ريال</p>
                        </td>
                        <td>
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
                            <span style="padding: 8px 16px; font-size: 12px; font-weight: 600; border-radius: 25px; {{ $colorStyle }} transition: all 0.3s ease;">
                                {{ $text }}
                            </span>
                        </td>
                        <td style="font-size: 14px; color: var(--t4); font-weight: 500;">
                            {{ \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d') }}
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <a href="{{ route('admin.financial.invoices.show', $invoice->id) }}" style="color: var(--blue); background: rgba(13, 110, 253, 0.1); border: 1px solid rgba(13, 110, 253, 0.2); cursor: pointer; padding: 8px; text-decoration: none; border-radius: 8px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.financial.invoices.edit', $invoice->id) }}" style="color: var(--green); background: rgba(25, 135, 84, 0.1); border: 1px solid rgba(25, 135, 84, 0.2); cursor: pointer; padding: 8px; text-decoration: none; border-radius: 8px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="تحرير">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.financial.invoices.download', $invoice->id) }}" style="color: var(--purple); background: rgba(111, 66, 193, 0.1); border: 1px solid rgba(111, 66, 193, 0.2); cursor: pointer; padding: 8px; text-decoration: none; border-radius: 8px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="تحميل">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form action="{{ route('admin.financial.invoices.destroy', $invoice->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: var(--red); background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2); cursor: pointer; padding: 8px; border-radius: 8px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 100px 20px;">
                            <div style="display: flex; flex-direction: column; align-items: center;">
                                <div style="width: 80px; height: 80px; background: var(--card2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; border: 3px solid rgba(13, 110, 253, 0.1);">
                                    <i class="fas fa-file-invoice" style="color: var(--t4); font-size: 32px;"></i>
                                </div>
                                <p style="color: var(--t4); font-weight: 600; margin-bottom: 8px; font-size: 18px;">لا توجد فواتير حالياً</p>
                                <p style="color: var(--t4); font-size: 14px;">ابدأ بإنشاء فاتورة جديدة</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if(isset($invoices) && method_exists($invoices, 'hasPages') && $invoices->hasPages())
            <div style="padding: 24px; border-top: 1px solid var(--bd); background: var(--card2);">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
