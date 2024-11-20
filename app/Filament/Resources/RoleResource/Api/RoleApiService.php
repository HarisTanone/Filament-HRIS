<?php
namespace App\Filament\Resources\RoleResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\RoleResource;
use Illuminate\Routing\Router;


class RoleApiService extends ApiService
{
    protected static string | null $resource = RoleResource::class;

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
