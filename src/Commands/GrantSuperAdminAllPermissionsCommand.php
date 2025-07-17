<?php

namespace MuhammadNawlo\MultitenantPlugin\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class GrantSuperAdminAllPermissionsCommand extends Command
{
    protected $signature = 'multitenant:grant-super-admin';
    protected $description = 'Grant all permissions to the super_admin role';

    public function handle()
    {
        $roleName = config('multitenant-plugin.super_admin_role', 'super_admin');
        $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        $permissions = Permission::all();
        $role->syncPermissions($permissions);
        $this->info("Granted all permissions (" . $permissions->count() . ") to role: {$roleName}");
    }
} 