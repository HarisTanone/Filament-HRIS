<?php

namespace App\Filament\Resources\AttendanceDocumentResource\Pages;

use App\Filament\Resources\AttendanceDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendanceDocument extends EditRecord
{
    protected static string $resource = AttendanceDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
