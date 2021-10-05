<?php

namespace Tests\Feature\Account;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PayAccountCreatorsTest extends TestCase
{
    public function test_account_pay_command()
    {
        $user = User::factory()
            ->state([
                'balance' => 0,
            ])
            ->create();

        $accounts = Account::factory()
            ->state([
                'creator_id' => $user->getKey(),
                'tax' => 1,
            ])
            ->state(new Sequence(
                [ // success
                    'bought_at_price' => 12,
                    'paid_at' => null,
                    'confirmed_at' => now(),
                    'bought_at' => now()->subMinute(),
                ],
                [ // success
                    'bought_at_price' => 13,
                    'paid_at' => null,
                    'confirmed_at' => now(),
                    'bought_at' => now()->subMinute(),
                ],
                [ // fail
                    'bought_at_price' => 23,
                    'paid_at' => null,
                    'confirmed_at' => now()->addHour(),
                    'bought_at' => now()->subMinute(),
                ],
                [ // fail
                    'bought_at_price' => 34,
                    'paid_at' => null,
                    'confirmed_at' => now(),
                    'bought_at' => now()->addHour(),
                ],
                [ // fail
                    'bought_at_price' => 45,
                    'paid_at' => now(),
                    'confirmed_at' => now(),
                    'bought_at' => now()->subMinute(),
                ],
                [ // fail
                    'bought_at_price' => null,
                    'price' => 9999,
                    'paid_at' => null,
                    'confirmed_at' => now(),
                    'bought_at' => now()->subMinute(),
                ],
            ))
            ->count(6)
            ->create();

        $this->artisan('account:pay');

        $this->assertEquals(23, $user->refresh()->balance);
        $this->assertTrue(now()->gte($accounts->where('bought_at_price', 12)->first()->refresh()->paid_at));
        $this->assertTrue(now()->gte($accounts->where('bought_at_price', 13)->first()->refresh()->paid_at));
    }
}
