<?php

namespace App\Http\Controllers;

use App\Models\TransactionLedger;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class LedgerAuditController extends Controller
{
    public function __construct(private VerificationService $verificationService)
    {
        $this->middleware('auth');
    }

    /**
     * GET /blockchain/ledger
     * Display paginated ledger entries (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $perPage = min($request->integer('per_page', 50), 200);
        $action = $request->query('action');

        $query = TransactionLedger::with('recorder:id,name')
            ->orderByDesc('id');

        if ($action) {
            $query->byAction($action);
        }

        $entries = $query->paginate($perPage)->through(fn($e) => [
            'id' => $e->id,
            'action' => $e->action,
            'reference_type' => class_basename($e->reference_type),
            'reference_id' => $e->reference_id,
            'previous_hash' => $e->previous_hash,
            'current_hash' => $e->current_hash,
            'payload' => $e->payload,
            'recorded_by' => $e->recorder?->name,
            'created_at' => $e->created_at->toDateTimeString(),
        ]);

        return response()->json($entries);
    }

    /**
     * GET /blockchain/ledger/verify
     * Verify the full hash chain. Returns tamper detection results.
     * Admin-only.
     */
    public function verify(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $fromId = $request->integer('from_id', 0);

        $result = $fromId > 0
            ? $this->verificationService->verifyChainFrom($fromId)
            : $this->verificationService->verifyChain();

        $httpStatus = $result['valid'] ? 200 : 200; // always 200 but surface in payload
        $result['ledger_summary'] = $this->verificationService->getLedgerSummary();

        return response()->json($result, $httpStatus);
    }

    /**
     * GET /blockchain/ledger/{entry}
     * Show a single ledger entry with hash validity check.
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeAdmin();

        $entry = TransactionLedger::with('recorder:id,name')->findOrFail($id);

        return response()->json([
            'id' => $entry->id,
            'action' => $entry->action,
            'reference_type' => $entry->reference_type,
            'reference_id' => $entry->reference_id,
            'previous_hash' => $entry->previous_hash,
            'current_hash' => $entry->current_hash,
            'hash_valid' => $entry->isHashValid(),
            'payload' => $entry->payload,
            'recorded_by' => $entry->recorder?->name,
            'created_at' => $entry->created_at->toDateTimeString(),
        ]);
    }

    /**
     * Ensure only admin users can access these endpoints.
     * Falls back to a 403 JSON response if not authorised.
     */
    private function authorizeAdmin(): void
    {
        if (
            !auth()->user() ||
            !method_exists(auth()->user(), 'isAdmin') ||
            !auth()->user()->isAdmin()
        ) {
            // Also allow users with an `admin` role attribute
            $user = auth()->user();
            $isAdmin = ($user && isset($user->role) && $user->role === 'admin');

            if (!$isAdmin) {
                abort(response()->json([
                    'error' => 'forbidden',
                    'message' => 'You do not have permission to access the ledger audit.',
                ], 403));
            }
        }
    }
}
