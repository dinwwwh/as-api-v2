<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use App\Traits\Loggable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CreatorAndUpdater, Loggable;

    protected $fillable = [
        'name',
        'balance',
        'gender',
        'login',
        'avatar_path',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email',
        'balance',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get permission that this user has directly
     *
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Get permission that this user has directly
     *
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Determine whether this user has a given permission
     *
     */
    public function hasPermission(string $permissionKey): bool
    {
        return $this->permissions()->where('key', $permissionKey)->first(['key'])
            || $this->roles()->whereRelation('permissions', 'key', $permissionKey)->first(['key']);
    }

    /**
     * Determine whether this user has all permissions
     *
     */
    public function hasAllPermissions(array $permissionKeys): bool
    {
        foreach ($permissionKeys as $permissionKey) {
            if (!$this->hasPermission($permissionKey)) return false;
        }
        return true;
    }

    /**
     * Determine whether this user has any permissions
     *
     */
    public function hasAnyPermissions(array $permissionKeys): bool
    {
        foreach ($permissionKeys as $permissionKey) {
            if ($this->hasPermission($permissionKey)) return true;
        }
        return false;
    }

    /**
     * Handle update balance of user
     *
     */
    public function updateBalance(int $amount, string $message): bool
    {
        $this->log($message, hiddenData: [
            'updatingBalance' => $this->balance,
            'updatedBalance' => $this->balance + $amount,
            'amount' => $amount,
        ]);

        return $this->update([
            'balance' => $this->balance + $amount,
        ]);
    }

    /**
     * Get accounts created by this model
     *
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'creator_id');
    }
}
