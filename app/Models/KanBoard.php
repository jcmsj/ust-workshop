<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use \LaravelArchivable\Archivable;

class KanBoard extends Model
{
    use HasUuid;
    use Archivable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'user_id',
    ];

    /**
     * Get the task lists for this board.
     */
    public function taskLists(): HasMany
    {
        return $this->hasMany(KanList::class, 'board_id');
    }
    
    /**
     * Get the user who owns the board.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
