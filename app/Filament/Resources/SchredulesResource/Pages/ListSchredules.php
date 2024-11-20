<?php

namespace App\Filament\Resources\SchredulesResource\Pages;

use App\Filament\Resources\SchredulesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchredules extends ListRecords
{
    protected static string $resource = SchredulesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->slideOver(),
        ];
    }
}
