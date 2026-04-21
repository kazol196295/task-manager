<?php

use App\Http\Controllers\Api\TaskController as ApiTaskController;
use Illuminate\Support\Facades\Route;

// Notice NO ->prefix('api') here, bootstrap/app.php handles it!
Route::name('api.')->group(function () {
    Route::get('/tasks/stats', [ApiTaskController::class, 'stats']);
    Route::patch('/tasks/{task}/status', [ApiTaskController::class, 'updateStatus']);
    Route::apiResource('tasks', ApiTaskController::class);
});
