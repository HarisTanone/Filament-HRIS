<?php

namespace App\Filament\Resources\SchredulesResource\Pages;

use App\Filament\Resources\SchredulesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchredules extends EditRecord
{
    protected static string $resource = SchredulesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
