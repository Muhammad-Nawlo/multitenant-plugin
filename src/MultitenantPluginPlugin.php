<?php

namespace MuhammadNawlo\MultitenantPlugin;

use Filament\Contracts\Plugin;
use Filament\Panel;

class MultitenantPluginPlugin implements Plugin
{
    public function getId(): string
    {
        return 'multitenant-plugin';
    }

    public function register(Panel $panel): void
    {
        // Register tenant resource
        if (config('multitenant-plugin.enable_tenant_resource', true)) {
            $panel->resources([
                \MuhammadNawlo\MultitenantPlugin\Resources\TenantResource::class,
            ]);
        }

        // Register tenant dashboard page
        if (config('multitenant-plugin.enable_dashboard', true)) {
            $panel->pages([
                \MuhammadNawlo\MultitenantPlugin\Pages\TenantDashboard::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        // Add tenant-aware middleware if needed
        if (config('multitenant-plugin.auto_scope_resources', true)) {
            // This will be handled by the traits
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
