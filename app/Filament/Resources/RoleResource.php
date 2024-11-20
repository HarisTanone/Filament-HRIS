<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Api\Transformers\RoleTransformer;
use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationGroup = 'Access Management';
    protected static ?int $navigationSort = 1; // Urutan navigasi
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('administrator')) {
            return true;
        }
        return $user && $user->hasAnyPermission(['view roles']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('edit roles');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('delete roles');
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create roles');
    }
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Role Name')
                    ->required(),

                CheckboxList::make('permissions')
                    ->label('Permissions')
                    ->relationship('permissions', 'name')
                    ->options(Permission::all()->pluck('name', 'id')->toArray())
                    ->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->wrap()
                    ->sortable(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add any relation managers here, if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getApiTransformer()
    {
        return RoleTransformer::class;
    }
}
