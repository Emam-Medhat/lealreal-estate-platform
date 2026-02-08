<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    protected Invoice $model;

    public function __construct(Invoice $invoice)
    {
        $this->model = $invoice;
    }

    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        $this->applyFilters($query, $filters);

        return $query->with(['client', 'property', 'company', 'agent'])->get();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        $this->applyFilters($query, $filters);

        return $query->with(['client', 'property', 'company', 'agent'])
                    ->latest('created_at')
                    ->paginate($perPage);
    }

    public function findById(int $id): ?Invoice
    {
        return $this->model->with(['client', 'property', 'company', 'agent', 'payments'])
                           ->find($id);
    }

    public function findByInvoiceNumber(string $invoiceNumber): ?Invoice
    {
        return $this->model->with(['client', 'property', 'company', 'agent', 'payments'])
                           ->where('invoice_number', $invoiceNumber)
                           ->first();
    }

    public function create(array $data): Invoice
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->find($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->find($id)->delete();
    }

    public function restore(int $id): bool
    {
        return $this->model->withTrashed()->find($id)->restore();
    }

    public function forceDelete(int $id): bool
    {
        return $this->model->withTrashed()->find($id)->forceDelete();
    }

    // Financial Methods
    public function getTotalRevenue(array $filters = []): float
    {
        $query = $this->model->newQuery();

        $this->applyFilters($query, $filters);

        return $query->where('status', 'paid')->sum('total') ?? 0;
    }

    public function getOutstandingAmount(array $filters = []): float
    {
        $query = $this->model->newQuery();

        $this->applyFilters($query, $filters);

        $invoices = $query->where('status', '!=', 'paid')->get();
        
        $total = 0;
        foreach ($invoices as $invoice) {
            $total += $invoice->getOutstandingAmountAttribute();
        }
        
        return $total;
    }

    public function getOverdueInvoicesList(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        $this->applyFilters($query, $filters);

        return $query->overdue()
                    ->with(['client', 'property', 'company'])
                    ->get();
    }

    public function getInvoicesByProperty(int $propertyId): Collection
    {
        return $this->model->where('property_id', $propertyId)
                           ->with(['client', 'company', 'agent'])
                           ->get();
    }

    public function getInvoicesByCompany(int $companyId): Collection
    {
        return $this->model->where('company_id', $companyId)
                           ->with(['client', 'property', 'agent'])
                           ->get();
    }

    public function getInvoicesByClient(int $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)
                           ->with(['property', 'company', 'agent'])
                           ->get();
    }

    public function getInvoicesByAgent(int $agentId): Collection
    {
        return $this->model->where('agent_id', $agentId)
                           ->with(['client', 'property', 'company'])
                           ->get();
    }

    // Status Methods
    public function getDraftInvoices(): Collection
    {
        return $this->model->draft()
                           ->with(['client', 'property', 'company'])
                           ->get();
    }

    public function getPendingInvoices(): Collection
    {
        return $this->model->pending()
                           ->with(['client', 'property', 'company'])
                           ->get();
    }

    public function getPaidInvoices(): Collection
    {
        return $this->model->paid()
                           ->with(['client', 'property', 'company'])
                           ->get();
    }

    public function getOverdueInvoices(): Collection
    {
        return $this->model->overdue()
                           ->with(['client', 'property', 'company'])
                           ->get();
    }

    // Date Range Methods
    public function getInvoicesByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])
                           ->with(['client', 'property', 'company'])
                           ->get();
    }

    public function getInvoicesDueIn(int $days): Collection
    {
        return $this->model->dueIn($days)
                           ->with(['client', 'property', 'company'])
                           ->get();
    }

    // Statistics
    public function getInvoiceStats(array $filters = []): array
    {
        $query = $this->model->newQuery();

        $this->applyFilters($query, $filters);

        $total = $query->count();
        $draft = $query->clone()->draft()->count();
        $pending = $query->clone()->pending()->count();
        $paid = $query->clone()->paid()->count();
        $overdue = $query->clone()->overdue()->count();

        return [
            'total' => $total,
            'draft' => $draft,
            'pending' => $pending,
            'paid' => $paid,
            'overdue' => $overdue,
            'revenue' => $this->getTotalRevenue($filters),
            'outstanding' => $this->getOutstandingAmount($filters),
        ];
    }

    public function getRevenueByMonth(int $year): array
    {
        $revenue = $this->model->where('status', 'paid')
                              ->whereYear('paid_date', $year)
                              ->selectRaw('MONTH(paid_date) as month, SUM(total) as revenue')
                              ->groupBy('month')
                              ->orderBy('month')
                              ->pluck('revenue', 'month')
                              ->toArray();

        $monthlyRevenue = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyRevenue[$month] = $revenue[$month] ?? 0;
        }

        return $monthlyRevenue;
    }

    public function getRevenueByProperty(int $propertyId): array
    {
        $invoices = $this->getInvoicesByProperty($propertyId);
        
        $paidRevenue = $invoices->where('status', 'paid')->sum('total');
        $pendingRevenue = $invoices->where('status', 'pending')->sum('total');
        $totalRevenue = $invoices->sum('total');

        return [
            'property_id' => $propertyId,
            'total_revenue' => $totalRevenue,
            'paid_revenue' => $paidRevenue,
            'pending_revenue' => $pendingRevenue,
            'total_invoices' => $invoices->count(),
            'paid_invoices' => $invoices->where('status', 'paid')->count(),
            'pending_invoices' => $invoices->where('status', 'pending')->count(),
        ];
    }

    public function getRevenueByCompany(int $companyId): array
    {
        $invoices = $this->getInvoicesByCompany($companyId);
        
        $paidRevenue = $invoices->where('status', 'paid')->sum('total');
        $pendingRevenue = $invoices->where('status', 'pending')->sum('total');
        $totalRevenue = $invoices->sum('total');

        return [
            'company_id' => $companyId,
            'total_revenue' => $totalRevenue,
            'paid_revenue' => $paidRevenue,
            'pending_revenue' => $pendingRevenue,
            'total_invoices' => $invoices->count(),
            'paid_invoices' => $invoices->where('status', 'paid')->count(),
            'pending_invoices' => $invoices->where('status', 'pending')->count(),
        ];
    }

    // Private Methods
    private function applyFilters(Builder $query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }

        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['due_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_from']);
        }

        if (isset($filters['due_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_to']);
        }

        if (isset($filters['min_amount'])) {
            $query->where('total', '>=', $filters['min_amount']);
        }

        if (isset($filters['max_amount'])) {
            $query->where('total', '<=', $filters['max_amount']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
    }
}
