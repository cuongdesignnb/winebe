<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use \App\Traits\HasMedia;

    protected $guarded = [];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function tastingNotes(): HasOne
    {
        return $this->hasOne(ProductNote::class);
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function taxons(): BelongsToMany
    {
        return $this->belongsToMany(Taxon::class, 'product_taxon');
    }
}
