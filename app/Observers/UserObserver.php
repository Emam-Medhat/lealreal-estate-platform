<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendVerificationEmail;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        if (Hash::needsRehash($user->password)) {
            $user->password = Hash::make($user->password);
        }

        if (!$user->uuid) {
            $user->uuid = (string) \Illuminate\Support\Str::uuid();
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Dispatch verification email job
        // SendVerificationEmail::dispatch($user);
        // Or if using Listener SendWelcomeEmail, we might do it there.
        // User checklist says: created() - إرسال بريد التفعيل (Send Verification Email)

        SendVerificationEmail::dispatch($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Log changes if needed
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Clean up related data
        $user->socialAccounts()->delete();
        $user->sessions()->delete();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
