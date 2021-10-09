<?php

namespace Tests\Feature\Tag;

use App\Models\Tag;
use Arr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class CreateTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['create_tag']);
        $router = route('tags.create');
        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(12),
            'type' => Arr::random([Tag::CATEGORY_TYPE, Tag::PROPERTY_TYPE]),
            'parent' => Tag::factory()->create()->toArray(),
        ];

        $this->actingAs($user)
            ->json('post', $router, $data)
            ->assertStatus(201);

        $this->assertDatabaseHas('tags', [
            'slug' => Str::slug($data['name']),
            'name' => $data['name'],
            'description' => $data['description'],
            'parent_slug' => $data['parent']['slug'],
        ]);
    }

    public function test_controller_loop_parent()
    {
        $user = $this->factoryUser(['create_tag']);
        $router = route('tags.create');
        $parent = Tag::factory()->create();
        $parent->update([
            'parent_slug' => $parent->getKey(),
        ]);
        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(12),
            'type' => Arr::random([Tag::CATEGORY_TYPE, Tag::PROPERTY_TYPE]),
            'parent' => $parent->toArray(),
        ];

        $this->actingAs($user)
            ->json('post', $router, $data)
            ->assertStatus(201);

        $this->assertDatabaseHas('tags', [
            'slug' => Str::slug($data['name']),
            'name' => $data['name'],
            'description' => $data['description'],
            'parent_slug' => null,
        ]);
    }

    public function test_middleware_lack_create_tag_permission()
    {
        $user = $this->factoryUser(['create_tag'], true);
        $router = route('tags.create');

        $this->actingAs($user)
            ->json('post', $router)
            ->assertStatus(403);
    }
}
