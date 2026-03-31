<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_returns_xml_response(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
    }

    public function test_sitemap_contains_static_pages(): void
    {
        $response = $this->get('/sitemap.xml');
        $body = $response->getContent();

        $this->assertStringContainsString('<loc>'.url('/').'</loc>', $body);
        $this->assertStringContainsString(route('shop.index'), $body);
        $this->assertStringContainsString(route('blog.index'), $body);
        $this->assertStringContainsString(route('about'), $body);
        $this->assertStringContainsString(route('contact'), $body);
        $this->assertStringContainsString(route('faq'), $body);
        $this->assertStringContainsString(route('privacy'), $body);
    }

    public function test_sitemap_includes_active_products(): void
    {
        $product = Product::factory()->create(['status' => 'active']);

        Cache::forget('sitemap.xml');

        $response = $this->get('/sitemap.xml');
        $body = $response->getContent();

        $this->assertStringContainsString(route('products.show', $product->slug), $body);
    }

    public function test_sitemap_excludes_inactive_products(): void
    {
        $product = Product::factory()->create(['status' => 'draft']);

        Cache::forget('sitemap.xml');

        $response = $this->get('/sitemap.xml');
        $body = $response->getContent();

        $this->assertStringNotContainsString(route('products.show', $product->slug), $body);
    }

    public function test_sitemap_includes_published_blog_posts(): void
    {
        $post = BlogPost::create([
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'body' => 'Content here',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        Cache::forget('sitemap.xml');

        $response = $this->get('/sitemap.xml');
        $body = $response->getContent();

        $this->assertStringContainsString(route('blog.show', $post->slug), $body);
    }

    public function test_sitemap_is_cached(): void
    {
        Cache::forget('sitemap.xml');

        $this->get('/sitemap.xml');

        $this->assertTrue(Cache::has('sitemap.xml'));
    }
}
