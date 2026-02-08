<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InvoiceRepositoryInterface
{
    public function all(array $filters = []): Collection;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function findById(int $id): ?Invoice;
    public function findByInvoiceNumber(string $invoiceNumber): ?Invoice;
    public function create(array $data): Invoice;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function restore(int $id): bool;
    public function forceDelete(int $id): bool;
    
    // Financial Methods
    public function getTotalRevenue(array $filters = []): float;
    public function getOutstandingAmount(array $filters = []): float;
    public function getOverdueInvoicesList(array $filters = []): Collection;
    public function getInvoicesByProperty(int $propertyId): Collection;
    public function getInvoicesByCompany(int $companyId): Collection;
    public function getInvoicesByClient(int $clientId): Collection;
    public function getInvoicesByAgent(int $agentId): Collection;
    
    // Status Methods
    public function getDraftInvoices(): Collection;
    public function getPendingInvoices(): Collection;
    public function getPaidInvoices(): Collection;
    public function getOverdueInvoices(): Collection;
    
    // Date Range Methods
    public function getInvoicesByDateRange(string $startDate, string $endDate): Collection;
    public function getInvoicesDueIn(int $days): Collection;
    
    // Statistics
    public function getInvoiceStats(array $filters = []): array;
    public function getRevenueByMonth(int $year): array;
    public function getRevenueByProperty(int $propertyId): array;
    public function getRevenueByCompany(int $companyId): array;
}
