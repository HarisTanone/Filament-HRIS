<?php

namespace App\Filament\Resources\TimeOffResource\Pages;

use App\Filament\Resources\TimeOffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeOff extends EditRecord
{
    protected static string $resource = TimeOffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
