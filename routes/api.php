<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('projects', ProjectController::class);

    Route::get('projects/{project}/stats', [ProjectController::class, 'stats']);

    Route::apiResource('projects.tasks', TaskController::class)->shallow();


    Route::get('projects/{project}/reports', [ReportController::class, 'list']);
    Route::get('projects/{project}/reports/project', [ReportController::class, 'projectReport']);
    Route::get('projects/{project}/reports/tasks', [ReportController::class, 'tasksReport']);
    Route::get('projects/{project}/reports/team', [ReportController::class, 'teamReport']);
    Route::post('projects/{project}/reports/custom', [ReportController::class, 'customReport']);
});
