<?php

namespace App\Models;

use App\Traits\CreatorAndUpdater;
use App\Traits\Loggable;
use App\Traits\Permissible;
use App\Traits\Rolable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        CreatorAndUpdater,
        Loggable,
        Rolable,
        Permissible;

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
     * Determine whether this user has a given permission
     *
     */
    public function hasPermission(Permission|string $permission): bool
    {
        $permissionKey = is_string($permission) ? $permission : $permission->getKey();
        return $this->permissions()->where('key', $permissionKey)->first(['key'])
            || $this->roles()->whereRelation('permissions', 'key', $permissionKey)->first(['key']);
    }

    /**
     * Determine whether this user has all permissions
     *
     */
    public function hasAllPermissions(Collection|array $permissions): bool
    {
        //TODO Need make this method use 2 sql queries
        $permissionKeys = is_array($permissions) ? $permissions : $permissions->getKey();
        foreach ($permissionKeys as $permissionKey) {
            if (!$this->hasPermission($permissionKey)) return false;
        }
        return true;
    }

    /**
     * Determine whether this user has any permissions
     *
     */
    public function hasAnyPermissions(Collection|array $permissions): bool
    {
        $permissionKeys = is_array($permissions) ? $permissions : $permissions->getKey();

        # Case user has permission directly
        if (
            $this->permissions()->whereIn('key', $permissionKeys)->first(['key'])
        ) return true;

        # Case user has permission through roles
        return !!$this->roles()->whereHas('permissions',  function (Builder $query) use ($permissionKeys) {
            $query->whereIn('key', $permissionKeys);
        })->first(['key']);
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
