<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'api_token',
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role',
        'fcm_token',
        'last_active_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var list<string>
     */
    protected $appends = [
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_active_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: User has many UserDevice
     */
    public function userDevices()
    {
        return $this->hasMany(\App\Models\UserDevice::class);
    }

    /**
     * Accessor: is_admin
     */
    public function getIsAdminAttribute(): bool
    {
        return ($this->role ?? 'user') === 'admin';
    }

    /**
     * Helper: isAdmin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Helper: Check if user is currently online (active in last 5 minutes)
     */
    public function isOnline(): bool
    {
        return $this->last_active_at && $this->last_active_at->greaterThanOrEqualTo(now()->subMinutes(5));
    }

    /**
     * Helper: Format last active time
     */
    public function lastActiveText(): string
    {
        if (!$this->last_active_at) {
            return 'Belum pernah aktif';
        }

        if ($this->isOnline()) {
            return 'Online sekarang';
        }

        return $this->last_active_at->diffForHumans();
    }

    /**
     * Model booted event
     */
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = self::generateShortUuid();
            }
        });
    }

    /**
     * Generate unique 8-character hex UUID (e.g. 8f3b1a2c)
     */
    public static function generateShortUuid(): string
    {
        do {
            $hex = bin2hex(random_bytes(4)); // 4 bytes = 8 hex chars
        } while (static::where('uuid', $hex)->exists());

        return $hex;
    }

    /**
     * Accessor for uuid (ensure always present)
     */
    public function getUuidAttribute($value): string
    {
        if (empty($value)) {
            $value = self::generateShortUuid();
            $this->attributes['uuid'] = $value;
            if ($this->exists) {
                $this->saveQuietly();
            }
        }
        return $value;
    }

    /**
     * Helper: Get current third-party personal access token
     */
    public function thirdPartyToken()
    {
        return $this->tokens()
            ->where('name', 'third_party_access')
            ->latest()
            ->first();
    }
}
