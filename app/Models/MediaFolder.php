<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFolder extends Model
{
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MediaFolder::class, 'parent_id')->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    public function assets()
    {
        return $this->hasMany(MediaAsset::class, 'folder_id')->latest();
    }
}
