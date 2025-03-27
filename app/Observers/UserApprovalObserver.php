<?php

namespace App\Observers;

use App\Models\User;
use App\Mail\UserApproved;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserApprovalObserver
{
    public function updated(User $user): void
    {
        if ($user->is_approved) {
            Log::info("User {$user->id} has been approved. Sending email notification.");
            Mail::to($user->email)->send(new UserApproved($user));
        }
    }
}
