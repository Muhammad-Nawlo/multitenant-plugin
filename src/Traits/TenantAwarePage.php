<?php

namespace MuhammadNawlo\MultitenantPlugin\Traits;

use Filament\Pages\Page;

trait TenantAwarePage
{
    use HasTenancy;

    protected function getTenantContext()
    {
        return $this->getCurrentTenant();
    }

    protected function getTenantData(): array
    {
        $tenant = $this->getCurrentTenant();
        
        if (!$tenant) {
            return [];
        }
        
        return [
            'id' => $tenant->getTenantKey(),
            'name' => $tenant->name ?? $tenant->getTenantKey(),
            'domain' => $tenant->domain ?? null,
        ];
    }

    protected function canAccess(): bool
    {
        if (!parent::canAccess()) {
            return false;
        }

        // Add tenant-specific access logic here if needed
        return true;
    }

    public function getTitle(): string
    {
        $tenant = $this->getCurrentTenant();
        $baseTitle = parent::getTitle();
        
        if ($tenant) {
            return $baseTitle . ' - ' . ($tenant->name ?? $tenant->getTenantKey());
        }
        
        return $baseTitle;
    }
} 