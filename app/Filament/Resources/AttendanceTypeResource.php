<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceTypeResource\Pages;
use App\Models\AttendanceType;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AttendanceTypeResource extends Resource
{
    protected static ?string $model = AttendanceType::class;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->hasAnyPermission(['view attendance_types']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('edit attendance_types');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('delete attendance_types');
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create attendance_types');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('type_name')
                        ->placeholder('Attendance Type')
                        ->required()
                        ->label('Attendance Type Name'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type_name')
                    ->sortable()
                    ->searchable()
                    ->label('Attendance Type Name')
            ])
            ->filters([
                // Kamu bisa menambahkan filter tambahan disini jika diperlukan
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->slideOver()->modalWidth('md'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Jika ada relasi yang perlu ditambahkan, bisa disini.
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('type_name'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceTypes::route('/'),
            // 'create' => Pages\CreateAttendanceType::route('/create'),
            // 'edit' => Pages\EditAttendanceType::route('/{record}/edit'),
        ];
    }
}
