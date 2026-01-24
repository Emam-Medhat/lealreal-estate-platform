<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPhaseController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\ProjectMilestoneController;
use App\Http\Controllers\ProjectTeamController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectBudgetController;
use App\Http\Controllers\ProjectExpenseController;
use App\Http\Controllers\ProjectRiskController;
use App\Http\Controllers\ProjectNoteController;
use Illuminate\Support\Facades\Route;

// Projects Management Routes
Route::middleware(['auth'])->prefix('projects')->name('projects.')->group(function () {
    
    // Main Project Routes
    Route::get('/dashboard', [ProjectController::class, 'dashboard'])->name('dashboard');
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/create', [ProjectController::class, 'create'])->name('create');
    Route::post('/', [ProjectController::class, 'store'])->name('store');
    Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
    Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
    Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
    Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
    Route::post('/{project}/duplicate', [ProjectController::class, 'duplicate'])->name('duplicate');
    Route::post('/{project}/archive', [ProjectController::class, 'archive'])->name('archive');
    Route::post('/{project}/restore', [ProjectController::class, 'restore'])->name('restore');
    Route::get('/{project}/stats', [ProjectController::class, 'getStats'])->name('stats');
    Route::get('/{project}/timeline', [ProjectController::class, 'getTimeline'])->name('timeline');
    Route::get('/{project}/gantt', [ProjectController::class, 'gantt'])->name('gantt');
    Route::get('/gantt', [ProjectController::class, 'ganttDashboard'])->name('gantt.dashboard');
    Route::get('/{project}/kanban', [ProjectController::class, 'kanban'])->name('kanban');
    Route::get('/export', [ProjectController::class, 'export'])->name('export');
    
    // Phases Dashboard (without project parameter)
    Route::get('/phases', [ProjectPhaseController::class, 'dashboard'])->name('phases.dashboard');
    
    // Project Phases Routes
    Route::prefix('{project}/phases')->name('phases.')->group(function () {
        Route::get('/', [ProjectPhaseController::class, 'index'])->name('index');
        Route::get('/create', [ProjectPhaseController::class, 'create'])->name('create');
        Route::post('/', [ProjectPhaseController::class, 'store'])->name('store');
        Route::get('/{phase}', [ProjectPhaseController::class, 'show'])->name('show');
        Route::get('/{phase}/edit', [ProjectPhaseController::class, 'edit'])->name('edit');
        Route::put('/{phase}', [ProjectPhaseController::class, 'update'])->name('update');
        Route::delete('/{phase}', [ProjectPhaseController::class, 'destroy'])->name('destroy');
        Route::post('/{phase}/toggle-status', [ProjectPhaseController::class, 'toggleStatus'])->name('toggleStatus');
        Route::post('/{phase}/update-progress', [ProjectPhaseController::class, 'updateProgress'])->name('updateProgress');
        Route::get('/{phase}/timeline', [ProjectPhaseController::class, 'getTimeline'])->name('timeline');
        Route::get('/{phase}/tasks', [ProjectPhaseController::class, 'getTasks'])->name('tasks');
    });
    
    // Project Tasks Routes
    Route::prefix('{project}/tasks')->name('tasks.')->group(function () {
        Route::get('/', [ProjectTaskController::class, 'index'])->name('index');
        Route::get('/create', [ProjectTaskController::class, 'create'])->name('create');
        Route::post('/', [ProjectTaskController::class, 'store'])->name('store');
        Route::get('/{task}', [ProjectTaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [ProjectTaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [ProjectTaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [ProjectTaskController::class, 'destroy'])->name('destroy');
        Route::post('/{task}/toggle-status', [ProjectTaskController::class, 'toggleStatus'])->name('toggleStatus');
        Route::post('/{task}/update-progress', [ProjectTaskController::class, 'updateProgress'])->name('updateProgress');
        Route::post('/{task}/assign', [ProjectTaskController::class, 'assign'])->name('assign');
        Route::post('/{task}/unassign', [ProjectTaskController::class, 'unassign'])->name('unassign');
        Route::post('/{task}/add-dependency', [ProjectTaskController::class, 'addDependency'])->name('addDependency');
        Route::post('/{task}/remove-dependency', [ProjectTaskController::class, 'removeDependency'])->name('removeDependency');
        Route::post('/{task}/add-comment', [ProjectTaskController::class, 'addComment'])->name('addComment');
        Route::post('/{task}/add-time-log', [ProjectTaskController::class, 'addTimeLog'])->name('addTimeLog');
        Route::post('/{task}/upload-file', [ProjectTaskController::class, 'uploadFile'])->name('uploadFile');
        Route::get('/{task}/details', [ProjectTaskController::class, 'getDetails'])->name('details');
        Route::post('/{task}/status', [ProjectTaskController::class, 'updateStatus'])->name('status');
        Route::get('/kanban-data', [ProjectTaskController::class, 'getKanbanData'])->name('kanbanData');
    });
    
    // Milestones Dashboard (without project parameter)
    Route::get('/milestones', [ProjectMilestoneController::class, 'dashboard'])->name('milestones.dashboard');
    
    // Project Milestones Routes
    Route::prefix('{project}/milestones')->name('milestones.')->group(function () {
        Route::get('/', [ProjectMilestoneController::class, 'index'])->name('index');
        Route::get('/create', [ProjectMilestoneController::class, 'create'])->name('create');
        Route::post('/', [ProjectMilestoneController::class, 'store'])->name('store');
        Route::get('/{milestone}', [ProjectMilestoneController::class, 'show'])->name('show');
        Route::get('/{milestone}/edit', [ProjectMilestoneController::class, 'edit'])->name('edit');
        Route::put('/{milestone}', [ProjectMilestoneController::class, 'update'])->name('update');
        Route::delete('/{milestone}', [ProjectMilestoneController::class, 'destroy'])->name('destroy');
        Route::post('/{milestone}/toggle-status', [ProjectMilestoneController::class, 'toggleStatus'])->name('toggleStatus');
        Route::post('/{milestone}/complete', [ProjectMilestoneController::class, 'complete'])->name('complete');
        Route::post('/{milestone}/add-task', [ProjectMilestoneController::class, 'addTask'])->name('addTask');
        Route::post('/{milestone}/remove-task', [ProjectMilestoneController::class, 'removeTask'])->name('removeTask');
        Route::post('/{milestone}/add-deliverable', [ProjectMilestoneController::class, 'addDeliverable'])->name('addDeliverable');
        Route::post('/{milestone}/remove-deliverable', [ProjectMilestoneController::class, 'removeDeliverable'])->name('removeDeliverable');
        Route::get('/upcoming', [ProjectMilestoneController::class, 'getUpcoming'])->name('upcoming');
        Route::get('/overdue', [ProjectMilestoneController::class, 'getOverdue'])->name('overdue');
    });
    
    // Project Teams Routes
    Route::prefix('{project}/teams')->name('teams.')->group(function () {
        Route::get('/', [ProjectTeamController::class, 'index'])->name('index');
        Route::get('/create', [ProjectTeamController::class, 'create'])->name('create');
        Route::post('/', [ProjectTeamController::class, 'store'])->name('store');
        Route::get('/{team}', [ProjectTeamController::class, 'show'])->name('show');
        Route::get('/{team}/edit', [ProjectTeamController::class, 'edit'])->name('edit');
        Route::put('/{team}', [ProjectTeamController::class, 'update'])->name('update');
        Route::delete('/{team}', [ProjectTeamController::class, 'destroy'])->name('destroy');
        Route::post('/{team}/add-member', [ProjectTeamController::class, 'addMember'])->name('addMember');
        Route::post('/{team}/remove-member', [ProjectTeamController::class, 'removeMember'])->name('removeMember');
        Route::post('/{team}/update-member-role', [ProjectTeamController::class, 'updateMemberRole'])->name('updateMemberRole');
        Route::get('/{team}/members', [ProjectTeamController::class, 'getMembers'])->name('members');
        Route::get('/available-users', [ProjectTeamController::class, 'getAvailableUsers'])->name('availableUsers');
    });
    
    // Project Members Routes
    Route::prefix('{project}/members')->name('members.')->group(function () {
        Route::get('/', [ProjectMemberController::class, 'index'])->name('index');
        Route::get('/create', [ProjectMemberController::class, 'create'])->name('create');
        Route::post('/', [ProjectMemberController::class, 'store'])->name('store');
        Route::get('/{member}', [ProjectMemberController::class, 'show'])->name('show');
        Route::get('/{member}/edit', [ProjectMemberController::class, 'edit'])->name('edit');
        Route::put('/{member}', [ProjectMemberController::class, 'update'])->name('update');
        Route::delete('/{member}', [ProjectMemberController::class, 'destroy'])->name('destroy');
        Route::post('/{member}/toggle-status', [ProjectMemberController::class, 'toggleStatus'])->name('toggleStatus');
        Route::post('/{member}/update-role', [ProjectMemberController::class, 'updateRole'])->name('updateRole');
        Route::get('/{member}/time-logs', [ProjectMemberController::class, 'getTimeLogs'])->name('timeLogs');
        Route::post('/{member}/add-time-log', [ProjectMemberController::class, 'addTimeLog'])->name('addTimeLog');
        Route::get('/stats', [ProjectMemberController::class, 'getStats'])->name('stats');
    });
    
    // Project Documents Routes
    Route::prefix('{project}/documents')->name('documents.')->group(function () {
        Route::get('/', [ProjectDocumentController::class, 'index'])->name('index');
        Route::get('/create', [ProjectDocumentController::class, 'create'])->name('create');
        Route::post('/', [ProjectDocumentController::class, 'store'])->name('store');
        Route::get('/{document}', [ProjectDocumentController::class, 'show'])->name('show');
        Route::get('/{document}/edit', [ProjectDocumentController::class, 'edit'])->name('edit');
        Route::put('/{document}', [ProjectDocumentController::class, 'update'])->name('update');
        Route::delete('/{document}', [ProjectDocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download', [ProjectDocumentController::class, 'download'])->name('download');
        Route::post('/{document}/approve', [ProjectDocumentController::class, 'approve'])->name('approve');
        Route::post('/{document}/reject', [ProjectDocumentController::class, 'reject'])->name('reject');
        Route::post('/{document}/upload', [ProjectDocumentController::class, 'upload'])->name('upload');
        Route::post('/{document}/create-version', [ProjectDocumentController::class, 'createVersion'])->name('createVersion');
        Route::get('/{document}/versions', [ProjectDocumentController::class, 'getVersions'])->name('versions');
        Route::get('/{document}/preview', [ProjectDocumentController::class, 'preview'])->name('preview');
        Route::get('/search', [ProjectDocumentController::class, 'search'])->name('search');
    });
    
    // Project Budgets Routes
    Route::prefix('{project}/budgets')->name('budgets.')->group(function () {
        Route::get('/', [ProjectBudgetController::class, 'index'])->name('index');
        Route::get('/create', [ProjectBudgetController::class, 'create'])->name('create');
        Route::post('/', [ProjectBudgetController::class, 'store'])->name('store');
        Route::get('/{budget}', [ProjectBudgetController::class, 'show'])->name('show');
        Route::get('/{budget}/edit', [ProjectBudgetController::class, 'edit'])->name('edit');
        Route::put('/{budget}', [ProjectBudgetController::class, 'update'])->name('update');
        Route::delete('/{budget}', [ProjectBudgetController::class, 'destroy'])->name('destroy');
        Route::post('/{budget}/add-expense', [ProjectBudgetController::class, 'addExpense'])->name('addExpense');
        Route::post('/{budget}/add-income', [ProjectBudgetController::class, 'addIncome'])->name('addIncome');
        Route::get('/{budget}/expenses', [ProjectBudgetController::class, 'getExpenses'])->name('expenses');
        Route::get('/{budget}/incomes', [ProjectBudgetController::class, 'getIncomes'])->name('incomes');
        Route::get('/{budget}/utilization', [ProjectBudgetController::class, 'getUtilization'])->name('utilization');
        Route::get('/{budget}/forecast', [ProjectBudgetController::class, 'getForecast'])->name('forecast');
    });
    
    // Project Expenses Routes
    Route::prefix('{project}/expenses')->name('expenses.')->group(function () {
        Route::get('/', [ProjectExpenseController::class, 'index'])->name('index');
        Route::get('/create', [ProjectExpenseController::class, 'create'])->name('create');
        Route::post('/', [ProjectExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}', [ProjectExpenseController::class, 'show'])->name('show');
        Route::get('/{expense}/edit', [ProjectExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}', [ProjectExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}', [ProjectExpenseController::class, 'destroy'])->name('destroy');
        Route::post('/{expense}/approve', [ProjectExpenseController::class, 'approve'])->name('approve');
        Route::post('/{expense}/reject', [ProjectExpenseController::class, 'reject'])->name('reject');
        Route::post('/{expense}/process-payment', [ProjectExpenseController::class, 'processPayment'])->name('processPayment');
        Route::post('/{expense}/upload-receipt', [ProjectExpenseController::class, 'uploadReceipt'])->name('uploadReceipt');
        Route::get('/{expense}/receipt', [ProjectExpenseController::class, 'downloadReceipt'])->name('receipt');
        Route::get('/by-category', [ProjectExpenseController::class, 'getByCategory'])->name('byCategory');
        Route::get('/summary', [ProjectExpenseController::class, 'getSummary'])->name('summary');
    });
    
    // Project Risks Routes
    Route::prefix('{project}/risks')->name('risks.')->group(function () {
        Route::get('/', [ProjectRiskController::class, 'index'])->name('index');
        Route::get('/create', [ProjectRiskController::class, 'create'])->name('create');
        Route::post('/', [ProjectRiskController::class, 'store'])->name('store');
        Route::get('/{risk}', [ProjectRiskController::class, 'show'])->name('show');
        Route::get('/{risk}/edit', [ProjectRiskController::class, 'edit'])->name('edit');
        Route::put('/{risk}', [ProjectRiskController::class, 'update'])->name('update');
        Route::delete('/{risk}', [ProjectRiskController::class, 'destroy'])->name('destroy');
        Route::post('/{risk}/toggle-status', [ProjectRiskController::class, 'toggleStatus'])->name('toggleStatus');
        Route::post('/{risk}/update-level', [ProjectRiskController::class, 'updateLevel'])->name('updateLevel');
        Route::post('/{risk}/add-action', [ProjectRiskController::class, 'addAction'])->name('addAction');
        Route::post('/{risk}/remove-action', [ProjectRiskController::class, 'removeAction'])->name('removeAction');
        Route::post('/{risk}/mitigate', [ProjectRiskController::class, 'mitigate'])->name('mitigate');
        Route::get('/by-level', [ProjectRiskController::class, 'getByLevel'])->name('byLevel');
        Route::get('/by-status', [ProjectRiskController::class, 'getByStatus'])->name('byStatus');
        Route::get('/matrix', [ProjectRiskController::class, 'getMatrix'])->name('matrix');
        Route::get('/assessment', [ProjectRiskController::class, 'getAssessment'])->name('assessment');
    });
    
    // Project Notes Routes
    Route::prefix('{project}/notes')->name('notes.')->group(function () {
        Route::get('/', [ProjectNoteController::class, 'index'])->name('index');
        Route::get('/create', [ProjectNoteController::class, 'create'])->name('create');
        Route::post('/', [ProjectNoteController::class, 'store'])->name('store');
        Route::get('/{note}', [ProjectNoteController::class, 'show'])->name('show');
        Route::get('/{note}/edit', [ProjectNoteController::class, 'edit'])->name('edit');
        Route::put('/{note}', [ProjectNoteController::class, 'update'])->name('update');
        Route::delete('/{note}', [ProjectNoteController::class, 'destroy'])->name('destroy');
        Route::post('/{note}/toggle-visibility', [ProjectNoteController::class, 'toggleVisibility'])->name('toggleVisibility');
        Route::post('/{note}/pin', [ProjectNoteController::class, 'pin'])->name('pin');
        Route::post('/{note}/unpin', [ProjectNoteController::class, 'unpin'])->name('unpin');
        Route::get('/pinned', [ProjectNoteController::class, 'getPinned'])->name('pinned');
        Route::get('/search', [ProjectNoteController::class, 'search'])->name('search');
        Route::get('/by-visibility', [ProjectNoteController::class, 'getByVisibility'])->name('byVisibility');
    });
    
    // API Routes for AJAX requests
    Route::prefix('{project}/api')->name('api.')->group(function () {
        Route::get('/stats', [ProjectController::class, 'getApiStats'])->name('stats');
        Route::get('/progress', [ProjectController::class, 'getApiProgress'])->name('progress');
        Route::get('/timeline-data', [ProjectController::class, 'getApiTimelineData'])->name('timelineData');
        Route::get('/team-performance', [ProjectController::class, 'getApiTeamPerformance'])->name('teamPerformance');
        Route::get('/budget-overview', [ProjectController::class, 'getApiBudgetOverview'])->name('budgetOverview');
        Route::get('/risk-summary', [ProjectController::class, 'getApiRiskSummary'])->name('riskSummary');
        Route::get('/recent-activities', [ProjectController::class, 'getApiRecentActivities'])->name('recentActivities');
        Route::get('/upcoming-deadlines', [ProjectController::class, 'getApiUpcomingDeadlines'])->name('upcomingDeadlines');
    });
});
