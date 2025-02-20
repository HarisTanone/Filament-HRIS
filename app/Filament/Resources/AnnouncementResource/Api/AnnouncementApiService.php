<?php
namespace App\Filament\Resources\AnnouncementResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\AnnouncementResource;
use Illuminate\Routing\Router;


class AnnouncementApiService extends ApiService
{
    protected static string | null $resource = AnnouncementResource::class;

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
