<?php

use App\Filament\Resources\AttendanceResource\Api\Handlers\MonthlyHandler;
use App\Filament\Resources\AttendanceResource\Api\Handlers\TodayHandler;
use App\Filament\Resources\TimeOffResource\Api\Handlers\GetByIDHandler;
use App\Filament\Resources\OvertimeResource\Api\Handlers\GetByIDHandler as OverTimeGetByID;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('admin/attendances/today/{employeeId}', [TodayHandler::class, 'handler']);
    Route::get('admin/attendance/monthly/{employeeId}/{month}', [MonthlyHandler::class, 'handler']);
    Route::get('admin/timeoff/employee/{employeeId}', [GetByIDHandler::class, 'handler']);
    Route::get('admin/overtime/employee/{employeeId}', [OverTimeGetByID::class, 'handler']);
});