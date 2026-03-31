<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\Category;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->applyDatabaseSettings();

        view()->composer('partials.frontend.header', function ($view) {
            $view->with('headerCategories',
                Category::whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->take(7)
                    ->get(['id', 'name', 'slug'])
            );
        });
    }

    /**
     * Override framework configs with values stored in app_settings table.
     * Wrapped in try/catch so a fresh install (migrations not yet run) won't crash.
     */
    private function applyDatabaseSettings(): void
    {
        try {
            $settings = AppSetting::getMany([
                'store_name' => config('app.name'),
                'mail_from_name' => config('mail.from.name'),
                'mail_from_address' => config('mail.from.address'),
                'admin_notification_email' => config('mail.from.address'),
                'store_timezone' => config('app.timezone', 'Africa/Lagos'),
                'session_lifetime' => (string) config('session.lifetime', 120),
            ]);

            config([
                'app.name' => $settings['store_name'],
                'mail.from.name' => $settings['mail_from_name'],
                'mail.from.address' => $settings['mail_from_address'],
                'mail.admin_email' => $settings['admin_notification_email'],
                'app.timezone' => $settings['store_timezone'],
                'session.lifetime' => (int) $settings['session_lifetime'],
            ]);
        } catch (\Throwable) {
            // DB unavailable (fresh install / artisan migrate) — use .env defaults.
        }
    }
}
