<?php

namespace App\Traits;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasCompany
{
    public static function bootHasCompany(): void
    {
        static::creating(function ($model) {
            if (auth()->check() && ! $model->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });

        static::addGlobalScope('company', function (Builder $builder) {
            if (!app()->runningInConsole() && Auth::hasUser()) {
                $builder->where('company_id', Auth::user()->company_id);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
