<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reminder harian (Fase 2/3): pembayaran jatuh tempo & terlambat, kegiatan deadline & terlambat.
Schedule::command('reminders:generate')->dailyAt('07:00')->timezone('Asia/Jakarta');
