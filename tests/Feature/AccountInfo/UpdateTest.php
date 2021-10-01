<?php

namespace Tests\Feature\AccountInfo;

use App\Models\AccountInfo;
use App\Models\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['update_account_type', 'manage_account_type']);
        $accountInfo = AccountInfo::factory()->create();
        $router = route('accountInfos.update', ['accountInfo' => $accountInfo]);
        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(12),
            'rules' => Rule::factory()->count(3)->create()->toArray(),
        ];

        $this->actingAs($user)
            ->json('put', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('account_infos', [
            'id' => $accountInfo->getKey(),
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        $this->assertEquals(count($data['rules']), $accountInfo->rules()->count());
    }
}
