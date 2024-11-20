<?php

namespace App\Filament\Resources\SchredulesResource\Pages;

use App\Filament\Resources\SchredulesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSchredules extends CreateRecord
{
    protected static string $resource = SchredulesResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
