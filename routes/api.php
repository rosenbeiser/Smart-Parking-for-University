<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — ParKar
|--------------------------------------------------------------------------
| Only the health-check endpoint is kept. The full application runs on
| Blade web routes. The old API controllers (AuthController, AdminParkingApplicationController,
| StudentParkingApplicationController, etc.) have been removed as the project
| uses session-based web authentication instead.
*/

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'app' => config('app.name')]);
});
