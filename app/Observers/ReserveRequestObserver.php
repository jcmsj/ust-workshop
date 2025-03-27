<?php

namespace App\Observers;

use App\Models\Reserve;
use App\Models\ReserveRequest;

class ReserveRequestObserver
{
    public function updated(ReserveRequest $ReserveRequest)
    {
        if (
            $ReserveRequest->isDirty('status') &&
            $ReserveRequest->status === ReserveRequest::STATUS_ACCEPTED
        ) {

            if (Reserve::where('user_id', $ReserveRequest->user_id)->doesntExist()) {
                Reserve::create([
                    'user_id' => $ReserveRequest->user_id,
                    'count' => $ReserveRequest->count,
                    'status' => Reserve::STATUS_ACCEPTED,
                ]);
            } else {
                Reserve::where('user_id', $ReserveRequest->user_id)->increment('count', $ReserveRequest->count);
            }
        }
    }
}
