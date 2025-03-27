<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationPayments extends Model
{
    protected $fillable = [
        'user_id',
        'payment_proof_url',
        'payment_details',
        'amount_due',
    ];

    const DIRECTORY = 'payment_proofs';
    const VISIBILITY = 'private';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
