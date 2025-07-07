<?php

namespace MuhammadNawlo\MultitenantPlugin\Resources\TenantResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use MuhammadNawlo\MultitenantPlugin\Resources\TenantResource;

class ListTenants extends ListRecords
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
