<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function getPaymentsByDateRange(string $startDate, string $endDate): Collection;
    public function getPaymentsByStatus(string $status): Collection;
    public function getPaymentsByMethod(string $method): Collection;
    public function getPaymentsByProperty(int $propertyId): Collection;
    public function getPaymentsByClient(int $clientId): Collection;
    public function getPaymentsByInvoice(int $invoiceId): Collection;
    public function getTotalRevenue(array $filters = []): float;
}
