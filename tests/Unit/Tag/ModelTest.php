<?php

namespace Tests\Unit\Tag;

use App\Models\Tag;
use Str;
use Tests\TestCase;

class ModelTest extends TestCase
{
    public function test_firstOrCreateMany_method()
    {
        $tag = Tag::factory()->create();
        $checkedTags = Tag::firstOrCreateMany([
            [
                'name' => $tag->name,
            ]
        ]);

        $this->assertEquals(1, $checkedTags->count());
        $this->assertTrue($tag->is($checkedTags->first()));

        $UncreatedTagName = Str::random(36);
        $description = Str::random(100);
        $checkedTags = Tag::firstOrCreateMany([
            [
                'name' => $tag->name,
            ],
            [
                'name' => $UncreatedTagName,
                'description' => $description,
            ]
        ]);

        $this->assertEquals(2, $checkedTags->count());
        $this->assertTrue($tag->is($checkedTags->first()));
        $this->assertEquals(Str::slug($UncreatedTagName), $checkedTags->last()->getKey());
    }

    public function test_getRepresentation_method()
    {
        // 1 layer
        $tag =  Tag::factory()->for(Tag::factory(), 'parent')->create();
        $this->assertEquals($tag->parent->getKey(), $tag->getRepresentation()->getKey());

        // 2 layers
        $tag =  Tag::factory()
            ->for(
                Tag::factory()->for(Tag::factory(), 'parent'),
                'parent'
            )
            ->create();
        $this->assertEquals($tag->parent->parent->getKey(), $tag->getRepresentation()->getKey());

        // Loop
        $tag =  Tag::factory()
            ->create();
        $tag->update([
            'parent_slug' => $tag->getKey(),
        ]);
        $this->assertEquals(null, $tag->getRepresentation());
    }
}
