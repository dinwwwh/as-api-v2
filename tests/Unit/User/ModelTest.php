<?php

namespace Tests\Unit\User;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Arr;
use Str;
use Tests\TestCase;

class ModelTest extends TestCase
{
    public function test_hasPermission_method()
    {
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();
        $user =  User::factory()->create();
        $user->roles()->attach($role);

        $this->assertFalse($user->hasPermission($permission->getKey()));

        $user->permissions()->attach($permission);
        $this->assertTrue($user->hasPermission($permission->getKey()));

        $user->permissions()->detach($permission);
        $this->assertFalse($user->hasPermission($permission->getKey()));

        $role->permissions()->attach($permission);
        $this->assertTrue($user->hasPermission($permission->getKey()));

        $user->roles()->detach($role);
        $this->assertFalse($user->hasPermission($permission->getKey()));
    }

    public function test_hasALlPermissions_method()
    {
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $user =  User::factory()->create();

        $this->assertFalse($user->hasAllPermissions([$permission1->getKey(), $permission2->getKey()]));

        $user->permissions()->attach($permission1);
        $this->assertFalse($user->hasAllPermissions([$permission1->getKey(), $permission2->getKey()]));

        $user->permissions()->attach($permission2);
        $this->assertTrue($user->hasAllPermissions([$permission1->getKey(), $permission2->getKey()]));
    }

    public function test_hasAnyPermissions_method()
    {
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();
        $user =  User::factory()->create();

        $this->assertFalse($user->hasAnyPermissions([$permission1->getKey(), $permission2->getKey()]));

        $user->permissions()->attach($permission1);
        $this->assertTrue($user->hasAnyPermissions([$permission1->getKey(), $permission2->getKey()]));

        $user->permissions()->attach($permission2);
        $this->assertTrue($user->hasAnyPermissions([$permission1->getKey(), $permission2->getKey()]));
    }

    public function test_updateBalance_method()
    {
        $user = User::factory()->state([
            'balance' => 20000,
        ])->create();

        $user->updateBalance(-20000, $message = Str::random());

        $this->assertEquals(0, $user->refresh()->balance);
        $this->assertDatabaseHas('logs', [
            'message' => $message,
            'type' => 'info',
            'loggable_id' => $user->getKey(),
            'loggable_type' => $user::class,
        ]);

        $user->updateBalance(10000, $message = Str::random());
        $this->assertEquals(10000, $user->refresh()->balance);

        $this->assertDatabaseHas('logs', [
            'message' => $message,
            'type' => 'info',
            'loggable_id' => $user->getKey(),
            'loggable_type' => $user::class,
        ]);
    }
}
