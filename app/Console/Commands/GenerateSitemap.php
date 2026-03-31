<?php

namespace App\Console\Commands;

use App\Http\Controllers\SitemapController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Force-regenerate the sitemap.xml cache immediately';

    public function handle(SitemapController $controller): int
    {
        Cache::forget('sitemap.xml');

        // Trigger a fresh build by calling the controller (which re-caches the result)
        $controller->index();

        $this->info('Sitemap regenerated successfully.');

        return self::SUCCESS;
    }
}
