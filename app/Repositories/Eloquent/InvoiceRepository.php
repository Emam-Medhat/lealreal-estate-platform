<?php

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
{
    /**
     * InvoiceRepository constructor.
     *
     * @param Invoice $model
     */
    public function __construct(Invoice $model)
    {
        parent::__construct($model);
    }

    public function findByInvoiceNumber(string $invoiceNumber): ?Invoice
    {
        return $this->model->where('invoice_number', $invoiceNumber)->first();
    }
    
    // Financial Methods
    public function getTotalRevenue(array $filters = []): float
    {
        return $this->applyFilters($filters)
            ->where('status', 'paid')
            ->sum('total_amount');
    }

    public function getOutstandingAmount(array $filters = []): float
    {
        return $this->applyFilters($filters)
            ->whereIn('status', ['pending', 'overdue'])
            ->sum('total_amount');
    }

    public function getOverdueInvoicesList(array $filters = []): Collection
    {
        return $this->applyFilters($filters)
            ->where('status', 'overdue')
            ->where('due_date', '<', now())
            ->get();
    }

    public function getInvoicesByProperty(int $propertyId): Collection
    {
        return $this->model->where('property_id', $propertyId)->get();
    }

    public function getInvoicesByCompany(int $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)->get();
    }

    public function getInvoicesByClient(int $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)->get();
    }

    public function getInvoicesByAgent(int $agentId): Collection
    {
        return $this->model->where('agent_id', $agentId)->get();
    }
    
    // Status Methods
    public function getDraftInvoices(): Collection
    {
        return $this->model->where('status', 'draft')->get();
    }

    public function getPendingInvoices(): Collection
    {
        return $this->model->where('status', 'pending')->get();
    }

    public function getPaidInvoices(): Collection
    {
        return $this->model->where('status', 'paid')->get();
    }

    public function getOverdueInvoices(): Collection
    {
        return $this->model->where('status', 'overdue')
            ->where('due_date', '<', now())
            ->get();
    }
    
    // Date Range Methods
    public function getInvoicesByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])->get();
    }

    public function getInvoicesDueIn(int $days): Collection
    {
        return $this->model->where('due_date', '<=', now()->addDays($days))
            ->where('status', '!=', 'paid')
            ->get();
    }
    
    // Statistics
    public function getInvoiceStats(array $filters = []): array
    {
        $query = $this->applyFilters($filters);
        
        return [
            'total' => $query->count(),
            'draft' => $query->where('status', 'draft')->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'paid' => $query->where('status', 'paid')->count(),
            'overdue' => $query->where('status', 'overdue')->count(),
            'total_amount' => $query->sum('total_amount'),
            'paid_amount' => $query->where('status', 'paid')->sum('total_amount'),
            'outstanding_amount' => $query->whereIn('status', ['pending', 'overdue'])->sum('total_amount'),
        ];
    }

    public function getRevenueByMonth(int $year): array
    {
        return $this->model->whereYear('created_at', $year)
            ->where('status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();
    }

    public function getRevenueByProperty(int $propertyId): array
    {
        return $this->model->where('property_id', $propertyId)
            ->where('status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();
    }

    public function getRevenueByCompany(int $companyId): array
    {
        return $this->model->where('company_id', $companyId)
            ->where('status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();
    }

    private function applyFilters(array $filters): Builder
    {
        $query = $this->model->newQuery();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }

        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }
}
