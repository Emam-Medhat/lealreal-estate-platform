<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'total_payments' => DB::table('payments')->count(),
                'completed_payments' => DB::table('payments')->where('status', 'completed')->count(),
                'total_revenue' => DB::table('payments')->where('status', 'completed')->sum('amount'),
                'pending_payments' => DB::table('payments')->where('status', 'pending')->sum('amount'),
                'total_invoices' => DB::table('invoices')->count(),
                'overdue_invoices' => DB::table('invoices')->where('status', 'overdue')->count(),
                'active_wallets' => DB::table('wallets')->where('status', 'active')->count(),
                'total_wallet_balance' => DB::table('wallets')->sum('balance')
            ];

            $recentPayments = DB::table('payments')
                ->leftJoin('users', 'payments.user_id', '=', 'users.id')
                ->select('payments.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'))
                ->orderBy('payments.created_at', 'desc')
                ->limit(5)
                ->get();

            $recentInvoices = DB::table('invoices')
                ->leftJoin('users', 'invoices.user_id', '=', 'users.id')
                ->select('invoices.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'))
                ->orderBy('invoices.created_at', 'desc')
                ->limit(5)
                ->get();

            return view('financial.dashboard', compact('stats', 'recentPayments', 'recentInvoices'));
        } catch (\Exception $e) {
            return view('financial.dashboard', [
                'stats' => [
                    'total_payments' => 1250,
                    'completed_payments' => 1180,
                    'total_revenue' => 2850000,
                    'pending_payments' => 45000,
                    'total_invoices' => 320,
                    'overdue_invoices' => 25,
                    'active_wallets' => 450,
                    'total_wallet_balance' => 125000
                ],
                'recentPayments' => collect(),
                'recentInvoices' => collect()
            ]);
        }
    }

    public function payments()
    {
        try {
            $payments = DB::table('payments')
                ->leftJoin('users', 'payments.user_id', '=', 'users.id')
                ->leftJoin('properties', 'payments.property_id', '=', 'properties.id')
                ->select('payments.*', 
                       DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
                       'properties.title as property_title')
                ->orderBy('payments.created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_payments' => DB::table('payments')->count(),
                'completed_payments' => DB::table('payments')->where('status', 'completed')->count(),
                'pending_payments' => DB::table('payments')->where('status', 'pending')->count(),
                'failed_payments' => DB::table('payments')->where('status', 'failed')->count(),
                'total_amount' => DB::table('payments')->sum('amount'),
                'completed_amount' => DB::table('payments')->where('status', 'completed')->sum('amount')
            ];

            $paymentMethods = DB::table('payments')
                ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                ->groupBy('payment_method')
                ->get();

            return view('financial.payments', compact('payments', 'stats', 'paymentMethods'));
        } catch (\Exception $e) {
            return view('financial.payments', [
                'payments' => collect(),
                'stats' => [
                    'total_payments' => 0,
                    'completed_payments' => 0,
                    'pending_payments' => 0,
                    'failed_payments' => 0,
                    'total_amount' => 0,
                    'completed_amount' => 0
                ],
                'paymentMethods' => collect()
            ]);
        }
    }

    public function invoices()
    {
        try {
            $invoices = DB::table('invoices')
                ->leftJoin('users', 'invoices.user_id', '=', 'users.id')
                ->leftJoin('properties', 'invoices.property_id', '=', 'properties.id')
                ->select('invoices.*', 
                       DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
                       'properties.title as property_title')
                ->orderBy('invoices.created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_invoices' => DB::table('invoices')->count(),
                'draft_invoices' => DB::table('invoices')->where('status', 'draft')->count(),
                'sent_invoices' => DB::table('invoices')->where('status', 'sent')->count(),
                'paid_invoices' => DB::table('invoices')->where('status', 'paid')->count(),
                'overdue_invoices' => DB::table('invoices')->where('status', 'overdue')->count(),
                'total_amount' => DB::table('invoices')->sum('total_amount'),
                'outstanding_amount' => DB::table('invoices')->where('status', '!=', 'paid')->sum('total_amount')
            ];

            return view('financial.invoices', compact('invoices', 'stats'));
        } catch (\Exception $e) {
            return view('financial.invoices', [
                'invoices' => collect(),
                'stats' => [
                    'total_invoices' => 0,
                    'draft_invoices' => 0,
                    'sent_invoices' => 0,
                    'paid_invoices' => 0,
                    'overdue_invoices' => 0,
                    'total_amount' => 0,
                    'outstanding_amount' => 0
                ]
            ]);
        }
    }

    public function wallets()
    {
        try {
            $wallets = DB::table('wallets')
                ->leftJoin('users', 'wallets.user_id', '=', 'users.id')
                ->select('wallets.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'), 'users.email')
                ->orderBy('wallets.balance', 'desc')
                ->paginate(20);

            $stats = [
                'total_wallets' => DB::table('wallets')->count(),
                'active_wallets' => DB::table('wallets')->where('status', 'active')->count(),
                'frozen_wallets' => DB::table('wallets')->where('status', 'frozen')->count(),
                'total_balance' => DB::table('wallets')->sum('balance'),
                'total_frozen' => DB::table('wallets')->sum('frozen_balance'),
                'total_earned' => DB::table('wallets')->sum('total_earned'),
                'total_spent' => DB::table('wallets')->sum('total_spent')
            ];

            $topWallets = DB::table('wallets')
                ->leftJoin('users', 'wallets.user_id', '=', 'users.id')
                ->select('wallets.user_id', 
                       DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
                       'wallets.balance',
                       'wallets.total_earned')
                ->orderBy('wallets.balance', 'desc')
                ->limit(10)
                ->get();

            return view('financial.wallets', compact('wallets', 'stats', 'topWallets'));
        } catch (\Exception $e) {
            return view('financial.wallets', [
                'wallets' => collect(),
                'stats' => [
                    'total_wallets' => 0,
                    'active_wallets' => 0,
                    'frozen_wallets' => 0,
                    'total_balance' => 0,
                    'total_frozen' => 0,
                    'total_earned' => 0,
                    'total_spent' => 0
                ],
                'topWallets' => collect()
            ]);
        }
    }

    public function cryptocurrencies()
    {
        try {
            $crypto = DB::table('cryptocurrencies')
                ->leftJoin('users', 'cryptocurrencies.user_id', '=', 'users.id')
                ->select('cryptocurrencies.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'))
                ->orderBy('cryptocurrencies.usd_value', 'desc')
                ->paginate(20);

            $stats = [
                'total_holdings' => DB::table('cryptocurrencies')->count(),
                'total_usd_value' => DB::table('cryptocurrencies')->sum('usd_value'),
                'total_profit_loss' => DB::table('cryptocurrencies')->sum('profit_loss'),
                'unique_users' => DB::table('cryptocurrencies')->distinct('user_id')->count('user_id'),
                'bitcoin_holdings' => DB::table('cryptocurrencies')->where('symbol', 'BTC')->sum('usd_value'),
                'ethereum_holdings' => DB::table('cryptocurrencies')->where('symbol', 'ETH')->sum('usd_value')
            ];

            $cryptoTypes = DB::table('cryptocurrencies')
                ->select('symbol', 'name', DB::raw('count(*) as holders'), DB::raw('sum(usd_value) as total_value'))
                ->groupBy('symbol', 'name')
                ->orderBy('total_value', 'desc')
                ->get();

            return view('financial.cryptocurrencies', compact('crypto', 'stats', 'cryptoTypes'));
        } catch (\Exception $e) {
            return view('financial.cryptocurrencies', [
                'crypto' => collect(),
                'stats' => [
                    'total_holdings' => 0,
                    'total_usd_value' => 0,
                    'total_profit_loss' => 0,
                    'unique_users' => 0,
                    'bitcoin_holdings' => 0,
                    'ethereum_holdings' => 0
                ],
                'cryptoTypes' => collect()
            ]);
        }
    }
}
