<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements
    MustVerifyEmail,
    FilamentUser
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'is_approved',
        'approved_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'name',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'approved_at' => 'datetime',
    ];

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }

        if ($panel->getId() == 'app') {
            return ! $this->isAdmin();
        }

        return $this->is_approved;
    }

    // Hack to disable email verification
    public function hasVerifiedEmail(): bool
    {
        return true;
    }

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getIsAdminAttribute()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getIsUserAttribute()
    {
        return $this->role === self::ROLE_USER;
    }

    public function reserve(): HasOne
    {
        return $this->hasOne(Reserve::class);
    }

    public function reserveRequests(): HasMany
    {
        return $this->hasMany(ReserveRequest::class);
    }

    public function handledRequests(): HasMany
    {
        return $this->hasMany(ReserveRequest::class, 'handled_by');
    }

    public static function users()
    {
        return User::where('role', User::ROLE_USER);
    }

    public static function admins()
    {
        return User::where('role', User::ROLE_ADMIN);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function registrationPayment(): HasOne
    {
        return $this->hasOne(RegistrationPayments::class);
    }

    public function getIsApprovedAttribute()
    {
        return !is_null($this->approved_at);
    }

    public function setIsApprovedAttribute(bool $value)
    {
        $this->approved_at = $value ? now() : null;
        $this->save();
    }

    public function approve() {
        $this->is_approved = true;
    }

    public function kanBoards(): HasMany
    {
        return $this->hasMany(KanBoard::class);
    }
}
