<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class LeadAssignment extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    const STATUS_TO_CALL = 'to call';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $fillable = ['user_id', 'lead_id', 'status'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userWithReserves(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->whereHas('reserve', function($query) {
            $query->where('count', '>', 0);
        });
    }

    public function note(): HasOneThrough
    {
        return $this->hasOneThrough(Notes::class, Lead::class, 'id', 'lead_id', 'lead_id', 'id');
    }
    public static function _status(string $status) {
        return LeadAssignment::where('status', $status);
    }

    public static function to_call() {
        return LeadAssignment::_status(self::STATUS_TO_CALL);
    }

    public static function success() {
        return LeadAssignment::_status(self::STATUS_SUCCESS);
    }

    public static function failed() {
        return LeadAssignment::_status(self::STATUS_FAILED);
    }

    public function description(): string
    {
        return $this->lead->name();
    }
}
