<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\{TextInput, RichEditor, FileUpload, Toggle, Card};
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationGroup = 'Other';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

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
                Card::make()
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Pengumuman')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('poster')
                            ->label('Upload Poster')
                            ->image()
                            ->directory('posters')
                            ->disk('public')
                            ->required(),

                        RichEditor::make('body')
                            ->label('Isi Pengumuman')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'orderedList',
                                'unorderedList',
                                'heading',
                            ]),

                        Toggle::make('isActive')
                            ->label('Aktif')
                            ->default(true),
                    ]),

                Hidden::make('user_id')
                    ->default(fn() => auth()->id())
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Diposkan Oleh'),
                Tables\Columns\BooleanColumn::make('isActive')->label('Aktif'),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Dibuat')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('isActive')
                    ->options([
                        true => 'Aktif',
                        false => 'Non-Aktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
