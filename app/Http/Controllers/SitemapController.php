<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Sitemap is cached for 24 hours.
     * Re-generates automatically after the TTL expires or when cache is cleared.
     */
    private const CACHE_TTL_HOURS = 24;

    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addHours(self::CACHE_TTL_HOURS), function (): string {
            return $this->buildSitemap();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }

    private function buildSitemap(): string
    {
        $urls = collect();

        // ── Static public pages ────────────────────────────────────────────
        $staticPages = [
            ['loc' => url('/'),               'changefreq' => 'daily',   'priority' => '1.0'],
            ['loc' => route('shop.index'),     'changefreq' => 'daily',   'priority' => '0.9'],
            ['loc' => route('blog.index'),     'changefreq' => 'daily',   'priority' => '0.8'],
            ['loc' => route('about'),          'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('contact'),        'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('faq'),            'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('return-policy'),  'changefreq' => 'yearly',  'priority' => '0.4'],
            ['loc' => route('terms'),          'changefreq' => 'yearly',  'priority' => '0.4'],
            ['loc' => route('privacy'),        'changefreq' => 'yearly',  'priority' => '0.4'],
        ];

        foreach ($staticPages as $page) {
            $urls->push($page);
        }

        // ── Dynamic: products ──────────────────────────────────────────────
        Product::query()
            ->active()
            ->select(['slug', 'updated_at'])
            ->orderBy('updated_at', 'desc')
            ->each(function (Product $product) use ($urls): void {
                $urls->push([
                    'loc' => route('products.show', $product->slug),
                    'lastmod' => $product->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ]);
            });

        // ── Dynamic: blog posts ────────────────────────────────────────────
        BlogPost::query()
            ->published()
            ->select(['slug', 'published_at', 'updated_at'])
            ->orderBy('published_at', 'desc')
            ->each(function (BlogPost $post) use ($urls): void {
                $lastmod = ($post->updated_at ?? $post->published_at)?->toAtomString();
                $entry = [
                    'loc' => route('blog.show', $post->slug),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
                if ($lastmod) {
                    $entry['lastmod'] = $lastmod;
                }
                $urls->push($entry);
            });

        return $this->renderXml($urls->all());
    }

    /** @param array<int, array<string, string>> $urls */
    private function renderXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e($url['loc'])."</loc>\n";
            if (! empty($url['lastmod'])) {
                $xml .= '    <lastmod>'.e($url['lastmod'])."</lastmod>\n";
            }
            if (! empty($url['changefreq'])) {
                $xml .= '    <changefreq>'.e($url['changefreq'])."</changefreq>\n";
            }
            if (! empty($url['priority'])) {
                $xml .= '    <priority>'.e($url['priority'])."</priority>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
