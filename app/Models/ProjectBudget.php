<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectBudget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'total_budget',
        'currency',
        'budget_type',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ProjectExpense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(ProjectIncome::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getTotalExpenses()
    {
        return $this->expenses()->sum('amount');
    }

    public function getTotalIncome()
    {
        return $this->incomes()->sum('amount');
    }

    public function getRemainingBudget()
    {
        return $this->total_budget - $this->getTotalExpenses();
    }

    public function getBudgetUtilization()
    {
        if ($this->total_budget == 0) return 0;
        return round(($this->getTotalExpenses() / $this->total_budget) * 100, 2);
    }

    public function getNetBudget()
    {
        return $this->getTotalIncome() - $this->getTotalExpenses();
    }

    public function isOverBudget()
    {
        return $this->getTotalExpenses() > $this->total_budget;
    }

    public function getExpensesByCategory()
    {
        return $this->expenses()
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn($group) => $group->sum('amount'));
    }

    public function getMonthlyExpenses()
    {
        return $this->expenses()
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function getMonthlyIncome()
    {
        return $this->incomes()
            ->selectRaw('DATE_FORMAT(income_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
