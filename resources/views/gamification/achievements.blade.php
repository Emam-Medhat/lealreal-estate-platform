@extends('layouts.app')

@section('title', 'الإنجازات العقارية - الألعاب')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-trophy text-warning me-2"></i>
            إنجازات العقارية
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="refreshAchievements()">
                <i class="fas fa-sync-alt me-1"></i>
                تحديث
            </button>
            <button class="btn btn-info" onclick="exportAchievements()">
                <i class="fas fa-download me-1"></i>
                تصدير
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="achievement-filters">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" class="form-control" id="search-achievements" placeholder="البحث عن إنجازات...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">النوع</label>
                        <select class="form-control" id="filter-type" onchange="filterAchievements()">
                            <option value="">كل الأنواع</option>
                            <option value="points">نقاط</option>
                            <option value="level">مستوى</option>
                            <option value="badges">شارات</option>
                            <option value="challenges">تحديات</option>
                            <option value="quests">مهام</option>
                            <option value="social">اجتماعي</option>
                            <option value="custom">مخصص</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الصعوبة</label>
                        <select class="form-control" id="filter-difficulty" onchange="filterAchievements()">
                            <option value="">كل الصعوبات</option>
                            <option value="easy">سهل</option>
                            <option value="medium">متوسط</option>
                            <option value="hard">صعب</option>
                            <option value="expert'>خبير</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select class="form-control" id="filter-status" onchange="filterAchievements()">
                            <option value="">كل الحالات</option>
                            <option value="unlocked">مقفل</option>
                            <option value="locked">مقفل</option>
                            <option value="hidden">مخفي</option>
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

    <!-- Achievements Grid -->
    <div class="row" id="achievements-grid">
        <!-- Achievement cards will be loaded here -->
    </div>

    <!-- User Achievements Summary -->
    <div class="row mt-4" id="user-achievements-summary" style="display: none;">
        <div class="col-12">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">إنجازاتك</h5>
                    <h2 class="mb-0" id="user-total-achievements">0</h2>
                    <p class="mb-0">إنجاز</p>
                    <button class="btn btn-light" onclick="showAvailableAchievements()">
                        عرض الإنجازات المتاحة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Achievement Details Modal -->
<div class="modal fade" id="achievementModal" tabindex="-1" aria-labelledby="achievementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="achievementModalLabel">تفاصيل الإنجاز</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="achievement-details">
                    <!-- Achievement details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button class="btn btn-primary" id="unlock-achievement-btn">فتح الإنجاز</button>
            </div>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="header">
                <h5 class="modal-title" id="progressModalLabel">تقدم الإنجاز</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="progress mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>تقدم:</span>
                        <div class="progress flex-grow-1">
                            <div class="progress-bar" role="progressbar" style="height: 25px;">
                                <div class="progress-bar bg-info" id="progress-bar" style="width: 0%">
                                    <span class="sr-only">0%</span>
                                </div>
                            </div>
                        </div>
                        <span id="progress-percentage">0%</span>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <p id="progress-description">جاري تحقيق متطلبات الإنجاز...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button class="btn btn-primary" id="save-progress">حفظ التقدم</button>
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

function refreshAchievements() {
    loadAchievements();
}

function clearFilters() {
    document.getElementById('search-achievements').value = '';
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
    loadAchievements();
}

function filterAchievements() {
    currentFilters.search = document.getElementById('search-achievements').value;
    currentFilters.type = document.getElementById('filter-type').value;
    currentFilters.difficulty = document.getElementById('filter-difficulty').value;
    currentFilters.status = document.getElementById('filter-status').value;
    
    currentPage = 1;
    loadAchievements();
}

function loadAchievements() {
    const params = new URLSearchParams({
        page: currentPage,
        search: currentFilters.search,
        type: currentFilters.type,
        difficulty: currentFilters.difficulty,
        status: currentFilters.status
    });
    
    fetch(`/gamification/achievements?${params}`)
        .then(response => response => {
            if (response.success) {
                renderAchievements(response.achievements);
                renderPagination(response.pagination);
                updateUserAchievementsSummary();
            } else {
                showNotification(response.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading achievements:', error);
            showNotification('حدث خطأ ما أثناء تحميل الإنجازات', 'error');
        });
}

function renderAchievements(achievements) {
    const grid = document.getElementById('achievements-grid');
    grid.innerHTML = '';
    
    if (achievements && achievements.length > 0) {
        achievements.forEach(achievement => {
            const card = createAchievementCard(achievement);
            grid.appendChild(card);
        });
    } else {
        grid.innerHTML = `
            <div class="col-12 text-center text-muted">
                <i class="fas fa-trophy fa-3x mb-3"></i>
                <h4>لا توجد إنجازات متاحة</h4>
                <p>ابدأكسب إنجازات من خلال اللعب</p>
            </div>
        `;
    }
}

function createAchievementCard(achievement) {
    const col = document.createElement('div');
    col.className = 'col-md-6 col-lg-4 mb-4';
    
    const difficultyColor = getDifficultyColor(achievement.difficulty);
    const statusColor = getStatusColor(achievement.status);
    const statusIcon = getStatusIcon(achievement.status);
    
    const progress = achievement.progress || 0;
    const progressPercentage = Math.round(progress);
    
    col.innerHTML = `
        <div class="card h-100 achievement-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-${difficultyColor} me-2">${achievement.difficulty_label}</span>
                        <span class="badge bg-${statusColor}">${achievement.status_label}</span>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="showAchievementDetails(${achievement.id})">عرض التفاصيل</a></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteAchievement(${achievement.id})">حذف</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5 class="card-title">${achievement.name}</h5>
                    <p class="card-text text-muted">${achievement.description}</p>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">النقاط المكافأة:</small>
                        <strong>${achievement.points_reward}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">الصعوبة:</small>
                        <span class="badge bg-${difficultyColor}">${achievement.difficulty_label}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="progress mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>التقدم:</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar bg-success" role="progressbar" style="height: 25px;">
                                        <div class="progress-bar" style="width: ${progressPercentage}%">
                                            <span class="sr-only">${progressPercentage}%</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="progress-percentage">${progressPercentage}%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    ${achievement.can_unlock ? 
                        `<button class="btn btn-success btn-sm" onclick="showAchievementDetails(${achievement.id})">عرض التفاصيل</button>` :
                        `<button class="btn btn-secondary btn-sm" disabled>مقفل</button>`
                    }
                </div>
            </div>
        </div>
    `;
    
    return col;
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

function getStatusColor(status) {
    const colors = {
        'unlocked': 'secondary',
        'locked' => 'warning',
        'hidden' => 'danger'
    };
    return colors[status] || 'secondary';
}

function getStatusIcon(status) {
    const icons = {
        'unlocked': 'fa-lock',
        'locked' => 'fa-lock',
        'hidden' => 'fa-eye-slash'
    };
    return icons[status] || 'fa-circle';
}

function showAchievementDetails(achievementId) {
    fetch(`/gamification/achievements/${achievementId}`)
        .then(response => response => {
            if (response.success) {
                renderAchievementDetails(response.achievement);
                new bootstrap.Modal.getInstance(document.getElementById('achievementModal')).show();
                
                const unlockBtn = document.getElementById('unlock-achievement-btn');
                if (response.achievement.can_unlock) {
                    unlockBtn.textContent = 'فتح الإنجاز';
                    unlockBtn.className = 'btn btn-primary';
                } else {
                    unlockBtn.textContent = 'غير متاح للفتح هذا الإنجاز';
                    unlockBtn.disabled = true;
                }
            } else {
                showNotification(response.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading achievement details:', error);
            showNotification('حدث خطأ ما أثناء تحميل تفاصيل الإنجاز', 'error');
        });
}

function deleteAchievement(achievementId) {
    if (confirm('هل أنت متأكد من حذف هذا الإنجاز؟')) {
        fetch(`/gamification/achievements/${achievementId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token]').getAttribute('content')
            }
        })
        .then(response => response => {
            if (response.success) {
                showNotification('تم حذف الإنجاز بنجاح', 'success');
                loadAchievements();
            } else {
                showNotification(response.message, 'error');
            }
        })
        .catch(error => {
            showNotification('حدث خطأ ما أثناء حذف الإنجاز', 'error');
        });
}

function showAvailableAchievements() {
    document.getElementById('user-achievements-summary').style.display = 'block';
}

function updateUserAchievementsSummary() {
    fetch('/gamification/user-achievements-summary')
        .then(response => {
            if (response.success) {
                document.getElementById('user-total-achievements').textContent = response.total_achievements;
                document.getElementById('user-achievements-summary').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading achievements summary:', error);
        });
}

function showProgressModal(achievementId) {
    fetch(`/gamification/achievement-progress/${achievementId}`)
        .then(response => response => {
            if (response.success) {
                renderProgressModal(response.achievement);
                new bootstrap.Modal.getInstance(document.getElementById('progressModal')).show();
            } else {
                showNotification(response.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading achievement progress:', error);
        });
}

function renderProgressModal(achievement) {
    const achievement = achievement.achievement;
    const progress = achievement.progress || 0;
    const progressPercentage = Math.round(progress);
    
    const progressDescription = getProgressDescription(achievement);
    
    document.getElementById('progress-bar').style.width = `${progressPercentage}%`;
    document.getElementById('progress-percentage').textContent = `${progressPercentage}%`;
    document.getElementById('progress-description').textContent = progressDescription;
}

function getProgressDescription(achievement) {
    const progress = achievement.progress || 0;
    
    if (progress === 0) {
        return 'لم تبدأ بعد';
    } elseif (progress < 25) {
        return 'بداية';
    } elseif (progress < 50) {
        return 'جاري';
    } elseif (progress < 75) {
        return 'متقدم';
    } else {
        return 'قريب من 100';
    }
}

function renderPagination(pagination) {
    const nav = document.getElementById('achievements-pagination');
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
    loadAchievements();
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
    loadAchievements();
    updateUserAchievementsSummary();
});
</script>

<style>
.achievement-card {
    transition: transform 0.2s ease-in-out;
}

.achievement-card:hover {
    transform: translateY(-5px);
}

.achievement-card .card-header {
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
