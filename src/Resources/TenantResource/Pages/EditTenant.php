<?php

namespace MuhammadNawlo\MultitenantPlugin\Resources\TenantResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use MuhammadNawlo\MultitenantPlugin\Resources\TenantResource;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
