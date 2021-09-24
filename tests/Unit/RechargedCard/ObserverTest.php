<?php

namespace Tests\Unit\RechargedCard;

use App\Models\RechargedCard;
use App\Models\User;
use Tests\TestCase;

class ObserverTest extends TestCase
{
    public function test_pay_method()
    {
        $user = User::factory()->state([
            'balance' => 1,
        ])->create();

        $rechargedCard = RechargedCard::factory()->state([
            'received_value' => 10000,
            'real_face_value' => 10000,
            'approver_id' => 1,
            'paid_at' => null,
            'creator_id' => $user->getKey(),
        ])->create();

        $this->assertEquals(10001, $user->refresh()->balance);

        $rechargedCard = RechargedCard::factory()->state([
            'received_value' => null,
            'real_face_value' => null,
            'approver_id' => 1,
            'paid_at' => null,
            'creator_id' => $user->getKey(),
        ])->create();

        $this->assertEquals(10001, $user->refresh()->balance);

        $rechargedCard->update([
            'received_value' => 10000,
            'real_face_value' => 10000,
        ]);
        $this->assertEquals(20001, $user->refresh()->balance);

        $rechargedCard = RechargedCard::factory()->state([
            'received_value' => 0,
            'real_face_value' => 0,
            'approver_id' => 1,
            'paid_at' => now(),
            'creator_id' => $user->getKey(),
        ])->create();

        $this->assertEquals(20001, $user->refresh()->balance);
    }
}
