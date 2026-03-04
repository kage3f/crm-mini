<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes, HasCompany;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'company',
        'status',
        'notes',
        'created_by',
    ];

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'lead'     => 'Lead',
            'client'   => 'Cliente',
            'inactive' => 'Inativo',
            default    => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'lead'     => 'badge-blue',
            'client'   => 'badge-green',
            'inactive' => 'badge-gray',
            default    => 'badge-gray',
        };
    }
}
