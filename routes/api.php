<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SalaryController;

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

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/user', [AuthController::class, 'currentUser']);

    Route::post('users/{id}/permissions', [UserController::class, 'assignPermissions']);
    Route::post('users/report', [UserController::class, 'downloadReport']);
    Route::get('users/permissions', [UserController::class, 'getPermission']);

    Route::apiResource('users', UserController::class);

    Route::apiResource('employee', EmployeeController::class);

    Route::apiResource('salary', SalaryController::class);
});
