<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Keep liveness independent from sessions, CSRF, APP_KEY, and the database.
Route::get('/health/startup', static fn () => response()->json(['status' => 'starting']))
    ->name('health.startup');

Route::get('/health/live', static fn () => response()->json(['status' => 'live']))
    ->name('health.live');

Route::get('/health/ready', static function () {
    try {
        DB::connection()->getPdo();

        return response()->json(['status' => 'ready']);
    } catch (Throwable) {
        return response()->json(['status' => 'not ready', 'error' => 'Database unavailable'], 503);
    }
})->name('health.ready');

return;

// This route file is loaded without a middleware group from bootstrap/app.php.
// The explicit return keeps it safe if included more than once by tooling.
