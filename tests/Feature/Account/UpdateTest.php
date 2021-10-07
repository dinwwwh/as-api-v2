<?php

namespace Tests\Feature\Account;

use App\Models\Account;
use App\Models\AccountInfo;
use App\Models\AccountType;
use App\Models\File;
use App\Models\Tag;
use App\Models\User;
use Database\Factories\AccountFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test_controller()
    {
        Storage::fake('public');

        $user = $this->factoryUser();
        $account = Account::factory()
            ->state([
                'creator_id' => $user,
            ])
            ->has(Tag::factory()->count(3))
            ->has(File::factory()->count(3))
            ->for(AccountType::factory()->has(AccountInfo::factory()->count(3)))
            ->create();
        $router = route('accounts.update', ['account' => $account]);
        $data = [
            'description' => $this->faker->sentence(12),
            'cost' => rand(1000, 9999),
            'price' => rand(1000, 9999),
            'tags' => Tag::factory()->count(rand(1, 5))->make()->toArray(),
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
                UploadedFile::fake()->image('image3.jpg'),
            ],
            'creatorInfos' => [],
        ];
        foreach ($account->accountType->creatorAccountInfos as $accountInfo) {
            $accountInfo = $accountInfo->toArray();
            $accountInfo['pivot']['value'] = Str::random();
            $data['creatorInfos'][] = $accountInfo;
        }

        $this->actingAs($user)
            ->json('put', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->getKey(),
            'description' => $data['description'],
            'price' => $data['price'],
            'cost' => $data['cost'],
        ]);

        $this->assertEquals(count($data['tags']), $account->tags()->count());
        $this->assertEquals(count($data['images']), $account->images()->count());
        $this->assertEquals(count($data['creatorInfos']), $account->creatorInfos()->count());

        foreach ($account->creatorInfos as $info) {
            $expectedValue = collect($data['creatorInfos'])
                ->where('id', $info->id)
                ->first()['pivot']['value'];
            $this->assertEquals($expectedValue, $info->pivot->value);
        }

        Storage::disk('public')->assertExists('account-images/' . $data['images'][0]->hashName());
        Storage::disk('public')->assertExists('account-images/' . $data['images'][1]->hashName());
        Storage::disk('public')->assertExists('account-images/' . $data['images'][2]->hashName());
    }

    public function test_request_invalid_creator_infos_field()
    {
        $user = $this->factoryUser();
        $accountType = AccountType::factory()
            ->has(AccountInfo::factory()->count(2))
            ->create();
        AccountInfo::factory() // This is account info that creator can not provide pivot value
            ->state([
                'can_creator' => false,
                'account_type_id' => $accountType->getKey(),
            ])
            ->create()
            ->getKey();
        $accountType->users()->attach($user);
        $account = Account::factory()->state([
            'account_type_id' => $accountType->getKey(),
            'creator_id' => $user->getKey(),
        ])
            ->create();
        $router = route('accounts.update', ['account' => $account]);
        $data = [
            'creatorInfos' => [],
        ];

        foreach ($accountType->accountInfos as $accountInfo) {
            $accountInfo = $accountInfo->toArray();
            $accountInfo['pivot']['value'] = Str::random();
            $data['creatorInfos'][] = $accountInfo;
        }

        $this->actingAs($user)
            ->json('put',  $router, $data)
            ->assertStatus(422);
    }

    public function test_middleware_fail_not_creator()
    {
        $user = $this->factoryUser(inverse: true);
        $account = Account::factory()
            ->for(User::factory(), 'creator')
            ->create();
        $router = route('accounts.update', ['account' => $account]);

        $this->actingAs($user)
            ->json('put', $router)
            ->assertStatus(403);
    }
}
