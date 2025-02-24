<?php

use App\Http\Controllers\UserSessionsController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TasksController;

Route::view('/', 'index');

Route::get('/login', [UserSessionsController::class, 'login'])->name('login');
Route::post('/login', [UserSessionsController::class, 'store'])->name('store');

Route::get('/register', [RegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegistrationController::class, 'register'])->name('register');

Route::group(['middleware' => 'auth'], function () {
    Route::post('/logout', [UserSessionsController::class, 'logout'])->name('logout');
    Route::resource('tasks', TasksController::class);

    Route::post('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken($request->token_name);

        return ['token' => $token->plainTextToken];
    });
});
