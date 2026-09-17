<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule::command('auction:broadcast-floating')->everyMinute();

// تذكيرات يومية 7 صباحاً: حصص اليوم + أقساط متأخرة
Schedule::command('lms:remind')->dailyAt('07:00');
