<?php

namespace MuhammadNawlo\MultitenantPlugin\Resources\TenantResource\Pages;

use MuhammadNawlo\MultitenantPlugin\Resources\TenantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 