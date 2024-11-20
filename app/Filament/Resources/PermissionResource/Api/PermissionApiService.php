<?php
namespace App\Filament\Resources\PermissionResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\PermissionResource;
use Illuminate\Routing\Router;


class PermissionApiService extends ApiService
{
    protected static string | null $resource = PermissionResource::class;

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
