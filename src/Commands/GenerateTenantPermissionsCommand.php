<?php

namespace MuhammadNawlo\MultitenantPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stancl\Tenancy\Database\Models\Tenant;

class GenerateTenantPermissionsCommand extends Command
{
    protected $signature = 'multitenant:generate-permissions 
                            {tenant? : The tenant ID to generate permissions for}
                            {--all : Generate permissions for all tenants}
                            {--role= : The role to assign permissions to}';

    protected $description = 'Generate tenant-specific permissions using Filament Shield';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $generateAll = $this->option('all');
        $roleName = $this->option('role');

        if ($generateAll) {
            $this->generatePermissionsForAllTenants($roleName);
        } elseif ($tenantId) {
            $this->generatePermissionsForTenant($tenantId, $roleName);
        } else {
            $this->error('Please specify a tenant ID or use --all flag');
            return 1;
        }
    }

    protected function generatePermissionsForAllTenants(?string $roleName): void
    {
        $tenants = Tenant::all();
        
        $this->info("Generating permissions for {$tenants->count()} tenants...");
        
        $bar = $this->output->createProgressBar($tenants->count());
        $bar->start();

        foreach ($tenants as $tenant) {
            $this->generatePermissionsForTenant($tenant->getTenantKey(), $roleName);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Permissions generated for all tenants successfully!');
    }

    protected function generatePermissionsForTenant(string $tenantId, ?string $roleName): void
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant with ID '{$tenantId}' not found.");
            return;
        }

        $this->info("Generating permissions for tenant: {$tenant->name} ({$tenantId})");

        // Get all existing permissions
        $permissions = Permission::all();
        
        // Create tenant-specific permissions
        foreach ($permissions as $permission) {
            $tenantPermissionName = $permission->name . '_' . $tenantId;
            
            // Check if permission already exists
            if (!Permission::where('name', $tenantPermissionName)->exists()) {
                Permission::create([
                    'name' => $tenantPermissionName,
                    'guard_name' => $permission->guard_name,
                ]);
                
                $this->line("Created permission: {$tenantPermissionName}");
            }
        }

        // Assign permissions to role if specified
        if ($roleName) {
            $this->assignPermissionsToRole($roleName, $tenantId);
        }

        $this->info("Permissions generated for tenant '{$tenantId}' successfully!");
    }

    protected function assignPermissionsToRole(string $roleName, string $tenantId): void
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->warn("Role '{$roleName}' not found. Creating it...");
            $role = Role::create([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        // Get tenant-specific permissions
        $tenantPermissions = Permission::where('name', 'like', '%_' . $tenantId)->get();
        
        // Assign permissions to role
        $role->syncPermissions($tenantPermissions);
        
        $this->info("Assigned {$tenantPermissions->count()} permissions to role '{$roleName}'");
    }

    protected function createTenantRole(string $tenantId): Role
    {
        $roleName = "tenant_{$tenantId}_admin";
        
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'web',
        ]);

        return $role;
    }

    protected function getTenantPermissions(string $tenantId): array
    {
        return [
            // Resource permissions
            "view_any_tenant_{$tenantId}",
            "view_tenant_{$tenantId}",
            "create_tenant_{$tenantId}",
            "update_tenant_{$tenantId}",
            "delete_tenant_{$tenantId}",
            "delete_any_tenant_{$tenantId}",
            
            // Page permissions
            "view_tenant_dashboard_{$tenantId}",
            
            // Custom permissions
            "manage_tenant_{$tenantId}",
            "access_tenant_{$tenantId}",
        ];
    }
} 