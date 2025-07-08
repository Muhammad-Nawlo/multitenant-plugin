<?php

namespace MuhammadNawlo\MultitenantPlugin\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Database\Models\Tenant;
use Stancl\Tenancy\TenancyManager;

class TenantPermissionService
{
    protected ?TenancyManager $tenancyManager;

    public function __construct(?TenancyManager $tenancyManager = null)
    {
        $this->tenancyManager = $tenancyManager;
    }

    /**
     * Get current tenant
     */
    public function getCurrentTenant(): ?Tenant
    {
        if (! $this->tenancyManager) {
            return null;
        }

        return $this->tenancyManager->tenant;
    }

    /**
     * Check if user has permission for current tenant
     */
    public function hasPermission(string $permission): bool
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            return auth()->user()->can($permission);
        }

        $tenantPermission = $permission . '_' . $tenant->getTenantKey();

        return auth()->user()->can($tenantPermission);
    }

    /**
     * Get tenant-specific permission name
     */
    public function getTenantPermission(string $permission): string
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            return $permission;
        }

        return $permission . '_' . $tenant->getTenantKey();
    }

    /**
     * Create permissions for a specific tenant
     */
    public function createTenantPermissions(string $tenantId, array $permissions = []): void
    {
        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            throw new \InvalidArgumentException("Tenant with ID '{$tenantId}' not found.");
        }

        // If no specific permissions provided, get all existing permissions
        if (empty($permissions)) {
            $permissions = Permission::all()->pluck('name')->toArray();
        }

        foreach ($permissions as $permission) {
            $tenantPermissionName = $permission . '_' . $tenantId;

            if (! Permission::where('name', $tenantPermissionName)->exists()) {
                Permission::create([
                    'name' => $tenantPermissionName,
                    'guard_name' => 'web',
                ]);
            }
        }
    }

    /**
     * Assign permissions to a role for a specific tenant
     */
    public function assignTenantPermissionsToRole(string $roleName, string $tenantId, array $permissions = []): void
    {
        $role = Role::where('name', $roleName)->first();

        if (! $role) {
            $role = Role::create([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        // If no specific permissions provided, get all tenant-specific permissions
        if (empty($permissions)) {
            $tenantPermissions = Permission::where('name', 'like', '%_' . $tenantId)->get();
        } else {
            $tenantPermissionNames = array_map(fn ($p) => $p . '_' . $tenantId, $permissions);
            $tenantPermissions = Permission::whereIn('name', $tenantPermissionNames)->get();
        }

        $role->syncPermissions($tenantPermissions);
    }

    /**
     * Get all permissions for a specific tenant
     */
    public function getTenantPermissions(string $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        return Permission::where('name', 'like', '%_' . $tenantId)->get();
    }

    /**
     * Get all roles that have permissions for a specific tenant
     */
    public function getTenantRoles(string $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        $tenantPermissions = $this->getTenantPermissions($tenantId);

        return Role::whereHas('permissions', function ($query) use ($tenantPermissions) {
            $query->whereIn('permissions.id', $tenantPermissions->pluck('id'));
        })->get();
    }

    /**
     * Create a default role for a tenant
     */
    public function createTenantRole(string $tenantId, ?string $roleName = null): Role
    {
        if (! $roleName) {
            $roleName = "tenant_{$tenantId}_admin";
        }

        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        return $role;
    }

    /**
     * Get available permissions for the current tenant context
     */
    public function getAvailablePermissions(): array
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            return Permission::all()->pluck('name')->toArray();
        }

        return Permission::where('name', 'like', '%_' . $tenant->getTenantKey())
            ->pluck('name')
            ->toArray();
    }

    /**
     * Check if a user has any permission for the current tenant
     */
    public function hasAnyTenantPermission(): bool
    {
        $tenant = $this->getCurrentTenant();

        if (! $tenant) {
            return true; // In central context, assume access
        }

        $tenantPermissions = Permission::where('name', 'like', '%_' . $tenant->getTenantKey())->get();

        foreach ($tenantPermissions as $permission) {
            if (auth()->user()->can($permission->name)) {
                return true;
            }
        }

        return false;
    }
}
