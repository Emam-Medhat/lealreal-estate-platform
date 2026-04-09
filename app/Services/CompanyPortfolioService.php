<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\CompanyBranch;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CompanyPortfolioService
{
    /**
     * Get company portfolio value
     */
    public function getPortfolioValue(int $companyId): array
    {
        $company = Company::findOrFail($companyId);

        // Get all properties for the company
        $properties = Property::where('company_id', $companyId)
            ->with(['images', 'documents'])
            ->get();

        $totalProperties = $properties->count();
        $totalValue = 0;
        $totalArea = 0;
        $averagePricePerSqm = 0;

        $propertyTypes = [];
        $statusBreakdown = [
            'for_sale' => 0,
            'for_rent' => 0,
            'sold' => 0,
            'pending' => 0,
            'off_market' => 0,
            'under_contract' => 0,
            'valuation_updated' => 0
        ];

        foreach ($properties as $property) {
            $propertyValue = $this->calculatePropertyValue($property);
            $totalValue += $propertyValue;
            $totalArea += $property['area'] ?? 0;

            if (isset($propertyTypes[$property->type])) {
                $propertyTypes[$property->type]++;
            } else {
                $propertyTypes[$property->type] = 1;
            }

            if ($property->status === 'sold') {
                $statusBreakdown['sold']++;
            } elseif ($property->status === 'for_rent') {
                $statusBreakdown['for_rent']++;
            } elseif ($property->status === 'pending') {
                $statusBreakdown['pending']++;
            } elseif ($property->status === 'off_market') {
                $statusBreakdown['off_market']++;
            } elseif ($property->status === 'under_contract') {
                $statusBreakdown['under_contract']++;
            } elseif ($property->status === 'valuation_updated') {
                $statusBreakdown['valuation_updated']++;
            }
        }

        $averagePricePerSqm = $totalArea > 0 ? $totalValue / $totalArea : 0;

        return [
            'total_properties' => $totalProperties,
            'total_value' => $totalValue,
            'total_area' => $totalArea,
            'average_price_per_sqm' => $averagePricePerSqm,
            'property_types' => $propertyTypes,
            'status_breakdown' => $statusBreakdown,
            'portfolio_growth' => $this->calculatePortfolioGrowth($companyId, $totalValue)
        ];
    }

    /**
     * Get portfolio performance
     */
    public function getPortfolioPerformance(int $companyId, string $period = 'year'): array
    {
        $company = Company::findOrFail($companyId);

        // Get date range
        $dateRange = $this->getDateRange($period);

        // Get properties for the period
        $properties = Property::where('company_id', $companyId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get(['id', 'type', 'price', 'status', 'created_at']);

        $currentProperties = $properties->where('status', 'active')->count();
        $soldProperties = $properties->where('status', 'sold')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get();

        $previousProperties = Property::where('company_id', $companyId)
            ->whereBetween('created_at', [
                now()->subYear()->startOfYear()->subYear(),
                now()->subYear()->endOfYear()
            ])
            ->get();

        $currentValue = $properties->sum('price');
        $previousValue = $previousProperties->sum('price');

        return [
            'period' => $period,
            'date_range' => $dateRange,
            'current_portfolio' => [
                'total_properties' => $currentProperties,
                'total_value' => $currentValue,
                'average_price' => $currentProperties > 0 ? $currentValue / $currentProperties : 0
            ],
            'portfolio_growth' => [
                'value_growth' => $currentValue - $previousValue,
                'value_growth_percentage' => $previousValue > 0 ? (($currentValue - $previousValue) / $previousValue) * 100 : 0,
                'property_count_growth' => $currentProperties - count($previousProperties)
            ]
        ];
    }

    /**
     * Calculate individual property value
     */
    private function calculatePropertyValue($property): float
    {
        $baseValue = $property->price ?? 0;

        // Add value for features
        if ($property->has_parking) {
            $baseValue += $baseValue * 0.05; // 5% of base value
        }

        if ($property->has_pool) {
            $baseValue += $baseValue * 0.03; // 3% of base value
        }

        if ($property->has_garden) {
            $baseValue += $baseValue * 0.02; // 2% of base value
        }

        if ($property->has_security_system) {
            $baseValue += $baseValue * 0.04; // 4% of base value
        }

        if ($property->has_elevator) {
            $baseValue += $baseValue * 0.06; // 6% of base value
        }

        if ($property->has_balcony) {
            $baseValue += $baseValue * 0.08; // 8% of base value
        }

        if ($property->has_renovation_potential) {
            $baseValue += $baseValue * 0.10; // 10% of base value
        }

        if ($property->is_furnished) {
            $baseValue += $baseValue * 0.15; // 15% of base value
        }

        if ($property->has_smart_home) {
            $baseValue += $baseValue * 0.12; // 12% of base value
        }

        if ($property->has_energy_efficient) {
            $baseValue += $baseValue * 0.07; // 7% of base value
        }

        return $baseValue;
    }

    /**
     * Get date range for period
     */
    private function getDateRange(string $period): array
    {
        $now = now();

        switch ($period) {
            case 'week':
                return [
                    'start' => $now->subWeek()->startOfWeek()->toDateString(),
                    'end' => $now->endOfWeek()->toDateString()
                ];
            case 'month':
                return [
                    'start' => $now->subMonth()->startOfMonth()->toDateString(),
                    'end' => $now->endOfMonth()->toDateString()
                ];
            case 'quarter':
                return [
                    'start' => $now->subQuarter()->startOfMonth()->toDateString(),
                    'end' => $now->endOfQuarter()->toDateString()
                ];
            case 'year':
                return [
                    'start' => $now->subYear()->startOfYear()->toDateString(),
                    'end' => $now->endOfYear()->toDateString()
                ];
            default:
                return [
                    'start' => $now->subMonth()->startOfMonth()->toDateString(),
                    'end' => $now()->endOfMonth()->toDateString()
                ];
        }
    }

    /**
     * Calculate portfolio growth
     */
    private function calculatePortfolioGrowth(int $companyId, float $currentValue): array
    {
        // Get previous period data
        $previousPeriod = now()->subYear()->startOfYear();
        $previousProperties = Property::where('company_id', $companyId)
            ->where('created_at', '<', $previousPeriod)
            ->get();

        $previousValue = $previousProperties->sum('price');
        $previousCount = $previousProperties->count();

        $valueGrowth = $previousValue > 0 ? (($currentValue - $previousValue) / $previousValue) * 100 : 0;
        $countGrowth = $previousCount > 0 ? (($currentProperties - $previousCount) / $previousCount) * 100 : 0;

        return [
            'value_growth' => $valueGrowth,
            'value_growth_percentage' => $valueGrowth,
            'count_growth' => $countGrowth,
            'current_value' => $currentValue,
            'previous_value' => $previousValue,
            'current_count' => Property::where('company_id', $companyId)->count(),
            'previous_count' => $previousCount
        ];
    }
}
