<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Reserve;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
class RegistrationObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // check if the user has a reserve
        if (Reserve::where('user_id', $user->id)->exists()) {
            return;
        }
        Reserve::create([
            'user_id' => $user->id,
            'count' => 0,
            'status' => Reserve::STATUS_ACCEPTED,
        ]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
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
