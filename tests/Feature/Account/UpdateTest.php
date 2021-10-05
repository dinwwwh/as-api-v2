<?php

namespace Tests\Feature\Account;

use App\Models\Account;
use App\Models\File;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
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
        ];

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

        Storage::disk('public')->assertExists('account-images/' . $data['images'][0]->hashName());
        Storage::disk('public')->assertExists('account-images/' . $data['images'][1]->hashName());
        Storage::disk('public')->assertExists('account-images/' . $data['images'][2]->hashName());
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
