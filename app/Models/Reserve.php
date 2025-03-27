<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use OwenIt\Auditing\Contracts\Auditable;
class Reserve extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['user_id', 'count', 'status'];

    const STATUS_ACCEPTED = 'accept';
    const STATUS_PAUSED = 'pause';
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function history(): HasManyThrough
    {
        return $this->hasManyThrough(ReserveRequest::class, User::class);
    }

    public function incrementCount(int $count): void
    {
        $this->increment('count', $count);
    }
}
