<?php

namespace Tests\Feature\Account;

use App\Models\Account;
use App\Models\AccountInfo;
use App\Models\AccountType;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Storage;
use Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    public function test_controller()
    {
        Storage::fake('public');

        $user = $this->factoryUser();
        $accountType = AccountType::factory()
            ->has(AccountInfo::factory()->count(2))
            ->create();
        $accountType->users()->attach($user);
        $router = route('accounts.create', ['accountType' => $accountType]);
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

        foreach ($accountType->creatorAccountInfos as $accountInfo) {
            $accountInfo = $accountInfo->toArray();
            $accountInfo['pivot']['value'] = Str::random();
            $data['creatorInfos'][] = $accountInfo;
        }

        $accountId =  $this->actingAs($user)
            ->json('post',  $router, $data)
            ->assertStatus(201)
            ->getData()
            ->data
            ->id;

        $account = Account::find($accountId);

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
        $router = route('accounts.create', ['accountType' => $accountType]);
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

        foreach ($accountType->accountInfos as $accountInfo) {
            $accountInfo = $accountInfo->toArray();
            $accountInfo['pivot']['value'] = Str::random();
            $data['creatorInfos'][] = $accountInfo;
        }

        $this->actingAs($user)
            ->json('post',  $router, $data)
            ->assertStatus(422);
    }

    public function test_middleware_lack_in_users_relationship()
    {
        $user = $this->factoryUser(inverse: true);
        $accountType = AccountType::factory()->create();
        $router = route('accounts.create', ['accountType' => $accountType]);

        $this->actingAs($user)
            ->json('post', $router)
            ->assertStatus(403);
    }
}
