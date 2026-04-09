<?php

namespace App\Services;

use App\Repositories\Contracts\PropertyRepositoryInterface;
use Carbon\Carbon;

class MarketService
{
    protected $propertyRepository;

    public function __construct(PropertyRepositoryInterface $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function getMarketMetrics(Carbon $startDate, Carbon $endDate, ?string $marketArea = null): array
    {
        return $this->propertyRepository->getMarketMetrics($startDate, $endDate, $marketArea);
    }
}
