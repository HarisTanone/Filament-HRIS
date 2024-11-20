<?php
namespace App\Filament\Resources\OvertimeResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\OvertimeResource;
use Illuminate\Routing\Router;


class OvertimeApiService extends ApiService
{
    protected static string | null $resource = OvertimeResource::class;

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
