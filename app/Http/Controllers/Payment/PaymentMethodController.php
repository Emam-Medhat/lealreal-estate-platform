<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\AddPaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $paymentMethods = PaymentMethod::where('user_id', Auth::id())
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('payments.methods.create');
    }

    public function store(AddPaymentMethodRequest $request)
    {
        try {
            $paymentMethod = PaymentMethod::create([
                'user_id' => Auth::id(),
                'type' => $request->type,
                'provider' => $request->provider,
                'card_last_four' => $request->type === 'card' ? substr($request->card_number, -4) : null,
                'card_brand' => $request->type === 'card' ? $this->getCardBrand($request->card_number) : null,
                'card_expiry_month' => $request->type === 'card' ? $request->expiry_month : null,
                'card_expiry_year' => $request->type === 'card' ? $request->expiry_year : null,
                'bank_name' => $request->type === 'bank' ? $request->bank_name : null,
                'bank_account_number' => $request->type === 'bank' ? $request->account_number : null,
                'bank_routing_number' => $request->type === 'bank' ? $request->routing_number : null,
                'wallet_address' => $request->type === 'crypto' ? $request->wallet_address : null,
                'wallet_network' => $request->type === 'crypto' ? $request->wallet_network : null,
                'is_default' => $request->is_default ?? false,
                'nickname' => $request->nickname,
                'metadata' => $request->metadata ?? [],
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            // If this is set as default, unset other defaults
            if ($paymentMethod->is_default) {
                PaymentMethod::where('user_id', Auth::id())
                    ->where('id', '!=', $paymentMethod->id)
                    ->update(['is_default' => false]);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'added_payment_method',
                'details' => "Added payment method: {$paymentMethod->type} - {$paymentMethod->nickname}",
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('payments.methods.index')
                ->with('success', 'Payment method added successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error adding payment method: ' . $e->getMessage());
        }
    }

    public function show(PaymentMethod $paymentMethod)
    {
        $this->authorize('view', $paymentMethod);
        return view('payments.methods.show', compact('paymentMethod'));
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);
        return view('payments.methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);

        $request->validate([
            'nickname' => 'required|string|max:100',
            'is_default' => 'boolean',
            'status' => 'required|in:active,inactive',
        ]);

        $paymentMethod->update([
            'nickname' => $request->nickname,
            'is_default' => $request->is_default ?? false,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        // If this is set as default, unset other defaults
        if ($paymentMethod->is_default) {
            PaymentMethod::where('user_id', Auth::id())
                ->where('id', '!=', $paymentMethod->id)
                ->update(['is_default' => false]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_payment_method',
            'details' => "Updated payment method: {$paymentMethod->type} - {$paymentMethod->nickname}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('payments.methods.index')
            ->with('success', 'Payment method updated successfully.');
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod)
    {
        $this->authorize('delete', $paymentMethod);

        try {
            $paymentMethod->update([
                'status' => 'deleted',
                'deleted_at' => now(),
                'deleted_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted_payment_method',
                'details' => "Deleted payment method: {$paymentMethod->type} - {$paymentMethod->nickname}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment method deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setDefault(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        $this->authorize('update', $paymentMethod);

        try {
            // Unset all other defaults for this user
            PaymentMethod::where('user_id', Auth::id())
                ->update(['is_default' => false]);

            // Set this as default
            $paymentMethod->update([
                'is_default' => true,
                'updated_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'set_default_payment_method',
                'details' => "Set payment method as default: {$paymentMethod->type} - {$paymentMethod->nickname}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Default payment method updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting default payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyPaymentMethod(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        $this->authorize('update', $paymentMethod);

        $request->validate([
            'verification_code' => 'required|string|max:10',
        ]);

        try {
            // Mock verification - in real implementation, this would verify with payment provider
            $isValid = $request->verification_code === '123456';

            if ($isValid) {
                $paymentMethod->update([
                    'is_verified' => true,
                    'verified_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'verified_payment_method',
                    'details' => "Verified payment method: {$paymentMethod->type} - {$paymentMethod->nickname}",
                    'ip_address' => $request->ip(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment method verified successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPaymentMethods(): JsonResponse
    {
        $paymentMethods = PaymentMethod::where('user_id', Auth::id())
            ->where('status', 'active')
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($method) {
                return [
                    'id' => $method->id,
                    'type' => $method->type,
                    'nickname' => $method->nickname,
                    'is_default' => $method->is_default,
                    'is_verified' => $method->is_verified,
                    'display_name' => $method->getDisplayName(),
                    'card_last_four' => $method->card_last_four,
                    'card_brand' => $method->card_brand,
                    'bank_name' => $method->bank_name,
                    'wallet_address' => $method->wallet_address ? substr($method->wallet_address, 0, 10) . '...' : null,
                ];
            });

        return response()->json([
            'success' => true,
            'payment_methods' => $paymentMethods
        ]);
    }

    public function getPaymentMethodStats(): JsonResponse
    {
        $stats = [
            'total_methods' => PaymentMethod::where('user_id', Auth::id())->count(),
            'active_methods' => PaymentMethod::where('user_id', Auth::id())->where('status', 'active')->count(),
            'verified_methods' => PaymentMethod::where('user_id', Auth::id())->where('is_verified', true)->count(),
            'by_type' => PaymentMethod::where('user_id', Auth::id())
                ->groupBy('type')
                ->selectRaw('type, COUNT(*) as count')
                ->get(),
            'by_provider' => PaymentMethod::where('user_id', Auth::id())
                ->groupBy('provider')
                ->selectRaw('provider, COUNT(*) as count')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function getCardBrand($cardNumber)
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        
        if (preg_match('/^4/', $cardNumber)) return 'visa';
        if (preg_match('/^5[1-5]/', $cardNumber)) return 'mastercard';
        if (preg_match('/^3[47]/', $cardNumber)) return 'amex';
        if (preg_match('/^6/', $cardNumber)) return 'discover';
        
        return 'unknown';
    }
}
