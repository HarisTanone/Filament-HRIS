<?php
namespace App\Filament\Resources\TimeOffResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\TimeOffResource;
use Illuminate\Routing\Router;


class TimeOffApiService extends ApiService
{
    protected static string | null $resource = TimeOffResource::class;

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
