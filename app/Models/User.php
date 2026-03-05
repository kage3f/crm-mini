<?php

namespace App\Models;

use App\Notifications\Auth\ResetPasswordQueuedNotification;
use App\Notifications\Auth\VerifyEmailQueuedNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HasCompany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, HasCompany;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getAvatarAttribute(): string
    {
        if (blank($this->avatar_url)) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6366f1&color=fff&size=128';
        }

        if (str_starts_with($this->avatar_url, 'http://') || str_starts_with($this->avatar_url, 'https://') || str_starts_with($this->avatar_url, '/')) {
            return $this->avatar_url;
        }

        return Storage::disk('public')->url($this->avatar_url);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailQueuedNotification());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordQueuedNotification($token));
    }
}
