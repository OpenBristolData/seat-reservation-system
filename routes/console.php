<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// Schedule seat reset daily at 4:05 PM
Schedule::command('seats:reset')
    ->dailyAt('16:05')
    ->timezone(config('app.timezone')); // e.g., 'America/New_York'