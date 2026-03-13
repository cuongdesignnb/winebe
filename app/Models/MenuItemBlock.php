<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemBlock extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings_json' => 'array',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
