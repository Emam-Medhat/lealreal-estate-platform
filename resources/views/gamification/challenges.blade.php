@extends('layouts.app')

@section('title', 'التحديات العقارية - الألعاب')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-tasks text-info me-2"></i>
            التحديات العقارية
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="createChallenge()">
                <i class="fas fa-plus me-1"></i>
                إنشاء تحدي
            </button>
            <button class="btn btn-success" onclick="refreshChallenges()">
                <i class="fas fa-sync-alt me-1"></i>
                تحديث
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="challenge-filters">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" class="form-control" id="search-challenges" placeholder="ابحث عن تحديات...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">النوع</label>
                        <select class="form-control" id="filter-type" onchange="filterChallenges()">
                            <option value="">الكل الأنواع</option>
                            <option value="points">نقاط</option>
                            <option value="level">مستوى</option>
                            <option value="badges">شارات</option>
                            <option value="property">عقار</option>
                            <option value="social">اجتماعي</option>
                            <option value="custom">مخصص</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الصعوبة</label>
                        <select class="form-control" id="filter-difficulty" onchange="filterChallenges()">
                            <option value="">كل الصعوبات</option>
                            <option value="easy">سهل</option>
                            <option value="medium">متوسط</option>
                            <option value="hard">صعب</option>
                            <option value="expert">خبير</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select class="form-control" id="filter-status" onchange="filterChallenges()">
                            <option value="">كل الحالات</option>
                            <option value="active">نشط</option>
                            <option value="completed">مكتمل</option>
                            <option value="expired">منتهي</option>
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

    <!-- Active Challenges Grid -->
    <div class="row" id="challenges-grid">
        <!-- Challenge cards will be loaded here -->
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-12">
            <nav aria-label="Challenges pagination">
                <ul class="pagination justify-content-center" id="challenges-pagination">
                    <!-- Pagination will be loaded here -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Challenge Details Modal -->
<div class="modal fade" id="challengeModal" tabindex="-1" aria-labelledby="challengeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="challengeModalLabel">تفاصيل التحدي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="challenge-details">
                    <!-- Challenge details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" id="join-challenge-btn">انضم للتحدي</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Challenge Modal -->
<div class="modal fade" id="createChallengeModal" tabindex="-1" aria-labelledby="createChallengeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createChallengeModalLabel">إنشاء تحدي جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="create-challenge-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="challenge-title" class="form-label">عنوان التحدي</label>
                                <input type="text" class="form-control" id="challenge-title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="challenge-type" class="form-label">النوع</label>
                                <select class="form-control" id="challenge-type" required>
                                    <option value="points">نقاط</option>
                                    <option value="level">مستوى</option>
                                    <option value="badges">شارات</option>
                                    <option value="property">عقار</option>
                                    <option value="social">اجتماعي</option>
                                    <option value="custom">مخصص</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="challenge-difficulty" class="form-label">الصعوبة</label>
                                <select class="form-control" id="challenge-difficulty" required>
                                    <option value="easy">سهل</option>
                                    <option value="medium">متوسط</option>
                                    <option value="hard">صعب</option>
                                    <option value="expert">خبير</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="challenge-status" class="form-label">الحالة</label>
                                <select class="form-control" id="challenge-status" required>
                                    <option value="draft">مسودة</option>
                                    <option value="active">نشط</option>
                                    <option value="completed">مكتمل</option>
                                    <option value="expired">منتهي</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start-date" class="form-label">تاريخ البدء</label>
                                <input type="datetime-local" class="form-control" id="start-date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end-date" class="form-label">تاريخ الانتهاء</label>
                                <input type="datetime-local" class="form-control" id="end-date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="max-participants" class="form-label">الحد الأقصى للمشاركين</label>
                                <input type="number" class="form-control" id="max-participants" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="points-reward" class="form-label">مكافأة النقاط</label>
                                <input type="number" class="form-control" id="points-reward" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="badge-reward" class="form-label">مكافأة الشارة</label>
                                <select class="form-control" id="badge-reward">
                                    <option value="">لا يوجد</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="challenge-description" class="form-label">وصف التحدي</label>
                                <textarea class="form-control" id="challenge-description" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="challenge-requirements" class="form-label">المتطلبات</label>
                                <textarea class="form-control" id="challenge-requirements" rows="3" placeholder="أدخل المتطلبات كـ JSON"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="saveChallenge()">حفظ التحدي</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let currentFilters = {
    search: '',
    type: '',
    difficulty: '',
    status: ''
};

function createChallenge() {
    new bootstrap.Modal(document.getElementById('createChallengeModal')).show();
}

function refreshChallenges() {
    loadChallenges();
}

function clearFilters() {
    document.getElementById('search-challenges').value = '';
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-difficulty').value = '';
    document.getElementById('filter-status').value = '';
    
    currentFilters = {
        search: '',
        type: '',
        difficulty: '',
        status: ''
    };
    
    currentPage = 1;
    loadChallenges();
}

function filterChallenges() {
    currentFilters.search = document.getElementById('search-challenges').value;
    currentFilters.type = document.getElementById('filter-type').value;
    currentFilters.difficulty = document.getElementById('filter-difficulty').value;
    currentFilters.status = document.getElementById('filter-status').value;
    
    currentPage = 1;
    loadChallenges();
}

function loadChallenges() {
    const params = new URLSearchParams({
        page: currentPage,
        search: currentFilters.search,
        type: currentFilters.type,
        difficulty: currentFilters.difficulty,
        status: currentFilters.status
    });
    
    fetch(`/gamification/challenges?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderChallenges(data.challenges);
                renderPagination(data.pagination);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading challenges:', error);
            showNotification('حدث خطأ ما أثناء تحميل التحديات', 'error');
        });
}

function renderChallenges(challenges) {
    const grid = document.getElementById('challenges-grid');
    grid.innerHTML = '';
    
    if (challenges && challenges.length > 0) {
        challenges.forEach(challenge => {
            const card = createChallengeCard(challenge);
            grid.appendChild(card);
        });
    } else {
        grid.innerHTML = `
            <div class="col-12 text-center text-muted">
                <i class="fas fa-tasks fa-3x mb-3"></i>
                <h4>لا توجد تحديات متاحة</h4>
                <p>قم بإنشاء تحدي جديد لبدء الألعاب</p>
            </div>
        `;
    }
}

function createChallengeCard(challenge) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4 mb-4';
    
    const statusColor = getStatusColor(challenge.status);
    const difficultyColor = getDifficultyColor(challenge.difficulty);
    const statusIcon = getStatusIcon(challenge.status);
    const difficultyIcon = getDifficultyIcon(challenge.difficulty);
    
    col.innerHTML = `
        <div class="card h-100 challenge-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <span class="badge bg-${difficultyColor} me-2">${challenge.difficulty_label}</span>
                    <span class="badge bg-${statusColor}">${challenge.status_label}</span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="editChallenge(${challenge.id})">تعديل</a></li>
                        <li><a class="dropdown-item" href="#" onclick="deleteChallenge(${challenge.id})">حذف</a></li>
                        <li><a class="dropdown-item" href="#" onclick="duplicateChallenge(${challenge.id})">نسخ</a></li>
                    </ul>
                </div>
            </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5 class="card-title">${challenge.title}</h5>
                    <p class="card-text text-muted">${challenge.description}</p>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <i class="${difficultyIcon} text-${difficultyColor} me-2"></i>
                        <small class="text-muted">الصعوبة: ${challenge.difficulty_label}</small>
                    </div>
                    <div>
                        <i class="${statusIcon} text-${statusColor} me-2"></i>
                        <small class="text-muted">الحالة: ${challenge.status_label}</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">مكافأة النقاط</small>
                        <strong>${challenge.points_reward}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">المشاركون</small>
                        <strong>${challenge.participants_count} / ${challenge.max_participants || '∞'}</strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">يوم متبقي</small>
                        <strong>${challenge.days_remaining}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">تاريخ البدء</small>
                        <strong>${new Date(challenge.start_date).toLocaleDateString('ar-SA')}</strong>
                    </div>
                </div>
                <div class="progress mb-2" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: ${challenge.completion_rate}%">
                        <span class="sr-only">${challenge.completion_rate}% مكتمل</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    ${challenge.can_join ? 
                        `<button class="btn btn-primary btn-sm" onclick="showChallengeDetails(${challenge.id})">عرض التفاصيل</button>` :
                        `<button class="btn btn-secondary btn-sm" disabled>غير متاح للانضمام</button>`
                    }
                    ${challenge.is_expired ? 
                        `<span class="badge bg-danger">منتهي</span>` :
                        challenge.is_full ? 
                        `<span class="badge bg-warning">ممتمل</span>` :
                        `<span class="badge bg-success">متاح للانضمام</span>`
                    }
                </div>
            </div>
        </div>
    `;
    
    return col;
}

function getStatusColor(status) {
    const colors = {
        'active': 'success',
        'completed': 'info',
        'expired': 'danger',
        'draft': 'secondary'
    };
    return colors[status] || 'secondary';
}

function getStatusIcon(status) {
    const icons = {
        'active': 'fa-play-circle',
        'completed': 'fa-check-circle',
        'expired': 'fa-times-circle',
        'draft': 'fa-edit'
    };
    return icons[status] || 'fa-circle';
}

function getDifficultyColor(difficulty) {
    const colors = {
        'easy': 'success',
        'medium': 'warning',
        'hard': 'danger',
        'expert': 'dark'
    };
    return colors[difficulty] || 'secondary';
}

function getDifficultyIcon(difficulty) {
    const icons = {
        'easy': 'fa-star',
        'medium': 'fa-star-half-alt',
        'hard': 'fa-star',
        'expert' 'fa-crown'
    };
    return icons[difficulty] || 'fa-star';
}

function showChallengeDetails(challengeId) {
    fetch(`/gamification/challenges/${challengeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderChallengeDetails(data.challenge);
                new bootstrap.Modal(document.getElementById('challengeModal')).show();
                
                const joinBtn = document.getElementById('join-challenge-btn');
                if (data.challenge.can_join) {
                    joinBtn.textContent = 'انضم للتحدي';
                    joinBtn.onclick = () => joinChallenge(challengeId);
                    joinBtn.className = 'btn btn-primary';
                } else {
                    joinBtn.textContent = 'غير متاح للانضمام';
                    joinBtn.disabled = true;
                    joinBtn.className = 'btn btn-secondary';
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading challenge details:', error);
            showNotification('حدث خطأ ما أثناء تحميل تفاصيل التحدي', 'error');
        });
}

function renderChallengeDetails(challenge) {
    const details = document.getElementById('challenge-details');
    
    details.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>معلومات أساسية</h6>
                <table class="table table-sm">
                    <tr><td>العنوان:</td><td>${challenge.title}</td></tr>
                    <tr><td>النوع:</td><td>${challenge.type_label}</td></tr>
                    <tr><td>الصعوبة:</td><td><span class="badge bg-${getDifficultyColor(challenge.difficulty)}">${challenge.difficulty_label}</span></td></tr>
                    <tr><td>الحالة:</td><span class="badge bg-${getStatusColor(challenge.status)}">${challenge.status_label}</span></td></tr>
                    <tr><td>المشاركون:</td><td>${challenge.participants_count} / ${challenge.max_participants || '∞'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>الجدول الزمني</h6>
                <table class="table table-sm">
                    <tr><td>تاريخ البدء:</td><td>${new Date(challenge.start_date).toLocaleDateString('ar-SA')}</td></tr>
                    <tr><td>تاريخ الانتهاء:</td><td>${new Date(challenge.end_date).toLocaleDateString('tar-SA')}</td></tr>
                    <tr><td>المدة:</td><td>${challenge.duration_in_days} يوم</td></tr>
                    <tr><td>الوقت المتبقي:</td><td>${challenge.days_remaining} يوم</td></tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h6>المكافآت</h6>
                <table class="table table-sm">
                    <tr><td>نقاط:</td><td><strong>${challenge.points_reward}</strong></td></tr>
                    <tr><td>شارة:</td><td>${challenge.badge_reward ? challenge.badge_reward.name : 'لا يوجد'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>الإحصائيات</h6>
                <table class="table table-sm">
                    <tr><td>معدد المشاركين:</td><td>${challenge.participants_count}</td></tr>
                    <tr><td>معدد المكتملين:</td><td>${challenge.completed_count}</td></tr>
                    <tr><td>معدد المنسحبين:</td><td>${challenge.abandoned_count}</td></tr>
                    <tr><td>معدل التسجيل:</td><td>${challenge.abandoned_count}</td></tr>
                    <tr><td>معدد المنضمام:</td><td>${challenge.participants_count - challenge.abandoned_count}</td></tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h6>الوصف</h6>
                <p>${challenge.description}</p>
            </div>
        </div>
    `;
}

function joinChallenge(challengeId) {
    fetch('/gamification/join-challenge', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            challenge_id: challengeId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم الانضمام للتحدي بنجاح', 'success');
            new bootstrap.Modal.getInstance(document.getElementById('challengeModal')).hide();
            loadChallenges();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ ما أثناء الانضمام للتحدي', 'error');
    });
}

function editChallenge(challengeId) {
    // Load challenge data into form
    fetch(`/gamification/challenges/${challengeId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const challenge = data.challenge;
                document.getElementById('challenge-title').value = challenge.title;
                document.getElementById('challenge-type').value = challenge.type;
                document.getElementById('challenge-difficulty').value = challenge.difficulty;
                document.getElementById('challenge-status').value = challenge.status;
                document.getElementById('start-date').value = challenge.start_date;
                document.getElementById('end-date').value = challenge.end_date;
                document.getElementById('max-participants').value = challenge.max_participants;
                document.getElementById('points-reward').value = challenge.points_reward;
                document.getElementById('badge-reward').value = challenge.badge_reward || '';
                document.getElementById('challenge-description').value = challenge.description;
                document.getElementById('challenge-requirements').value = JSON.stringify(challenge.requirements || {});
                
                new bootstrap.Modal(document.getElementById('createChallengeModal')).show();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('حدث خطأ ما أثناء تحميل بيانات التحدي', 'error');
        });
}

function deleteChallenge(challengeId) {
    if (confirm('هل أنت متأكد من حذف هذا التحدي؟')) {
        fetch(`/gamification/challenges/${challengeId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('تم حذف التحدي بنجاح', 'success');
                loadChallenges();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('حدث خطأ ما أثناء حذف التحدي', 'error');
        });
    }
}

function duplicateChallenge(challengeId) {
    fetch(`/gamification/challenges/${challengeId}/duplicate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم نسخ التحدي بنجاح', 'success');
            loadChallenges();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ ما أثناء نسخ التحدي', 'error');
    });
}

function saveChallenge() {
    const formData = {
        title: document.getElementById('challenge-title').value,
        type: document.getElementById('challenge-type').value,
        difficulty: document.getElementById('challenge-difficulty').value,
        status: document.getElementById('challenge-status').value,
        start_date: document.getElementById('start-date').value,
        end_date: document.getElementById('end-date').value,
        max_participants: document.getElementById('max-participants').value,
        points_reward: document.getElementById('points-reward').value,
        badge_reward: document.getElementById('badge-reward').value,
        description: document.getElementById('challenge-description').value,
        requirements: document.getElementById('challenge-requirements').value
    };
    
    fetch('/gamification/challenges', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم حفظ التحدي بنجاح', 'success');
            new bootstrap.Modal.getInstance(document.getElementById('createChallengeModal')).hide();
            loadChallenges();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ ما أثناء حفظ التحدي', 'error');
    });
}

function renderPagination(pagination) {
    const nav = document.getElementById('challenges-pagination');
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
    loadChallenges();
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
    loadChallenges();
});
</script>

<style>
.challenge-card {
    transition: transform 0.2s ease-in-out;
}

.challenge-card:hover {
    transform: translateY(-5px);
}

.challenge-card .card-header {
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
