<?php
namespace App\Filament\Resources\AttendanceTypeResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\AttendanceTypeResource;
use Illuminate\Routing\Router;


class AttendanceTypeApiService extends ApiService
{
    protected static string | null $resource = AttendanceTypeResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
