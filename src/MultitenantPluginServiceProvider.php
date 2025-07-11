<?php

namespace MuhammadNawlo\MultitenantPlugin;

use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use MuhammadNawlo\MultitenantPlugin\Commands\GenerateTenantPermissionsCommand;
use MuhammadNawlo\MultitenantPlugin\Commands\MultitenantPluginCommand;
use MuhammadNawlo\MultitenantPlugin\Commands\SetupTenancyCommand;
use MuhammadNawlo\MultitenantPlugin\Testing\TestsMultitenantPlugin;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MultitenantPluginServiceProvider extends PackageServiceProvider
{
    public static string $name = 'multitenant-plugin';

    public static string $viewNamespace = 'multitenant-plugin';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('muhammad-nawlo/multitenant-plugin');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
        // Register the plugin with Filament
        $this->app->singleton(MultitenantPluginManager::class);

        // Register the main plugin class
        $this->app->singleton('multitenant-plugin', function ($app) {
            try {
                $tenancyManager = $app->make(\Stancl\Tenancy\TenancyManager::class);

                return new \MuhammadNawlo\MultitenantPlugin\MultitenantPlugin($tenancyManager);
            } catch (\Exception $e) {
                // If tenancy manager is not available, create plugin without it
                return new \MuhammadNawlo\MultitenantPlugin\MultitenantPlugin(null);
            }
        });

        // Register the tenant permission service
        $this->app->singleton('tenant-permission-service', function ($app) {
            try {
                $tenancyManager = $app->make(\Stancl\Tenancy\TenancyManager::class);

                return new \MuhammadNawlo\MultitenantPlugin\Services\TenantPermissionService($tenancyManager);
            } catch (\Exception $e) {
                // If tenancy manager is not available, return null
                return null;
            }
        });
    }

    public function packageBooted(): void
    {
        // Register Filament Resources and Pages
        $this->registerFilamentComponents();

        // Automatically register the plugin with Filament panels
        $this->registerWithFilament();

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/multitenant-plugin/{$file->getFilename()}"),
                ], 'multitenant-plugin-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsMultitenantPlugin);
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            MultitenantPluginCommand::class,
            SetupTenancyCommand::class,
            GenerateTenantPermissionsCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_multitenant-plugin_table',
        ];
    }

    /**
     * Register Filament components (resources and pages)
     */
    protected function registerFilamentComponents(): void
    {
        // Only register if Filament is installed
        if (! class_exists(\Filament\FilamentManager::class)) {
            return;
        }

        // Filament components are registered through the plugin class
        // This method is kept for future use if needed
    }

    /**
     * Register the plugin with Filament panels
     */
    protected function registerWithFilament(): void
    {
        // Only register if Filament is installed
        if (! class_exists(\Filament\FilamentManager::class)) {
            return;
        }

        // Register with all panels
        $this->app->booted(function () {
            if (class_exists(\Filament\Panel::class)) {
                // Register the TenantManagementPanel
                if (class_exists(\MuhammadNawlo\MultitenantPlugin\Panels\TenantManagementPanel::class)) {
                    \Filament\Facades\Filament::registerPanel(new \MuhammadNawlo\MultitenantPlugin\Panels\TenantManagementPanel);
                }
            }
        });
    }
}
