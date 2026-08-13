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

// Schedule unanswered public job request alerts
Schedule::command('requests:notify-unanswered')
    ->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders.log'));

// Schedule admin daily digest
Schedule::command('admin:daily-digest')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders.log'));

// Schedule review reminders
Schedule::command('reviews:send-reminders')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders.log'));

// Schedule low-rating craftsman alerts
Schedule::command('admin:notify-low-ratings')
    ->weeklyOn(1, '08:30')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reminders.log'));
