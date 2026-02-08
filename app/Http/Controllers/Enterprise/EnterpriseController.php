<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use App\Services\EnterpriseService;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnterpriseController extends Controller
{
    private EnterpriseService $enterpriseService;

    public function __construct(EnterpriseService $enterpriseService)
    {
        $this->enterpriseService = $enterpriseService;
    }

    public function dashboard()
    {
        $stats = $this->enterpriseService->getDashboardStats();
        $recentAccounts = $this->enterpriseService->getRecentAccounts();
        $systemHealth = $this->enterpriseService->getSystemHealth();
        
        return view('enterprise.dashboard', compact('stats', 'recentAccounts', 'systemHealth'));
    }

    public function accounts()
    {
        $accounts = $this->enterpriseService->getAllAccounts();
        return view('enterprise.accounts', compact('accounts'));
    }

    public function createAccount()
    {
        $users = User::orderBy('first_name')->get();
        return view('enterprise.create-account', compact('users'));
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'tenant_name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive,suspended,banned,pending_verification',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'max_users' => 'nullable|integer|min:1',
            'storage_limit' => 'nullable|integer|min:1',
            'bandwidth_limit' => 'nullable|integer|min:1',
            'api_calls_limit' => 'nullable|integer|min:1',
            'trial_expires_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Get user info for account creation
            $user = User::find($validated['user_id']);
            
            // Create enterprise account using existing table structure
            $account = \App\Models\EnterpriseAccount::create([
                'company_name' => $validated['tenant_name'],
                'company_type' => 'enterprise',
                'industry' => 'Real Estate',
                'size' => 'Medium',
                'website' => '',
                'phone' => $user->phone ?? '',
                'address' => $user->address ?? '',
                'city' => $user->city ?? '',
                'country' => $user->country ?? '',
                'postal_code' => $user->postal_code ?? '',
                'contact_person' => $user->first_name . ' ' . $user->last_name,
                'contact_email' => $user->email,
                'contact_phone' => $user->phone ?? '',
                'billing_address' => $user->address ?? '',
                'payment_method' => 'stripe',
                'subscription_plan' => 'enterprise',
                'status' => $validated['status'],
                'upgraded_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return redirect()
                ->route('enterprise.accounts')
                ->with('success', 'Enterprise account created successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create enterprise account: ' . $e->getMessage());
        }
    }

    public function viewAccount($id)
    {
        $account = \App\Models\EnterpriseAccount::find($id);
        
        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Account not found']);
        }

        return response()->json([
            'success' => true,
            'account' => [
                'company_name' => $account->company_name,
                'contact_email' => $account->contact_email,
                'contact_person' => $account->contact_person,
                'status' => ucfirst($account->status),
                'company_type' => ucfirst($account->company_type),
                'created_at' => $account->created_at ? \Carbon\Carbon::parse($account->created_at)->format('M d, Y') : 'Unknown'
            ]
        ]);
    }

    public function editAccount($id)
    {
        $account = \App\Models\EnterpriseAccount::find($id);
        $users = User::orderBy('first_name')->get();
        
        if (!$account) {
            return redirect()->route('enterprise.accounts')->with('error', 'Account not found');
        }
        
        return view('enterprise.edit-account', compact('account', 'users'));
    }

    public function updateAccount(Request $request, $id)
    {
        $account = \App\Models\EnterpriseAccount::find($id);
        
        if (!$account) {
            return redirect()->route('enterprise.accounts')->with('error', 'Account not found');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,suspended,banned,pending_verification',
            'contact_email' => 'required|email',
            'contact_person' => 'required|string|max:255'
        ]);

        try {
            $account->update([
                'company_name' => $validated['company_name'],
                'status' => $validated['status'],
                'contact_email' => $validated['contact_email'],
                'contact_person' => $validated['contact_person'],
                'updated_at' => now()
            ]);

            return redirect()
                ->route('enterprise.accounts')
                ->with('success', 'Account updated successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update account: ' . $e->getMessage());
        }
    }

    public function toggleAccountStatus($id)
    {
        $account = \App\Models\EnterpriseAccount::find($id);
        
        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Account not found']);
        }

        // Toggle status
        $newStatus = $account->status === 'active' ? 'suspended' : 'active';
        $account->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Account status updated successfully',
            'new_status' => $newStatus
        ]);
    }

    public function subscriptions()
    {
        $subscriptions = $this->enterpriseService->getAllSubscriptions();
        return view('enterprise.subscriptions', compact('subscriptions'));
    }

    public function createSubscription()
    {
        $users = User::orderBy('first_name')->get();
        $plans = $this->enterpriseService->getSubscriptionPlans();
        
        return view('enterprise.create-subscription', compact('users', 'plans'));
    }

    public function editSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        $users = User::orderBy('first_name')->get();
        $plans = $this->enterpriseService->getSubscriptionPlans();
        
        return view('enterprise.edit-subscription', compact('subscription', 'users', 'plans'));
    }

    public function updateSubscription(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|in:pending,active,expired,cancelled,suspended',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'payment_method' => 'nullable|string',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'auto_renew' => 'boolean',
            'activated_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $subscription->update([
                'user_id' => $validated['user_id'],
                'plan_id' => $validated['plan_id'],
                'status' => $validated['status'],
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'billing_cycle' => $validated['billing_cycle'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'auto_renew' => $validated['auto_renew'] ?? false,
                'activated_at' => $validated['activated_at'],
                'notes' => $validated['notes']
            ]);

            return redirect()
                ->route('enterprise.subscriptions')
                ->with('success', 'Subscription updated successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update subscription: ' . $e->getMessage());
        }
    }

    public function storeSubscription(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|in:pending,active,expired,cancelled,suspended',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'payment_method' => 'nullable|string',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'auto_renew' => 'boolean',
            'activated_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $subscription = Subscription::create([
                'user_id' => $validated['user_id'],
                'plan_id' => $validated['plan_id'],
                'status' => $validated['status'],
                'starts_at' => $validated['starts_at'],
                'ends_at' => $validated['ends_at'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'billing_cycle' => $validated['billing_cycle'],
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'auto_renew' => $validated['auto_renew'] ?? false,
                'activated_at' => $validated['activated_at'],
                'notes' => $validated['notes'],
                'cancelled_at' => null,
                'upgraded_at' => null,
                'last_renewed_at' => null
            ]);

            return redirect()
                ->route('enterprise.subscriptions')
                ->with('success', 'Subscription created successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    public function viewSubscription($id)
    {
        $subscription = Subscription::with(['user', 'plan'])->find($id);
        
        if (!$subscription) {
            return response()->json(['success' => false, 'message' => 'Subscription not found']);
        }

        return response()->json([
            'success' => true,
            'subscription' => [
                'user_name' => $subscription->user->first_name . ' ' . $subscription->user->last_name,
                'user_email' => $subscription->user->email,
                'plan_name' => $subscription->plan->name ?? 'Unknown Plan',
                'amount' => number_format($subscription->amount, 2),
                'status' => ucfirst($subscription->status),
                'starts_at' => $subscription->starts_at,
                'ends_at' => $subscription->ends_at,
                'billing_cycle' => ucfirst($subscription->billing_cycle),
                'payment_status' => ucfirst($subscription->payment_status),
                'auto_renew' => $subscription->auto_renew ? 'Yes' : 'No',
                'notes' => $subscription->notes
            ]
        ]);
    }

    public function toggleSubscriptionStatus($id)
    {
        $subscription = Subscription::find($id);
        
        if (!$subscription) {
            return response()->json(['success' => false, 'message' => 'Subscription not found']);
        }

        // Toggle status
        $newStatus = $subscription->status === 'active' ? 'suspended' : 'active';
        $subscription->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription status updated successfully',
            'new_status' => $newStatus
        ]);
    }

    public function billing()
    {
        $billingData = $this->enterpriseService->getBillingData();
        return view('enterprise.billing', compact('billingData'));
    }

    public function reports()
    {
        $reports = $this->enterpriseService->getReports();
        return view('enterprise.reports', compact('reports'));
    }

    public function configureTenant(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'enterprise_id' => 'required|integer|exists:enterprise_accounts,id',
                'domain' => 'string',
                'subdomain' => 'string',
                'white_label_enabled' => 'boolean',
                'branding' => 'array',
                'features' => 'array',
                'integrations' => 'array'
            ]);

            $result = $this->enterpriseService->configureTenant(
                $request->enterprise_id,
                $request->all()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to configure tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDashboard(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'enterprise_id' => 'required|integer|exists:enterprise_accounts,id'
            ]);

            $result = $this->enterpriseService->getEnterpriseDashboard($request->enterprise_id);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateReport(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'enterprise_id' => 'required|integer|exists:enterprise_accounts,id',
                'report_type' => 'required|in:usage,financial,performance,security,compliance,custom',
                'options' => 'array'
            ]);

            $result = $this->enterpriseService->generateEnterpriseReport(
                $request->enterprise_id,
                $request->report_type,
                $request->options ?? []
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function manageUsers(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'enterprise_id' => 'required|integer|exists:enterprise_accounts,id',
                'operations' => 'required|array',
                'operations.*.action' => 'required|in:create,update,delete,suspend,activate',
                'operations.*.user_data' => 'required|array'
            ]);

            $result = $this->enterpriseService->manageEnterpriseUsers(
                $request->enterprise_id,
                $request->operations
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to manage users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function integrateSystem(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'enterprise_id' => 'required|integer|exists:enterprise_accounts,id',
                'integration_data' => 'required|array'
            ]);

            $result = $this->enterpriseService->integrateThirdPartySystem(
                $request->enterprise_id,
                $request->integration_data
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to integrate system',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPlans(): JsonResponse
    {
        try {
            $plans = [
                'starter' => [
                    'name' => 'Starter',
                    'price' => 99,
                    'features' => ['50 properties', '5 users', 'Basic support']
                ],
                'professional' => [
                    'name' => 'Professional',
                    'price' => 299,
                    'features' => ['500 properties', '25 users', 'Priority support']
                ],
                'enterprise' => [
                    'name' => 'Enterprise',
                    'price' => 999,
                    'features' => ['Unlimited', 'Dedicated support', 'Custom features']
                ]
            ];

            return response()->json([
                'success' => true,
                'plans' => $plans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get plans',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
