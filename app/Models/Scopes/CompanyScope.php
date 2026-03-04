<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole() || ! Auth::hasUser()) {
            return;
        }

        $companyId = Auth::user()->company_id;

        if (blank($companyId)) {
            throw new \RuntimeException(
                'Authenticated user has no company_id. Cannot apply CompanyScope.'
            );
        }

        $builder->where($model->qualifyColumn('company_id'), $companyId);
    }
}
