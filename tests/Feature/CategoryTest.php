<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_have_parent(): void
    {
        $parent = Category::factory()->create(['name' => 'Lace Fabric']);
        $child = Category::factory()->create([
            'name' => 'Swiss Voile Lace',
            'parent_id' => $parent->id,
        ]);

        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertEquals($parent->id, $child->parent->id);
    }

    public function test_category_can_have_children(): void
    {
        $parent = Category::factory()->create(['name' => 'Lace Fabric']);
        $child1 = Category::factory()->create(['name' => 'Swiss Voile', 'parent_id' => $parent->id]);
        $child2 = Category::factory()->create(['name' => 'Guipure Cord', 'parent_id' => $parent->id]);

        $this->assertCount(2, $parent->children);
        $this->assertTrue($parent->children->contains($child1));
        $this->assertTrue($parent->children->contains($child2));
    }

    public function test_top_level_category_has_no_parent(): void
    {
        $category = Category::factory()->create();

        $this->assertNull($category->parent_id);
        $this->assertNull($category->parent);
    }

    public function test_deleting_parent_nullifies_children(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $parent->delete();

        $child->refresh();
        $this->assertNull($child->parent_id);
    }
}
