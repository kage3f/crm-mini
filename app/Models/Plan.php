<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price_monthly',
        'client_limit',
        'user_limit',
        'has_kanban',
        'has_tasks',
        'features',
        'stripe_price_id',
    ];

    protected $casts = [
        'features'   => 'array',
        'has_kanban' => 'boolean',
        'has_tasks'  => 'boolean',
    ];

    public function isFree(): bool
    {
        return $this->slug === 'free';
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price_monthly === 0) {
            return 'Grátis';
        }
        return 'R$ ' . number_format($this->price_monthly / 100, 2, ',', '.');
    }
}
