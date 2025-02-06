<?php

use App\Filament\Resources\AttendanceResource\Api\Handlers\MonthlyHandler;
use App\Filament\Resources\AttendanceResource\Api\Handlers\TodayHandler;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']); // Login
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('admin/attendances/today/{employeeId}', [TodayHandler::class, 'handler']);
    Route::get('admin/attendance/monthly/{employeeId}/{month}', [MonthlyHandler::class, 'handler']);
});