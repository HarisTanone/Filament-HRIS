<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Api\Transformers\EmployeeTransformer;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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

        return $user && $user->hasAnyPermission(['view employees', 'view own employee']);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user && $user->hasPermissionTo('create employees');
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        if ($user->hasRole('user')) {
            return $record->user_id == $user->id;
        }

        return $user && $user->hasPermissionTo('edit employees');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        return $user && $user->hasPermissionTo('delete employees');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Card 1: Personal Info
                Card::make()
                    ->description('Personal Information')
                    ->icon('heroicon-m-identification')
                    ->schema([
                        FileUpload::make('photo')
                            ->image()
                            ->directory('employee_photos')
                            ->label('Employee Photo')
                            ->hint('Upload your professional photo')
                            ->columnSpan(3),

                        TextInput::make('full_name')
                            ->placeholder('Haris Tanone')
                            ->required()
                            ->label('Full Name')
                            ->hint('Enter your full legal name')
                            ->columnSpan(1),

                        TextInput::make('email')
                            ->email()
                            ->placeholder('Tanoneharis@Gmail.com')
                            ->required()
                            ->label('Email')
                            ->hint('Enter a valid email address')
                            ->columnSpan(1),

                        TextInput::make('mobile_phone')
                            ->placeholder('081225704292')
                            ->required()
                            ->label('Mobile Phone')
                            ->columnSpan(1),

                        TextInput::make('place_of_birth')
                            ->placeholder('Salatiga')
                            ->label('Place of Birth')
                            ->columnSpan(1),

                        DatePicker::make('birthdate')
                            ->label('Birthdate')
                            ->columnSpan(1),

                        Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->label('Gender')
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->collapsible(),


                // Card 2: Address Info
                Card::make()
                    ->icon('heroicon-m-map-pin')
                    ->description('Address Information')
                    ->schema([
                        TextInput::make('religion')
                            ->placeholder('Kristen')
                            ->label('Religion')
                            ->columnSpan(1),

                        TextInput::make('nik')
                            ->numeric()
                            ->placeholder('123456789012345')
                            ->label('NIK')
                            ->columnSpan(1),

                        TextInput::make('citizen_id_address')
                            ->placeholder('Salatiga')
                            ->label('Citizen ID Address')
                            ->columnSpan(1),

                        TextInput::make('residential_address')
                            ->placeholder('Salatiga')
                            ->label('Residential Address')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),

                // Card 3: Work Info
                Card::make()
                    ->icon('heroicon-m-briefcase')
                    ->description('Work Information')
                    ->schema([
                        DatePicker::make('join_date')
                            ->label('Join Date')
                            ->columnSpan(1),

                        Select::make('manager_id')
                            ->relationship('manager', 'full_name')
                            ->placeholder('Pilih Manajer')
                            ->visible(fn() => auth()->user() && auth()->user()->hasAnyRole(['administrator', 'hrd']))
                            // ->required()
                            ->label('Manager')
                            ->columnSpan(1),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->visible(fn() => auth()->user() && auth()->user()->hasAnyRole(['administrator', 'hrd']))
                            ->label('User')
                            ->columnSpan(1),

                        Select::make('office_id')
                            ->relationship('office', 'office_name')
                            ->placeholder('Pilih Kantor')
                            ->required()
                            ->visible(fn() => auth()->user() && auth()->user()->hasAnyRole(['administrator', 'hrd']))
                            ->label('Office')
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('mobile_phone')->searchable(),
                TextColumn::make('place_of_birth')->searchable(),
                TextColumn::make('birthdate'),
                TextColumn::make('office.office_name')->label('Kantor')->searchable(),

            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();
                if ($user && $user->hasRole('user')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->authorize(fn() => !auth()->user()->hasRole('user')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Personal')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        // Left column for the photo
                                        ImageEntry::make('photo')
                                            ->height(500)
                                            ->width(330)
                                            ->label('')
                                            ->square()->stacked()->alignment('center')
                                            ->columnSpan(1),

                                        // Right column for the personal information fields
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('nik'),
                                                TextEntry::make('full_name')
                                                    ->label('Full Name')
                                                    ->placeholder('Enter full name'),
                                                TextEntry::make('email')
                                                    ->label('Email')
                                                    ->markdown(),
                                                TextEntry::make('mobile_phone')
                                                    ->label('Mobile Phone')
                                                    ->placeholder('Enter mobile phone number'),
                                                TextEntry::make('place_of_birth')
                                                    ->label('Place of Birth')
                                                    ->placeholder('Enter place of birth'),
                                                TextEntry::make('birthdate')
                                                    ->label('Birthdate')
                                                    ->placeholder('Select birthdate'),
                                                TextEntry::make('gender')
                                                    ->label('Gender')
                                                    ->placeholder('Select gender'),
                                                TextEntry::make('religion')
                                                    ->label('Religion')
                                                    ->placeholder('Enter religion'),
                                            ])
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        Tabs\Tab::make('Address')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('citizen_id_address')->label('Citizen Address'),
                                        TextEntry::make('residential_address')->label('Residential Address'),
                                    ]),
                            ])
                            ->columnSpanFull(),

                        Tabs\Tab::make('Work')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('join_date')
                                            ->label('Join Date'),
                                        TextEntry::make('barcode')
                                            ->label('Barcode'),
                                        TextEntry::make('Manager.full_name')
                                            ->label('Manager'),
                                        TextEntry::make('User.name')
                                            ->label('User'),
                                        TextEntry::make('Office.office_name')
                                            ->label('Office'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
            'view' => Pages\ViewEmployee::route('/{record}'),
        ];
    }

    public static function getApiTransformer()
    {
        return EmployeeTransformer::class;
    }
}
