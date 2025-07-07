<?php

namespace MuhammadNawlo\MultitenantPlugin\Traits;

use Stancl\Tenancy\TenancyManager;

trait HasTenancy
{
    protected function getTenancyManager(): TenancyManager
    {
        return app(TenancyManager::class);
    }

    protected function getCurrentTenant()
    {
        return $this->getTenancyManager()->tenant;
    }

    protected function getTenantId()
    {
        $tenant = $this->getCurrentTenant();

        return $tenant ? $tenant->getTenantKey() : null;
    }

    protected function scopeToTenant($query)
    {
        $tenantId = $this->getTenantId();

        if ($tenantId && method_exists($query->getModel(), 'scopeTenant')) {
            return $query->tenant($tenantId);
        }

        return $query;
    }

    protected function isTenantContext(): bool
    {
        return $this->getCurrentTenant() !== null;
    }
}
