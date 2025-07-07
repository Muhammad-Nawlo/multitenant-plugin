<?php

namespace MuhammadNawlo\MultitenantPlugin;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use MuhammadNawlo\MultitenantPlugin\Commands\MultitenantPluginCommand;
use MuhammadNawlo\MultitenantPlugin\Commands\SetupTenancyCommand;
use MuhammadNawlo\MultitenantPlugin\Testing\TestsMultitenantPlugin;

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
        $this->app->singleton(MultitenantPluginPlugin::class);
        
        // Register the main plugin class
        $this->app->singleton('multitenant-plugin', function ($app) {
            return new \MuhammadNawlo\MultitenantPlugin\MultitenantPlugin(
                $app->make(\Stancl\Tenancy\TenancyManager::class)
            );
        });
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Register Filament Resources and Pages
        $this->registerFilamentComponents();

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

    protected function getAssetPackageName(): ?string
    {
        return 'muhammad-nawlo/multitenant-plugin';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('multitenant-plugin', __DIR__ . '/../resources/dist/components/multitenant-plugin.js'),
            Css::make('multitenant-plugin-styles', __DIR__ . '/../resources/dist/multitenant-plugin.css'),
            Js::make('multitenant-plugin-scripts', __DIR__ . '/../resources/dist/multitenant-plugin.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            MultitenantPluginCommand::class,
            SetupTenancyCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
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
        if (!class_exists(\Filament\FilamentManager::class)) {
            return;
        }

        // Register tenant resource if enabled
        if (config('multitenant-plugin.enable_tenant_resource', true)) {
            \Filament\Resources\Resource::register([
                \MuhammadNawlo\MultitenantPlugin\Resources\TenantResource::class,
            ]);
        }

        // Register tenant dashboard if enabled
        if (config('multitenant-plugin.enable_dashboard', true)) {
            \Filament\Pages\Page::register([
                \MuhammadNawlo\MultitenantPlugin\Pages\TenantDashboard::class,
            ]);
        }
    }
}
