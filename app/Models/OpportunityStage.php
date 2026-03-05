<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpportunityStage extends Model
{
    use HasFactory, HasCompany;

    protected $fillable = ['company_id', 'name', 'color', 'order'];

    public function opportunities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Opportunity::class, 'stage_id');
    }
}
