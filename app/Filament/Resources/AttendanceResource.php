<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Api\Transformers\AttendanceTransformer;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Schedule;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $navigationGroup = 'Time Management';

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
        if ($user->hasRole('user')) {
            return true;
        }
        return $user && $user->hasAnyPermission(['view attendances', 'view own attendances']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('edit attendances');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('delete attendances');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if ($user && $user->can('create attendances') && !self::hasAttendanceToday()) {
            return true;
        }

        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('employee_id')
                    ->default(function () {
                        return Auth::user()->employee->id ?? null;
                    }),
                Forms\Components\Hidden::make('office_id')
                    ->default(function () {
                        return Auth::user()->employee->office_id ?? null;
                    }),
                Forms\Components\Hidden::make('schedule_id')
                    ->default(function () {
                        $employee_id = Auth::user()->employee->id ?? null;
                        if (!$employee_id)
                            return null;

                        $today = Carbon::now()->locale('id');
                        $dayName = ucfirst($today->dayName);

                        return Schedule::where('employee_id', $employee_id)
                            ->where('week_day', $dayName)
                            ->where('is_active', true)
                            ->first()?->id;
                    }),

                Forms\Components\Hidden::make('attendance_notes')
                    ->default(function (Get $get) {
                        $clockIn = $get('clock_in');
                        $clockOut = $get('clock_out');
                        $scheduleId = $get('schedule_id');

                        $attendance = new Attendance([
                            'clock_in' => $clockIn,
                            'clock_out' => $clockOut,
                            'schedule_id' => $scheduleId,
                        ]);
                        return $attendance->generateAttendanceNotes();
                    }),

                Forms\Components\Hidden::make('status')
                    ->default('Hadir'),
                Card::make()
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->required()
                            ->directory('attendance-photos'),
                        Map::make('location')
                            ->liveLocation(true, true, 10000)
                            ->showMarker()
                            ->zoom(16)
                            ->draggable(false)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                }
                            }),
                        Forms\Components\Hidden::make('latitude'),
                        Forms\Components\Hidden::make('longitude'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Nama Karyawan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d M Y'))
                    ->searchable(),
                // Tables\Columns\TextColumn::make('office.office_name')
                //     ->label('Kantor')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('clock_in')
                    ->label('Jam Masuk')
                    ->dateTime()->formatStateUsing(fn($state) => Carbon::parse($state)->format('H:i'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('clock_out')
                    ->label('Jam Keluar')
                    ->dateTime()->formatStateUsing(fn($state) => Carbon::parse($state)->format('H:i'))
                    ->sortable(),
                // Tables\Columns\ImageColumn::make('photo')
                //     ->label('Foto Masuk'),
                // Tables\Columns\ImageColumn::make('photo_out')
                //     ->label('Foto Keluar'),

                Tables\Columns\TextColumn::make('attendance_notes')
                    ->hidden(fn() => !auth()->user()->hasRole(['administrator', 'hrd']))
                    ->label('Notes')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Tidak Hadir' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->actions([
                Action::make('clock_in')
                    ->label('Clock In')
                    ->icon('heroicon-o-clock')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->required()
                            ->directory('attendance-photos'),
                        Forms\Components\Hidden::make('latitude'),
                        Forms\Components\Hidden::make('longitude'),
                    ])
                    ->hidden(fn(Attendance $record): bool => $record->clock_in !== null)
                    ->action(function (Attendance $record, array $data): void {
                        $record->update([
                            'clock_in' => now(),
                            'photo' => $data['photo'],
                            'status' => 'Hadir',
                            'latitude' => $data['latitude'],
                            'longitude' => $data['longitude'],
                        ]);
                    }),

                Action::make('clock_out')
                    ->label('Clock Out')
                    ->icon('heroicon-o-clock')
                    ->slideOver()
                    ->color('danger')
                    ->form([
                        Forms\Components\FileUpload::make('photo_out')
                            ->image()
                            ->required()
                            ->directory('attendance-photos'),
                        Map::make('location')
                            ->liveLocation(true, true, 10000)
                            ->showMarker()
                            ->zoom(16)
                            ->draggable(false)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('latitude_out', $state['lat']);
                                    $set('longitude_out', $state['lng']);
                                }
                            }),
                        Forms\Components\Hidden::make('latitude_out'),
                        Forms\Components\Hidden::make('longitude_out'),
                    ])
                    ->hidden(fn(Attendance $record): bool => $record->clock_out !== null || $record->clock_in === null || auth()->user()->hasRole('hrd'))
                    ->action(function (Attendance $record, array $data): void {
                        // $clockInTime = $record->clock_in;
                        $record->update([
                            // 'clock_in' => $clockInTime,
                            'clock_out' => Carbon::now()->format('Y-m-d H:i:s'),
                            'photo_out' => $data['photo_out'],
                            'latitude_out' => $data['latitude_out'] ?? null,
                            'longitude_out' => $data['longitude_out'] ?? null,
                        ]);
                    }),
                Action::make('view_attendance')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->modal()
                    ->form(fn(Attendance $record) => [
                        Tabs::make('Attendance Details')
                            ->tabs([
                                Tab::make('Clock In')
                                    ->schema([
                                        FileUpload::make('photo')
                                            ->label('Foto Clock In')
                                            ->image()
                                            ->directory('attendance-photos')
                                            ->default($record->photo) // Field untuk Clock In
                                            ->disabled(),
                                        Map::make('location')
                                            ->showMarker()
                                            ->default(fn() => ['lat' => $record->latitude, 'lng' => $record->longitude]) // Field untuk Clock In
                                            ->zoom(16)
                                            ->draggable(false)
                                    ]),
                                Tab::make('Clock Out')
                                    ->schema([
                                        FileUpload::make('photo_out') // Field untuk Clock Out
                                            ->label('Foto Clock Out')
                                            ->image()
                                            ->directory('attendance-photos')
                                            ->default($record->photo_out) // Field untuk Clock Out
                                            ->disabled(),
                                        Map::make('location_out') // Field untuk Clock Out
                                            ->showMarker()
                                            ->default(fn() => ['lat' => $record->latitude_out, 'lng' => $record->longitude_out]) // Field untuk Clock Out
                                            ->zoom(16)
                                            ->draggable(false)
                                    ]),
                            ]),
                    ])->modalSubmitAction(false),

                // ->action(fn(Attendance $record) => null),
            ])->filters([
                    // Filter::make('created_at')
                    //     ->form([
                    //         DatePicker::make('created_from'),
                    //         DatePicker::make('created_until'),
                    //     ])
                    //     ->query(function (Builder $query, array $data): Builder {
                    //         return $query
                    //             ->when(
                    //                 $data['created_from'],
                    //                 fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                    //             )
                    //             ->when(
                    //                 $data['created_until'],
                    //                 fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                    //             );
                    //     })
                    // filter by range date
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->authorize(fn() => auth()->user()->hasRole('administrator')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->user()->hasRole('user')) {
            return parent::getEloquentQuery()->where('employee_id', auth()->user()->employee->id);
        }
        return parent::getEloquentQuery();
    }

    protected static function hasAttendanceToday(): bool
    {
        return Attendance::where('employee_id', Auth::user()->employee->id)
            ->whereDate('created_at', today())
            ->exists();
    }

    public static function getApiTransformer()
    {
        return AttendanceTransformer::class;
    }
}
