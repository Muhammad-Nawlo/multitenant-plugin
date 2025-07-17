<?php

namespace MuhammadNawlo\MultitenantPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Arr;

class MultitenantGenerateCommand extends Command
{
    protected $signature = 'multitenant:generate
        {--all : Generate permissions/policies for all entities}
        {--panel= : Panel class (FQCN) to generate for}
        {--resource= : One or many resources separated by comma}
        {--exclude : Exclude the given entities during generation}
        {--policies : Also generate policy stubs}
        {--minimal : Output minimal info}
    ';

    protected $description = 'Generate permissions and/or policies for Filament resources registered to panels.';

    public function handle()
    {
        $panels = $this->getPanels();
        $targetPanel = $this->option('panel');
        $resources = [];
        foreach ($panels as $panelClass) {
            if ($targetPanel && $panelClass !== $targetPanel) continue;
            $resources = array_merge($resources, $this->getPanelResources($panelClass));
        }
        $resources = array_unique($resources);

        $selectedResources = $this->filterResources($resources);
        $created = 0;
        $policies = [];
        foreach ($selectedResources as $resourceClass) {
            $created += $this->generatePermissionsForResource($resourceClass);
            if ($this->option('policies')) {
                $policies[] = $this->generatePolicyStub($resourceClass);
            }
        }
        if ($this->option('minimal')) {
            $this->info("Permissions created: $created");
            if ($this->option('policies')) {
                $this->info("Policies generated: " . count(array_filter($policies)));
            }
        } else {
            $this->table(
                ['Resource', 'Permissions', 'Policy'],
                collect($selectedResources)->map(function ($resource, $i) use ($policies) {
                    $slug = $this->getResourceSlug($resource);
                    $perms = implode(", ", $this->getPermissionNames($slug));
                    $policy = isset($policies[$i]) && $policies[$i] ? '✅' : '❌';
                    return [
                        $resource,
                        $perms,
                        $policy,
                    ];
                })
            );
        }
    }

    protected function getPanels(): array
    {
        $panelPath = app_path('../src/Panels');
        $panelNamespace = 'MuhammadNawlo\\MultitenantPlugin\\Panels';
        $panels = [];
        foreach (glob($panelPath . '/*.php') as $panelFile) {
            $panelClass = $panelNamespace . '\\' . pathinfo($panelFile, PATHINFO_FILENAME);
            if (!class_exists($panelClass)) {
                require_once $panelFile;
            }
            if (class_exists($panelClass)) {
                $panels[] = $panelClass;
            }
        }
        return $panels;
    }

    protected function getPanelResources($panelClass): array
    {
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

    protected function filterResources(array $resources): array
    {
        $input = $this->option('resource');
        $exclude = $this->option('exclude');
        if ($this->option('all')) {
            return $resources;
        }
        if ($input) {
            $inputArr = array_map('trim', explode(',', $input));
            if ($exclude) {
                return array_filter($resources, fn($r) => !in_array(class_basename($r), $inputArr));
            } else {
                return array_filter($resources, fn($r) => in_array(class_basename($r), $inputArr));
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
            if (!Permission::where('name', $permissionName)->exists()) {
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
                $created++;
            }
        }
        return $created;
    }

    protected function getPermissionNames($resourceSlug)
    {
        return [
            'view_any_' . $resourceSlug,
            'view_' . $resourceSlug,
            'create_' . $resourceSlug,
            'update_' . $resourceSlug,
            'delete_' . $resourceSlug,
            'delete_any_' . $resourceSlug,
            'restore_' . $resourceSlug,
            'force_delete_' . $resourceSlug,
        ];
    }

    protected function getResourceSlug($resourceClass)
    {
        if (class_exists($resourceClass) && method_exists($resourceClass, 'getSlug')) {
            return $resourceClass::getSlug();
        }
        $basename = class_basename($resourceClass);
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $basename));
    }

    protected function generatePolicyStub($resourceClass)
    {
        // You can extend this to use your stub system
        // For now, just return true to indicate a policy would be generated
        return true;
    }
} 