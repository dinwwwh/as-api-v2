<?php

namespace Tests\Feature\RechargedCard;

use App\Models\RechargedCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EndApprovingTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['approve_recharged_card']);
        $rechargedCard = RechargedCard::factory()->state([
            'real_face_value' => null,
            'received_value' => null,
            'approver_id' => $user,
            'creator_id' => 1,
        ])->create();
        $router = route('rechargedCards.endApproving', ['rechargedCard' => $rechargedCard]);
        $data = [
            'realFaceValue' => 10000,
            'receivedValue' => 9000,
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('recharged_cards', [
            'id' => $rechargedCard->getKey(),
            'real_face_value' => $data['realFaceValue'],
            'received_value' => $data['receivedValue'],
        ]);
    }

    public function test_middleware_invalid_card()
    {
        $user = $this->factoryUser(['approve_recharged_card']);
        $rechargedCard = RechargedCard::factory()->state([
            'real_face_value' => 0,
            'received_value' => null,
            'approver_id' => $user,
            'creator_id' => 1,
        ])->create();
        $router = route('rechargedCards.endApproving', ['rechargedCard' => $rechargedCard]);
        $data = [
            'realFaceValue' => 10000,
            'receivedValue' => 9000,
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(403);
    }

    public function test_middleware_invalid_card_2()
    {
        $user = $this->factoryUser(['approve_recharged_card']);
        $rechargedCard = RechargedCard::factory()->state([
            'real_face_value' => null,
            'received_value' => null,
            'approver_id' => null,
            'creator_id' => 1,
        ])->create();
        $router = route('rechargedCards.endApproving', ['rechargedCard' => $rechargedCard]);
        $data = [
            'realFaceValue' => 10000,
            'receivedValue' => 9000,
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(403);
    }

    public function test_middleware_no_approve_permission()
    {
        $user = $this->factoryUser(['approve_recharged_card'], true);
        $rechargedCard = RechargedCard::factory()->state([
            'real_face_value' => null,
            'received_value' => null,
            'approver_id' => $user,
            'creator_id' => 1,
        ])->create();
        $router = route('rechargedCards.endApproving', ['rechargedCard' => $rechargedCard]);
        $data = [
            'realFaceValue' => 10000,
            'receivedValue' => 9000,
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(403);
    }

    public function test_middleware_has_manage_card()
    {
        $user = $this->factoryUser(['approve_recharged_card', 'manage_recharged_card']);
        $rechargedCard = RechargedCard::factory()->state([
            'real_face_value' => null,
            'received_value' => null,
            'approver_id' => User::factory(),
            'creator_id' => 1,
        ])->create();
        $router = route('rechargedCards.endApproving', ['rechargedCard' => $rechargedCard]);
        $data = [
            'realFaceValue' => 10000,
            'receivedValue' => 9000,
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(200);
    }
}
