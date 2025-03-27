<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use \LaravelArchivable\Archivable;
class KanTask extends Model
{
    use HasUuid;
    use Archivable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'list_id',
        'content',
        'order',
        'lead_id',
    ];

    /**
     * Get the task list that owns the task.
     */
    public function taskList(): BelongsTo
    {
        return $this->belongsTo(KanList::class, 'list_id');
    }

    /**
     * Get the lead associated with the task.
     * This relationship is nullable.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class)->withDefault(null);
    }
    
    /**
     * Check if the task has an associated lead.
     *
     * @return bool
     */
    public function hasLead(): bool
    {
        return $this->lead_id !== null;
    }
}
