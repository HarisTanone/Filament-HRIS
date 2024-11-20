<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Api\Transformers\PermissionTransformer;
use App\Filament\Resources\PermissionResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;
    protected static ?string $navigationGroup = 'Access Management';
    protected static ?int $navigationSort = 1; // Urutan navigasi
    protected static ?string $navigationIcon = 'heroicon-o-check';
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('administrator')) {
            return true;
        }
        return $user && $user->hasAnyPermission(['view permissions']);
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('edit permissions');
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();
        return $user && $user->hasPermissionTo('delete permissions');
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && $user->can('create permissions');
    }
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Permission Name')
                    ->placeholder('CRUD Permissions')
                    ->required(),
            ])->columns(1);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Permission Name')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                // buatkan action edit
                Tables\Actions\EditAction::make()->slideOver()->modalWidth('md'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            // 'create' => Pages\CreatePermission::route('/create'),
            // 'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function getApiTransformer()
    {
        return PermissionTransformer::class;
    }
}
