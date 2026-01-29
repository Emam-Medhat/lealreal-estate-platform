@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">فريق المشروع: {{ $project->name }}</h1>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة للمشروع
                </a>
            </div>

            <!-- Team Information -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">معلومات الفريق</h5>
                    <button class="btn btn-sm btn-primary" onclick="editTeamInfo()">
                        <i class="fas fa-edit"></i> تعديل
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>اسم الفريق:</strong> {{ $team->name }}</p>
                            @if($team->description)
                                <p><strong>الوصف:</strong> {{ $team->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($team->teamLeader)
                                <p><strong>قائد الفريق:</strong> {{ $team->teamLeader->full_name }}</p>
                            @endif
                            <p><strong>الحالة:</strong> 
                                <span class="badge badge-{{ $team->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ $team->status == 'active' ? 'نشط' : 'غير نشط' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">أعضاء الفريق</h5>
                    <button class="btn btn-sm btn-primary" onclick="showAddMemberModal()">
                        <i class="fas fa-user-plus"></i> إضافة عضو
                    </button>
                </div>
                <div class="card-body">
                    @if($team->members && $team->members->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>العضو</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الدور</th>
                                        <th>تاريخ الانضمام</th>
                                        <th>الحالة</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($team->members as $member)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($member->user->full_name, 0, 1)) }}
                                                    </div>
                                                    {{ $member->user->full_name }}
                                                </div>
                                            </td>
                                            <td>{{ $member->user->email }}</td>
                                            <td>{{ $member->role->name ?? 'غير محدد' }}</td>
                                            <td>{{ $member->joined_at ? $member->joined_at->format('Y-m-d') : 'غير محدد' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $member->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ $member->status == 'active' ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" onclick="editMember({{ $member->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="removeMember({{ $member->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا يوجد أعضاء في الفريق</h5>
                            <p class="text-muted">اضغط على "إضافة عضو" لبدء إضافة أعضاء الفريق</p>
                            <button class="btn btn-primary" onclick="showAddMemberModal()">
                                <i class="fas fa-user-plus"></i> إضافة عضو
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة عضو جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMemberForm">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="team_id" value="{{ $team->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">المستخدم</label>
                        <select class="form-select" name="user_id" id="user_id" required>
                            <option value="">اختر المستخدم</option>
                            @foreach(\App\Models\User::where('account_status', 'active')->get() as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">الدور</label>
                        <input type="text" class="form-control" name="role" id="role" placeholder="مثال: مطور، مصمم، مدير" required>
                    </div>
                    <div class="mb-3">
                        <label for="hourly_rate" class="form-label">الساعة بالريال</label>
                        <input type="number" class="form-control" name="hourly_rate" id="hourly_rate" step="0.01" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة عضو</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showAddMemberModal() {
    $('#addMemberModal').modal('show');
}

function editTeamInfo() {
    // TODO: Implement team info editing
    alert('سيتم تطبيق تعديل معلومات الفريق قريباً');
}

function editMember(memberId) {
    // TODO: Implement member editing
    alert('سيتم تطبيق تعديل العضو قريباً');
}

function removeMember(memberId) {
    if (confirm('هل أنت متأكد من حذف هذا العضو؟')) {
        // TODO: Implement member removal
        alert('سيتم تطبيق حذف العضو قريباً');
    }
}

$('#addMemberForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '{{ route("projects.teams.store", $project) }}',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            $('#addMemberModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('حدث خطأ: ' + xhr.responseJSON.message);
        }
    });
});
</script>
@endpush
