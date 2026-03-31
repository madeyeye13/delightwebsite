<?php

use App\Jobs\SendOrderReminderEmails;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SendOrderReminderEmails)->hourly();

Schedule::command('currency:update-rates')
    ->cron('0 2 */2 * *')
    ->withoutOverlapping();