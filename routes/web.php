<?php

use App\Http\Controllers\LeaveRecordController;
use App\Http\Controllers\TimeRecordController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('index');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Authenticated group (Verified as well)
Route::middleware([
    'auth',
    'verified',
])->group(function () {

    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Clock route to TimeRecordController
    Route::post('/clock', [TimeRecordController::class, 'handleClock']);

        // Add leave route to LeaveRecordController
    Route::post('/leave', [LeaveRecordController::class, 'addLeave']);

});

require __DIR__.'/auth.php';
