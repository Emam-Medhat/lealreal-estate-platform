<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateWallet
{
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        // logic to create wallet record
        // \App\Models\Wallet::create(['user_id' => $user->id]);

        // As per schema, wallet_balance is on users table, so maybe just set default?
        // It's already default 0.00.
        // If there's a separate UserWallet table (2025_12_20_155810_create_user_wallets_table.php exists)

        // We will assume separate model
        // \App\Models\UserWallet::create(['user_id' => $user->id]);
    }
}
