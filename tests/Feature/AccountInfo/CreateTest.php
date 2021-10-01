<?php

namespace Tests\Feature\AccountInfo;

use App\Models\AccountInfo;
use App\Models\AccountType;
use App\Models\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['update_account_type', 'manage_account_type']);
        $accountType = AccountType::factory()->create();
        $router = route('accountInfos.create', ['accountType' => $accountType]);
        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(12),
            'rules' => Rule::factory()->count(3)->create()->toArray(),
        ];

        $resData = $this->actingAs($user)
            ->json('post', $router, $data)
            ->assertStatus(201)
            ->getData()
            ->data;
        $accountInfo = AccountInfo::find($resData->id);

        $this->assertDatabaseHas('account_infos', [
            'id' => $resData->id,
            'name' => $data['name'],
            'description' => $data['description'],
            'account_type_id' => $accountType->getKey(),
        ]);

        $this->assertEquals(count($data['rules']), $accountInfo->rules()->count());
    }
}
