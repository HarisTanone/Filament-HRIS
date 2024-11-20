<?php

namespace App\Filament\Resources\AttendanceDocumentResource\Pages;

use App\Filament\Resources\AttendanceDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendanceDocument extends CreateRecord
{
    protected static string $resource = AttendanceDocumentResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
