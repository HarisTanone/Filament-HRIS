<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Api\Transformers\ScheduleTransformer;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Tables\Columns\TextColumn;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;
    protected static ?string $navigationGroup = 'Time Management';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('user')) {
            return static::getModel()::count();
        }

        return null;
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->hasAnyPermission(['view schedules', 'view own schedules']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('edit schedules');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('delete schedules');
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create schedules');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('employee_id')
                    ->label('Employee')
                    ->relationship('employee', 'full_name')
                    ->required(),
                TextInput::make('shift_name')
                    ->label('Shift Name')
                    ->placeholder('e.g., Morning, Afternoon, Night')
                    ->required(),
                DateTimePicker::make('start_time')
                    ->label('Start Time')
                    ->required(),
                DateTimePicker::make('end_time')
                    ->label('End Time')
                    ->nullable(),
                Select::make('week_day')
                    ->label('Day of the Week')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                        'Minggu' => 'Minggu',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Textarea::make('notes')
                    ->label('Notes')
                    ->placeholder('Additional information about the schedule')
                    ->nullable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')->label('Employee')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('shift_name')->label('Shift Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('start_time')->label('Start Time')->dateTime('d M Y H:i'),
                Tables\Columns\TextColumn::make('end_time')->label('End Time')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('week_day')->label('Day'),
                Tables\Columns\BooleanColumn::make('is_active')->label('Active'),
            ])
            ->filters([
                // Optionally add filters here if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            // 'create' => Pages\CreateSchedule::route('/create'),
            // 'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getApiTransformer()
    {
        return ScheduleTransformer::class;
    }
}
