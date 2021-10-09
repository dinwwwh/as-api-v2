<?php

namespace Tests\Feature\Tag;

use App\Models\Account;
use App\Models\Tag;
use Arr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Str;
use Tests\TestCase;

class MigrateTest extends TestCase
{
    public function test_controller()
    {
        $user = $this->factoryUser(['update_tag']);
        $tag = Tag::factory()
            ->has(Account::factory()->count(1), 'accounts')
            ->create();
        $migratedTag = Tag::factory()
            ->create();
        $router = route('tags.migrate', ['tag' => $tag, 'migratedTag' => $migratedTag]);
        $data = [
            'hasMigrateInFuture' => true,
        ];

        $this->actingAs($user)
            ->json('patch', $router, $data)
            ->assertStatus(200);

        $this->assertDatabaseHas('tags', [
            'slug' => $tag->getKey(),
            'parent_slug' => $migratedTag->getKey(),
        ]);

        $this->assertDatabaseMissing('taggables', [
            'tag_slug' => $tag->getKey(),
        ]);

        $this->assertDatabaseHas('taggables', [
            'tag_slug' => $migratedTag->getKey(),
        ]);
    }
}
