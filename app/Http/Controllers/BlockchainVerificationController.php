<?php

namespace App\Http\Controllers;

use App\Models\BlockchainVerification;
use App\Repositories\Contracts\BlockchainVerificationRepositoryInterface;
use App\Services\BlockchainVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BlockchainVerificationController extends Controller
{
    protected $verificationRepository;
    protected $verificationService;

    public function __construct(
        BlockchainVerificationRepositoryInterface $verificationRepository,
        BlockchainVerificationService $verificationService
    ) {
        $this->verificationRepository = $verificationRepository;
        $this->verificationService = $verificationService;
    }

    public function index()
    {
        $user = Auth::user();
        
        $verificationStats = $this->verificationRepository->getVerificationStats($user->id);
        $recentVerifications = $this->verificationRepository->getRecentVerifications($user->id);

        return view('security.blockchain.index', compact('verificationStats', 'recentVerifications'));
    }

    public function create()
    {
        return view('security.blockchain.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'verification_type' => 'required|in:ownership,transaction,document,identity,smart_contract',
            'blockchain_network' => 'required|in:ethereum,polygon,bnb_chain,avalanche,arbitrum',
            'transaction_hash' => 'required|string|max:255',
            'block_number' => 'required|integer|min:1',
            'contract_address' => 'nullable|string|max:255',
            'smart_contract_data' => 'nullable|array',
            'verification_data' => 'required|array',
            'verification_data.*.field' => 'required|string|max:100',
            'verification_data.*.value' => 'required|string|max:500',
            'verification_data.*.verified' => 'boolean',
            'digital_signature' => 'required|string',
            'public_key' => 'required|string|max:255',
            'gas_used' => 'nullable|integer|min:0',
            'gas_price' => 'nullable|numeric|min:0',
            'transaction_fee' => 'nullable|numeric|min:0',
            'confirmation_blocks' => 'required|integer|min:1',
            'verification_method' => 'required|in:smart_contract,oracle,zero_knowledge,traditional',
            'verification_level' => 'required|in:basic,standard,enhanced,premium',
            'metadata' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = Auth::id();
        
        try {
            $blockchainVerification = $this->verificationService->createVerification($validated);

            // Log verification attempt
            Log::info('Blockchain verification performed', [
                'user_id' => Auth::id(),
                'verification_id' => $blockchainVerification->id,
                'transaction_hash' => $validated['transaction_hash'],
                'status' => $blockchainVerification->verification_status,
            ]);

            return redirect()->route('security.blockchain.show', $blockchainVerification)
                ->with('success', 'تم إجراء التحقق بالبلوكشين بنجاح');
        } catch (\Exception $e) {
            Log::error('Blockchain verification failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to perform verification: ' . $e->getMessage());
        }
    }

    public function show(BlockchainVerification $blockchainVerification)
    {
        $this->authorize('view', $blockchainVerification);
        
        return view('security.blockchain.show', compact('blockchainVerification'));
    }

    public function verify(Request $request, BlockchainVerification $blockchainVerification)
    {
        $this->authorize('verify', $blockchainVerification);

        $validated = $request->validate([
            'verification_method' => 'required|in:smart_contract,oracle,zero_knowledge,traditional',
            'verification_level' => 'required|in:basic,standard,enhanced,premium',
            'additional_data' => 'nullable|array',
        ]);

        try {
            $this->verificationService->performVerification($blockchainVerification, $validated);

            return redirect()->route('security.blockchain.show', $blockchainVerification)
                ->with('success', 'تم التحقق بالبلوكشين بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'Verification failed: ' . $e->getMessage());
        }
    }

    public function smartContractVerification(Request $request)
    {
        $validated = $request->validate([
            'contract_address' => 'required|string|max:255',
            'function_name' => 'required|string|max:100',
            'parameters' => 'required|array',
            'blockchain_network' => 'required|in:ethereum,polygon,bnb_chain,avalanche,arbitrum',
        ]);

        $result = $this->verificationService->executeSmartContractVerification($validated);

        return response()->json($result);
    }

    public function oracleVerification(Request $request)
    {
        $validated = $request->validate([
            'oracle_address' => 'required|string|max:255',
            'data_request' => 'required|array',
            'blockchain_network' => 'required|in:ethereum,polygon,bnb_chain,avalanche,arbitrum',
        ]);

        $result = $this->verificationService->performOracleVerification($validated);

        return response()->json($result);
    }

    public function zeroKnowledgeVerification(Request $request)
    {
        $validated = $request->validate([
            'proof_data' => 'required|string',
            'public_inputs' => 'required|array',
            'verification_key' => 'required|string',
            'circuit_type' => 'required|string|max:100',
        ]);

        $result = $this->verificationService->performZeroKnowledgeVerification($validated);

        return response()->json($result);
    }

    public function transactionVerification(Request $request)
    {
        $validated = $request->validate([
            'transaction_hash' => 'required|string|max:255',
            'blockchain_network' => 'required|in:ethereum,polygon,bnb_chain,avalanche,arbitrum',
            'verification_depth' => 'required|in:basic,full,deep',
        ]);

        $result = $this->verificationService->verifyTransaction($validated);

        return response()->json($result);
    }

    public function batchVerification(Request $request)
    {
        $validated = $request->validate([
            'transactions' => 'required|array|max:50',
            'transactions.*.hash' => 'required|string|max:255',
            'transactions.*.network' => 'required|in:ethereum,polygon,bnb_chain,avalanche,arbitrum',
            'verification_level' => 'required|in:basic,standard',
        ]);

        $result = $this->verificationService->batchVerification($validated['transactions'], $validated['verification_level']);

        return response()->json($result);
    }

    public function analytics()
    {
        $user = Auth::user();
        
        $analytics = [
            'verification_trends' => $this->verificationRepository->getVerificationTrends($user->id),
            'network_distribution' => $this->verificationRepository->getNetworkDistribution($user->id),
            'verification_types' => $this->verificationRepository->getVerificationTypes($user->id),
            'success_rates' => $this->verificationRepository->getSuccessRates($user->id),
            'gas_usage_analysis' => $this->verificationRepository->getGasUsageAnalysis($user->id),
            'blockchain_performance' => $this->verificationRepository->getBlockchainPerformance($user->id),
        ];

        return view('security.blockchain.analytics', compact('analytics'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:csv,xlsx,pdf',
            'date_range' => 'required|in:last_week,last_month,last_quarter,last_year,custom',
            'start_date' => 'nullable|date|required_if:date_range,custom',
            'end_date' => 'nullable|date|required_if:date_range,custom|after_or_equal:start_date',
            'verification_status' => 'nullable|in:pending,verified,failed',
            'blockchain_network' => 'nullable|in:ethereum,polygon,bnb_chain,avalanche,arbitrum',
        ]);

        $validated['user_id'] = Auth::id();
        $verifications = $this->verificationRepository->getFilteredVerifications($validated);

        switch ($validated['format']) {
            case 'csv':
                return $this->exportCSV($verifications);
            case 'xlsx':
                return $this->exportExcel($verifications);
            case 'pdf':
                return $this->exportPDF($verifications);
        }
    }

    private function exportCSV($verifications)
    {
        $filename = 'blockchain_verifications_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($verifications) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Verification ID', 'Type', 'Network', 'Transaction Hash',
                'Status', 'Score', 'Created At', 'Verified At'
            ]);

            // Data
            foreach ($verifications as $verification) {
                fputcsv($file, [
                    $verification->id,
                    $verification->verification_id,
                    $verification->verification_type,
                    $verification->blockchain_network,
                    $verification->transaction_hash,
                    $verification->verification_status,
                    $verification->verification_score,
                    $verification->created_at,
                    $verification->verified_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($verifications)
    {
        // Implementation for Excel export
        return response()->download('blockchain_verifications.xlsx');
    }

    private function exportPDF($verifications)
    {
        // Implementation for PDF export
        return response()->download('blockchain_verifications.pdf');
    }
}
