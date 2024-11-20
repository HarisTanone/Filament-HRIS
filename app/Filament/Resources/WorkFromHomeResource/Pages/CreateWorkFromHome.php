<?php

namespace App\Filament\Resources\WorkFromHomeResource\Pages;

use App\Filament\Resources\WorkFromHomeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkFromHome extends CreateRecord
{
    protected static string $resource = WorkFromHomeResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
