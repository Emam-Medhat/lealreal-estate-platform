@extends('layouts.app')

@section('title', 'لوحة تحكم الألعاب العقارية')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-trophy text-warning me-2"></i>
            لوحة تحكم الألعاب العقارية
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt me-1"></i>
                تحديث
            </button>
            <button class="btn btn-info" onclick="exportData()">
                <i class="fas fa-download me-1"></i>
                تصدير
            </button>
        </div>
    </div>

    <!-- User Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">نقاطي الإجمالية</h5>
                    <h2 class="mb-0" id="total-points">0</h2>
                    <small>نقطة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">المستوى الحالي</h5>
                    <h2 class="mb-0" id="current-level">1</h2>
                    <small>مستوى</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">الشارات المكتسبة</h5>
                    <h2 class="mb-0" id="badges-earned">0</h2>
                    <small>شارة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">السلسلة الحالية</h5>
                    <h2 class="mb-0" id="current-streak">0</h2>
                    <small>يوم</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">تقدم المستوى</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" id="level-progress" style="width: 0%">
                            <span class="sr-only">0%</span>
                        </div>
                    </div>
                    <small class="text-muted">نقاط الخبرة: <span id="exp-current">0</span> / <span id="exp-required">100</span></small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">الإنجازات القادمة</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>التحديات</span>
                        <span class="badge bg-primary" id="challenges-completed">0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>المهام</span>
                        <span class="badge bg-success" id="quests-completed">0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>الإنجازات</span>
                        <span class="badge bg-info" id="achievements-unlocked">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-primary w-100" onclick="openPointsModal()">
                                <i class="fas fa-coins me-1"></i>
                                كسب نقاط
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-success w-100" onclick="openBadgesModal()">
                                <i class="fas fa-medal me-1"></i>
                                الشارات
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-info w-100" onclick="openChallengesModal()">
                                <i class="fas fa-tasks me-1"></i>
                                التحديات
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-warning w-100" onclick="openRewardsModal()">
                                <i class="fas fa-gift me-1"></i>
                                المكافآت
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">النشاط الحديث</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active" onclick="filterActivity('all')">الكل</button>
                        <button class="btn btn-outline-secondary" onclick="filterActivity('points')">نقاط</button>
                        <button class="btn btn-outline-secondary" onclick="filterActivity('badges')">شارات</button>
                        <button class="btn btn-outline-secondary" onclick="filterActivity('challenges')">تحديات</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="timeline" id="recent-activity">
                        <!-- Activity items will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">لوحة المتصدرين</h5>
                </div>
                <div class="card-body">
                    <div class="leaderboard" id="leaderboard">
                        <!-- Leaderboard will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Points Modal -->
<div class="modal fade" id="pointsModal" tabindex="-1" aria-labelledby="pointsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pointsModalLabel">كسب نقاط</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="earn-points-form">
                    <div class="mb-3">
                        <label for="points-amount" class="form-label">عدد النقاط</label>
                        <input type="number" class="form-control" id="points-amount" min="1" max="1000" required>
                    </div>
                    <div class="mb-3">
                        <label for="points-reason" class="form-label">السبب</label>
                        <textarea class="form-control" id="points-reason" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="points-type" class="form-label">النوع</label>
                        <select class="form-control" id="points-type">
                            <option value="earned">مكتسبة</option>
                            <option value="bonus">مكافأة</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="earnPoints()">كسب النقاط</button>
            </div>
        </div>
    </div>
</div>

<!-- Badges Modal -->
<div class="modal fade" id="badgesModal" tabindex="-1" aria-labelledby="badgesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="badgesModalLabel">الشارات المتاحة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="available-badges">
                    <!-- Badges will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Challenges Modal -->
<div class="modal fade" id="challengesModal" tabindex="-1" aria-labelledby="challengesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="challengesModalLabel">التحديات النشطة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="active-challenges">
                    <!-- Challenges will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rewards Modal -->
<div class="modal fade" id="rewardsModal" tabindex="-1" aria-labelledby="rewardsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rewardsModalLabel">المكافآت المتاحة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="available-rewards">
                    <!-- Rewards will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dashboard functionality
let currentFilter = 'all';

function refreshDashboard() {
    location.reload();
}

function exportData() {
    window.open('/gamification/export', '_blank');
}

function filterActivity(type) {
    currentFilter = type;
    loadRecentActivity();
}

function openPointsModal() {
    loadAvailableBadges();
    loadAvailableChallenges();
    loadAvailableRewards();
    new bootstrap.Modal(document.getElementById('pointsModal')).show();
}

function openBadgesModal() {
    loadAvailableBadges();
    new bootstrap.Modal(document.getElementById('badgesModal')).show();
}

function openChallengesModal() {
    loadAvailableChallenges();
    new bootstrap.Modal(document.getElementById('challengesModal')).show();
}

function openRewardsModal() {
    loadAvailableRewards();
    new bootstrap.Modal(document.getElementById('rewardsModal')).show();
}

function earnPoints() {
    const amount = document.getElementById('points-amount').value;
    const reason = document.getElementById('points-reason').value;
    const type = document.getElementById('points-type').value;
    
    fetch('/gamification/earn-points', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            points: amount,
            reason: reason,
            type: type
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('pointsModal')).hide();
            showNotification('تم كسب النقاط بنجاح', 'success');
            refreshDashboard();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ ما', 'error');
    });
}

function loadRecentActivity() {
    fetch(`/gamification/activity?filter=${currentFilter}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('recent-activity');
            container.innerHTML = '';
            
            if (data.activities && data.activities.length > 0) {
                data.activities.forEach(activity => {
                    const item = createActivityItem(activity);
                    container.appendChild(item);
                });
            } else {
                container.innerHTML = '<p class="text-muted text-center">لا يوجد نشاط حديث</p>';
            }
        })
    .catch(error => {
        console.error('Error loading activity:', error);
    });
}

function createActivityItem(activity) {
    const div = document.createElement('div');
    div.className = 'timeline-item mb-3';
    
    const icon = getActivityIcon(activity.type);
    const time = new Date(activity.created_at).toLocaleString('ar-SA');
    
    div.innerHTML = `
        <div class="d-flex align-items-start">
            <div class="timeline-icon me-3">
                <i class="${icon} text-${getActivityColor(activity.type)}"></i>
            </div>
            <div class="timeline-content">
                <div class="d-flex justify-content-between align-items-center">
                    <strong>${activity.reason}</strong>
                    <small class="text-muted">${time}</small>
                </div>
                ${activity.description ? `<p class="mb-0 text-muted">${activity.description}</p>` : ''}
                ${activity.points ? `<small class="text-success">+${activity.points} نقطة</small>` : ''}
            </div>
        </div>
    `;
    
    return div;
}

function getActivityIcon(type) {
    const icons = {
        'earned': 'fa-coins',
        'bonus': 'fa-gift',
        'penalty': 'fa-minus-circle',
        'level_up': 'fa-level-up-alt',
        'badge': 'fa-medal',
        'challenge': 'fa-trophy',
        'quest': 'fa-scroll',
        'achievement': 'fa-star'
    };
    return icons[type] || 'fa-circle';
}

function getActivityColor(type) {
    const colors = {
        'earned': 'success',
        'bonus': 'primary',
        'penalty': 'danger',
        'level_up': 'info',
        'badge': 'warning',
        'challenge': 'success',
        'quest': 'info',
        'achievement': 'success'
    };
    return colors[type] || 'secondary';
}

function loadAvailableBadges() {
    fetch('/gamification/available-badges')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('available-badges');
            container.innerHTML = '';
            
            if (data.length > 0) {
                data.forEach(badge => {
                    const item = createBadgeItem(badge);
                    container.appendChild(item);
                });
            } else {
                container.innerHTML = '<p class="text-muted text-center">لا توجد شارات متاحة</p>';
            }
        })
    .catch(error => {
        console.error('Error loading badges:', error);
    });
}

function createBadgeItem(badge) {
    const div = document.createElement('div');
    div.className = 'col-md-6 mb-3';
    
    div.innerHTML = `
        <div class="card h-100 ${badge.can_earn ? 'border-success' : 'border-secondary'}">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="${badge.icon}" style="font-size: 2rem; color: ${badge.rarity_color}"></i>
                </div>
                <h6 class="card-title">${badge.name}</h6>
                <p class="card-text small text-muted">${badge.description}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">${badge.points_required} نقطة</small>
                    ${badge.can_earn ? '<span class="badge bg-success">متاح</span>' : '<span class="badge bg-secondary">غير متاح</span>'}
                </div>
            </div>
        </div>
    `;
    
    return div;
}

function loadAvailableChallenges() {
    fetch('/gamification/active-challenges')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('active-challenges');
            container.innerHTML = '';
            
            if (data.length > 0) {
                data.forEach(challenge => {
                    const item = createChallengeItem(challenge);
                    container.appendChild(item);
                });
            } else {
                container.innerHTML = '<p class="text-muted text-center">لا توجد تحديات نشطة</p>';
            }
        })
    .catch(error => {
        console.error('Error loading challenges:', error);
    });
}

function createChallengeItem(challenge) {
    const div = document.createElement('div');
    div.className = 'col-md-6 mb-3';
    
    div.innerHTML = `
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title">${challenge.title}</h6>
                    <span class="badge bg-${challenge.difficulty_color}">${challenge.difficulty_label}</span>
                </div>
                <p class="card-text small text-muted">${challenge.description}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">${challenge.points_reward} نقطة</small>
                    <small class="text-muted">${challenge.days_remaining} يوم متبقي</small>
                </div>
                ${challenge.can_join ? 
                    `<button class="btn btn-primary btn-sm w-100" onclick="joinChallenge(${challenge.id})">انضم</button>` :
                    `<button class="btn btn-secondary btn-sm w-100" disabled>ممتمل</button>`
                }
            </div>
        </div>
    `;
    
    return div;
}

function loadAvailableRewards() {
    fetch('/gamification/available-rewards')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('available-rewards');
            container.innerHTML = '';
            
            if (data.length > 0) {
                data.forEach(reward => {
                    const item = createRewardItem(reward);
                    container.appendChild(item);
                });
            } else {
                container.innerHTML = '<p class="text-muted text-center">لا توجد مكافآت متاحة</p>';
            }
        })
    .catch(error => {
        console.error('Error loading rewards:', error);
    });
}

function createRewardItem(reward) {
    const div = document.createElement('div');
    div.className = 'col-md-6 mb-3';
    
    div.innerHTML = `
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title">${reward.name}</h6>
                    <span class="badge bg-primary">${reward.type_label}</span>
                </div>
                <p class="card-text small text-muted">${reward.description}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">${reward.formatted_points_cost}</small>
                    ${reward.can_redeem ? 
                        `<button class="btn btn-success btn-sm w-100" onclick="claimReward(${reward.id})">استبدال</button>` :
                        `<button class="btn btn-secondary btn-sm w-100" disabled>غير متاح</button>`
                }
            </div>
        </div>
    `;
    
    return div;
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
            loadAvailableChallenges();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ ما', 'error');
    });
}

function claimReward(rewardId) {
    fetch('/gamification/claim-reward', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reward_id: rewardId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم استبدال المكافأة بنجاح', 'success');
            loadAvailableRewards();
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('حدث خطأ ما', 'error');
    });
}

function showNotification(message, type) {
    // Simple notification implementation
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
    loadRecentActivity();
    loadLeaderboard();
});

function loadLeaderboard() {
    fetch('/gamification/leaderboard?type=points&period=all_time')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('leaderboard');
            container.innerHTML = '';
            
            if (data.leaderboard && data.leaderboard.length > 0) {
                data.leaderboard.slice(0, 10).forEach((entry, index) => {
                    const item = createLeaderboardItem(entry, index + 1);
                    container.appendChild(item);
                });
            } else {
                container.innerHTML = '<p class="text-muted text-center">لا توجد بيانات لوحة المتصدرين</p>';
            }
        })
    .catch(error => {
        console.error('Error loading leaderboard:', error);
    });
}

function createLeaderboardItem(entry, rank) {
    const div = document.createElement('div');
    div.className = 'd-flex justify-content-between align-items-center mb-2';
    
    div.innerHTML = `
        <div class="d-flex align-items-center">
            <span class="badge bg-primary me-2">${rank}</span>
            <strong>${entry.user_name}</strong>
        </div>
        <div class="text-end">
            <strong>${entry.score}</strong>
            <small class="text-muted">نقطة</small>
        </div>
    `;
    
    return div;
}
</script>

<style>
.timeline-item {
    border-left: 3px solid #e9ecef;
    padding-left: 20px;
    position: relative;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 5px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e9ecef;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
}

.timeline-content {
    flex: 1;
}

.card.h-100 {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
}
</style>
@endsection
