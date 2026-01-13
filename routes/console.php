<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

// Jalankan setiap hari jam 00:01 (1 menit setelah tengah malam)
Schedule::command('employees:update-expired-contracts')
    ->dailyAt('00:01')
    ->withoutOverlapping()
    ->onSuccess(function () {
        Log::info('Cron job update expired contracts berhasil dijalankan pada ' . now());
    })
    ->onFailure(function () {
        Log::error('Cron job update expired contracts gagal pada ' . now());
    });