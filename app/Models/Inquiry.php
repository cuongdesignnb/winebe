<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($inquiry) {
            try {
                $adminEmail = config('mail.from.address');
                \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\InquiryCreated($inquiry));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send inquiry email: " . $e->getMessage());
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
