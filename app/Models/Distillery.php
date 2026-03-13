<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distillery extends Model
{
    protected $guarded = [];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
