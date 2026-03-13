<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxon extends Model
{
    use \App\Traits\HasMedia;

    protected $table = 'taxa';
    protected $guarded = [];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_taxon');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxon::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Taxon::class, 'parent_id');
    }
}
