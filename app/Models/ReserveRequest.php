<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReserveRequest extends Model
{
    /** @use HasFactory<\Database\Factories\ReserveRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'count',
        'cost_per_lead',
        'status',
        'handled_by',
        'handled_at',
        'payment_details'
    ];
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeHasPendingRequest($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->where('status', self::STATUS_PENDING)
            ->exists();
    }

    public static function getPendingRequest($userId)
    {
        return self::where('user_id', $userId)
            ->where('status', self::STATUS_PENDING)
            ->first();
    }

    public function accept()
    {
        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);
    }

    public function getTotalCostAttribute(): float
    {
        return $this->count * $this->cost_per_lead;
    }
}
