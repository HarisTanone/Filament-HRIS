<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeOffResource\Api\Transformers\TimeOffTransformer;
use App\Filament\Resources\TimeOffResource\Pages;
use App\Models\Employee;
use App\Models\TimeOff;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
// 
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
// use Filament\Forms\Components\Select;

class TimeOffResource extends Resource
{
    protected static ?string $model = TimeOff::class;
    protected static ?string $navigationGroup = 'Time Management';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

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
        return $user && $user->hasAnyPermission(['view time_offs', 'view own time_offs']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('edit time_offs');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('delete time_offs');
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create time_offs');
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $isAdmin = $user && $user->hasRole('administrator');
        $isHRD = $user && $user->hasRole('hrd');

        return $form
            ->schema([
                Select::make('employee_id')
                    ->label('Karyawan')
                    ->relationship('employee', 'full_name')
                    ->required()
                    ->columnSpan(['sm' => 2, 'xl' => 3, '2xl' => 4])
                    ->placeholder('Pilih Karyawan')
                    ->default(function () {
                        $user = auth()->user();
                        if ($user && $user->hasRole('user')) {
                            $employee = Employee::where('user_id', $user->id)->first();
                            return $employee ? $employee->id : null;
                        }
                        return null;
                    })
                    ->options(function () {
                        $user = auth()->user();
                        if ($user && $user->hasRole('user')) {
                            $employee = Employee::where('user_id', $user->id)->first();
                            return $employee ? [$employee->id => $employee->full_name] : [];
                        }
                        return Employee::all()->pluck('full_name', 'id');
                    })
                    ->disabled(!$isAdmin && !$isHRD), // Nonaktifkan untuk selain admin dan HRD

                Card::make()
                    ->schema([
                        Section::make('Informasi Cuti')
                            ->schema([
                                Select::make('attendance_type_id')
                                    ->label('Tipe Cuti')
                                    ->relationship('attendanceType', 'type_name')
                                    ->required()
                                    ->placeholder('Pilih Tipe Cuti'),
                                // ->disabled(!$isAdmin && !$isHRD), // Nonaktifkan untuk selain admin

                                DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->placeholder('Pilih Tanggal Mulai'),
                                // ->disabled(!$isAdmin && !$isHRD), // Nonaktifkan untuk selain admin

                                DatePicker::make('end_date')
                                    ->label('Tanggal Selesai')
                                    ->required()
                                    ->placeholder('Pilih Tanggal Selesai')
                                // ->disabled(!$isAdmin && !$isHRD), // Nonaktifkan untuk selain admin
                            ])
                            ->columns(3),
                    ])
                    ->columnSpan(3),

                Card::make()
                    ->schema([
                        Section::make('Detail Cuti')
                            ->schema([
                                FileUpload::make('document')
                                    ->nullable()
                                    ->disk('public')
                                    ->label('Dokumen Pendukung')
                                    ->image()
                                    ->maxSize(10240)
                                    ->helperText('Upload dokumen pendukung jika ada'),
                                // ->disabled(!$isAdmin && !$isHRD), // Nonaktifkan untuk selain admin

                                Textarea::make('reason')
                                    ->nullable()
                                    ->label('Alasan Cuti')
                                    ->placeholder('Tuliskan alasan pengajuan cuti')
                                // ->disabled(!$isAdmin && !$isHRD), // Nonaktifkan untuk selain admin
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(3),

                Card::make()
                    ->schema([
                        Section::make('Status dan Persetujuan')
                            ->schema([
                                Select::make('status')
                                    ->label('Status Pengajuan')
                                    ->options([
                                        'Menunggu' => 'Menunggu',
                                        'Disetujui' => 'Disetujui',
                                        'Ditolak' => 'Ditolak',
                                    ])
                                    ->default('Menunggu')
                                    ->required()
                                    ->placeholder('Pilih Status')
                                    ->disabled(!$isAdmin && !$isHRD), // Nonaktifkan untuk selain admin

                                Select::make('approved_by')
                                    ->default(function () {
                                        $employee = Employee::query()->where('user_id', auth()->user()->id)->first();
                                        return $employee->manager_id;
                                    })
                                    ->label('Disetujui Oleh')
                                    ->relationship('approver', 'full_name')
                                    ->nullable()
                                    ->placeholder('Pilih yang menyetujui')
                                    ->disabled(!$isAdmin && !$isHRD), // Nonaktifkan untuk selain admin

                                DatePicker::make('approved_at')
                                    ->nullable()
                                    ->readOnly()
                                    ->default(now()->format('Y-m-d H:i:s'))
                                    ->label('Tanggal Disetujui')
                                    ->placeholder('Pilih Tanggal Disetujui')
                                    ->disabled(!$isAdmin && !$isHRD), // Nonaktifkan untuk selain admin
                            ])
                            ->columns(3),
                    ])
                    ->columnSpan(3),
            ]);
    }


    public static function table(Table $table): Table
    {
        $user = auth()->user();
        $employee_id = Employee::query()->where('user_id', $user->id)->first();
        $query = TimeOff::query();
        if ($user->hasRole('user')) {
            $query->where('employee_id', $employee_id->id);
        }
        return $table
            ->query($query)
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Karyawan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('attendanceType.type_name')
                    ->label('Tipe Cuti')
                    ->sortable(),
                TextColumn::make('reason')
                    ->label('Alasan Cuti')
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'Menunggu',
                        'success' => 'Disetujui',
                        'danger' => 'Ditolak',
                    ])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ])
                    ->label('Status Pengajuan'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('updateStatus')
                    ->requiresConfirmation()
                    ->slideOver()
                    ->modalDescription('Are you sure you want to process this TimeOff request?')
                    ->modalHeading('TimeOFF Request')
                    ->modalWidth('sm')
                    ->visible(fn(Overtime $record): bool => $record->status === 'Menunggu')
                    ->form([
                        Select::make('status')
                            ->label('Status Pengajuan')
                            ->options([
                                'Disetujui' => 'Disetujui',
                                'Ditolak' => 'Ditolak',
                            ])
                            ->default(fn($state, $record) => $record->status ?? 'Menunggu')
                            ->required()
                            ->placeholder('Pilih Status'),
                    ])
                    ->action(function (array $data, TimeOff $record): void {
                        $user = auth()->user();
                        $employee = $user->employee;

                        $record->status = $data['status'];

                        $record->approved_by = $employee['id'];
                        $record->approved_at = now();

                        $record->save();
                    })
                    ->label('Status')
                    ->icon('heroicon-s-pencil')
                    ->color('success')
                    ->visible(fn() => auth()->user() && auth()->user()->hasAnyRole(['administrator', 'hrd']))
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListTimeOffs::route('/'),
            'create' => Pages\CreateTimeOff::route('/create'),
            'edit' => Pages\EditTimeOff::route('/{record}/edit'),
        ];
    }

    public static function getApiTransformer()
    {
        return TimeOffTransformer::class;
    }
}
