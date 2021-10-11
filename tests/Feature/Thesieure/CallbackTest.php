<?php

namespace Tests\Feature\Thesieure;

use App\Models\RechargedCard;
use App\Models\User;
use App\Services\Thesieure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CallbackTest extends TestCase
{
    public function test_controller()
    {
        $card = RechargedCard::factory()
            ->state([
                'approver_id' => User::where('login', config('thesieure.user.login'))->first()->getKey(),
                'real_face_value' => null,
                'received_value' => null,
                'face_value' => 50000,
            ])
            ->create();
        $router = route('thesieure.callback');
        $data = [
            'request_id' => $card->getKey(),
            'value' => 20000,
            'amount' => 15000,
            'callback_sign' => Thesieure::generateSign($card),
        ];

        $this->json('get', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('recharged_cards', [
            'id' => $card->getKey(),
            'real_face_value' => $data['value'],
            'received_value' => $data['amount'],
        ]);
    }

    public function test_controller_invalid_callback_sign()
    {
        $card = RechargedCard::factory()
            ->state([
                'approver_id' => User::where('login', config('thesieure.user.login'))->first()->getKey(),
                'real_face_value' => null,
                'received_value' => null,
                'face_value' => 50000,
            ])
            ->create();
        $router = route('thesieure.callback');
        $data = [
            'request_id' => $card->getKey(),
            'value' => 20000,
            'amount' => 15000,
            'callback_sign' => 'invalid_callback_sign',
        ];

        $this->json('get', $router, $data)
            ->assertStatus(403);
    }

    public function test_controller_invalid_card()
    {
        $card = RechargedCard::factory()
            ->state([
                'approver_id' => User::where('login', config('thesieure.user.login'))->first()->getKey(),
                'real_face_value' => 0,
                'received_value' => 0,
                'face_value' => 50000,
            ])
            ->create();
        $router = route('thesieure.callback');
        $data = [
            'request_id' => $card->getKey(),
            'value' => 20000,
            'amount' => 15000,
            'callback_sign' => Thesieure::generateSign($card),
        ];

        $this->json('get', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('recharged_cards', [
            'id' => $card->getKey(),
            'real_face_value' => $card->real_face_value,
            'received_value' => $card->received_value,
        ]);
    }

    public function test_controller_invalid_card_2()
    {
        $card = RechargedCard::factory()
            ->state([
                // 'approver_id' => User::where('login', config('thesieure.user.login'))->first()->getKey(),
                'real_face_value' => null,
                'received_value' => null,
                'face_value' => 50000,
            ])
            ->create();
        $router = route('thesieure.callback');
        $data = [
            'request_id' => $card->getKey(),
            'value' => 20000,
            'amount' => 15000,
            'callback_sign' => Thesieure::generateSign($card),
        ];

        $this->json('get', $router, $data)
            ->assertStatus(403);
    }
}
