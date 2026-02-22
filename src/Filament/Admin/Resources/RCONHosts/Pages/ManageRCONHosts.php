<?php

namespace Alec_016\GamePlayerManager\Filament\Admin\Resources\RCONHosts\Pages;

use Alec_016\GamePlayerManager\Filament\Admin\Resources\RCONHosts\RCONHostResource;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageRCONHosts extends ManageRecords
{
    protected static string $resource = RCONHostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false)
                ->hiddenLabel()
                ->icon('tabler-plus'),
        ];
    }

}