<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Client;
use App\Models\Agent;
use App\Models\Company;
use App\Models\PropertyOwnership;
use App\Models\PropertyTransaction;
use App\Models\AgentCommission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PropertyService
{
    /**
     * Create a new property
     */
    public function createProperty(array $data): Property
    {
        try {
            DB::beginTransaction();

            $property = Property::create($data);

            // Create initial ownership record if owner provided
            if (isset($data['owner_id'])) {
                PropertyOwnership::create([
                    'property_id' => $property->id,
                    'previous_owner_id' => null,
                    'new_owner_id' => $data['owner_id'],
                    'transfer_date' => now(),
                    'transfer_type' => 'initial',
                    'status' => 'completed',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            Log::info('Property created successfully', ['property_id' => $property->id]);

            return $property;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create property', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update an existing property
     */
    public function updateProperty(Property $property, array $data): Property
    {
        try {
            DB::beginTransaction();

            $property->update($data);

            DB::commit();

            Log::info('Property updated successfully', ['property_id' => $property->id]);

            return $property->refresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update property', ['property_id' => $property->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete a property
     */
    public function deleteProperty(Property $property): bool
    {
        try {
            DB::beginTransaction();

            // Check if property has active transactions or contracts
            if ($property->transactions()->where('status', '!=', 'completed')->exists()) {
                throw new \Exception('Cannot delete property with active transactions');
            }

            $property->delete();

            DB::commit();

            Log::info('Property deleted successfully', ['property_id' => $property->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete property', ['property_id' => $property->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Mark property as sold
     */
    public function markAsSold(Property $property, array $saleData): bool
    {
        try {
            DB::beginTransaction();

            $property->markAsSold($saleData);

            // Create ownership transfer record
            if (isset($saleData['buyer_id'])) {
                PropertyOwnership::create([
                    'property_id' => $property->id,
                    'previous_owner_id' => $property->owner_id,
                    'new_owner_id' => $saleData['buyer_id'],
                    'transfer_date' => $saleData['sale_date'] ?? now(),
                    'transfer_type' => 'sale',
                    'transfer_amount' => $saleData['sale_price'] ?? 0,
                    'status' => 'completed',
                    'created_by' => auth()->id(),
                ]);
            }

            // Update transaction status if exists
            if (isset($saleData['transaction_id'])) {
                $transaction = PropertyTransaction::find($saleData['transaction_id']);
                if ($transaction) {
                    $transaction->completeTransaction();
                }
            }

            DB::commit();

            Log::info('Property marked as sold', ['property_id' => $property->id]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark property as sold', ['property_id' => $property->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Transfer property ownership
     */
    public function transferOwnership(Property $property, int $newOwnerId, array $transferData): bool
    {
        try {
            DB::beginTransaction();

            $oldOwnerId = $property->owner_id;

            // Create ownership transfer record
            PropertyOwnership::create([
                'property_id' => $property->id,
                'previous_owner_id' => $oldOwnerId,
                'new_owner_id' => $newOwnerId,
                'transfer_date' => $transferData['transfer_date'] ?? now(),
                'transfer_type' => $transferData['transfer_type'] ?? 'transfer',
                'transfer_amount' => $transferData['transfer_amount'] ?? 0,
                'status' => 'pending',
                'agent_id' => $transferData['agent_id'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Update property owner
            $property->transferOwnership($newOwnerId);

            DB::commit();

            Log::info('Property ownership transferred', [
                'property_id' => $property->id,
                'from_owner' => $oldOwnerId,
                'to_owner' => $newOwnerId
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to transfer ownership', ['property_id' => $property->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get property statistics
     */
    public function getPropertyStatistics(array $filters = []): array
    {
        $query = Property::query();

        // Apply filters
        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }
        if (isset($filters['owner_id'])) {
            $query->where('owner_id', $filters['owner_id']);
        }
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $totalProperties = $query->count();
        $availableProperties = $query->where('status', 'available')->count();
        $soldProperties = $query->where('status', 'sold')->count();
        $rentedProperties = $query->where('status', 'rented')->count();
        $totalValue = $query->sum('price');
        $averagePrice = $query->avg('price');

        return [
            'total_properties' => $totalProperties,
            'available_properties' => $availableProperties,
            'sold_properties' => $soldProperties,
            'rented_properties' => $rentedProperties,
            'total_value' => $totalValue,
            'average_price' => $averagePrice,
            'availability_rate' => $totalProperties > 0 ? ($availableProperties / $totalProperties) * 100 : 0,
        ];
    }

    /**
     * Get available properties
     */
    public function getAvailableProperties(array $filters = []): Collection
    {
        $query = Property::where('status', 'available');

        // Apply filters
        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        if (isset($filters['min_area'])) {
            $query->where('area', '>=', $filters['min_area']);
        }
        if (isset($filters['max_area'])) {
            $query->where('area', '<=', $filters['max_area']);
        }

        return $query->with(['owner', 'agent', 'company'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get property ownership history
     */
    public function getOwnershipHistory(Property $property): Collection
    {
        return $property->ownershipHistory()
                      ->with(['previousOwner', 'newOwner', 'agent'])
                      ->orderBy('transfer_date', 'desc')
                      ->get();
    }

    /**
     * Get property transactions
     */
    public function getPropertyTransactions(Property $property): Collection
    {
        return $property->transactions()
                      ->with(['client', 'agent', 'company'])
                      ->orderBy('created_at', 'desc')
                      ->get();
    }

    /**
     * Get property commissions
     */
    public function getPropertyCommissions(Property $property): Collection
    {
        return $property->commissions()
                      ->with(['agent'])
                      ->orderBy('created_at', 'desc')
                      ->get();
    }

    /**
     * Calculate property ROI
     */
    public function calculateROI(Property $property): array
    {
        $totalInvestment = $property->purchase_price + $property->renovation_cost;
        $currentValue = $property->price;
        $totalIncome = $property->rental_income ?? 0;
        $totalExpenses = $property->maintenance_cost + $property->tax_cost;

        $roi = 0;
        if ($totalInvestment > 0) {
            $netProfit = ($currentValue - $totalInvestment) + ($totalIncome - $totalExpenses);
            $roi = ($netProfit / $totalInvestment) * 100;
        }

        return [
            'total_investment' => $totalInvestment,
            'current_value' => $currentValue,
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_profit' => ($currentValue - $totalInvestment) + ($totalIncome - $totalExpenses),
            'roi_percentage' => $roi,
        ];
    }

    /**
     * Get property performance metrics
     */
    public function getPropertyPerformance(Property $property): array
    {
        $daysOnMarket = $property->created_at->diffInDays(now());
        $viewsCount = $property->views_count ?? 0;
        $inquiriesCount = $property->inquiries_count ?? 0;
        $showingsCount = $property->showings_count ?? 0;

        return [
            'days_on_market' => $daysOnMarket,
            'views_count' => $viewsCount,
            'inquiries_count' => $inquiriesCount,
            'showings_count' => $showingsCount,
            'views_per_day' => $daysOnMarket > 0 ? $viewsCount / $daysOnMarket : 0,
            'inquiry_rate' => $viewsCount > 0 ? ($inquiriesCount / $viewsCount) * 100 : 0,
            'showing_rate' => $inquiriesCount > 0 ? ($showingsCount / $inquiriesCount) * 100 : 0,
        ];
    }

    /**
     * Get similar properties
     */
    public function getSimilarProperties(Property $property, int $limit = 5): Collection
    {
        return Property::where('id', '!=', $property->id)
                     ->where('type', $property->type)
                     ->where('status', 'available')
                     ->whereBetween('price', [
                         $property->price * 0.8,
                         $property->price * 1.2
                     ])
                     ->whereBetween('area', [
                         $property->area * 0.8,
                         $property->area * 1.2
                     ])
                     ->limit($limit)
                     ->with(['owner', 'agent'])
                     ->get();
    }

    /**
     * Search properties
     */
    public function searchProperties(array $criteria): Collection
    {
        $query = Property::query();

        // Text search
        if (isset($criteria['search'])) {
            $searchTerm = $criteria['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%")
                  ->orWhere('city', 'like', "%{$searchTerm}%")
                  ->orWhere('state', 'like', "%{$searchTerm}%");
            });
        }

        // Apply filters
        if (isset($criteria['type'])) {
            $query->where('type', $criteria['type']);
        }
        if (isset($criteria['status'])) {
            $query->where('status', $criteria['status']);
        }
        if (isset($criteria['company_id'])) {
            $query->where('company_id', $criteria['company_id']);
        }
        if (isset($criteria['agent_id'])) {
            $query->where('agent_id', $criteria['agent_id']);
        }
        if (isset($criteria['owner_id'])) {
            $query->where('owner_id', $criteria['owner_id']);
        }

        // Price range
        if (isset($criteria['min_price'])) {
            $query->where('price', '>=', $criteria['min_price']);
        }
        if (isset($criteria['max_price'])) {
            $query->where('price', '<=', $criteria['max_price']);
        }

        // Area range
        if (isset($criteria['min_area'])) {
            $query->where('area', '>=', $criteria['min_area']);
        }
        if (isset($criteria['max_area'])) {
            $query->where('area', '<=', $criteria['max_area']);
        }

        // Location
        if (isset($criteria['city'])) {
            $query->where('city', $criteria['city']);
        }
        if (isset($criteria['state'])) {
            $query->where('state', $criteria['state']);
        }
        if (isset($criteria['country'])) {
            $query->where('country', $criteria['country']);
        }

        // Features
        if (isset($criteria['bedrooms'])) {
            $query->where('bedrooms', $criteria['bedrooms']);
        }
        if (isset($criteria['bathrooms'])) {
            $query->where('bathrooms', $criteria['bathrooms']);
        }
        if (isset($criteria['garage'])) {
            $query->where('garage', $criteria['garage']);
        }
        if (isset($criteria['pool'])) {
            $query->where('pool', $criteria['pool']);
        }

        return $query->with(['owner', 'agent', 'company'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Generate property report
     */
    public function generatePropertyReport(array $filters = []): array
    {
        $query = Property::with(['owner', 'agent', 'company']);

        // Apply filters
        if (isset($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (isset($filters['agent_id'])) {
            $query->where('agent_id', $filters['agent_id']);
        }
        if (isset($filters['owner_id'])) {
            $query->where('owner_id', $filters['owner_id']);
        }
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $properties = $query->orderBy('created_at', 'desc')->get();

        return [
            'properties' => $properties,
            'summary' => $this->getPropertyStatistics($filters),
            'filters' => $filters
        ];
    }

    /**
     * Get agent property portfolio
     */
    public function getAgentPortfolio(Agent $agent): Collection
    {
        return $agent->properties()
                    ->with(['owner', 'company'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get owner property portfolio
     */
    public function getOwnerPortfolio(Client $owner): Collection
    {
        return $owner->ownedProperties()
                    ->with(['agent', 'company'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get company property portfolio
     */
    public function getCompanyPortfolio(Company $company): Collection
    {
        return $company->properties()
                    ->with(['owner', 'agent'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
}
