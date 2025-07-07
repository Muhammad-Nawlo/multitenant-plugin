<?php

namespace MuhammadNawlo\MultitenantPlugin\Traits;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Pages\Page;

trait TenantAwareShieldPage
{
    use HasPanelShield;
    use HasTenancy;

    protected function getTenantContext()
    {
        return $this->getCurrentTenant();
    }

    protected function getTenantData(): array
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

    protected function canAccess(): bool
    {
        if (! parent::canAccess()) {
            return false;
        }

        $tenant = $this->getCurrentTenant();

        if ($tenant) {
            // Check tenant-specific permission for this page
            $permission = 'view_' . static::getSlug() . '_' . $tenant->getTenantKey();

            return auth()->user()->can($permission);
        }

        // In central context, check global permission
        return auth()->user()->can('view_' . static::getSlug());
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

    /**
     * Check if the current user has permission for this page in the current tenant context
     */
    protected function canAccessPage(string $permission): bool
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            // In central context, check global permissions
            return auth()->user()->can($permission);
        }

        // In tenant context, check tenant-specific permissions
        return auth()->user()->can($permission . '_' . $tenant->getTenantKey());
    }

    /**
     * Get tenant-specific permission name for pages
     */
    protected function getTenantPagePermission(string $permission): string
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            return $permission;
        }

        return $permission . '_' . $tenant->getTenantKey();
    }
}
