<?php

namespace App\Http\Controllers;

use App\Models\SmartContract;
use App\Models\CryptoTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmartContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $contracts = SmartContract::latest()->paginate(20);
        
        return view('blockchain.contracts', compact('contracts'));
    }

    public function deployContract(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'abi' => 'required|string',
            'bytecode' => 'required|string',
            'contract_type' => 'required|string|in:erc20,erc721,custom',
            'description' => 'nullable|string',
            'properties' => 'nullable|array',
            'gas_limit' => 'required|integer|min:0',
            'gas_price' => 'required|numeric|min:0',
            'deployment_cost' => 'nullable|numeric|min:0',
            'owner_address' => 'required|string|max:255',
            'deployed_at' => 'nullable|date',
            'status' => 'required|string|in:draft,deployed,failed,deprecated',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        $contract = SmartContract::create([
            'name' => $request->name,
            'address' => $request->address,
            'abi' => $request->abi,
            'bytecode' => $request->bytecode,
            'contract_type' => $request->contract_type,
            'description' => $request->description,
            'properties' => $request->properties ?? [],
            'gas_limit' => $request->gas_limit,
            'gas_price' => $request->gas_price,
            'deployment_cost' => $request->deployment_cost ?? 0,
            'owner_address' => $request->owner_address,
            'deployed_at' => $request->deployed_at ?? null,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'contract' => $contract
        ]);
    }

    public function getContracts(Request $request)
    {
        $contracts = SmartContract::with(['transactions'])->latest()->paginate(20);
        
        return response()->json($contracts);
    }

    public function getContract(Request $request)
    {
        $address = $request->address;
        
        $contract = SmartContract::where('address', $address)->first();
        
        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }

        return response()->json($contract);
    }

    public function executeContract(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'function_name' => 'required|string',
            'parameters' => 'nullable|array',
            'from_address' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'gas_limit' => 'nullable|integer|min:0'
        ]);

        $contract = SmartContract::where('address', $request->address)->first();
        
        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }

        $result = $this->executeContractFunction($contract, $request->all());

        return response()->json([
            'status' => $result['status'],
            'result' => $result['result'],
            'gas_used' => $result['gas_used'],
            'transaction_hash' => $result['transaction_hash']
        ]);
    }

    public function callContract(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'function_name' => 'required|string',
            'parameters' => 'nullable|array',
            'from_address' => 'nullable|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'gas_limit' => 'nullable|integer|min:0'
        ]);

        $contract = SmartContract::where('address', $request->address)->first();
        
        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }

        $result = $this->callContractFunction($contract, $request->all());

        return response()->json([
            'status' => $result['status'],
            'result' => $result['result'],
            'gas_used' => $result['gas_used'],
            'transaction_hash' => $result['transaction_hash']
        ]);
    }

    public function updateContract(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'properties' => 'nullable|array',
            'gas_limit' => 'nullable|integer|min:0',
            'gas_price' => 'nullable|numeric|min:0'
        ]);

        $contract = SmartContract::where('address', $request->address)->first();
        
        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }

        $contract->update([
            'name' => $request->name ?? $contract->name,
            'description' => $request->description ?? $contract->description,
            'properties' => $request->properties ?? $contract->properties,
            'gas_limit' => $request->gas_limit ?? $contract->gas_limit,
            'gas_price' => $request->gas_price ?? $contract->gas_price,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'contract' => $contract
        ]);
    }

    public function getContractCode(Request $request)
    {
        $address = $request->address;
        
        $contract = SmartContract::where('address', $address)->first();
        
        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }

        return response()->json([
            'abi' => $contract->abi,
            'bytecode' => $contract->bytecode,
            'source_code' => $this->getSourceCode($contract->bytecode)
        ]);
    }

    public function getContractTransactions(Request $request)
    {
        $address = $request->address;
        
        $contract = SmartContract::where('address', $address)->first();
        
        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }

        $transactions = CryptoTransaction::where('contract_address', $address)
            ->with(['user'])
            ->latest()
            ->paginate(50);

        return response()->json($transactions);
    }

    public function getContractStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_contracts' => SmartContract::count(),
            'deployed_contracts' => SmartContract::where('status', 'deployed')->count(),
            'failed_contracts' => SmartContract::where('status', 'failed')->count(),
            'total_transactions' => CryptoTransaction::where('contract_address', '!=', null)->count(),
            'total_gas_used' => CryptoTransaction::where('contract_address', '!=', null)->sum('gas_used'),
            'average_gas_price' => $this->getAverageGasPrice($startDate),
            'contract_types' => $this->getContractTypes($startDate),
            'deployment_costs' => SmartContract::where('status', 'deployed')->sum('deployment_cost'),
            'active_contracts' => SmartContract::where('status', 'deployed')->where('updated_at', '>', now()->subDays(30))->count()
        ];

        return response()->json($stats);
    }

    public function verifyContract(Request $request)
    {
        $address = $request->address;
        
        $contract = SmartContract::where('address', $address)->first();
        
        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }

        $verification = $this->performContractVerification($contract);

        return response()->json([
            'is_valid' => $verification['is_valid'],
            'verification_details' => $verification['details'],
            'contract_hash' => $verification['hash']
        ]);
    }

    public function deprecateContract(Request $request)
    {
        $address = $request->address;
        
        $contract = SmartContract::where('address', $address)->first();
        
        if (!$contract) {
            return response()->json(['error' => 'Contract not found'], 404);
        }

        $contract->update([
            'status' => 'deprecated',
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'contract' => $contract
        ]);
    }

    private function executeContractFunction($contract, $params)
    {
        try {
            // Simplified execution simulation
            $gasUsed = rand(1000, 50000);
            $transactionHash = hash('tx_' . time());
            
            return [
                'status' => 'success',
                'result' => 'Function executed successfully',
                'gas_used' => $gasUsed,
                'transaction_hash' => $transactionHash
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
                'gas_used' => 0,
                'transaction_hash' => null
            ];
        }
    }

    private function callContractFunction($contract, $params)
    {
        try {
            // Simplified call simulation
            $gasUsed = rand(1000, 50000);
            $transactionHash = hash('call_' . time());
            
            return [
                'status' => 'success',
                'result' => 'Function called successfully',
                'gas_used' => $gasUsed,
                'transaction_hash' => $transactionHash
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
                'gas_used' => 0,
                'transaction_hash' => null
            ];
        }
    }

    private function getSourceCode($bytecode)
    {
        // Simplified source code extraction
        return substr($bytecode, 0, 50);
    }

    private function performContractVerification($contract)
    {
        // Simplified verification
        return [
            'is_valid' => true,
            'details' => [
                'bytecode_valid' => strlen($contract->bytecode) === 68,
                'address_valid' => strlen($contract->address) === 42,
                'abi_valid' => $contract->abi === '0x' || strlen($contract->abi) === 42
            ],
            'hash' => hash($contract->address . $contract->bytecode)
        ];
    }

    private function getContractTypes($startDate)
    {
        return SmartContract::where('created_at', '>', $startDate)
            ->selectRaw('contract_type, COUNT(*) as count')
            ->groupBy('contract_type')
            ->orderByDesc('count')
            ->get();
    }

    private function getAverageGasPrice($startDate)
    {
        $totalGas = CryptoTransaction::where('created_at', '>=', $startDate)->sum('gas_used');
        $totalFees = CryptoTransaction::where('created_at', '>=', $startDate)->sum('gas_price');
        
        return $totalGas > 0 ? $totalFees / $totalGas : 0;
    }

    public function exportContracts(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $contracts = SmartContract::latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportContractsToCsv($contracts);
        }

        return response()->json($contracts);
    }

    private function exportContractsToCsv($contracts)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="smart_contracts.csv"'
        ];

        $callback = function() use ($contracts) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Address', 'Name', 'Type', 'ABI', 'Bytecode', 'Status', 'Gas Limit', 'Gas Price', 'Created At'
            ]);
            
            foreach ($contracts as $contract) {
                fputcsv($file, [
                    $contract->address,
                    $contract->name,
                    $contract->contract_type,
                    $contract->abi,
                    $contract->bytecode,
                    $contract->status,
                    $contract->gas_limit,
                    $contract->gas_price,
                    $contract->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
