<?php

namespace KumaGames\GamePlayerManager\Filament\Server\Resources\PlayerResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use KumaGames\GamePlayerManager\Filament\Server\Resources\PlayerResource;
use KumaGames\GamePlayerManager\Services\MinecraftPlayerProvider;
use Filament\Facades\Filament;

class ViewPlayer extends ViewRecord
{
    protected static string $resource = PlayerResource::class;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('minecraft-player-manager::messages.pages.view');
    }

    protected function resolveRecord(string | int $key): \Illuminate\Database\Eloquent\Model
    {
        $server = Filament::getTenant();
        $serverId = $server->uuid ?? 'server-1';
        
        $provider = new MinecraftPlayerProvider();
        $details = $provider->getPlayerDetails($serverId, $key);
        
        $data = array_merge(['id' => $key], $details);
        
        $player = new \KumaGames\GamePlayerManager\Models\Player($data);
        $player->exists = true; // IMPORTANT: Required for Filament Actions to recognize the record context
        return $player;
    }

    public function refreshPlayer(): void
    {
        // Re-fetch data from RCON/File
        $freshRecord = $this->resolveRecord($this->record->id);
        $this->record = $freshRecord;
        $this->fillForm();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('op_toggle')
                ->label(fn () => $this->record->is_op ? __('minecraft-player-manager::messages.actions.op.label_deop') : __('minecraft-player-manager::messages.actions.op.label_op'))
                ->icon(fn () => $this->record->is_op ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                ->color(fn () => $this->record->is_op ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn () => $this->record->is_op ? __('minecraft-player-manager::messages.actions.op.heading_deop') : __('minecraft-player-manager::messages.actions.op.heading_op'))
                ->modalDescription(fn () => $this->record->is_op ? 
                    __('minecraft-player-manager::messages.actions.op.desc_deop') : 
                    __('minecraft-player-manager::messages.actions.op.desc_op'))
                ->action(function ($record) {
                    $server = Filament::getTenant();
                    $serverId = $server->uuid ?? 'server-1';
                    $provider = new MinecraftPlayerProvider();
                    
                    if ($record->is_op) {
                         $provider->deop($serverId, $record->name);
                         \Filament\Notifications\Notification::make()->title(__('minecraft-player-manager::messages.actions.op.notify_deop'))->success()->send();
                    } else {
                         $provider->op($serverId, $record->name);
                         \Filament\Notifications\Notification::make()->title(__('minecraft-player-manager::messages.actions.op.notify_op'))->success()->send();
                    }
                    
                    // Refresh full data
                    $this->refreshPlayer();
                }),

            \Filament\Actions\Action::make('clear_inventory')
                ->label(__('minecraft-player-manager::messages.actions.clear_inventory.label'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription(__('minecraft-player-manager::messages.actions.clear_inventory.desc'))
                ->action(function ($record) {
                    $server = Filament::getTenant();
                    $serverId = $server->uuid ?? 'server-1';
                    $provider = new MinecraftPlayerProvider();
                    $provider->clearInventory($serverId, $record->name);
                    \Filament\Notifications\Notification::make()->title(__('minecraft-player-manager::messages.actions.clear_inventory.notify'))->success()->send();
                    
                    // Refresh data
                    $this->refreshPlayer();
                }),
                
            \Filament\Actions\Action::make('kick')
                ->label(__('minecraft-player-manager::messages.actions.kick.label'))
                ->icon('heroicon-o-arrow-right-start-on-rectangle')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\TextInput::make('reason')
                        ->label(__('minecraft-player-manager::messages.actions.kick.reason'))
                        ->default(__('minecraft-player-manager::messages.actions.kick.default_reason')),
                ])
                ->action(function (array $data, $record) {
                    $server = Filament::getTenant();
                    $serverId = $server->uuid ?? 'server-1';
                    $provider = new MinecraftPlayerProvider();
                    $provider->kick($serverId, $record->name, $data['reason']);
                    \Filament\Notifications\Notification::make()->title(__('minecraft-player-manager::messages.actions.kick.notify'))->success()->send();
                    
                    // Wait a moment for server to process kick before refreshing
                    sleep(1);
                    $this->refreshPlayer();
                }),

            \Filament\Actions\Action::make('ban')
                ->label(__('minecraft-player-manager::messages.actions.ban.label'))
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\TextInput::make('reason')
                        ->label(__('minecraft-player-manager::messages.actions.ban.reason'))
                        ->default(__('minecraft-player-manager::messages.actions.ban.default_reason')),
                ])
                ->action(function (array $data, $record) {
                    $server = Filament::getTenant();
                    $serverId = $server->uuid ?? 'server-1';
                    $provider = new MinecraftPlayerProvider();
                    $provider->ban($serverId, $record->name, $data['reason']);
                    \Filament\Notifications\Notification::make()->title(__('minecraft-player-manager::messages.actions.ban.notify'))->success()->send();
                    
                    // Wait a moment before refreshing
                    sleep(1);
                    $this->refreshPlayer();
                }),
        ];
    }
}
