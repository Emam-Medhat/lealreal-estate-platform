<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function getPaymentsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])
                           ->with(['client', 'property', 'invoice'])
                           ->get();
    }

    public function getPaymentsByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)
                           ->with(['client', 'property', 'invoice'])
                           ->get();
    }

    public function getPaymentsByMethod(string $method): Collection
    {
        return $this->model->where('payment_method', $method)
                           ->with(['client', 'property', 'invoice'])
                           ->get();
    }

    public function getPaymentsByProperty(int $propertyId): Collection
    {
        return $this->model->where('property_id', $propertyId)
                           ->with(['client', 'invoice'])
                           ->get();
    }

    public function getPaymentsByClient(int $clientId): Collection
    {
        return $this->model->where('client_id', $clientId)
                           ->with(['property', 'invoice'])
                           ->get();
    }

    public function getPaymentsByInvoice(int $invoiceId): Collection
    {
        return $this->model->where('invoice_id', $invoiceId)
                           ->with(['client'])
                           ->get();
    }

    public function getTotalRevenue(array $filters = []): float
    {
        $query = $this->model->newQuery();

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['property_id'])) {
            $query->where('property_id', $filters['property_id']);
        }

        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        return $query->where('status', 'completed')->sum('amount') ?? 0;
    }
}
