<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($order) {
            try {
                \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderCreated($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send order email: " . $e->getMessage());
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
