<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }

    public function getActiveClients(int $limit = 10): Collection
    {
        return $this->model->where('status', 'active')
                           ->orderBy('created_at', 'desc')
                           ->take($limit)
                           ->get();
    }

    public function searchClients(string $query, int $limit = 20): Collection
    {
        return $this->model->where('name', 'like', "%{$query}%")
                           ->orWhere('email', 'like', "%{$query}%")
                           ->orWhere('phone', 'like', "%{$query}%")
                           ->orWhere('company_name', 'like', "%{$query}%")
                           ->take($limit)
                           ->get();
    }

    public function getClientsByAgent(int $agentId, int $limit = 20): Collection
    {
        return $this->model->where('agent_id', $agentId)
                           ->orderBy('created_at', 'desc')
                           ->take($limit)
                           ->get();
    }

    public function getClientsByProperty(int $propertyId): Collection
    {
        // Assuming there is a pivot table client_property or relationship via invoices/payments
        // Since Client model has 'properties' BelongsToMany relationship
        return $this->model->whereHas('properties', function ($q) use ($propertyId) {
            $q->where('property_id', $propertyId);
        })->get();
    }

    public function getClientTransactions(int $clientId): Collection
    {
        return $this->model->find($clientId)
                           ->payments()
                           ->with(['invoice', 'property'])
                           ->latest()
                           ->get();
    }

    public function getClientInvoices(int $clientId): Collection
    {
        return $this->model->find($clientId)
                           ->invoices()
                           ->with(['property', 'payments'])
                           ->latest()
                           ->get();
    }
}
