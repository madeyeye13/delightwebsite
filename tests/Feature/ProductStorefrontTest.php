<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_array_includes_variant_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 50]);

        $variant1 = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Red',
            'hex' => '#ff0000',
            'stock' => 10,
            'stock_unit' => 'units',
            'is_default' => true,
            'sort_order' => 0,
        ]);

        $variant2 = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Blue',
            'hex' => '#0000ff',
            'stock' => 20,
            'stock_unit' => 'units',
            'is_default' => false,
            'sort_order' => 1,
        ]);

        $product->load('variants');
        $data = $product->toStorefrontArray();

        $this->assertCount(2, $data['variants']);

        $this->assertEquals('Red', $data['variants'][0]['color']);
        $this->assertEquals(10, $data['variants'][0]['stock']);
        $this->assertArrayHasKey('priceAdjustment', $data['variants'][0]);

        $this->assertEquals('Blue', $data['variants'][1]['color']);
        $this->assertEquals(20, $data['variants'][1]['stock']);
    }

    public function test_storefront_array_without_variants(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 25]);
        $product->load('variants');

        $data = $product->toStorefrontArray();

        $this->assertEmpty($data['variants']);
        $this->assertEquals(25, $data['stockQuantity']);
    }
}
