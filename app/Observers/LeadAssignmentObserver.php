<?php

namespace App\Observers;

use App\Models\LeadAssignment;
use App\Http\Mail\NewLead;
use App\Models\Reserve;
use App\Models\User;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadAssignmentObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the LeadAssignment "created" event.
     */
    public function created(LeadAssignment $leadAssignment): void
    {
        Mail::to(
            $leadAssignment->user->email, 
            $leadAssignment->user->name
        )->send(new NewLead($leadAssignment->lead, $leadAssignment));
        Log::info('LEAD ASSIGNMENT OBSERVER: ' . $leadAssignment->id);

        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role == User::ROLE_ADMIN) {
                $reserve = Reserve::where('user_id', $leadAssignment->user_id)->first();
                if (!$reserve) {
                    $user->reserve->create([
                        'user_id' => $leadAssignment->user_id,
                        'count' => 1,
                        'status' => Reserve::STATUS_PAUSED,
                    ]);
                }
                $reserve->decrement('count', 1);
                $reserve->save();
            }
        }
    }

    /**
     * Handle the LeadAssignment "updated" event.
     */
    public function updated(LeadAssignment $leadAssignment): void
    {
        //
    }

    /**
     * Handle the LeadAssignment "deleted" event.
     */
    public function deleted(LeadAssignment $leadAssignment): void
    {
        //
    }

    /**
     * Handle the LeadAssignment "restored" event.
     */
    public function restored(LeadAssignment $leadAssignment): void
    {
        //
    }

    /**
     * Handle the LeadAssignment "force deleted" event.
     */
    public function forceDeleted(LeadAssignment $leadAssignment): void
    {
        //
    }
}
