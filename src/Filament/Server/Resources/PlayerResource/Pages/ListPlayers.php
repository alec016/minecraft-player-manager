<?php

namespace KumaGames\GamePlayerManager\Filament\Server\Resources\PlayerResource\Pages;

use Filament\Resources\Pages\ListRecords;
use KumaGames\GamePlayerManager\Filament\Server\Resources\PlayerResource;
use KumaGames\GamePlayerManager\Services\MinecraftPlayerProvider;
use Illuminate\Database\Eloquent\Model;
use Filament\Facades\Filament;

use Filament\Resources\Components\Tab;

class ListPlayers extends ListRecords
{
    protected static string $resource = PlayerResource::class;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('minecraft-player-manager::messages.pages.list');
    }

    public ?string $activeFilter = 'online';

    protected $queryString = [
        'activeFilter' => ['except' => 'online'],
    ];

    public function getTableRecords(): \Illuminate\Support\Collection|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Contracts\Pagination\CursorPaginator
    {
        $server = Filament::getTenant();
        $serverId = $server->uuid ?? 'server-1'; 
        
        $provider = new MinecraftPlayerProvider();
        $data = $provider->getPlayers($serverId);
        $collection = collect($data)->map(fn ($item) => new \KumaGames\GamePlayerManager\Models\Player($item));

        if ($this->activeFilter === 'online') {
            $collection = $collection->where('online', true);
        } elseif ($this->activeFilter === 'offline') {
            $collection = $collection->where('online', false);
        } elseif ($this->activeFilter === 'op') {
            $collection = $collection->where('is_op', true);
        } elseif ($this->activeFilter === 'banned') {
            $collection = $collection->where('is_banned', true);
        }

        // Apply Search
        $search = $this->getTableSearch();
        if (filled($search)) {
            $collection = $collection->filter(fn ($record) => str_contains(strtolower($record->name), strtolower($search)));
        }

        return $collection;
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('filter_all')
                ->label(__('minecraft-player-manager::messages.filters.all'))
                ->color(fn() => $this->activeFilter === 'all' ? 'primary' : 'gray')
                ->action(fn() => $this->activeFilter = 'all'),
            \Filament\Actions\Action::make('filter_online')
                ->label(__('minecraft-player-manager::messages.filters.online'))
                ->color(fn() => $this->activeFilter === 'online' ? 'success' : 'gray')
                ->action(fn() => $this->activeFilter = 'online'),
            \Filament\Actions\Action::make('filter_offline')
                ->label(__('minecraft-player-manager::messages.filters.offline'))
                ->color(fn() => $this->activeFilter === 'offline' ? 'gray' : 'gray') // Gray vs Gray? Maybe 'secondary' or 'info'
                ->action(fn() => $this->activeFilter = 'offline'),
            \Filament\Actions\Action::make('filter_op')
                ->label(__('minecraft-player-manager::messages.filters.op'))
                ->color(fn() => $this->activeFilter === 'op' ? 'warning' : 'gray')
                ->action(fn() => $this->activeFilter = 'op'),
            \Filament\Actions\Action::make('filter_banned')
                ->label(__('minecraft-player-manager::messages.filters.banned'))
                ->color(fn() => $this->activeFilter === 'banned' ? 'danger' : 'gray')
                ->action(fn() => $this->activeFilter = 'banned'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \KumaGames\GamePlayerManager\Filament\Server\Widgets\PlayerCountWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
    
    public function getTableRecordKey(Model|array $record): string
    {
        return $record['id'];
    }

    public function resolveTableRecord(?string $key): ?Model
    {
        $server = Filament::getTenant();
        $serverId = $server->uuid ?? 'server-1';
        
        $provider = new MinecraftPlayerProvider();
        
        $players = $provider->getPlayers($serverId);
        $recordRaw = collect($players)->firstWhere('id', $key);
        
        if (!$recordRaw) {
            return null;
        }

        $details = $provider->getPlayerDetails($serverId, $key);
        $data = array_merge($recordRaw, $details);
        
        return new \KumaGames\GamePlayerManager\Models\Player($data);
    }
}
