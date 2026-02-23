<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClientRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveClients(int $limit = 10): Collection;
    public function searchClients(string $query, int $limit = 20): Collection;
    public function getClientsByAgent(int $agentId, int $limit = 20): Collection;
    public function getClientsByProperty(int $propertyId): Collection;
    public function getClientTransactions(int $clientId): Collection;
    public function getClientInvoices(int $clientId): Collection;
}
