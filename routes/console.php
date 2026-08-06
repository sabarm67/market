<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Runs after price/Shariah data would be refreshed for the day, per ADR-0002's
// delayed/batch data model — see FRS Module 8.
Schedule::command('alerts:evaluate')->dailyAt('08:00');
