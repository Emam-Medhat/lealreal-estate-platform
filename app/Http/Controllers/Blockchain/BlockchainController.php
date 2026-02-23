<?php

namespace App\Http\Controllers\Blockchain;

use App\Http\Controllers\Controller;
use App\Services\BlockchainService;
use App\Repositories\Contracts\SmartContractRepositoryInterface;
use App\Repositories\Contracts\NftRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlockchainController extends Controller
{
    private BlockchainService $blockchainService;
    private SmartContractRepositoryInterface $smartContractRepository;
    private NftRepositoryInterface $nftRepository;
    private PropertyRepositoryInterface $propertyRepository;

    public function __construct(
        BlockchainService $blockchainService,
        SmartContractRepositoryInterface $smartContractRepository,
        NftRepositoryInterface $nftRepository,
        PropertyRepositoryInterface $propertyRepository
    ) {
        $this->blockchainService = $blockchainService;
        $this->smartContractRepository = $smartContractRepository;
        $this->nftRepository = $nftRepository;
        $this->propertyRepository = $propertyRepository;
    }

    public function index()
    {
        $statistics = $this->blockchainService->getBlockchainStatistics();
        $stats = $statistics['statistics'] ?? [];
        
        $contracts = $this->smartContractRepository->getRecent(5);
        $nfts = $this->nftRepository->getRecent(5);
        $properties = $this->propertyRepository->getLatestActive(5);

        return view('blockchain.dashboard', [
            'stats' => $stats,
            'contracts' => $contracts,
            'nfts' => $nfts,
            'properties' => $properties,
            'recentBlocks' => $this->blockchainService->getRecentBlocks(),
            'recentTransactions' => $this->blockchainService->getRecentTransactions()
        ]);
    }

    public function transactions()
    {
        $transactions = $this->blockchainService->getAllTransactions();
        $stats = $this->blockchainService->getTransactionStats();
        
        return view('blockchain.transactions', compact('transactions', 'stats'));
    }

    public function contracts(Request $request)
    {
        $contracts = $this->smartContractRepository->getFiltered($request->all(), 15);
        $stats = $this->blockchainService->getContractStats();
        
        return view('blockchain.contracts', compact('contracts', 'stats'));
    }

    public function nft(Request $request)
    {
        $nfts = $this->nftRepository->getFiltered($request->all(), 15);
        $stats = $this->blockchainService->getNFTStats();
        
        return view('blockchain.nft', compact('nfts', 'stats'));
    }

    public function wallet()
    {
        $user = auth()->user();
        $wallet = $this->blockchainService->getUserWallet($user->id);
        $transactions = $this->blockchainService->getUserTransactions($user->id);
        
        return view('blockchain.wallet', compact('wallet', 'transactions'));
    }

    public function deployContract(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string',
                'network' => 'required|string',
                'deployed_by' => 'required|integer|exists:users,id'
            ]);

            $result = $this->blockchainService->deploySmartContract($request->all());

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deploy contract',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function mintNFT(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'property_id' => 'required|integer|exists:properties,id',
                'standard' => 'required|string',
                'network' => 'required|string',
                'owner_address' => 'required|string',
                'created_by' => 'required|integer|exists:users,id'
            ]);

            $property = $this->propertyRepository->findById($request->property_id);
            if (!$property) {
                return response()->json(['success' => false, 'message' => 'Property not found'], 404);
            }

            $result = $this->blockchainService->mintPropertyNFT($property, $request->all());

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mint NFT',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function executeFunction(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'contract_address' => 'required|string',
                'function' => 'required|string',
                'parameters' => 'array',
                'network' => 'required|string'
            ]);

            $result = $this->blockchainService->executeSmartContractFunction(
                $request->contract_address,
                $request->function,
                $request->parameters ?? [],
                $request->network
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute function',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBalance(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'address' => 'required|string',
                'network' => 'required|string'
            ]);

            $result = $this->blockchainService->getBlockchainBalance(
                $request->address,
                $request->network
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get balance',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTransactionStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'tx_hash' => 'required|string',
                'network' => 'required|string'
            ]);

            $result = $this->blockchainService->getTransactionStatus(
                $request->tx_hash,
                $request->network
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get transaction status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPropertyNFTs(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'property_id' => 'required|integer|exists:properties,id'
            ]);

            $result = $this->blockchainService->getPropertyNFTs($request->property_id);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get property NFTs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getStatistics(): JsonResponse
    {
        try {
            $result = $this->blockchainService->getBlockchainStatistics();

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getContracts(Request $request): JsonResponse
    {
        try {
            $contracts = $this->smartContractRepository->getFiltered($request->all(), $request->per_page ?? 20);

            return response()->json([
                'success' => true,
                'contracts' => $contracts->items(),
                'pagination' => [
                    'current_page' => $contracts->currentPage(),
                    'total_pages' => $contracts->lastPage(),
                    'total_items' => $contracts->total(),
                    'per_page' => $contracts->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get contracts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getContract(Request $request, string $address): JsonResponse
    {
        try {
            $contract = $this->smartContractRepository->findByAddress($address);
            
            if (!$contract) {
                return response()->json(['success' => false, 'message' => 'Contract not found'], 404);
            }

            return response()->json([
                'success' => true,
                'contract' => $contract
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get contract',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createDAO(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'property_id' => 'required|integer|exists:properties,id',
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'network' => 'required|string',
                'total_supply' => 'required|integer|min:1',
                'voting_period' => 'required|integer|min:1',
                'quorum' => 'required|integer|min:1|max:100',
                'created_by' => 'required|integer|exists:users,id'
            ]);

            $property = $this->propertyRepository->findById($request->property_id);
            if (!$property) {
                return response()->json(['success' => false, 'message' => 'Property not found'], 404);
            }
            
            $result = $this->blockchainService->createPropertyDAO($property, $request->all());

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create DAO',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
