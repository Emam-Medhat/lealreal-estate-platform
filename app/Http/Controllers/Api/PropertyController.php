<?php

namespace App\Http\Controllers\Api;

use App\Services\PropertyService;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Property;
use App\Models\Client;
use App\Models\Agent;
use App\Models\Company;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;
    protected InvoiceService $invoiceService;
    protected PaymentService $paymentService;

    public function __construct(
        PropertyService $propertyService,
        InvoiceService $invoiceService,
        PaymentService $paymentService
    ) {
        $this->propertyService = $propertyService;
        $this->invoiceService = $invoiceService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of properties.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $properties = $this->propertyService->generatePropertyReport($filters);
            
            return response()->json([
                'success' => true,
                'data' => $properties,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created property.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|string',
                'status' => 'required|string',
                'price' => 'required|numeric|min:0',
                'area' => 'required|numeric|min:0',
                'bedrooms' => 'nullable|integer|min:0',
                'bathrooms' => 'nullable|integer|min:0',
                'garage' => 'nullable|integer|min:0',
                'pool' => 'nullable|boolean',
                'address' => 'required|string',
                'city' => 'required|string',
                'state' => 'required|string',
                'country' => 'required|string',
                'postal_code' => 'nullable|string',
                'owner_id' => 'nullable|exists:clients,id',
                'agent_id' => 'nullable|exists:agents,id',
                'company_id' => 'nullable|exists:companies,id',
                'purchase_price' => 'nullable|numeric|min:0',
                'renovation_cost' => 'nullable|numeric|min:0',
                'maintenance_cost' => 'nullable|numeric|min:0',
                'tax_cost' => 'nullable|numeric|min:0',
                'rental_income' => 'nullable|numeric|min:0',
                'views_count' => 'nullable|integer|min:0',
                'inquiries_count' => 'nullable|integer|min:0',
                'showings_count' => 'nullable|integer|min:0',
                'featured' => 'nullable|boolean',
                'images' => 'nullable|array',
                'documents' => 'nullable|array',
                'metadata' => 'nullable|array',
            ]);

            $property = $this->propertyService->createProperty($data);
            
            return response()->json([
                'success' => true,
                'data' => $property->load(['owner', 'agent', 'company']),
                'message' => 'Property created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified property.
     */
    public function show(Property $property): JsonResponse
    {
        try {
            $property->load([
                'owner',
                'agent',
                'company',
                'ownershipHistory.previousOwner',
                'ownershipHistory.newOwner',
                'transactions.client',
                'commissions.agent',
                'invoices',
                'payments',
                'documents'
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $property,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified property.
     */
    public function update(Request $request, Property $property): JsonResponse
    {
        try {
            $data = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'type' => 'sometimes|string',
                'status' => 'sometimes|string',
                'price' => 'sometimes|numeric|min:0',
                'area' => 'sometimes|numeric|min:0',
                'bedrooms' => 'sometimes|integer|min:0',
                'bathrooms' => 'sometimes|integer|min:0',
                'garage' => 'sometimes|integer|min:0',
                'pool' => 'sometimes|boolean',
                'address' => 'sometimes|string',
                'city' => 'sometimes|string',
                'state' => 'sometimes|string',
                'country' => 'sometimes|string',
                'postal_code' => 'sometimes|string',
                'owner_id' => 'sometimes|exists:clients,id',
                'agent_id' => 'sometimes|exists:agents,id',
                'company_id' => 'sometimes|exists:companies,id',
                'purchase_price' => 'sometimes|numeric|min:0',
                'renovation_cost' => 'sometimes|numeric|min:0',
                'maintenance_cost' => 'sometimes|numeric|min:0',
                'tax_cost' => 'sometimes|numeric|min:0',
                'rental_income' => 'sometimes|numeric|min:0',
                'views_count' => 'sometimes|integer|min:0',
                'inquiries_count' => 'sometimes|integer|min:0',
                'showings_count' => 'sometimes|integer|min:0',
                'featured' => 'sometimes|boolean',
                'images' => 'sometimes|array',
                'documents' => 'sometimes|array',
                'metadata' => 'sometimes|array',
            ]);

            $updatedProperty = $this->propertyService->updateProperty($property, $data);
            
            return response()->json([
                'success' => true,
                'data' => $updatedProperty->load(['owner', 'agent', 'company']),
                'message' => 'Property updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified property.
     */
    public function destroy(Property $property): JsonResponse
    {
        try {
            $this->propertyService->deleteProperty($property);
            
            return response()->json([
                'success' => true,
                'message' => 'Property deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark property as sold.
     */
    public function markAsSold(Request $request, Property $property): JsonResponse
    {
        try {
            $data = $request->validate([
                'buyer_id' => 'required|exists:clients,id',
                'sale_price' => 'required|numeric|min:0',
                'sale_date' => 'nullable|date',
                'transaction_id' => 'nullable|exists:property_transactions,id',
                'notes' => 'nullable|string',
            ]);

            $this->propertyService->markAsSold($property, $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Property marked as sold successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transfer property ownership.
     */
    public function transferOwnership(Request $request, Property $property): JsonResponse
    {
        try {
            $data = $request->validate([
                'new_owner_id' => 'required|exists:clients,id',
                'transfer_type' => 'required|string',
                'transfer_amount' => 'nullable|numeric|min:0',
                'transfer_date' => 'nullable|date',
                'agent_id' => 'nullable|exists:agents,id',
                'notes' => 'nullable|string',
            ]);

            $this->propertyService->transferOwnership($property, $data['new_owner_id'], $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Property ownership transferred successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $statistics = $this->propertyService->getPropertyStatistics($filters);
            
            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available properties.
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $filters = $request->all();
            $properties = $this->propertyService->getAvailableProperties($filters);
            
            return response()->json([
                'success' => true,
                'data' => $properties,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property ownership history.
     */
    public function ownershipHistory(Property $property): JsonResponse
    {
        try {
            $history = $this->propertyService->getOwnershipHistory($property);
            
            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property transactions.
     */
    public function transactions(Property $property): JsonResponse
    {
        try {
            $transactions = $this->propertyService->getPropertyTransactions($property);
            
            return response()->json([
                'success' => true,
                'data' => $transactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property commissions.
     */
    public function commissions(Property $property): JsonResponse
    {
        try {
            $commissions = $this->propertyService->getPropertyCommissions($property);
            
            return response()->json([
                'success' => true,
                'data' => $commissions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate property ROI.
     */
    public function roi(Property $property): JsonResponse
    {
        try {
            $roi = $this->propertyService->calculateROI($property);
            
            return response()->json([
                'success' => true,
                'data' => $roi,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property performance metrics.
     */
    public function performance(Property $property): JsonResponse
    {
        try {
            $performance = $this->propertyService->getPropertyPerformance($property);
            
            return response()->json([
                'success' => true,
                'data' => $performance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get similar properties.
     */
    public function similar(Request $request, Property $property): JsonResponse
    {
        try {
            $data = $request->validate([
                'limit' => 'nullable|integer|min:1|max:20',
            ]);

            $limit = $data['limit'] ?? 5;
            $similarProperties = $this->propertyService->getSimilarProperties($property, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $similarProperties,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search properties.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'search' => 'nullable|string',
                'type' => 'nullable|string',
                'status' => 'nullable|string',
                'company_id' => 'nullable|exists:companies,id',
                'agent_id' => 'nullable|exists:agents,id',
                'owner_id' => 'nullable|exists:clients,id',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'min_area' => 'nullable|numeric|min:0',
                'max_area' => 'nullable|numeric|min:0',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'country' => 'nullable|string',
                'bedrooms' => 'nullable|integer|min:0',
                'bathrooms' => 'nullable|integer|min:0',
                'garage' => 'nullable|integer|min:0',
                'pool' => 'nullable|boolean',
            ]);

            $properties = $this->propertyService->searchProperties($data);
            
            return response()->json([
                'success' => true,
                'data' => $properties,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get agent property portfolio.
     */
    public function agentPortfolio(Request $request, $agentId): JsonResponse
    {
        try {
            $agent = Agent::findOrFail($agentId);
            $properties = $this->propertyService->getAgentPortfolio($agent);
            
            return response()->json([
                'success' => true,
                'data' => $properties,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get owner property portfolio.
     */
    public function ownerPortfolio(Request $request, $ownerId): JsonResponse
    {
        try {
            $owner = Client::findOrFail($ownerId);
            $properties = $this->propertyService->getOwnerPortfolio($owner);
            
            return response()->json([
                'success' => true,
                'data' => $properties,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get company property portfolio.
     */
    public function companyPortfolio(Request $request, $companyId): JsonResponse
    {
        try {
            $company = Company::findOrFail($companyId);
            $properties = $this->propertyService->getCompanyPortfolio($company);
            
            return response()->json([
                'success' => true,
                'data' => $properties,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property invoice history.
     */
    public function invoiceHistory(Request $request, $propertyId): JsonResponse
    {
        try {
            $property = Property::findOrFail($propertyId);
            $filters = $request->all();
            $invoices = $this->invoiceService->getPropertyInvoiceHistory($property, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $invoices,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get property payment history.
     */
    public function paymentHistory(Request $request, $propertyId): JsonResponse
    {
        try {
            $property = Property::findOrFail($propertyId);
            $filters = $request->all();
            $payments = $this->paymentService->getPropertyPaymentHistory($property, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $payments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
