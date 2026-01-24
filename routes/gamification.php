<?php

use Illuminate\Support\Facades\Route;

// Gamification Routes
Route::prefix('gamification')->name('gamification.')->middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Routes
    Route::get('/dashboard', [App\Http\Controllers\PropertyGamificationController::class, 'dashboard'])->name('dashboard');
    Route::get('/user/{user}', [App\Http\Controllers\PropertyGamificationController::class, 'show'])->name('show');
    Route::get('/user/{user}/analytics', [App\Http\Controllers\PropertyGamificationController::class, 'analytics'])->name('analytics');
    Route::post('/earn-points', [App\Http\Controllers\PropertyGamificationController::class, 'earnPoints'])->name('earn.points');
    Route::post('/check-level-up', [App\Http\Controllers\PropertyGamificationController::class, 'checkLevelUp'])->name('check.level.up');
    Route::post('/reset-progress', [App\Http\Controllers\PropertyGamificationController::class, 'resetProgress'])->name('reset.progress');
    
    // Points Routes
    Route::get('/points', [App\Http\Controllers\PropertyPointsController::class, 'index'])->name('points.index');
    Route::get('/points/create', [App\Http\Controllers\PropertyPointsController::class, 'create'])->name('points.create');
    Route::post('/points', [App\Http\Controllers\PropertyPointsController::class, 'store'])->name('points.store');
    Route::get('/points/{points}', [App\Http\Controllers\PropertyPointsController::class, 'show'])->name('points.show');
    Route::get('/points/{points}/edit', [App\Http\Controllers\PropertyPointsController::class, 'edit'])->name('points.edit');
    Route::put('/points/{points}', [App\Http\Controllers\PropertyPointsController::class, 'update'])->name('points.update');
    Route::delete('/points/{points}', [App\Http\Controllers\PropertyPointsController::class, 'destroy'])->name('points.destroy');
    Route::post('/points/bulk-award', [App\Http\Controllers\PropertyPointsController::class, 'bulkAward'])->name('points.bulk.award');
    Route::get('/points/user/{user}', [App\Http\Controllers\PropertyPointsController::class, 'userPoints'])->name('points.user');
    Route::get('/points/analytics', [App\Http\Controllers\PropertyPointsController::class, 'analytics'])->name('points.analytics');
    Route::get('/points/export', [App\Http\Controllers\PropertyPointsController::class, 'export'])->name('points.export');
    
    // Badges Routes
    Route::get('/badges', [App\Http\Controllers\PropertyBadgeController::class, 'index'])->name('badges.index');
    Route::get('/badges/create', [App\Http\Controllers\PropertyBadgeController::class, 'create'])->name('badges.create');
    Route::post('/badges', [App\Http\Controllers\PropertyBadgeController::class, 'store'])->name('badges.store');
    Route::get('/badges/{badge}', [App\Http\Controllers\PropertyBadgeController::class, 'show'])->name('badges.show');
    Route::get('/badges/{badge}/edit', [App\Http\Controllers\PropertyBadgeController::class, 'edit'])->name('badges.edit');
    Route::put('/badges/{badge}', [App\Http\Controllers\PropertyBadgeController::class, 'update'])->name('badges.update');
    Route::delete('/badges/{badge}', [App\Http\Controllers\PropertyBadgeController::class, 'destroy'])->name('badges.destroy');
    Route::post('/badges/{badge}/award', [App\Http\Controllers\PropertyBadgeController::class, 'award'])->name('badges.award');
    Route::post('/badges/{badge}/revoke', [App\Http\Controllers\PropertyBadgeController::class, 'revoke'])->name('badges.revoke');
    Route::get('/badges/user/{user}', [App\Http\Controllers\PropertyBadgeController::class, 'userBadges'])->name('badges.user');
    Route::get('/badges/available', [App\Http\Controllers\PropertyBadgeController::class, 'available'])->name('badges.available');
    Route::post('/badges/check-award', [App\Http\Controllers\PropertyBadgeController::class, 'checkAndAward'])->name('badges.check.award');
    Route::get('/badges/analytics', [App\Http\Controllers\PropertyBadgeController::class, 'analytics'])->name('badges.analytics');
    
    // Leaderboard Routes
    Route::get('/leaderboard', [App\Http\Controllers\PropertyLeaderboardController::class, 'index'])->name('leaderboard.index');
    Route::get('/leaderboard/user/{user}', [App\Http\Controllers\PropertyLeaderboardController::class, 'userRankings'])->name('leaderboard.user');
    Route::get('/leaderboard/generate', [App\Http\Controllers\PropertyLeaderboardController::class, 'generate'])->name('leaderboard.generate');
    Route::get('/leaderboard/analytics', [App\Http\Controllers\PropertyLeaderboardController::class, 'analytics'])->name('leaderboard.analytics');
    Route::get('/leaderboard/export', [App\Http\Controllers\PropertyLeaderboardController::class, 'export'])->name('leaderboard.export');
    Route::get('/leaderboard/stats', [App\Http\Controllers\PropertyLeaderboardController::class, 'stats'])->name('leaderboard.stats');
    
    // Challenges Routes
    Route::get('/challenges', [App\Http\Controllers\PropertyChallengeController::class, 'index'])->name('challenges.index');
    Route::get('/challenges/create', [App\Http\Controllers\PropertyChallengeController::class, 'create'])->name('challenges.create');
    Route::post('/challenges', [App\Http\Controllers\PropertyChallengeController::class, 'store'])->name('challenges.store');
    Route::get('/challenges/{challenge}', [App\Http\Controllers\PropertyChallengeController::class, 'show'])->name('challenges.show');
    Route::get('/challenges/{challenge}/edit', [App\Http\Controllers\PropertyChallengeController::class, 'edit'])->name('challenges.edit');
    Route::put('/challenges/{challenge}', [App\Http\Controllers\PropertyChallengeController::class, 'update'])->name('challenges.update');
    Route::delete('/challenges/{challenge}', [App\Http\Controllers\PropertyChallengeController::class, 'destroy'])->name('challenges.destroy');
    Route::post('/challenges/{challenge}/join', [App\Http\Controllers\PropertyChallengeController::class, 'join'])->name('challenges.join');
    Route::post('/challenges/{challenge}/leave', [App\Http\Controllers\PropertyChallengeController::class, 'leave'])->name('challenges.leave');
    Route::post('/challenges/{challenge}/progress', [App\Http\Controllers\PropertyChallengeController::class, 'updateProgress'])->name('challenges.progress');
    Route::get('/challenges/user/{user}', [App\Http\Controllers\PropertyChallengeController::class, 'userChallenges'])->name('challenges.user');
    Route::get('/challenges/active', [App\Http\Controllers\PropertyChallengeController::class, 'active'])->name('challenges.active');
    Route::get('/challenges/analytics', [App\Http\Controllers\PropertyChallengeController::class, 'analytics'])->name('challenges.analytics');
    
    // Rewards Routes
    Route::get('/rewards', [App\Http\Controllers\PropertyRewardController::class, 'index'])->name('rewards.index');
    Route::get('/rewards/create', [App\Http\Controllers\PropertyRewardController::class, 'create'])->name('rewards.create');
    Route::post('/rewards', [App\Http\Controllers\PropertyRewardController::class, 'store'])->name('rewards.store');
    Route::get('/rewards/{reward}', [App\Http\Controllers\PropertyRewardController::class, 'show'])->name('rewards.show');
    Route::get('/rewards/{reward}/edit', [App\Http\Controllers\PropertyRewardController::class, 'edit'])->name('rewards.edit');
    Route::put('/rewards/{reward}', [App\Http\Controllers\PropertyRewardController::class, 'update'])->name('rewards.update');
    Route::delete('/rewards/{reward}', [App\Http\Controllers\PropertyRewardController::class, 'destroy'])->name('rewards.destroy');
    Route::post('/rewards/{reward}/redeem', [App\Http\Controllers\PropertyRewardController::class, 'redeem'])->name('rewards.redeem');
    Route::put('/rewards/redemptions/{redemption}', [App\Http\Controllers\PropertyRewardController::class, 'updateRedemptionStatus'])->name('rewards.update.redemption');
    Route::get('/rewards/user/{user}', [App\Http\Controllers\PropertyRewardController::class, 'userRedemptions'])->name('rewards.user');
    Route::get('/rewards/available', [App\Http\Controllers\PropertyRewardController::class, 'available'])->name('rewards.available');
    Route::get('/rewards/analytics', [App\Http\Controllers\PropertyRewardController::class, 'analytics'])->name('rewards.analytics');
    Route::get('/rewards/export', [App\Http\Controllers\PropertyRewardController::class, 'export'])->name('rewards.export');
    
    // Levels Routes
    Route::get('/levels', [App\Http\Controllers\PropertyLevelController::class, 'index'])->name('levels.index');
    Route::get('/levels/create', [App\Http\Controllers\PropertyLevelController::class, 'create'])->name('levels.create');
    Route::post('/levels', [App\Http\Controllers\PropertyLevelController::class, 'store'])->name('levels.store');
    Route::get('/levels/{level}', [App\Http\Controllers\PropertyLevelController::class, 'show'])->name('levels.show');
    Route::get('/levels/{level}/edit', [App\Http\Controllers\PropertyLevelController::class, 'edit'])->name('levels.edit');
    Route::put('/levels/{level}', [App\Http\Controllers\PropertyLevelController::class, 'update'])->name('levels.update');
    Route::delete('/levels/{level}', [App\Http\Controllers\PropertyLevelController::class, 'destroy'])->name('levels.destroy');
    Route::post('/levels/check-level-up', [App\Http\Controllers\PropertyLevelController::class, 'checkLevelUp'])->name('levels.check.level.up');
    Route::get('/levels/user/{user}', [App\Http\Controllers\PropertyLevelController::class, 'userLevels'])->name('levels.user');
    Route::get('/levels/progression', [App\Http\Controllers\PropertyLevelController::class, 'progression'])->name('levels.progression');
    Route::get('/levels/analytics', [App\Http\Controllers\PropertyLevelController::class, 'analytics'])->name('levels.analytics');
    Route::get('/levels/privileges', [App\Http\Controllers\PropertyLevelController::class, 'privileges'])->name('levels.privileges');
    
    // Achievements Routes
    Route::get('/achievements', [App\Http\Controllers\PropertyAchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/create', [App\Http\Controllers\PropertyAchievementController::class, 'create'])->name('achievements.create');
    Route::post('/achievements', [App\Http\Controllers\PropertyAchievementController::class, 'store'])->name('achievements.store');
    Route::get('/achievements/{achievement}', [App\Http\Controllers\PropertyAchievementController::class, 'show'])->name('achievements.show');
    Route::get('/achievements/{achievement}/edit', [App\Http\Controllers\PropertyAchievementController::class, 'edit'])->name('achievements.edit');
    Route::put('/achievements/{achievement}', [App\Http\Controllers\PropertyAchievementController::class, 'update'])->name('achievements.update');
    Route::delete('/achievements/{achievement}', [App\Http\Controllers\PropertyAchievementController::class, 'destroy'])->name('achievements.destroy');
    Route::post('/achievements/{achievement}/unlock', [App\Http\Controllers\PropertyAchievementController::class, 'unlock'])->name('achievements.unlock');
    Route::post('/achievements/{achievement}/progress', [App\Http\Controllers\PropertyAchievementController::class, 'updateProgress'])->name('achievements.progress');
    Route::get('/achievements/user/{user}', [App\Http\Controllers\PropertyAchievementController::class, 'userAchievements'])->name('achievements.user');
    Route::get('/achievements/available', [App\Http\Controllers\PropertyAchievementController::class, 'available'])->name('achievements.available');
    Route::post('/achievements/check-unlock', [App\Http\Controllers\PropertyAchievementController::class, 'checkAndUnlock'])->name('achievements.check.unlock');
    Route::get('/achievements/analytics', [App\Http\Controllers\PropertyAchievementController::class, 'analytics'])->name('achievements.analytics');
    
    // Quests Routes
    Route::get('/quests', [App\Http\Controllers\PropertyQuestController::class, 'index'])->name('quests.index');
    Route::get('/quests/create', [App\Http\Controllers\PropertyQuestController::class, 'create'])->name('quests.create');
    Route::post('/quests', [App\Http\Controllers\PropertyQuestController::class, 'store'])->name('quests.store');
    Route::get('/quests/{quest}', [App\Http\Controllers\PropertyQuestController::class, 'show'])->name('quests.show');
    Route::get('/quests/{quest}/edit', [App\Http\Controllers\PropertyQuestController::class, 'edit'])->name('quests.edit');
    Route::put('/quests/{quest}', [App\Http\Controllers\PropertyQuestController::class, 'update'])->name('quests.update');
    Route::delete('/quests/{quest}', [App\Http\Controllers\PropertyQuestController::class, 'destroy'])->name('quests.destroy');
    Route::post('/quests/{quest}/accept', [App\Http\Controllers\PropertyQuestController::class, 'accept'])->name('quests.accept');
    Route::post('/quests/{quest}/abandon', [App\Http\Controllers\PropertyQuestController::class, 'abandon'])->name('quests.abandon');
    Route::post('/quests/{quest}/progress', [App\Http\Controllers\PropertyQuestController::class, 'updateProgress'])->name('quests.progress');
    Route::get('/quests/user/{user}', [App\Http\Controllers\PropertyQuestController::class, 'userQuests'])->name('quests.user');
    Route::get('/quests/active', [App\Http\Controllers\PropertyQuestController::class, 'active'])->name('quests.active');
    Route::get('/quests/analytics', [App\Http\Controllers\PropertyQuestController::class, 'analytics'])->name('quests.analytics');
    
    // API Routes for AJAX requests
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/activity', [App\Http\Controllers\PropertyGamificationController::class, 'getActivity'])->name('activity');
        Route::get('/user-position', [App\Http\Controllers\PropertyGamificationController::class, 'getUserPosition'])->name('user.position');
        Route::get('/user-points-summary', [App\Http\Controllers\PropertyGamificationController::class, 'getUserPointsSummary'])->name('user.points.summary');
        Route::get('/user-achievements-summary', [App\Http\Controllers\PropertyGamificationController::class, 'getUserAchievementsSummary'])->name('user.achievements.summary');
        
        Route::post('/join-challenge', [App\Http\Controllers\PropertyChallengeController::class, 'apiJoinChallenge'])->name('join.challenge');
        Route::post('/claim-reward', [App\Http\Controllers\PropertyRewardController::class, 'apiClaimReward'])->name('claim.reward');
        Route::post('/complete-quest', [App\Http\Controllers\PropertyQuestController::class, 'apiCompleteQuest'])->name('complete.quest');
        
        Route::get('/available-badges', [App\Http\Controllers\PropertyBadgeController::class, 'apiAvailable'])->name('available.badges');
        Route::get('/active-challenges', [App\Http\Controllers\PropertyChallengeController::class, 'apiActive'])->name('active.challenges');
        Route::get('/available-rewards', [App\Http\Controllers\PropertyRewardController::class, 'apiAvailable'])->name('available.rewards');
        
        Route::get('/leaderboard-stats', [App\Http\Controllers\PropertyLeaderboardController::class, 'apiStats'])->name('leaderboard.stats');
        Route::get('/top-performers', [App\Http\Controllers\PropertyLeaderboardController::class, 'apiTopPerformers'])->name('top.performers');
        Route::get('/category-distribution', [App\Http\Controllers\PropertyLeaderboardController::class, 'apiCategoryDistribution'])->name('category.distribution');
        
        Route::get('/achievement-progress/{achievement}', [App\Http\Controllers\PropertyAchievementController::class, 'apiProgress'])->name('achievement.progress');
        Route::get('/reward-details/{reward}', [App\Http\Controllers\PropertyRewardController::class, 'apiDetails'])->name('reward.details');
        
        Route::get('/export', [App\Http\Controllers\PropertyGamificationController::class, 'export'])->name('export');
        Route::get('/export-leaderboard', [App\Http\Controllers\PropertyLeaderboardController::class, 'apiExport'])->name('export.leaderboard');
    });
});
