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
        if (!$this->option('force') && $this->isTenancyConfigured()) {
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
        
        if (!File::exists($modelPath)) {
            $this->info('Creating Tenant model...');
            
            $stub = File::get(__DIR__ . '/../../stubs/Tenant.php.stub');
            File::put($modelPath, $stub);
            
            $this->info('Tenant model created at: ' . $modelPath);
        }
    }

    protected function updateUserModel(): void
    {
        $userModelPath = app_path('Models/User.php');
        
        if (File::exists($userModelPath)) {
            $content = File::get($userModelPath);
            
            // Check if already has tenant trait
            if (!str_contains($content, 'BelongsToTenant')) {
                $this->info('Updating User model to be tenant-aware...');
                
                // Add the trait import
                $content = str_replace(
                    'use Illuminate\Foundation\Auth\User as Authenticatable;',
                    "use Illuminate\Foundation\Auth\User as Authenticatable;\nuse Stancl\Tenancy\Database\Concerns\BelongsToTenant;",
                    $content
                );
                
                // Add the trait to the class
                $content = str_replace(
                    'class User extends Authenticatable',
                    "class User extends Authenticatable\n{\n    use BelongsToTenant;",
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
        
        if (!File::exists($middlewarePath)) {
            $this->info('Creating tenant middleware...');
            
            $stub = File::get(__DIR__ . '/../../stubs/EnsureValidTenantSession.php.stub');
            File::put($middlewarePath, $stub);
            
            $this->info('Tenant middleware created at: ' . $middlewarePath);
        }
    }
} 