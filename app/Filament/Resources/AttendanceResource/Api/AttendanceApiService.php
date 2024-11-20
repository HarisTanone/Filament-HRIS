<?php
namespace App\Filament\Resources\AttendanceResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\AttendanceResource;
use Illuminate\Routing\Router;


class AttendanceApiService extends ApiService
{
    protected static string | null $resource = AttendanceResource::class;

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
