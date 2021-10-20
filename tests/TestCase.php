<?php

namespace Tests;

use App\Models\Permission;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

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

    /**
     * Generate a raw DB query to search for a JSON field.
     *
     */
    function castToJson($json)
    {
        // Convert from array to json and add slashes, if necessary.
        if (is_array($json)) {
            $json = addslashes(json_encode($json));
        }
        // Or check if the value is malformed.
        elseif (is_null($json) || is_null(json_decode($json))) {
            throw new Exception('A valid JSON string was not provided.');
        }
        return DB::raw("CAST('{$json}' AS JSON)");
    }
}
