<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Article extends Model implements Sortable
{
    /** @use HasFactory<\Database\Factories\ArticleFactory> */
    use HasFactory;
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'display_order',
        'sort_when_creating' => true,
        'sort_on_has_many' => false,
        'sort_on_belongs_to' => false,
        'sort_on_belongs_to_relations' => [],
    ];

    protected $fillable = [
        'title',
        'summary',
        'body',
        'author_id',
        'slug',
        'category_id',
        'publish_status',
        'published_at',
        'penname',
        'display_order'
    ];

    protected $casts = [
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function buildSortQuery()
    {
        return static::query()->where('category_id', $this->category_id);
    }
}
