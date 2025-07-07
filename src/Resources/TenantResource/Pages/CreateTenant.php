<?php

namespace MuhammadNawlo\MultitenantPlugin\Resources\TenantResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use MuhammadNawlo\MultitenantPlugin\Resources\TenantResource;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
