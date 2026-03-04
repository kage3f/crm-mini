<?php

namespace App\Traits;

use App\Models\Company;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait HasCompany
{
    public static function bootHasCompany(): void
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function ($model) {
            if (Auth::hasUser() && blank($model->company_id)) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }

    public static function withoutCompanyScope(): Builder
    {
        return static::withoutGlobalScope(CompanyScope::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function belongsToCurrentCompany(): bool
    {
        return Auth::hasUser()
            && (int) $this->company_id === (int) Auth::user()->company_id;
    }

    public function hasCompany(): bool
    {
        return !blank($this->company_id);
    }
}
