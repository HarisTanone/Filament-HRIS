<?php

namespace App\Filament\Resources\WorkFromHomeResource\Pages;

use App\Filament\Resources\WorkFromHomeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkFromHome extends EditRecord
{
    protected static string $resource = WorkFromHomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
