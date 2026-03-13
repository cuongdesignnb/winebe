<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use \App\Traits\HasMedia;

    protected $guarded = [];

    protected $casts = [
        'settings_json' => 'array',
        'published_at' => 'datetime',
    ];

    public function blocks()
    {
        return $this->hasMany(CmsPageBlock::class, 'page_id')->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && $this->published_at && $this->published_at->lte(now());
    }
}
