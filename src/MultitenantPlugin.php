<?php

namespace MuhammadNawlo\MultitenantPlugin;

class MultitenantPlugin
{
    protected ?TenancyManager $tenancyManager;

    public function __construct(?TenancyManager $tenancyManager = null)
    {
        $this->tenancyManager = $tenancyManager ?? app(TenancyManager::class);
    }

    public function getTenancyManager(): ?TenancyManager
    {
        return $this->tenancyManager;
    }

    public function getCurrentTenant()
    {
        if (!$this->tenancyManager) {
            return null;
        }
        return $this->tenancyManager->tenant;
    }

    public function getTenantId()
    {
        $tenant = $this->getCurrentTenant();

        return $tenant ? $tenant->getTenantKey() : null;
    }

    public function scopeToTenant($query)
    {
        $tenantId = $this->getTenantId();

        if ($tenantId && method_exists($query->getModel(), 'scopeTenant')) {
            return $query->tenant($tenantId);
        }

        return $query;
    }

    public function isTenantContext(): bool
    {
        return $this->getCurrentTenant() !== null;
    }

    public function getTenantData(): array
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            return [];
        }

        return [
            'id' => $tenant->getTenantKey(),
            'name' => $tenant->name ?? $tenant->getTenantKey(),
            'domain' => $tenant->domain ?? null,
        ];
    }
}
