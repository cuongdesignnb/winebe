<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    protected $guarded = [];

    protected $casts = [
        'meta_json' => 'array',
    ];

    protected $appends = ['url'];

    public function folder()
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function getUrlAttribute()
    {
        if (\Illuminate\Support\Str::startsWith($this->original_path, 'http')) {
            return $this->original_path;
        }
        return \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->original_path);
    }
}
