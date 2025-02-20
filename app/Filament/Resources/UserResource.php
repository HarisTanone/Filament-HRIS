<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Api\Transformers\UserTransformer;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as SpatieRole;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

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
        return $user && $user->hasAnyPermission(['view users', 'view own users']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('edit users');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('delete users');
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        // ->unique(User::class, 'email')
                        ->maxLength(255),

                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->required()
                        ->minLength(8)
                        ->maxLength(255),

                    Select::make('roles')
                        ->label('Role')
                        ->relationship('roles', 'name')
                        ->options(function () {
                            if (auth()->user()->hasRole('administrator')) {
                                return SpatieRole::all()->pluck('name', 'id');
                            } else {
                                return SpatieRole::where('name', '!=', 'administrator')->pluck('name', 'id');
                            }
                        })
                        ->visible(fn() => auth()->user() && auth()->user()->hasAnyRole(['administrator', 'hrd']))
                        ->required(),
                ])
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        $user = auth()->user();
        $query = User::query();
        if ($user->hasRole('administrator')) {
            $query->with('roles');
        }
        if ($user->hasRole('hrd')) {
            $query->whereHas('roles', function ($query) {
                $query->whereIn('name', ['hrd', 'user']);
            });
        }

        if ($user->hasRole('user')) {
            $query->where('id', $user->id);
        }
        return $table
            ->query($query)
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->searchable()
                    ->sortable(),
            ])->reorderable()
            ->defaultSort('sort_order', 'asc')
            ->actions([
                EditAction::make()->slideOver()->modalWidth('md')
                    ->visible(fn(Model $record) => auth()->user()->can('edit users') || auth()->user()->id === $record->id),
                DeleteAction::make()
                    ->visible(fn(Model $record) => auth()->user()->can('delete users')),
                Action::make('updateStatus')
                    ->form([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->maxLength(255),
                    ])
                    ->action(function (array $data, User $record): void {
                        $record->password = Hash::make($data['password']);
                        $record->save();
                        Notification::make()
                            ->title('Password Updated')
                            ->success()
                            ->body('Password has been successfully updated.')
                            ->send();
                    })
                    ->label('Update')
                    ->icon('heroicon-s-pencil')
                    ->color('warning')
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getApiTransformer()
    {
        return UserTransformer::class;
    }
}
