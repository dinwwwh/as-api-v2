<?php

namespace Tests\Feature\RechargedCard;

use App\Models\RechargedCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StartApprovingTest extends TestCase
{
    public function test_controller()
    {
        $rechargedCard = RechargedCard::factory()->state([
            'approver_id' => null,
            'real_face_value' => null,
            'received_value' => null,
        ])->create();
        $router = route('rechargedCards.startApproving', ['rechargedCard' => $rechargedCard]);
        $user = $this->factoryUser(['approve_recharged_card']);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(200);

        $this->assertDatabaseHas('recharged_cards', [
            'id' => $rechargedCard->getKey(),
            'approver_id' => $user->getKey(),
            'real_face_value' => null,
            'received_value' => null,
        ]);
    }

    public function test_middleware_invalid_card()
    {
        $rechargedCard = RechargedCard::factory()->state([
            'approver_id' => 1,
            'real_face_value' => null,
            'received_value' => null,
        ])->create();
        $router = route('rechargedCards.startApproving', ['rechargedCard' => $rechargedCard]);
        $user = $this->factoryUser(['approve_recharged_card']);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }

    public function test_middleware_invalid_card_2()
    {
        $rechargedCard = RechargedCard::factory()->state([
            'approver_id' => null,
            'real_face_value' => 0,
            'received_value' => 0,
        ])->create();
        $router = route('rechargedCards.startApproving', ['rechargedCard' => $rechargedCard]);
        $user = $this->factoryUser(['approve_recharged_card']);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }

    public function test_middleware_no_approve_permission()
    {
        $rechargedCard = RechargedCard::factory()->state([
            'approver_id' => null,
            'real_face_value' => null,
            'received_value' => null,
        ])->create();
        $router = route('rechargedCards.startApproving', ['rechargedCard' => $rechargedCard]);
        $user = $this->factoryUser(['approve_recharged_card'], true);

        $this->actingAs($user)
            ->json('patch', $router)
            ->assertStatus(403);
    }
}
