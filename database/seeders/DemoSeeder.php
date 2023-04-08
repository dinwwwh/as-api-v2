<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $user = User::whereEmail('dinhdjj@gmail.com')->first();

        /** @var \App\Models\Tag $tag */
        $tag = Tag::factory()
            ->hasAttached(Account::factory()->count(2))
            ->create([
                'type' => 1,
                'name' => 'League of Legends',
                'slug' => 'league-of-legends',
            ]);


        $accountType = AccountType::factory()->create([
            'name' => 'Empty information - League of Legends'
        ]);
        $accountType->users()->attach($user);
        $tag->accountTypes()->attach($accountType);

        Account::factory()
            ->count(10)
            ->for($user, 'creator')
            ->for($accountType)
            ->create([
                'confirmed_at' => now(),
                'status' => Account::SELLING_STATUS,
            ]);

        Account::factory()
            ->buying()
            ->count(10)
            ->for($user, 'creator')
            ->for($accountType)
            ->create();

        Account::factory()
            ->approvable()
            ->count(10)
            ->for($user, 'creator')
            ->for($accountType)
            ->create();
    }
}
