<?php

namespace MuhammadNawlo\MultitenantPlugin\Traits;

use Stancl\Tenancy\TenancyManager;

trait HasTenancy
{
    protected function getTenancyManager(): ?TenancyManager
    {
        try {
            return app(TenancyManager::class);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getCurrentTenant()
    {
        $manager = $this->getTenancyManager();

        return $manager ? $manager->tenant : null;
    }

    protected function getTenantId()
    {
        $tenant = $this->getCurrentTenant();

        return $tenant ? $tenant->getTenantKey() : null;
    }

    protected function applyTenantScope($query)
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
