<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SeatController as AdminSeatController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Auth\GoogleController;


//Testing
Route::get('/debug-api', function () {
    $testEmail = 'gihangunathilakavck@gmail.com'; // Replace with a valid test email
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->post('https://prohub.slt.com.lk/ProhubTrainees/api/MainApi/AllActiveTrainees', [
        'secretKey' => 'TraineesApi_SK_8d!x7F#mZ3@pL2vW'
    ]);

    return response()->json([
        'status' => $response->status(),
        'body' => $response->body(),
        'successful' => $response->successful(),
        'email' => $testEmail
    ]);
});

// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


 
// Google Auth Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Intern Routes
Route::middleware(['auth', 'intern'])->group(function () {
    Route::get('/dashboard', [ReservationController::class, 'dashboard'])->name('dashboard');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    // Add this route (if missing)
Route::get('/reservations/available-seats', [ReservationController::class, 'getAvailableSeats'])
    ->name('reservations.available-seats');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Seat Management
     Route::resource('seats', AdminSeatController::class)
         ->names([
             'index' => 'admin.seats.index',
             'create' => 'admin.seats.create',
             'store' => 'admin.seats.store',
             'show' => 'admin.seats.show',
             'edit' => 'admin.seats.edit',
             'update' => 'admin.seats.update',
             'destroy' => 'admin.seats.destroy'
         ]);
    
    // Reservation Management
    Route::get('/reservations', [AdminReservationController::class, 'index'])->name('admin.reservations.index');
    Route::delete('/reservations/{reservation}', [AdminReservationController::class, 'destroy'])->name('admin.reservations.destroy');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
});