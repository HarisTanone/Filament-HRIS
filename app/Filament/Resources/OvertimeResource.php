<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeResource\Api\Transformers\OvertimeTransformer;
use App\Filament\Resources\OvertimeResource\Pages;
use App\Models\Employee;
use App\Models\Overtime;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OvertimeResource extends Resource
{
    protected static ?string $model = Overtime::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Time Management';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('administrator')) {
            return true;
        }
        return $user && $user->hasAnyPermission(['view overtimes']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('edit overtimes');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('delete overtimes');
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create overtimes');
    }
    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('administrator');
        $isHRD = $user && $user->hasRole('hrd');
        $isUser = $user && $user->hasRole('user');
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label('Employee Name')
                    ->relationship('employee', 'full_name')
                    ->required()
                    ->searchable(!$isUser)
                    ->default(function () {
                        $user = auth()->user();
                        if ($user && $user->hasRole('user')) {
                            return $user->employee['id'];
                        }
                        return null;
                    })
                    ->options(function () {
                        $user = auth()->user();
                        if ($user && $user->hasRole('user')) {
                            $employee = $user->employee;
                            return $employee ? [$employee['id'] => $employee['full_name']] : [];
                        }
                        return Employee::all()->pluck('full_name', 'id');
                    }),

                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->default(now())
                    ->maxDate(now()),

                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->seconds(false),


                Forms\Components\TimePicker::make('end_time')
                    ->required()
                    ->seconds(false)
                    ->after('start_time')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {

                        $start = Carbon::parse($get('start_time'));
                        $end = Carbon::parse($state);

                        if ($start->greaterThan($end)) {
                            $end->addDay();
                        }

                        $dayOfWeek = $start->dayOfWeek;
                        $workEnd = Carbon::createFromTime(18, 30);
                        $saturdayWorkStart = Carbon::createFromTime(14, 30);

                        if ($dayOfWeek >= Carbon::MONDAY && $dayOfWeek <= Carbon::FRIDAY) {
                            $overtimeStart = $workEnd;
                        } elseif ($dayOfWeek == Carbon::SATURDAY) {
                            $overtimeStart = $saturdayWorkStart;
                        } else {
                            $output = 0.00;
                            $set('total_hours', $output);
                            return;
                        }

                        $totalHours = $start->diffInHours($end);
                        $totalMinutes = $start->diffInMinutes($end) % 60;

                        $totalHoursDecimal = $totalHours + ($totalMinutes / 60);
                        $totalHoursDecimal = number_format($totalHoursDecimal, 2);

                        if ($end->greaterThan($overtimeStart)) {
                            $overtimeHours = $overtimeStart->diffInHours($end);
                            $overtimeMinutes = $overtimeStart->diffInMinutes($end) % 60;
                            $overtimeDecimal = $overtimeHours + ($overtimeMinutes / 60);
                            $overtimeDecimal = number_format($overtimeDecimal, 2);
                            $output = $overtimeDecimal;
                        }
                        $set('total_hours', $output);
                    }),

                Forms\Components\TextInput::make('total_hours')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->label('Total Hours'),

                Forms\Components\Select::make('status')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->required()
                    ->disabled(!$isAdmin && !$isHRD)
                    ->default('Menunggu'),

                Forms\Components\Textarea::make('reason')
                    ->required()
                    ->rows(3)
                    ->placeholder('Enter reason for overtime'),

                Forms\Components\Select::make('approved_by')
                    ->relationship('approver', 'full_name')
                    ->searchable()
                    ->preload()
                    ->visible(fn(string $context): bool => $context === 'edit')
                    ->label('Approved By'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $query = Overtime::query();
        if ($user->hasRole('user')) {
            $query->where('employee_id', $user->employee['id']);
        }
        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->label('Employee'),

                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reason')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('H:i'))
                    ->label('Start'),

                Tables\Columns\TextColumn::make('end_time')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->format('H:i'))
                    ->label('End'),


                Tables\Columns\TextColumn::make('total_hours')
                    ->numeric(2)
                    ->sortable()
                    ->label('Hours'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'Menunggu',
                        'success' => 'Disetujui',
                        'danger' => 'Ditolak',
                    ]),

                Tables\Columns\TextColumn::make('approver.full_name')
                    ->label('Approved By'),

                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ]),
                Filter::make('date'),
            ])
            ->actions([

                // Approve/Reject Action
                Action::make('approve_reject')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->label('Status')
                    ->visible(fn(Overtime $record): bool => $record->status === 'Menunggu')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'Disetujui' => 'Approve',
                                'Ditolak' => 'Reject',
                            ])
                            ->required(),
                    ])
                    ->action(function (Overtime $record, array $data): void {
                        $user = auth()->user();
                        $record->update([
                            'status' => $data['status'],
                            'approved_by' => $user->employee['id'],
                            'approved_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Approve/Reject Overtime')
                    ->visible(fn() => auth()->user() && auth()->user()->hasAnyRole(['administrator', 'hrd']))
                    ->modalDescription('Are you sure you want to process this overtime request?')
                    ->slideOver(),

                // Edit Action
                Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->slideOver()
                    ->modalWidth('lg')
                    ->modalHeading('Edit Overtime Request')
                    ->form([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Select::make('employee_id')
                                    ->relationship('employee', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Employee Name')
                                    ->disabled(fn(Overtime $record): bool => $record->status !== 'Menunggu'),

                                Forms\Components\DatePicker::make('date')
                                    ->required()
                                    ->maxDate(now())
                                    ->disabled(fn(Overtime $record): bool => $record->status !== 'Menunggu'),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TimePicker::make('start_time')
                                            ->required()
                                            ->seconds(false)
                                            ->disabled(fn(Overtime $record): bool => $record->status !== 'Menunggu'),

                                        Forms\Components\TimePicker::make('end_time')
                                            ->required()
                                            ->seconds(false)
                                            ->after('start_time')
                                            ->reactive()
                                            ->disabled(fn(Overtime $record): bool => $record->status !== 'Menunggu')
                                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                                $start = Carbon::parse($get('start_time'));
                                                $end = Carbon::parse($state);

                                                if ($start->greaterThan($end)) {
                                                    $end->addDay();
                                                }

                                                $dayOfWeek = $start->dayOfWeek;
                                                $workEnd = Carbon::createFromTime(18, 30);
                                                $saturdayWorkStart = Carbon::createFromTime(14, 30);

                                                if ($dayOfWeek >= Carbon::MONDAY && $dayOfWeek <= Carbon::FRIDAY) {
                                                    $overtimeStart = $workEnd;
                                                } elseif ($dayOfWeek == Carbon::SATURDAY) {
                                                    $overtimeStart = $saturdayWorkStart;
                                                } else {
                                                    $output = 0.00;
                                                    $set('total_hours', $output);
                                                    return;
                                                }

                                                $totalHours = $start->diffInHours($end);
                                                $totalMinutes = $start->diffInMinutes($end) % 60;

                                                $totalHoursDecimal = $totalHours + ($totalMinutes / 60);
                                                $totalHoursDecimal = number_format($totalHoursDecimal, 2);

                                                if ($end->greaterThan($overtimeStart)) {
                                                    $overtimeHours = $overtimeStart->diffInHours($end);
                                                    $overtimeMinutes = $overtimeStart->diffInMinutes($end) % 60;
                                                    $overtimeDecimal = $overtimeHours + ($overtimeMinutes / 60);
                                                    $overtimeDecimal = number_format($overtimeDecimal, 2);
                                                    $output = $overtimeDecimal;
                                                }
                                                $set('total_hours', $output);
                                            }),
                                    ]),

                                Forms\Components\TextInput::make('total_hours')
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->label('Total Hours'),

                                Forms\Components\Card::make()
                                    ->schema([
                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'Menunggu' => 'Menunggu',
                                                'Disetujui' => 'Disetujui',
                                                'Ditolak' => 'Ditolak',
                                            ])
                                            ->required()
                                            ->default('Menunggu')
                                            ->disabled(fn(Overtime $record): bool => $record->status !== 'Menunggu'),

                                        Forms\Components\Textarea::make('reason')
                                            ->required()
                                            ->rows(3)
                                            ->placeholder('Enter reason for overtime')
                                            ->disabled(fn(Overtime $record): bool => $record->status !== 'Menunggu'),
                                    ])
                                    ->columns(1),

                                Forms\Components\Select::make('approved_by')
                                    ->relationship('approver', 'full_name')
                                    ->searchable()
                                    ->preload()
                                    ->label('Approved By')
                                    ->visible(fn(Overtime $record): bool => $record->status !== 'Menunggu')
                                    ->disabled(),
                            ]),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        // Format dates and times consistently
                        $data['start_time'] = Carbon::parse($data['start_time'])->format('Y-m-d H:i:s');
                        $data['end_time'] = Carbon::parse($data['end_time'])->format('Y-m-d H:i:s');

                        return $data;
                    })
                    ->action(function (Overtime $record, array $data): void {
                        $record->update($data);
                    })
                    ->visible(fn(Overtime $record): bool => $record->status === 'Menunggu' && auth()->user()->hasAnyRole(['administrator', 'hrd'])),

                // Delete Action
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user() && auth()->user()->hasAnyRole(['administrator', 'hrd'])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOvertimes::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('user')) {
            return static::getModel()::count();
        }
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getApiTransformer()
    {
        return OvertimeTransformer::class;
    }
}