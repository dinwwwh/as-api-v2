<?php

namespace Tests\Feature\RechargedCard;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Str;
use Tests\TestCase;

class RechargeTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser();
        $router = route('rechargedCards.recharge');
        $data = [
            'telco' => 'viettel',
            'serial' => Str::random(),
            'faceValue' => 100000,
            'code' => Str::random(),
        ];

        $this->actingAs($user)
            ->json('post', $router, $data)
            ->assertStatus(201);

        $this->assertDatabaseHas('recharged_cards', [
            'telco' => $data['telco'],
            'face_value' => $data['faceValue'],
            'serial' => $data['serial'],
            'code' => $data['code'],
        ]);
    }
}
