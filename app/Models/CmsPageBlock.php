<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPageBlock extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'settings_json' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'page_id');
    }
}
