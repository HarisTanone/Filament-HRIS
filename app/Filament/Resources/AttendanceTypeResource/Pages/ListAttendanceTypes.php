<?php

namespace App\Filament\Resources\AttendanceTypeResource\Pages;

use App\Filament\Resources\AttendanceTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendanceTypes extends ListRecords
{
    protected static string $resource = AttendanceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->slideOver()->modalWidth('md'),
        ];
    }
}
