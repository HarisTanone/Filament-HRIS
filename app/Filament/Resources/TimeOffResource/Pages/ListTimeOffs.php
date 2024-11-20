<?php

namespace App\Filament\Resources\TimeOffResource\Pages;

use App\Filament\Resources\TimeOffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTimeOffs extends ListRecords
{
    protected static string $resource = TimeOffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
