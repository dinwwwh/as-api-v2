<?php

namespace Tests;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Factory a user for testing
     *
     */
    public function factoryUser(array $permissions = [], bool $inverse = false, ?User $user = null): User
    {
        if ($user === null) {
            $user = User::factory()->create();
        }

        if ($inverse) {
            $permissions = Permission::whereNotIn('key', $permissions)->get();
        }

        $user->roles()->sync([]);
        $user->permissions()->sync($permissions);

        return $user->refresh();
    }
}
