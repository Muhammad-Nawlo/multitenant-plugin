<?php

namespace MuhammadNawlo\MultitenantPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SetupTenancyCommand extends Command
{
    protected $signature = 'multitenant:setup {--force : Force the setup even if already configured}';

    protected $description = 'Setup tenancy configuration for the application';

    public function handle()
    {
        $this->info('Setting up multitenant plugin...');

        // Check if tenancy is already configured
        if (! $this->option('force') && $this->isTenancyConfigured()) {
            $this->warn('Tenancy appears to be already configured. Use --force to override.');

            return 1;
        }

        // Publish tenancy configuration
        $this->info('Publishing tenancy configuration...');
        Artisan::call('vendor:publish', [
            '--provider' => 'Stancl\Tenancy\TenancyServiceProvider',
            '--tag' => 'config',
        ]);

        // Publish tenancy migrations
        $this->info('Publishing tenancy migrations...');
        Artisan::call('vendor:publish', [
            '--provider' => 'Stancl\Tenancy\TenancyServiceProvider',
            '--tag' => 'migrations',
        ]);

        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate');

        // Create tenant model if it doesn't exist
        $this->createTenantModel();

        // Update User model to be tenant-aware
        $this->updateUserModel();

        // Create middleware
        $this->createTenantMiddleware();

        $this->info('Multitenant plugin setup completed successfully!');
        $this->info('Next steps:');
        $this->info('1. Configure your tenant domains in the tenancy config');
        $this->info('2. Add tenant middleware to your routes');
        $this->info('3. Use the TenantAwareResource trait in your Filament resources');
    }

    protected function isTenancyConfigured(): bool
    {
        return File::exists(config_path('tenancy.php'));
    }

    protected function createTenantModel(): void
    {
        $modelPath = app_path('Models/Tenant.php');
        $modelDir = app_path('Models');

        if (! File::exists($modelPath)) {
            $this->info('Creating Tenant model...');

            try {
                // Ensure the models directory exists
                if (! File::exists($modelDir)) {
                    File::makeDirectory($modelDir, 0755, true);
                    $this->info('Created models directory: ' . $modelDir);
                }

                $stubPath = __DIR__ . '/../../stubs/Tenant.php.stub';

                if (! File::exists($stubPath)) {
                    $this->error('Stub file not found: ' . $stubPath);

                    return;
                }

                $stub = File::get($stubPath);
                File::put($modelPath, $stub);

                $this->info('Tenant model created at: ' . $modelPath);
            } catch (\Exception $e) {
                $this->error('Failed to create Tenant model: ' . $e->getMessage());
            }
        }
    }

    protected function updateUserModel(): void
    {
        $userModelPath = app_path('Models/User.php');

        if (File::exists($userModelPath)) {
            $content = File::get($userModelPath);

            // Check if already has tenant trait
            if (! str_contains($content, 'BelongsToTenant')) {
                $this->info('Updating User model to be tenant-aware...');

                // Add the trait import if not present
                if (!str_contains($content, 'use Stancl\Tenancy\Database\Concerns\BelongsToTenant;')) {
                    $content = preg_replace(
                        '/(use Illuminate\\Foundation\\Auth\\User as Authenticatable;)/',
                        "\1\nuse Stancl\\Tenancy\\Database\\Concerns\\BelongsToTenant;",
                        $content
                    );
                }

                // Add the trait usage after the class opening
                $content = preg_replace(
                    '/(class\\s+User\\s+extends\\s+Authenticatable\\s*{)/',
                    "\1\n    use BelongsToTenant;",
                    $content
                );

                File::put($userModelPath, $content);
                $this->info('User model updated to be tenant-aware.');
            }
        }
    }

    protected function createTenantMiddleware(): void
    {
        $middlewarePath = app_path('Http/Middleware/EnsureValidTenantSession.php');
        $middlewareDir = app_path('Http/Middleware');

        if (! File::exists($middlewarePath)) {
            $this->info('Creating tenant middleware...');

            try {
                // Ensure the middleware directory exists
                if (! File::exists($middlewareDir)) {
                    File::makeDirectory($middlewareDir, 0755, true);
                    $this->info('Created middleware directory: ' . $middlewareDir);
                }

                $stubPath = __DIR__ . '/../../stubs/EnsureValidTenantSession.php.stub';

                if (! File::exists($stubPath)) {
                    $this->error('Stub file not found: ' . $stubPath);

                    return;
                }

                $stub = File::get($stubPath);
                File::put($middlewarePath, $stub);

                $this->info('Tenant middleware created at: ' . $middlewarePath);
            } catch (\Exception $e) {
                $this->error('Failed to create tenant middleware: ' . $e->getMessage());
            }
        }
    }
}
