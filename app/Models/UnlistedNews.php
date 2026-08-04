<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnlistedNews extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'link',
        'video_preview_image',
        'short_content',
        'content',
        'status',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by', 'uid');
    }

    public function unlistedStocks()
    {
        return $this->belongsToMany(
            UnlistedStock::class,
            'news_unlisted_stock',
            'news_id',
            'ul_stocks_fincode',
            'id',
            'UL_STOCKS_FINCODE'
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
