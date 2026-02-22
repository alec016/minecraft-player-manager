<?php

namespace Alec_016\GamePlayerManager\Filament\Admin\Resources\RCONHosts;

use Alec_016\GamePlayerManager\Models\RconHost;
use Alec_016\GamePlayerManager\Filament\Admin\Resources\RCONHosts\Pages\ManageRCONHosts;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RCONHostResource extends Resource
{
    
    protected static ?string $model = RconHost::class;
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-server';

    public static function getNavigationLabel(): string
    {
        return trans('minecraft-player-manager::messages.rcon_hosts');
    }

    public static function getModelLabel(): string
    {
        return trans('minecraft-player-manager::messages.rcon_host');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('minecraft-player-manager::messages.rcon_hosts');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count() ?: null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('host')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => static::canEdit($record)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateIcon('tabler-server')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('game-player-manager::messages.no_rconhosts'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->components([
            TextInput::make('host')
                ->label(trans('game-player-manager::messages.host'))
                ->placeholder(trans('game-player-manager::messages.no_host'))
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
        ->components([
            TextEntry::make('host')
                ->label(trans('game-player-manager::messages.host'))
                ->columnSpanFull()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRCONHosts::route('/'),
        ];
    }
}