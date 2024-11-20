<?php

namespace App\Filament\Resources\TimeOffResource\Pages;

use App\Filament\Resources\TimeOffResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTimeOff extends CreateRecord
{
    protected static string $resource = TimeOffResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
