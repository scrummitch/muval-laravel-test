<?php

use App\Http\Controllers\API\TasksApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('tasks', TasksApiController::class);

Route::get('user', function () {
    return request()->user();
});
