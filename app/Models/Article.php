<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'featured_image',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'locked_at'    => 'datetime',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by', 'uid');
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by', 'uid');
    }

    public function unlistedStocks()
    {
        return $this->belongsToMany(
            UnlistedStock::class,
            'article_unlisted_stock',
            'article_id',
            'ul_stocks_fincode',
            'id',
            'UL_STOCKS_FINCODE'
        );
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
