<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_visible' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'settings_json' => 'array',
    ];

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->title;
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }
}
