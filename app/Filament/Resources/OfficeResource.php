<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Office;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Table;

class OfficeResource extends Resource
{
    protected static ?string $model = Office::class;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('user')) {
            return false;
        }
        return $user && $user->hasAnyPermission(['view users']);
    }



    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('edit offices');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('delete offices');
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create offices');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('office_name')
                    ->placeholder('Office Name')
                    ->required()
                    ->maxLength(255),

                TextArea::make('description')
                    ->placeholder('Add some details about the office...')
                    ->nullable(),
                Fieldset::make('Office Location Coordinates')
                    ->schema([
                        TextInput::make('latitude')
                            ->numeric()
                            ->regex('/^\-?\d*(\.\d+)?$/')
                            ->placeholder('Latitude')
                            ->required(),

                        TextInput::make('longitude')
                            ->numeric()
                            ->regex('/^\-?\d*(\.\d+)?$/')
                            ->placeholder('Longitude')
                            ->required(),
                        TextInput::make('radius')
                            ->numeric()
                            ->placeholder('Radius (m)')
                            ->required(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('office_name')
                    ->sortable()
                    ->searchable()
                    ->label('Office Name'),
                TextColumn::make('description')
                    ->sortable()
                    ->searchable()
                    ->label('Description'),
                TextColumn::make('latitude')
                    ->sortable()
                    ->label('Latitude'),
                TextColumn::make('longitude')
                    ->sortable()
                    ->label('Longitude'),
                TextColumn::make('radius')
                    ->sortable()
                    ->label('Radius (m)'),
            ])
            ->filters([
                // Kamu bisa menambahkan filter tambahan disini jika diperlukan
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
                Infolists\Components\TextEntry::make('office_name'),
                Infolists\Components\TextEntry::make('latitude'),
                Infolists\Components\TextEntry::make('longitude')
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
