<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasCompany;

    protected $fillable = [
        'company_id',
        'user_id',
        'type',
        'subject_type',
        'subject_id',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'json',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public static function log(string $type, string $description, ?Model $subject = null, array $properties = []): self
    {
        return self::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
        ]);
    }
}
