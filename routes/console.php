<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule appointment reminders
Schedule::command('appointments:send-reminders --type=email')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders.log'));

Schedule::command('appointments:send-reminders --type=sms')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders.log'));

// Schedule subscription expiry reminders
Schedule::command('subscriptions:send-expiry-reminders')
    ->daily()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders.log'));

// Schedule stale recruitment lead alerts
Schedule::command('recruitment:notify-stale-leads')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders.log'));
