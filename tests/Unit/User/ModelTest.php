<?php

namespace Tests\Unit\User;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
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
}
