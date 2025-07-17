<?php

namespace MuhammadNawlo\MultitenantPlugin\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class GenerateResourcePermissionsCommand extends Command
{
    protected $signature = 'multitenant:generate-resource-permissions {resource}';

    protected $description = 'Generate all standard permissions for a given resource';

    public function handle()
    {
        $resource = $this->argument('resource');
        $resourceSlug = $this->getResourceSlug($resource);
        $actions = [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'delete_any',
            'restore',
            'force_delete',
        ];
        $created = 0;
        foreach ($actions as $action) {
            $permissionName = $action . '_' . $resourceSlug;
            if (! Permission::where('name', $permissionName)->exists()) {
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
                $this->info("Created permission: {$permissionName}");
                $created++;
            }
        }
        $this->info("Total permissions created: {$created}");
    }

    protected function getResourceSlug($resource)
    {
        if (class_exists($resource) && method_exists($resource, 'getSlug')) {
            return $resource::getSlug();
        }
        // Fallback: use class basename in snake_case
        $basename = class_basename($resource);

        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $basename));
    }
}
