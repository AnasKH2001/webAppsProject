<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ComplaintHistoryController;
use App\Http\Controllers\GovernmentEntityController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');





Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('logout');

Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])
    ->middleware('guest')
    ->name('verify-otp');

Route::post('/resend-otp', [AuthController::class, 'resendOtp'])
    ->middleware('guest')
    ->name('resend-otp');


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/create_employee', [UserController::class, 'createEmployee']);
});


Route::middleware(['auth:sanctum'])->get('/entities', [GovernmentEntityController::class, 'index']);


// Citizens: only their own complaints
Route::middleware(['auth:sanctum', 'citizen.complaint'])->group(function () {
    Route::get('/citizen_complaint/{id}', [ComplaintController::class, 'show']);
    Route::patch('/citizen_complaint/{id}', [ComplaintController::class, 'update']);
});

// Citizens: submit and manage their own complaints
Route::middleware(['auth:sanctum'])->group(function () {
    // Submit a new complaint
    Route::post('/make_complaint', [ComplaintController::class, 'store']);
});

// Citizens: list their own complaints
Route::middleware(['auth:sanctum'])->get('/my_complaints', [ComplaintController::class, 'index']);


// Employees: only complaints for their entity
Route::middleware(['auth:sanctum', 'employee.complaint'])->group(function () {
    Route::get('/employee_complaint/{id}', [ComplaintController::class, 'show']);
    Route::patch('/employee_complaint/{id}', [ComplaintController::class, 'update']);
    Route::get('/employee_complaints', [ComplaintController::class, 'index']);
});

// Admins: all complaints
Route::middleware(['auth:sanctum', 'admin.complaint'])->group(function () {
    Route::get('/admin_complaint/{id}', [ComplaintController::class, 'show']);
    Route::patch('/admin_complaints/{id}', [ComplaintController::class, 'update']);
    Route::get('/complaints', [ComplaintController::class, 'index']);
});


Route::middleware(['auth:sanctum'])->get('/complaints/reference/{ref}', [ComplaintController::class, 'showByReference']);


Route::middleware(['auth:sanctum'])->get('/complaints/{id}/history', [ComplaintHistoryController::class, 'history']);
