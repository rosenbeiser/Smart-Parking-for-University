<?php


use App\Http\Controllers\Web\AdminWebController;
use App\Http\Controllers\Web\PermitController;
use App\Http\Controllers\Web\StudentWebController;
use App\Http\Controllers\Web\WeatherController;
use App\Http\Controllers\Web\WebAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — ParKar Blade Frontend
|--------------------------------------------------------------------------
*/

// ── Root — redirect guests to login, authenticated to their dashboard ─────────
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// ── Landing page (direct access) ──────────────────────────────────────────────
Route::get('/landing', [WebAuthController::class, 'landing'])->name('landing');

// ── Guest Auth Routes ─────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [WebAuthController::class, 'login'])->name('login.post');
    Route::get('/register',  [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register'])->name('register.post');
});

// OTP verify (accessible while not fully logged in, session must have challenge_id)
Route::get('/verify-otp',  [WebAuthController::class, 'showOtp'])->name('otp.show');
Route::post('/verify-otp', [WebAuthController::class, 'verifyOtp'])->name('otp.verify');

// Logout
Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('web.auth')
    ->name('logout');

// ── Student & Teacher Routes ──────────────────────────────────────────────────
Route::middleware(['web.auth', 'web.role:student,teacher'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard',                    [StudentWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/applications',                 [StudentWebController::class, 'applications'])->name('applications');

    // Lab 12 — CountryMiddleware: only allow applications from Bangladesh (BD)
    Route::get('/apply',                        [StudentWebController::class, 'showApply'])->name('apply')->middleware('country:BD');
    Route::post('/apply',                       [StudentWebController::class, 'submitApply'])->name('apply.post')->middleware('country:BD');

    Route::get('/renew/{application}',          [StudentWebController::class, 'showRenew'])->name('renew');
    Route::post('/renew/{application}',         [StudentWebController::class, 'submitRenew'])->name('renew.post');
    Route::get('/documents',                    [StudentWebController::class, 'documents'])->name('documents');
    Route::get('/vehicles',                     [StudentWebController::class, 'vehicles'])->name('vehicles');
    Route::get('/profile',                      [StudentWebController::class, 'profile'])->name('profile');
    Route::post('/profile',                     [StudentWebController::class, 'updateProfile'])->name('profile.post');
    Route::get('/notifications',                [StudentWebController::class, 'notifications'])->name('notifications');
});

// ── Permit Routes ─────────────────────────────────────────────────────────────
Route::middleware(['web.auth'])->group(function () {
    Route::get('/permit/{ticket}',                      [PermitController::class, 'show'])->name('permit.show');
    Route::get('/permit/{ticket}/download',             [PermitController::class, 'download'])->name('permit.download');
    Route::get('/permit/application/{application}',     [PermitController::class, 'showByApplication'])->name('permit.by-application');
});

// ── Document Access (any authenticated user, ownership enforced in controller) ──
Route::middleware(['web.auth'])->group(function () {
    Route::get('/documents/{document}/view',     [AdminWebController::class, 'viewDocument'])->name('documents.view');
    Route::get('/documents/{document}/download', [AdminWebController::class, 'downloadDocument'])->name('documents.download');
});

// ── Admin Routes ──────────────────────────────────────────────────────────────
Route::middleware(['web.auth', 'web.role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                          [AdminWebController::class, 'dashboard'])->name('dashboard');
    Route::get('/applications',                       [AdminWebController::class, 'applications'])->name('applications');
    Route::get('/applications/{application}',         [AdminWebController::class, 'showApplication'])->name('applications.show');
    Route::post('/applications/{application}/review', [AdminWebController::class, 'reviewApplication'])->name('applications.review');
    Route::get('/documents/{document}/view',          [AdminWebController::class, 'viewDocument'])->name('documents.view');
    Route::get('/documents/{document}/download',      [AdminWebController::class, 'downloadDocument'])->name('documents.download');
});

// ── General Authenticated Routes (dashboard, applications) ────────────────────
Route::middleware(['web.auth'])->group(function () {
    // /dashboard resolves to the student dashboard for all authenticated users
    Route::get('/dashboard', function () {
        return redirect()->route('student.dashboard');
    })->name('dashboard');



    // Lab 12 — External API JSON endpoint (Open-Meteo via Guzzle) for AJAX weather search
    Route::get('/weather-json', [WeatherController::class, 'getWeatherJson'])->name('weather.json');
});

require __DIR__.'/auth.php';
