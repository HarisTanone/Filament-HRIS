<?php

namespace App\Filament\Resources\AttendanceDocumentResource\Pages;

use App\Filament\Resources\AttendanceDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceDocuments extends ListRecords
{
    protected static string $resource = AttendanceDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
