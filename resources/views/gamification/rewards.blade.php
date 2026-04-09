@extends('layouts.app')

@section('title', 'المكافآت العقارية - الألعاب')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-gift text-warning me-2"></i>
            المكافآت العقارية
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="createReward()">
                <i class="fas fa-plus me-1"></i>
                إنشاء مكافأة
            </button>
            <button class="btn btn-success" onclick="refreshRewards()">
                <i class="fas fa-sync-alt me-1"></i>
                تحديث
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="reward-filters">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" class="form-control" id="search-rewards" placeholder="ابحث عن مكافآت...">
                    </div>
                    <div class="col-md-2">
                        <label class="pleform-label">النوع</label>
                        <select class="form-control" id="filter-type" onchange="filterRewards()">
                            <option value="">كل الأنواع</option>
                            <option value="points">نقاط</option>
                            <option value="badge">شارة</option>
                            <option value="discount">خصم</option>
                            <option value="product">منتج</option>
                            <option value="service">خدمة</option>
                            <option value="custom">مخصص</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الفئة</label>
                        <select class="form-control" id="filter-category" onchange="filterRewards()">
                            <option value="">كل الفئات</option>
                            <option value="electronics">إلكترونيات</option>
                            <option value="vouchers">قسائم شرائية</option>
                            <option value="experiences">تجارب</option>
                            <option value="merchandise">بضائع</option>
                            <option value="digital">رقمية</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select class="form-control" id="filter-status" onchange="filterRewards()">
                            <option value="">كل الحالات</option>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                            <option value="limited">محدود</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                            <i class="fas fa-times"></i>
                            مسح
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Rewards Grid -->
    <div class="row" id="rewards-grid">
        @if($availableRewards && count($availableRewards) > 0)
            @foreach($availableRewards as $reward)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 reward-card" data-reward-id="{{ $reward['id'] }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="reward-icon">
                                    <i class="{{ $reward['icon'] ?? 'fas fa-gift' }} fa-2x text-primary"></i>
                                </div>
                                <span class="badge bg-{{ $reward['is_active'] ? 'success' : 'secondary' }}">
                                    {{ $reward['is_active'] ? 'نشط' : 'غير نشط' }}
                                </span>
                            </div>
                            
                            <h5 class="card-title">{{ $reward['name'] ?? 'مكافأة' }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($reward['description'] ?? '', 80) }}</p>
                            
                            <div class="reward-meta">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-info">
                                        <i class="fas fa-coins me-1"></i>
                                        {{ $reward['points_required'] ?? 0 }} نقطة
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        {{ $reward['category'] ?? 'عام' }}
                                    </span>
                                </div>
                                
                                @if($reward['reward_value'] && $reward['reward_value'] > 0)
                                    <div class="text-success fw-bold">
                                        {{ $reward['reward_type'] == 'discount' ? 'خصم ' : 'قيمة ' }} 
                                        {{ number_format($reward['reward_value'], 2) }}
                                        {{ $reward['reward_type'] == 'discount' ? '%' : 'ريال' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-primary btn-sm w-100" onclick="showRewardDetails({{ $reward['id'] }})">
                                <i class="fas fa-eye me-1"></i>
                                عرض التفاصيل
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">لا توجد مكافآت متاحة حالياً</h4>
                    <p class="text-muted">سيتم إضافة مكافآت جديدة قريباً</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-12">
            <nav aria-label="Rewards pagination">
                <ul class="pagination justify-content-center" id="rewards-pagination">
                    <!-- Pagination will be loaded here -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- User Points Summary -->
    <div class="row mt-4" id="user-points-summary" style="display: none;">
        <div class="col-12">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">نقاطك الحالية</h5>
                    <h2 class="mb-0" id="user-total-points">0</h2>
                    <p class="mb-0">نقطة</p>
                    <button class="btn btn-light" onclick="showAvailableRewards()">
                        عرض المكافآت المتاحة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reward Details Modal -->
<div class="modal fade" id="rewardModal" tabindex="-1" aria-labelledby="rewardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rewardModalLabel">تفاصيل المكافأة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reward-details">
                    <!-- Reward details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button class="btn btn-primary" id="claim-reward-btn">استبدال المكافأة</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Reward Modal -->
<div class="modal fade" id="createRewardModal" tabindex="-1" aria-labelledby="createRewardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRewardModalLabel">إنشاء مكافأة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="create-reward-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-name" class="form-label">اسم المكافأة</label>
                                <input type="text" class="form-control" id="reward-name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-type" class="form-label">النوع</label>
                                <select class="form-control" id="reward-type" required>
                                    <option value="points">نقاط</option>
                                    <option value="badge">شارة</option>
                                    <option value="discount">خصم</option>
                                    <option value="product">منتج</option>
                                    <option value="service">خدمة</option>
                                    <option value="custom">مخصص</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-category" class="form-label">الفئة</label>
                                <input type="text" class="form-control" id="reward-category" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-points-cost" class="form-label">تكلفة النقاط</label>
                                <input type="number" class="form-control" id="reward-points-cost" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-value" class="form-label">القيمة</label>
                                <input type="number" class="form-control" id="reward-value" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-stock-quantity" class="form-label">الكمية المتوفرة</label>
                                <input type="number" class="form-control" id="reward-stock-quantity" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-max-redemptions-per-user" class="form-label">الحد الأقصى للمستخدم</label>
                                <input type="number" class="form-control" id="reward-max-redemptions-per-user" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-expiry-date" class="form-label">تاريخ الانتهاء</label>
                                <input type="datetime-local" class="form-control" id="reward-expiry-date">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="reward-description" class="form-label">الوصف المكافأة</label>
                                <textarea class="form-control" id="reward-description" rows="4" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-terms-conditions" class="form-label">الشروط والأحكام</label>
                                <textarea class="form-control" id="reward-terms-conditions" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reward-redemption-instructions" class="form-label">تعليمات الاستبدال</label>
                                <textarea class="form-control" id="reward-redemption-instructions" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="saveReward()">حفظ المكافأة</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let currentFilters = {
    search: '',
    type: '',
    category: '',
    status: ''
};

function createReward() {
    new bootstrap.Modal(document.getElementById('createRewardModal')).show();
}

function refreshRewards() {
    loadRewards();
}

function clearFilters() {
    document.getElementById('search-rewards').value = '';
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-category').value = '';
    document.getElementById('filter-status').value = '';
    
    currentFilters = {
        search: '',
        type: '',
        category: '',
        status: ''
    };
    
    currentPage = 1;
    loadRewards();
}

function filterRewards() {
    currentFilters.search = document.getElementById('search-rewards').value;
    currentFilters.type = document.getElementById('filter-type').value;
    currentFilters.category = document.getElementById('filter-category').value;
    currentFilters.status = document.getElementById('filter-status').value;
    
    currentPage = 1;
    loadRewards();
}

function loadRewards() {
    const params = new URLSearchParams({
        page: currentPage,
        search: currentFilters.search,
        type: currentFilters.type,
        category: currentFilters.category,
        status: currentFilters.status
    });
    
    fetch(`/gamification/rewards?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderRewards(data.rewards);
                renderPagination(data.pagination);
                updateUserPointsSummary();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading rewards:', error);
            showNotification('حدث خطأ ما أثناء تحميل المكافآت', 'error');
        });
}

function renderRewards(rewards) {
    const grid = document.getElementById('rewards-grid');
    grid.innerHTML = '';
    
    if (rewards && rewards.length > 0) {
        rewards.forEach(reward => {
            const card = createRewardCard(reward);
            grid.appendChild(card);
        });
    } else {
        grid.innerHTML = `
            <div class="col-12 text-center text-muted">
                <i class="fas fa-gift fa-3x mb-3"></i>
                <h4>لا توجد مكافآت متاحة</h4>
                <p>قم بإنشاء مكافأة جديدة لبدء الألعاب</p>
            </div>
        `;
    }
}

function createRewardCard(reward) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4 mb-4';
    
    const typeColor = getTypeColor(reward.type);
    const statusColor = getStatusColor(reward.status);
    const statusIcon = getStatusIcon(reward.status);
    
    col.innerHTML = `
        <div class="card h-100 reward-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-${typeColor} me-2">${reward.type_label}</span>
                        <span class="badge bg-${statusColor}">${reward.status_label}</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="editReward(${reward.id})">تعديل</a></li>
                            <li><a class="dropdown-item" href="#" onclick="duplicateReward(${reward.id})">نسخ</a></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteReward(${reward.id})">حذف</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5 class="card-title">${reward.name}</h5>
                    <p class="card-text text-muted">${reward.description}</p>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <i class="fas fa-coins text-warning me-2"></i>
                        <strong>${reward.formatted_points_cost}</strong>
                    </div>
                    <div>
                        <span class="badge bg-${typeColor}">${reward.type_label}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">القيمة:</small>
                        <strong>${reward.formatted_value}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">المتوفرة:</small>
                        <strong>${reward.formatted_stock}</strong>
                    </div>
                </div>
                <div class="progress mb-2" style="height: 8px;">
                    <div class="progress-bar bg-${reward.can_redeem ? 'success' : 'secondary'}" style="width: ${reward.redeem_percentage}%">
                        <span class="sr-only">${reward.redeem_percentage}% متاح للاستبدال</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    ${reward.can_redeem ? 
                        `<button class="btn btn-success btn-sm" onclick="showRewardDetails(${reward.id})">عرض التفاصيل</button>` :
                        `<button class="btn btn-secondary btn-sm" disabled>غير متاح</button>`
                    }
                    ${reward.is_expired ? 
                        `<span class="badge bg-danger">منتهي</span>` :
                        reward.is_out_of_stock ? 
                        `<span class="badge bg-warning">نفد من المخزون</span>` :
                        `<span class="badge bg-success">متاح</span>`
                    }
                </div>
            </div>
        </div>
    `;
    
    return col;
}

function getTypeColor(type) {
    const colors = {
        'points': 'primary',
        'badge': 'warning',
        'discount': 'success',
        'product': 'info',
        'service': 'secondary',
        'custom': 'dark'
    };
    return colors[type] || 'secondary';
}

function getStatusColor(status) {
    const colors = {
        'active': 'success',
        'inactive': 'secondary',
        'limited' => 'warning'
    };
    return colors[status] || 'secondary';
}

function getStatusIcon(status) {
    const icons = {
        'active': 'fa-check-circle',
        'inactive': 'fa-times-circle',
        'limited': 'fa-exclamation-triangle'
    };
    return icons[status] || 'fa-circle';
}

function showRewardDetails(rewardId) {
    fetch(`/gamification/rewards/${rewardId}`)
        .then(response => response => response.json())
        .then(data => {
            if (data.success) {
                renderRewardDetails(data.reward);
                new bootstrap.Modal(document.getElementById('rewardModal')).show();
                
                const claimBtn = document.getElementById('claim-reward-btn');
                if (data.reward.can_redeem) {
                    claimBtn.textContent = 'استبدال المكافأة';
                    claimBtn.className = 'btn btn-primary';
                } else {
                    claimBtn.textContent = 'غير متاح';
                    claimBtn.disabled = true;
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading reward details:', error);
            showNotification('حدث خطأ ما أثناء تحميل تفاصيل المكافأة', 'error');
        });
}

function renderRewardDetails(reward) {
    const details = document.getElementById('reward-details');
    
    details.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>معلومات أساسية</h6>
                <table class="table table-sm">
                    <tr><td>الاسم:</td><td>${reward.name}</td></tr>
                    <tr><td>النوع:</td><span class="badge bg-${getTypeColor(reward.type)}">${reward.type_label}</span></td></tr>
                    <tr><td>الفئة:</td>${reward.category}</td></tr>
                    <tr><td>الحالة:</td><span class="badge bg-${getStatusColor(reward.status)}">${reward.status_label}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>التفاصيل</h6>
                <p>${reward.description}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h6>المتسعير</h6>
                <table class="table table-sm">
                    <tr><td>تكلفة النقاط:</td><strong>${reward.points_cost}</strong></td></tr>
                    <tr><td>القيمة:</td><strong>${reward.value}</strong></td></tr>
                    <tr><td>الكمية:</td><strong>${reward.stock_quantity || 'غير محدود'}</strong></td></tr>
                    <tr><td>الحد الأقصى للمستخدم:</td><strong>${reward.max_redemptions_per_user || 'غير محدود'}</strong></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>الشروط والأحكام</h6>
                <p>${reward.terms_conditions || 'لا توجد شروط محددة'}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h6>تعليمات الاستبدال</h6>
                <p>${reward.redemption_instructions || 'لا توجد تعليمات محددة'}</p>
            </div>
        </div>
    `;
}

function editReward(rewardId) {
    // Load reward data into form
    fetch(`/gamification/rewards/${rewardId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const reward = data.reward;
                document.getElementById('reward-name').value = reward.name;
                document.getElementById('reward-type').value = reward.type;
                document.getElementById('reward-category').value = reward.category;
                document.getElementById('reward-status').value = reward.status;
                document.getElementById('reward-points-cost').value = reward.points_cost;
                document.getElementById('reward-value').value = reward.value;
                document.getElementById('reward-stock-quantity').value = reward.stock_quantity;
                document.getElementById('reward-max-redemptions-per-user').value = reward.max_redemptions_per_user;
                document.getElementById('reward-expiry-date').value = reward.expiry_date ? new Date(reward.expiry_date).toISOString().slice(0, 16) : '';
                document.getElementById('reward-description').value = reward.description;
                document.getElementById('reward-terms-conditions').value = reward.terms_conditions || '';
                document.getElementById('reward-redemption-instructions').value = reward.redemption_instructions || '';
                
                new bootstrap.Modal(document.getElementById('createRewardModal')).show();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading reward data:', error);
            showNotification('حدث خطأ ما أثناء تحميل بيانات المكافأة', 'error');
        });
}

function deleteReward(rewardId) {
    if (confirm('هل أنت متأكد من حذف هذه المكافأة؟')) {
        fetch(`/gamification/rewards/${rewardId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('تم حذف المكافأة بنجاح', 'success');
                loadRewards();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('حدث خطأ ما أثناء حذف المكافأة', 'error');
        });
    }

function duplicateReward(rewardId) {
    fetch(`/gamification/rewards/${rewardId}/duplicate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('تم نسخ المكافأة بنجاح', 'success');
                loadRewards();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('حدث خطأ ما أثناء نسخ المكافأة', 'error');
        });
}

function saveReward() {
    const formData = {
        name: document.getElementById('reward-name').value,
        type: document.getElementById('reward-type').value,
        category: document.getElementById('reward-category').value,
        points_cost: document.getElementById('reward-points-cost').value,
        value: document.getElementById('reward-value').value,
        stock_quantity: document.getElementById('reward-stock-quantity').value,
        max_redemptions_per_user: document.getElementById('reward-max-redemptions-per-user').value,
        expiry_date: document.getElementById('reward-expiry-date').value,
        status: document.getElementById('reward-status').value,
        description: document.getElementById('reward-description').value,
        terms_conditions: document.getElementById('reward-terms-conditions').value,
        redemption_instructions: document.getElementById('reward-redemption-instructions').value
    };
    
    fetch('/gamification/rewards', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('تم حفظ المكافأة بنجاح', 'success');
                new bootstrap.Modal.getInstance(document.getElementById('createRewardModal')).hide();
                loadRewards();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('حدث خطأ ما أثناء حفظ المكافأة', 'error');
        });
}

function claimReward(rewardId) {
    fetch('/gamification/claim-reward', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TONE': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reward_id: rewardId
        })
    })
    .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('تم استبدال المكافأة بنجاح', 'success');
                new bootstrap.Modal.getInstance(document.getElementById('rewardModal')).hide();
                loadRewards();
                updateUserPointsSummary();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('حدث خطأ أثناء استبدال المكافأة', 'error');
        });
}

function showAvailableRewards() {
    document.getElementById('user-points-summary').style.display = 'block';
}

function updateUserPointsSummary() {
    fetch('/gamification/user-points-summary')
        .then(response => response => {
            if (response.success) {
                document.getElementById('user-total-points').textContent = response.total_points;
                document.getElementById('user-points-summary').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading user points summary:', error);
        });
}

function renderPagination(pagination) {
    const nav = document.getElementById('rewards-pagination');
    nav.innerHTML = '';
    
    if (pagination.total_pages > 1) {
        let paginationHTML = '';
        
        // Previous button
        paginationHTML += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${pagination.current_page - 1})" ${pagination.current_page === 1 ? 'tabindex="-1"' : ''}>
                    السابق
                </a>
            </li>
        `;
        
        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.total_pages, pagination.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                paginationHTML += `
                    <li class="page-item active">
                        <span class="page-link">${i}</span>
                    </li>
                `;
            } else {
                paginationHTML += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="goToPage(${i})">${i}</a>
                    </li>
                `;
            }
        }
        
        // Next button
        paginationHTML += `
            <li class="page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${pagination.current_page + 1})" ${pagination.current_page === pagination.total_pages ? 'tabindex="-1"' : ''}>
                    التالي
                </a>
            </li>
        `;
        
        nav.innerHTML = paginationHTML;
    }
}

function goToPage(page) {
    currentPage = page;
    loadRewards();
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Load initial data
document.addEventListener('DOMContentLoaded', function() {
    // Only load via AJAX when filtering or searching, not on initial page load
    updateUserPointsSummary();
});
</script>

<style>
.reward-card {
    transition: transform 0.2s ease-in-out;
}

.reward-card:hover {
    transform: translateY(-5px);
}

.reward-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.badge {
    font-size: 0.75rem;
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    transition: width 0.3s ease;
}

.table-sm td {
    padding: 0.5rem;
}

.table-sm th {
    border-top: none;
    border-bottom: 1px solid #dee2e6;
}
</style>
@endsection
