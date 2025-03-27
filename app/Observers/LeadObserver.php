<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Reserve;
use App\Models\LeadAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LeadObserver
{
    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        // write to log
        Log::info('LEAD OBSERVER: ' . $lead->id);
        if (Auth::check()) {
            $user = Auth::user();
            // If authenticated non-admin user, assign lead to themselves
            if ($user->role !== User::ROLE_ADMIN) {
                // no need to decrease reserve count 
                LeadAssignment::create([
                    'user_id' => $user->id,
                    'lead_id' => $lead->id,
                    'status' => LeadAssignment::STATUS_TO_CALL,
                ]);
            }
            return;
        }

        // Find available agents with reserves
        // Reserve::where('status', Reserve::STATUS_ACCEPTED)->where('count', '>', 0)->orderBy('updated_at')->get();

        $agents = User::leftJoin(
            'lead_assignments',
            'user_id',
            '=',
            'lead_assignments.user_id'
        )
            ->leftJoin('reserves', 'users.id', '=', 'reserves.user_id')
            ->select('users.id', DB::raw('MAX(lead_assignments.created_at) as last_assignment_time'))
            ->where('reserves.status', Reserve::STATUS_ACCEPTED)
            ->where('reserves.count', '>', 0)
            ->groupBy('users.id')->orderBy('last_assignment_time')->get();
        Log::info('LEAD OBSERVER: Candidates=' . $agents->count());
        if ($agents->count() == 0) {
            Log::info('LEAD OBSERVER: No agents available');
            return;
        }

        $first = $agents[0];
        $last = $agents[$agents->count() - 1];

        // If last has no assignments, prioritize them
        $agent = $last->last_assignment_time ? $first : $last;
        if ($agent) {
            DB::beginTransaction();
            // Create lead assignment
            LeadAssignment::create([
                'user_id' => $agent->id,
                'lead_id' => $lead->id,
                'status' => LeadAssignment::STATUS_TO_CALL,
            ]);

            // log current reserve count
            Log::info('LEAD OBSERVER: Reserve count=' . $agent->reserve->count);
            // Decrease reserve count   
            $reserve = Reserve::where('user_id', $agent->id)->first();
            $reserve->decrement('count',1);
            Log::info('LEAD OBSERVER: Lead assigned to agent=' . $agent->id);

            // log new reserve count
            Log::info('LEAD OBSERVER: New reserve count=' . $reserve->count);
            DB::commit();
            return;
        } else {
            Log::info('LEAD OBSERVER: No agents available');
        }

        throw new \Exception('No agent available');
    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "force deleted" event.
     */
    public function forceDeleted(Lead $lead): void
    {
        //
    }
}
