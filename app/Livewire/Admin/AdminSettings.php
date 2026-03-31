<?php

namespace App\Livewire\Admin;

use App\Models\AppSetting;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AdminSettings extends Component
{
    public string $activeTab = 'general';

    // ── General ──────────────────────────────────────────────────────────────
    public string $store_name = '';

    public string $store_tagline = '';

    public string $store_email = '';

    public string $store_phone = '';

    public string $store_address = '';

    public string $store_city = '';

    public string $store_country = 'Nigeria';

    // ── Email ────────────────────────────────────────────────────────────────
    public string $mail_from_name = '';

    public string $mail_from_address = '';

    public string $admin_notification_email = '';

    public bool $notify_new_order = true;

    public bool $notify_new_contact = true;

    public bool $notify_new_subscriber = true;

    public bool $notify_low_stock = true;

    // ── Store ────────────────────────────────────────────────────────────────
    public string $store_timezone = 'Africa/Lagos';

    public string $store_currency = 'NGN';

    public bool $maintenance_mode = false;

    public string $currency_symbol = '₦';

    public string $store_status = 'open';

    // ── SEO ──────────────────────────────────────────────────────────────────
    public string $seo_title = '';

    public string $seo_description = '';

    public string $seo_keywords = '';

    public bool $seo_noindex = false;

    // ── Security ─────────────────────────────────────────────────────────────
    public int $session_lifetime = 120;

    public bool $require_email_verification = true;

    public bool $force_https = true;

    public function mount(): void
    {
        $settings = AppSetting::getMany([
            'store_name' => config('app.name', '1st Delightsome'),
            'store_tagline' => 'Premium Fabrics & Textiles',
            'store_email' => 'hello@delightsome.com',
            'store_phone' => '+234 800 000 0000',
            'store_address' => '30b Opebi Rd, Opebi',
            'store_city' => 'Ikeja, Lagos',
            'store_country' => 'Nigeria',
            'mail_from_name' => config('mail.from.name', '1st Delightsome'),
            'mail_from_address' => config('mail.from.address', 'hello@delightsome.com'),
            'admin_notification_email' => config('mail.from.address', 'admin@delightsome.com'),
            'notify_new_order' => '1',
            'notify_new_contact' => '1',
            'notify_new_subscriber' => '0',
            'notify_low_stock' => '1',
            'store_timezone' => 'Africa/Lagos',
            'store_currency' => 'NGN',
            'maintenance_mode' => '0',
            'currency_symbol' => '₦',
            'store_status' => 'open',
            'seo_title' => '1st Delightsome Fabrics | Premium Textiles in Lagos',
            'seo_description' => 'Shop premium fabrics and textiles at 1st Delightsome. Based in Ikeja, Lagos.',
            'seo_keywords' => 'fabric, textiles, Lagos, aso-oke, ankara',
            'seo_noindex' => '0',
            'session_lifetime' => '120',
            'require_email_verification' => '1',
            'force_https' => '1',
        ]);

        $this->store_name = $settings['store_name'];
        $this->store_tagline = $settings['store_tagline'];
        $this->store_email = $settings['store_email'];
        $this->store_phone = $settings['store_phone'];
        $this->store_address = $settings['store_address'];
        $this->store_city = $settings['store_city'];
        $this->store_country = $settings['store_country'];
        $this->mail_from_name = $settings['mail_from_name'];
        $this->mail_from_address = $settings['mail_from_address'];
        $this->admin_notification_email = $settings['admin_notification_email'];
        $this->notify_new_order = (bool) $settings['notify_new_order'];
        $this->notify_new_contact = (bool) $settings['notify_new_contact'];
        $this->notify_new_subscriber = (bool) $settings['notify_new_subscriber'];
        $this->notify_low_stock = (bool) $settings['notify_low_stock'];
        $this->store_timezone = $settings['store_timezone'];
        $this->store_currency = $settings['store_currency'];
        $this->maintenance_mode = (bool) $settings['maintenance_mode'];
        $this->currency_symbol = $settings['currency_symbol'];
        $this->store_status = $settings['store_status'];
        $this->seo_title = $settings['seo_title'];
        $this->seo_description = $settings['seo_description'];
        $this->seo_keywords = $settings['seo_keywords'];
        $this->seo_noindex = (bool) $settings['seo_noindex'];
        $this->session_lifetime = (int) $settings['session_lifetime'];
        $this->require_email_verification = (bool) $settings['require_email_verification'];
        $this->force_https = (bool) $settings['force_https'];
    }

    public function saveGeneral(): void
    {
        $this->validate([
            'store_name' => ['required', 'string', 'max:80'],
            'store_tagline' => ['nullable', 'string', 'max:120'],
            'store_email' => ['required', 'email', 'max:255'],
            'store_phone' => ['nullable', 'string', 'max:30'],
            'store_address' => ['nullable', 'string', 'max:200'],
            'store_city' => ['nullable', 'string', 'max:100'],
            'store_country' => ['nullable', 'string', 'max:80'],
        ]);

        foreach (['store_name', 'store_tagline', 'store_email', 'store_phone', 'store_address', 'store_city', 'store_country'] as $key) {
            AppSetting::set($key, $this->$key);
        }

        $this->dispatch('toast', type: 'success', message: 'General settings saved.');
    }

    public function saveEmail(): void
    {
        $this->validate([
            'mail_from_name' => ['required', 'string', 'max:80'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'admin_notification_email' => ['required', 'email', 'max:255'],
        ]);

        foreach (['mail_from_name', 'mail_from_address', 'admin_notification_email', 'notify_new_order', 'notify_new_contact', 'notify_new_subscriber', 'notify_low_stock'] as $key) {
            AppSetting::set($key, is_bool($this->$key) ? ($this->$key ? '1' : '0') : $this->$key);
        }

        $this->dispatch('toast', type: 'success', message: 'Email & notification settings saved.');
    }

    public function saveStore(): void
    {
        $this->validate([
            'store_timezone' => ['required', 'string'],
            'store_currency' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'store_status' => ['required', 'in:open,closed,by_appointment'],
        ]);

        foreach (['store_timezone', 'store_currency', 'maintenance_mode', 'currency_symbol', 'store_status'] as $key) {
            AppSetting::set($key, is_bool($this->$key) ? ($this->$key ? '1' : '0') : $this->$key);
        }

        $this->dispatch('toast', type: 'success', message: 'Store settings saved.');
    }

    public function saveSeo(): void
    {
        $this->validate([
            'seo_title' => ['nullable', 'string', 'max:120'],
            'seo_description' => ['nullable', 'string', 'max:300'],
            'seo_keywords' => ['nullable', 'string', 'max:300'],
        ]);

        foreach (['seo_title', 'seo_description', 'seo_keywords', 'seo_noindex'] as $key) {
            AppSetting::set($key, is_bool($this->$key) ? ($this->$key ? '1' : '0') : $this->$key);
        }

        $this->dispatch('toast', type: 'success', message: 'SEO settings saved.');
    }

    public function saveSecurity(): void
    {
        $this->validate([
            'session_lifetime' => ['required', 'integer', 'min:15', 'max:10080'],
        ]);

        foreach (['session_lifetime', 'require_email_verification', 'force_https'] as $key) {
            AppSetting::set($key, is_bool($this->$key) ? ($this->$key ? '1' : '0') : $this->$key);
        }

        $this->dispatch('toast', type: 'success', message: 'Security settings saved.');
    }

    public function render(): View
    {
        return view('livewire.admin.admin-settings');
    }
}
