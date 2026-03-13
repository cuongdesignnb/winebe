<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $guarded = [];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function distilleries()
    {
        return $this->hasMany(Distillery::class);
    }
}
