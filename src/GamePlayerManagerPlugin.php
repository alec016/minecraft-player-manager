<?php

namespace KumaGames\GamePlayerManager;

use Filament\Contracts\Plugin;
use Filament\Panel;

class GamePlayerManagerPlugin implements Plugin
{
    public function getId(): string
    {
        return 'minecraft-player-manager';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();
        
        // Discover Resources, Pages, and Widgets dynamically based on panel ID (Admin, Server, etc.)
        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "KumaGames\\GamePlayerManager\\Filament\\$id\\Resources");
        $panel->discoverPages(plugin_path($this->getId(), "src/Filament/$id/Pages"), "KumaGames\\GamePlayerManager\\Filament\\$id\\Pages");
        $panel->discoverWidgets(plugin_path($this->getId(), "src/Filament/$id/Widgets"), "KumaGames\\GamePlayerManager\\Filament\\$id\\Widgets");
    }

    public function boot(Panel $panel): void
    {
        // Register Views
        \Illuminate\Support\Facades\View::addNamespace('minecraft-player-manager', plugin_path('minecraft-player-manager', 'resources/views'));
        \Illuminate\Support\Facades\Lang::addNamespace('minecraft-player-manager', __DIR__ . '/../resources/lang');

        // Only register widgets for the Server panel
        if ($panel->getId() === 'server') {
            \App\Filament\Server\Pages\Console::registerCustomWidgets(
                \App\Enums\ConsoleWidgetPosition::AboveConsole, 
                [\KumaGames\GamePlayerManager\Filament\Server\Widgets\PlayerCountWidget::class]
            );
        }
    }
}
