<?php

namespace MuhammadNawlo\MultitenantPlugin\Traits;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;

trait TenantAwareShieldResource
{
    use HasTenancy;
    use HasPanelShield;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        
        if ($this->isTenantContext()) {
            $query = $this->scopeToTenant($query);
        }
        
        return $query;
    }

    protected function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if ($this->isTenantContext()) {
            $query = $this->scopeToTenant($query);
        }
        
        return $query;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tenant Management';
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 3;
    }

    protected function getTableActionsFormColumns(): int
    {
        return 3;
    }

    /**
     * Check if the current user has permission for this resource in the current tenant context
     */
    protected function canAccessResource(string $permission): bool
    {
        $tenant = $this->getCurrentTenant();
        
        if (!$tenant) {
            // In central context, check global permissions
            return auth()->user()->can($permission);
        }
        
        // In tenant context, check tenant-specific permissions
        return auth()->user()->can($permission . '_' . $tenant->getTenantKey());
    }

    /**
     * Get tenant-specific permission name
     */
    protected function getTenantPermission(string $permission): string
    {
        $tenant = $this->getCurrentTenant();
        
        if (!$tenant) {
            return $permission;
        }
        
        return $permission . '_' . $tenant->getTenantKey();
    }

    /**
     * Override canViewAny to check tenant-specific permissions
     */
    public static function canViewAny(): bool
    {
        $instance = new static();
        
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();
            return auth()->user()->can('view_any_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }
        
        return auth()->user()->can('view_any_' . static::getSlug());
    }

    /**
     * Override canView to check tenant-specific permissions
     */
    public static function canView($record): bool
    {
        $instance = new static();
        
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();
            return auth()->user()->can('view_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }
        
        return auth()->user()->can('view_' . static::getSlug());
    }

    /**
     * Override canCreate to check tenant-specific permissions
     */
    public static function canCreate(): bool
    {
        $instance = new static();
        
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();
            return auth()->user()->can('create_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }
        
        return auth()->user()->can('create_' . static::getSlug());
    }

    /**
     * Override canEdit to check tenant-specific permissions
     */
    public static function canEdit($record): bool
    {
        $instance = new static();
        
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();
            return auth()->user()->can('update_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }
        
        return auth()->user()->can('update_' . static::getSlug());
    }

    /**
     * Override canDelete to check tenant-specific permissions
     */
    public static function canDelete($record): bool
    {
        $instance = new static();
        
        if ($instance->isTenantContext()) {
            $tenant = $instance->getCurrentTenant();
            return auth()->user()->can('delete_' . static::getSlug() . '_' . $tenant->getTenantKey());
        }
        
        return auth()->user()->can('delete_' . static::getSlug());
    }
} 