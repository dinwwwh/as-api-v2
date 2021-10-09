<?php

namespace Tests\Feature\Tag;

use App\Models\Account;
use App\Models\Tag;
use Arr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['update_tag']);
        $tag = Tag::factory()
            ->has(Account::factory()->count(1), 'accounts')
            ->create();
        $router = route('tags.update', ['tag' => $tag]);
        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(12),
            'type' => Arr::random([Tag::CATEGORY_TYPE, Tag::PROPERTY_TYPE]),
            'parent' => Tag::factory()->create()->toArray(),
        ];

        $status =  $this->actingAs($user)
            ->json('put', $router, $data)
            ->status();
        $this->assertTrue(in_array($status, [200, 201]));

        $this->assertDatabaseMissing('tags', [
            'slug' => $tag->getKey(),
        ]);
        $this->assertDatabaseMissing('taggables', [
            'tag_slug' => $tag->getKey(),
        ]);

        $this->assertDatabaseHas('tags', [
            'slug' => Str::slug($data['name']),
            'name' => $data['name'],
            'description' => $data['description'],
            'parent_slug' => $data['parent']['slug'],
        ]);
        $this->assertDatabaseHas('taggables', [
            'tag_slug' => Str::slug($data['name']),
        ]);
    }

    public function test_middleware_lack_update_tag_permission()
    {
        $user = $this->factoryUser(['update_tag'], true);
        $tag = Tag::factory()->create();
        $router = route('tags.update', ['tag' => $tag]);

        $this->actingAs($user)
            ->json('put', $router)
            ->assertStatus(403);
    }
}
