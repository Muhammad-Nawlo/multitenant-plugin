<?php

namespace MuhammadNawlo\MultitenantPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class GeneratePanelPermissionsCommand extends Command
{
    protected $signature = 'multitenant:generate-panel-permissions';

    protected $description = 'Generate all standard permissions for all resources registered to all panels';

    public function handle()
    {
        $panelPath = app_path('../src/Panels');
        $panelNamespace = 'MuhammadNawlo\\MultitenantPlugin\\Panels';
        $resourceNamespace = 'MuhammadNawlo\\MultitenantPlugin\\Resources';
        $created = 0;

        foreach (glob($panelPath . '/*.php') as $panelFile) {
            $panelClass = $panelNamespace . '\\' . pathinfo($panelFile, PATHINFO_FILENAME);
            if (! class_exists($panelClass)) {
                require_once $panelFile;
            }
            if (! class_exists($panelClass)) {
                $this->warn("Panel class not found: $panelClass");

                continue;
            }
            $panel = new $panelClass;
            if (! method_exists($panel, 'boot')) {
                $this->warn("Panel class $panelClass does not have a boot method.");

                continue;
            }
            // Use reflection to get resources registered in boot()
            $resources = $this->getPanelResources($panelClass);
            foreach ($resources as $resourceClass) {
                $created += $this->generatePermissionsForResource($resourceClass);
            }
        }
        $this->info("Total permissions created: $created");
    }

    protected function getPanelResources($panelClass)
    {
        // Use reflection to find the resources array in the boot method
        $resources = [];
        $ref = new \ReflectionClass($panelClass);
        $file = $ref->getFileName();
        $lines = file($file);
        $inResources = false;
        foreach ($lines as $line) {
            if (Str::contains($line, '->resources([')) {
                $inResources = true;

                continue;
            }
            if ($inResources) {
                if (Str::contains($line, ']);')) {
                    break;
                }
                if (preg_match('/([A-Za-z0-9_\\]+)::class/', $line, $matches)) {
                    $resources[] = $matches[1];
                }
            }
        }

        return $resources;
    }

    protected function generatePermissionsForResource($resourceClass)
    {
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
        $resourceSlug = $this->getResourceSlug($resourceClass);
        foreach ($actions as $action) {
            $permissionName = $action . '_' . $resourceSlug;
            if (! Permission::where('name', $permissionName)->exists()) {
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
                $this->info("Created permission: {$permissionName}");
                $created++;
            }
        }

        return $created;
    }

    protected function getResourceSlug($resourceClass)
    {
        if (class_exists($resourceClass) && method_exists($resourceClass, 'getSlug')) {
            return $resourceClass::getSlug();
        }
        // Fallback: use class basename in snake_case
        $basename = class_basename($resourceClass);

        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $basename));
    }
}
