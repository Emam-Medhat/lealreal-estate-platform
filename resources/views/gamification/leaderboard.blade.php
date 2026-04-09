@extends('layouts.app')

@section('title', 'لوحة المتصدرين - الألعاب العقارية')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-trophy text-warning me-2"></i>
            لوحة المتصدرين
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="refreshLeaderboard()">
                <i class="fas fa-sync-alt me-1"></i>
                تحديث
            </button>
            <button class="btn btn-info" onclick="exportLeaderboard()">
                <i class="fas fa-download me-1"></i>
                تصدير
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="leaderboard-filters">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">النوع</label>
                        <select class="form-control" id="leaderboard-type" onchange="filterLeaderboard()">
                            <option value="points">النقاط</option>
                            <option value="level">المستوى</option>
                            <option value="badges">الشارات</option>
                            <option value="challenges">التحديات</option>
                            <option value="quests">المهام</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الفترة</label>
                        <select class="form-control" id="leaderboard-period" onchange="filterLeaderboard()">
                            <option value="daily">يومي</option>
                            <option value="weekly">أسبوعي</option>
                            <option value="monthly">شهري</option>
                            <option value="quarterly">ربع سنوي</option>
                            <option value="yearly">سنوي</option>
                            <option value="all_time">كل الأوقات</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الفئة</label>
                        <select class="form-control" id="leaderboard-category" onchange="filterLeaderboard()">
                            <option value="general">عام</option>
                            <option value="weekly">أسبوعي</option>
                            <option value="monthly">شهري</option>
                            <option value="special">خاص</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">العرض</label>
                        <select class="form-control" id="leaderboard-limit" onchange="filterLeaderboard()">
                            <option value="10">أفضل 10</option>
                            <option value="25">أفضل 25</option>
                            <option value="50">أفضل 50</option>
                            <option value="100">أفضل 100</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Leaderboard Content -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <span id="leaderboard-title">أفضل اللاعبين - النقاط</span>
                        <small class="text-muted" id="leaderboard-subtitle">كل الأوقات</small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>الترتيب</th>
                                    <th>اللاعب</th>
                                    <th>النقاط</th>
                                    <th>المستوى</th>
                                    <th>الشارات</th>
                                    <th>تغيير الترتيب</th>
                                    <th>آخر تحديث</th>
                                </tr>
                            </thead>
                            <tbody id="leaderboard-tbody">
                                <!-- Leaderboard rows will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Leaderboard pagination">
                        <ul class="pagination justify-content-center" id="leaderboard-pagination">
                            <!-- Pagination will be loaded here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Statistics Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">إحصائيات لوحة المتصدرين</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>إجمالي المشاركين</span>
                            <strong id="total-participants">0</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>متوسط النقاط</span>
                            <strong id="average-score">0</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>أعلى نقاط</span>
                            <strong id="highest-score">0</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>أقل نقاط</span>
                            <strong id="lowest-score">0</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performers -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">أفضل الأداء</h5>
                </div>
                <div class="card-body">
                    <div id="top-performers">
                        <!-- Top performers will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">توزيع حسب الفئة</h5>
                </div>
                <div class="card-body">
                    <div id="category-distribution">
                        <!-- Category distribution will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Position Card -->
    <div class="row mt-4" id="user-position-card" style="display: none;">
        <div class="col-12">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">موقعك في لوحة المتصدرين</h5>
                    <h2 class="mb-0" id="user-rank">-</h2>
                    <p class="mb-0">ترتيب <span id="user-score">0</span> نقطة</p>
                    <small class="text-muted" id="user-category">في الفئة العامة</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let currentType = 'points';
let currentPeriod = 'all_time';
let currentCategory = 'general';
let currentLimit = 10;

function refreshLeaderboard() {
    loadLeaderboard();
}

function exportLeaderboard() {
    const type = document.getElementById('leaderboard-type').value;
    const period = document.getElementById('leaderboard-period').value;
    const category = document.getElementById('leaderboard-category').value;
    
    window.open(`/gamification/export-leaderboard?type=${type}&period=${period}&category=${category}`, '_blank');
}

function filterLeaderboard() {
    currentType = document.getElementById('leaderboard-type').value;
    currentPeriod = document.getElementById('leaderboard-period').value;
    currentCategory = document.getElementById('leaderboard-category').value;
    currentLimit = parseInt(document.getElementById('leaderboard-limit').value);
    currentPage = 1;
    
    loadLeaderboard();
    updateTitle();
    loadStatistics();
}

function updateTitle() {
    const typeLabels = {
        'points': 'النقاط',
        'level': 'المستوى',
        'badges': 'الشارات',
        'challenges': 'التحديات',
        'quests': 'المهام'
    };
    
    const periodLabels = {
        'daily': 'يومي',
        'weekly': 'أسبوعي',
        'monthly': 'شهري',
        'quarterly': 'ربع سنوي',
        'yearly': 'سنوي',
        'all_time': 'كل الأوقات'
    };
    
    const categoryLabels = {
        'general': 'عام',
        'weekly': 'أسبوعي',
        'monthly': 'شهري',
        'special': 'خاص'
    };
    
    document.getElementById('leaderboard-title').textContent = `أفضل اللاعبين - ${typeLabels[currentType]}`;
    document.getElementById('leaderboard-subtitle').textContent = periodLabels[currentPeriod];
}

function loadLeaderboard() {
    const params = new URLSearchParams({
        type: currentType,
        period: currentPeriod,
        category: currentCategory,
        page: currentPage,
        limit: currentLimit
    });
    
    fetch(`/gamification/leaderboard?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderLeaderboard(data.leaderboard);
                renderPagination(data.pagination);
                updateTitle();
                loadStatistics();
                checkUserPosition();
            } else {
                showNotification(data.message, 'error');
            }
        })
    .catch(error => {
        console.error('Error loading leaderboard:', error);
        showNotification('حدث خطأ ما أثناء تحميل لوحة المتصدرين', 'error');
        });
}

function renderLeaderboard(leaderboard) {
    const tbody = document.getElementById('leaderboard-tbody');
    tbody.innerHTML = '';
    
    if (leaderboard && leaderboard.length > 0) {
        leaderboard.forEach((entry, index) => {
            const row = createLeaderboardRow(entry, index + 1);
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted">
                    لا توجد بيانات لعرضها
                </td>
            </tr>
        `;
    }
}

function createLeaderboardRow(entry, rank) {
    const row = document.createElement('tr');
    
    const rankChange = entry.change || 0;
    const rankChangeIcon = getRankChangeIcon(rankChange);
    const rankChangeColor = getRankChangeColor(rankChange);
    
    row.innerHTML = `
        <td>
            <span class="badge bg-primary">${rank}</span>
            ${rankChangeIcon ? `<i class="fas fa-${rankChangeIcon} ms-2 text-${rankChangeColor}"></i>` : ''}
        </td>
        <td>
            <div class="d-flex align-items-center">
                <img src="https://picsum.photos/seed/${entry.user_id}/40/40.jpg" 
                     alt="${entry.user_name}" 
                     class="rounded-circle me-2" 
                     width="30" height="30">
                <strong>${entry.user_name}</strong>
            </div>
        </td>
        <td>
            <strong>${entry.score}</strong>
        </td>
        <td>
            <span class="badge bg-info">${entry.level || 1}</span>
        </td>
        <td>
            <span class="badge bg-warning">${entry.badges_count || 0}</span>
        </td>
        <td>
            <span class="text-${rankChangeColor}">${entry.rank_change_label}</span>
        </td>
        <td>
            <small class="text-muted">${new Date(entry.updated_at).toLocaleString('ar-SA')}</small>
        </td>
    `;
    
    return row;
}

function getRankChangeIcon(change) {
    if (change > 0) return 'arrow-up';
    if (change < 0) return 'arrow-down';
    return 'minus';
}

function getRankChangeColor(change) {
    if (change > 0) return 'success';
    if (change < 0) return 'danger';
    return 'secondary';
}

function renderPagination(pagination) {
    const nav = document.getElementById('leaderboard-pagination');
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
    loadLeaderboard();
}

function loadStatistics() {
    const params = new URLSearchParams({
        type: currentType,
        period: currentPeriod,
        category: currentCategory
    });
    
    fetch(`/gamification/leaderboard-stats?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderStatistics(data.stats);
            } else {
                console.error('Error loading statistics:', data.message);
            }
        })
    .catch(error => {
        console.error('Error loading statistics:', error);
    });
}

function renderStatistics(stats) {
    document.getElementById('total-participants').textContent = stats.total_participants;
    document.getElementById('average-score').textContent = Math.round(stats.average_score);
    document.getElementById('highest-score').textContent = stats.highest_score;
    document.getElementById('lowest-score').textContent = stats.lowest_score;
}

function loadTopPerformers() {
    fetch(`/gamification/top-performers?type=${currentType}&period=${currentPeriod}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTopPerformers(data.top_performers);
            } else {
                console.error('Error loading top performers:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading top performers:', error);
        });
}

function renderTopPerformers(performers) {
    const container = document.getElementById('top-performers');
    container.innerHTML = '';
    
    if (performers && performers.length > 0) {
        performers.forEach((performer, index) => {
            const item = createTopPerformerItem(performer, index + 1);
            container.appendChild(item);
        });
    } else {
        container.innerHTML = '<p class="text-muted text-center">لا توجد بيانات</p>';
    }
}

function createTopPerformerItem(performer, rank) {
    const div = document.createElement('div');
    div.className = 'd-flex justify-content-between align-items-center mb-2';
    
    div.innerHTML = `
        <div class="d-flex align-items-center">
            <span class="badge bg-primary me-2">${rank}</span>
            <img src="https://picsum.photos/seed/${performer.user_id}/40/40.jpg" 
                 alt="${performer.user_name}" 
                 class="rounded-circle me-2" 
                 width="30" height="30">
            <div>
                <strong>${performer.user_name}</strong>
                <br>
                <small class="text-muted">${performer.total_points} نقطة</small>
            </div>
        </div>
        <div class="text-end">
            <small class="text-muted">${performer.challenges_completed} تحدي</small>
            <br>
            <small class="text-muted">${performer.badges_earned} شارة</small>
        </div>
    `;
    
    return div;
}

function loadCategoryDistribution() {
    fetch(`/gamification/category-distribution?type=${currentType}&period=${currentPeriod}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderCategoryDistribution(data.distribution);
            } else {
                console.error('Error loading category distribution:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading category distribution:', error);
        });
}

function renderCategoryDistribution(distribution) {
    const container = document.getElementById('category-distribution');
    container.innerHTML = '';
    
    if (distribution && distribution.length > 0) {
        distribution.forEach(item => {
            const percentage = Math.round(item.percentage);
            const div = document.createElement('div');
            div.className = 'mb-2';
            
            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <span>${item.category}</span>
                    <span class="badge bg-secondary">${item.count}</span>
                </div>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar" style="width: ${percentage}%">
                        <span class="sr-only">${percentage}%</span>
                    </div>
                </div>
            `;
            
            container.appendChild(div);
        });
    } else {
        container.innerHTML = '<p class="text-muted text-center">لا توجد بيانات</p>';
    }
}

function checkUserPosition() {
    fetch('/gamification/user-position')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.position) {
                renderUserPosition(data.position);
            }
        })
    .catch(error => {
            console.error('Error checking user position:', error);
        });
}

function renderUserPosition(position) {
    const card = document.getElementById('user-position-card');
    card.style.display = 'block';
    
    document.getElementById('user-rank').textContent = position.rank;
    document.getElementById('user-score').textContent = position.score;
    document.getElementById('user-category').textContent = position.category_label;
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
    loadLeaderboard();
    loadTopPerformers();
    loadCategoryDistribution();
});
</script>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.table-dark th {
    border-bottom: 2px solid #495057;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
}

.pagination .page-link {
    color: #007bff;
    border: 1px solid #dee2e6;
}

.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.bg-info {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
}
</style>
@endsection
