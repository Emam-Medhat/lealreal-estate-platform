<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th style="width: 30%">العنوان</th>
            <td>{{ $schedule->title }}</td>
        </tr>
        <tr>
            <th>العقار</th>
            <td>{{ $schedule->property->title ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>نوع الصيانة</th>
            <td>
                @if($schedule->maintenance_type == 'preventive')
                    <span class="badge badge-info">وقائية</span>
                @elseif($schedule->maintenance_type == 'corrective')
                    <span class="badge badge-warning">تصحيحية</span>
                @else
                    <span class="badge badge-secondary">{{ $schedule->maintenance_type }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>الأولوية</th>
            <td>
                @if($schedule->priority == 'high')
                    <span class="badge badge-danger">عالية</span>
                @elseif($schedule->priority == 'medium')
                    <span class="badge badge-warning">متوسطة</span>
                @else
                    <span class="badge badge-success">منخفضة</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>الحالة</th>
            <td>
                @if($schedule->status == 'completed')
                    <span class="badge badge-success">مكتملة</span>
                @elseif($schedule->status == 'pending')
                    <span class="badge badge-warning">قيد الانتظار</span>
                @elseif($schedule->status == 'in_progress')
                    <span class="badge badge-info">جاري التنفيذ</span>
                @elseif($schedule->status == 'cancelled')
                    <span class="badge badge-danger">ملغاة</span>
                @else
                    <span class="badge badge-secondary">{{ $schedule->status }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>تاريخ الجدولة</th>
            <td>{{ $schedule->scheduled_date->format('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <th>الفريق المكلف</th>
            <td>{{ $schedule->maintenanceTeam->name ?? 'غير محدد' }}</td>
        </tr>
        <tr>
            <th>مزود الخدمة</th>
            <td>{{ $schedule->serviceProvider->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>الوصف</th>
            <td>{{ $schedule->description ?? 'لا يوجد وصف' }}</td>
        </tr>
        @if($schedule->assignedBy)
        <tr>
            <th>تم التعيين بواسطة</th>
            <td>{{ $schedule->assignedBy->name }}</td>
        </tr>
        @endif
        @if($schedule->cancellation_reason)
        <tr>
            <th class="text-danger">سبب الإلغاء</th>
            <td class="text-danger">{{ $schedule->cancellation_reason }}</td>
        </tr>
        @endif
    </table>
</div>

<div class="mt-3 text-left">
    <a href="{{ route('maintenance.schedule.edit', $schedule->id) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i> تعديل
    </a>
    @if($schedule->status != 'cancelled' && $schedule->status != 'completed')
        <form action="{{ route('maintenance.schedule.cancel', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إلغاء هذه الجدولة؟')">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="fas fa-times"></i> إلغاء
            </button>
        </form>
    @endif
</div>