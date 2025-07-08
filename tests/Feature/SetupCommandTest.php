<?php

namespace MuhammadNawlo\MultitenantPlugin\Tests\Feature;

use Illuminate\Support\Facades\File;
use MuhammadNawlo\MultitenantPlugin\Tests\TestCase;

class SetupCommandTest extends TestCase
{
    public function test_setup_command_can_be_run()
    {
        $this->artisan('multitenant:setup')
            ->expectsOutput('Setting up multitenant plugin...')
            ->assertExitCode(0);
    }

    public function test_setup_command_creates_tenant_model()
    {
        // Clean up any existing model
        $modelPath = app_path('Models/Tenant.php');
        if (File::exists($modelPath)) {
            File::delete($modelPath);
        }

        $this->artisan('multitenant:setup');

        $this->assertTrue(File::exists($modelPath));
    }

    public function test_setup_command_creates_middleware()
    {
        // Clean up any existing middleware
        $middlewarePath = app_path('Http/Middleware/EnsureValidTenantSession.php');
        if (File::exists($middlewarePath)) {
            File::delete($middlewarePath);
        }

        $this->artisan('multitenant:setup');

        $this->assertTrue(File::exists($middlewarePath));
    }

    public function test_setup_command_creates_directories_if_they_dont_exist()
    {
        // Remove directories if they exist
        $modelDir = app_path('Models');
        $middlewareDir = app_path('Http/Middleware');

        if (File::exists($modelDir)) {
            File::deleteDirectory($modelDir);
        }
        if (File::exists($middlewareDir)) {
            File::deleteDirectory($middlewareDir);
        }

        $this->artisan('multitenant:setup');

        $this->assertTrue(File::exists($modelDir));
        $this->assertTrue(File::exists($middlewareDir));
    }
}
