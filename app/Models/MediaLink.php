<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaLink extends Model
{
    protected $guarded = [];

    protected $casts = [
        'transformation_json' => 'array',
    ];

    public function asset()
    {
        return $this->belongsTo(MediaAsset::class, 'asset_id');
    }

    public function model()
    {
        return $this->morphTo();
    }
}
