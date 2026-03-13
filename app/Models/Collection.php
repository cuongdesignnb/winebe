<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use \App\Traits\HasMedia;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'collection_product')->withPivot('sort_order');
    }
}
