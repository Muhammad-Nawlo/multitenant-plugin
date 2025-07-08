# Filament Multitenant Plugin

A comprehensive multitenant plugin for Filament that integrates seamlessly with `stancl/tenancy` and `filament-shield` to make multitenant applications easier to build and manage. This plugin provides complete tenant management with role-based permissions and beautiful Filament interfaces.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/muhammad-nawlo/multitenant-plugin.svg?style=flat-square)](https://packagist.org/packages/muhammad-nawlo/multitenant-plugin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/muhammad-nawlo/multitenant-plugin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/muhammad-nawlo/multitenant-plugin/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/muhammad-nawlo/multitenant-plugin/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/muhammad-nawlo/multitenant-plugin/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/muhammad-nawlo/multitenant-plugin.svg?style=flat-square)](https://packagist.org/packages/muhammad-nawlo/multitenant-plugin)

## Features

- 🏢 **Tenant Management**: Complete CRUD operations for tenants through Filament
- 📊 **Tenant Dashboard**: Beautiful dashboard with tenant statistics and quick actions
- 🔧 **Easy Integration**: Simple traits to make your resources tenant-aware
- 🛡️ **Shield Integration**: Role-based permissions with tenant-specific access control
- ⚙️ **Flexible Configuration**: Extensive configuration options
- 🚀 **Quick Setup**: Automated setup command for fast deployment
- 🎨 **Modern UI**: Beautiful Filament interface for tenant management
- 🔒 **Robust Error Handling**: Graceful degradation when tenancy isn't available
- 📝 **Comprehensive Documentation**: Detailed examples and usage guides
- 🛠️ **Production Ready**: Thoroughly tested with proper error handling and edge cases covered

## Installation

1. **Install the package via Composer:**

```bash
composer require muhammad-nawlo/multitenant-plugin
```

2. **Install required dependencies (if not already installed):**

```bash
# Install tenancy package
composer require stancl/tenancy

# Install shield package (optional, for permissions)
composer require bezhanSalleh/filament-shield
```

3. **Publish the configuration:**

```bash
php artisan vendor:publish --tag="multitenant-plugin-config"
```

4. **Run the setup command:**

```bash
php artisan multitenant:setup
```

5. **Add the plugin to your Filament panel:**

```php
use MuhammadNawlo\MultitenantPlugin\MultitenantPluginPlugin;

// In your panel configuration
$panel->plugins([
    MultitenantPluginPlugin::make(),
]);
```

## Quick Start

### Making Resources Tenant-Aware

Use the `TenantAwareResource` trait in your Filament resources:

```php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use MuhammadNawlo\MultitenantPlugin\Traits\TenantAwareResource;

class PostResource extends Resource
{
    use TenantAwareResource;

    // Your resource configuration...
}
```

### Making Resources Tenant-Aware with Shield Permissions

Use the `TenantAwareShieldResource` trait for resources that need both tenancy and permissions:

```php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use MuhammadNawlo\MultitenantPlugin\Traits\TenantAwareShieldResource;

class PostResource extends Resource
{
    use TenantAwareShieldResource;

    // Your resource configuration...
    // This will automatically check tenant-specific permissions
    // and scope data to the current tenant
}
```

### Making Pages Tenant-Aware

Use the `TenantAwarePage` trait in your Filament pages:

```php
<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use MuhammadNawlo\MultitenantPlugin\Traits\TenantAwarePage;

class Dashboard extends Page
{
    use TenantAwarePage;

    // Your page configuration...
}
```

### Making Pages Tenant-Aware with Shield Permissions

Use the `TenantAwareShieldPage` trait for pages that need both tenancy and permissions:

```php
<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use MuhammadNawlo\MultitenantPlugin\Traits\TenantAwareShieldPage;

class Dashboard extends Page
{
    use TenantAwareShieldPage;

    // Your page configuration...
    // This will automatically check tenant-specific permissions
    // and provide tenant context information
}
```

### Making Models Tenant-Aware

Add the `BelongsToTenant` trait to your models:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Post extends Model
{
    use BelongsToTenant;

    // Your model configuration...
}
```

## Configuration

The plugin provides extensive configuration options in `config/multitenant-plugin.php`:

```php
return [
    // Tenant model class
    'tenant_model' => \Stancl\Tenancy\Database\Models\Tenant::class,
    
    // Navigation group for tenant resources
    'navigation_group' => 'Tenant Management',
    
    // Enable/disable features
    'enable_dashboard' => true,
    'enable_tenant_resource' => true,
    
    // Auto-scope resources to current tenant
    'auto_scope_resources' => true,
    
    // Middleware configuration
    'tenant_middleware' => ['web', 'auth', 'tenant'],
    'central_middleware' => ['web', 'auth'],
];
```

## Usage

### Tenant Management

The plugin provides a complete tenant management interface:

- **List Tenants**: View all tenants with search and filtering
- **Create Tenant**: Add new tenants with custom data
- **Edit Tenant**: Modify tenant information
- **Delete Tenant**: Remove tenants safely
- **Switch Tenant**: Switch between tenant contexts

### Tenant Dashboard

Access the tenant dashboard to see:

- Current tenant information
- Tenant statistics (total, active, recent)
- Quick actions for tenant management
- Navigation to tenant resources

### API Methods

The traits provide several useful methods:

```php
// Get current tenant
$tenant = $this->getCurrentTenant();

// Get tenant ID
$tenantId = $this->getTenantId();

// Check if in tenant context
$isTenantContext = $this->isTenantContext();

// Scope query to current tenant
$query = $this->scopeToTenant($query);
```

## Commands

### Setup Command

```bash
php artisan multitenant:setup
```

This command will:

- Publish tenancy configuration
- Publish and run migrations
- Create tenant model
- Update User model to be tenant-aware
- Create tenant middleware

### Generate Tenant Permissions

```bash
# Generate permissions for a specific tenant
php artisan multitenant:generate-permissions tenant-1

# Generate permissions for all tenants
php artisan multitenant:generate-permissions --all

# Generate permissions and assign to a role
php artisan multitenant:generate-permissions tenant-1 --role=tenant_admin
```

This command will:

- Create tenant-specific permissions for all existing permissions
- Optionally assign permissions to specified roles
- Support bulk generation for all tenants
- Create tenant-specific permission names (e.g., `view_any_post_tenant-1`)

### Force Setup

```bash
php artisan multitenant:setup --force
```

Force setup even if tenancy is already configured.

## Middleware

The plugin includes middleware for tenant initialization:

```php
// In your routes
Route::middleware(['web', 'auth', 'tenant'])->group(function () {
    // Tenant-specific routes
});

Route::middleware(['web', 'auth'])->group(function () {
    // Central administration routes
});
```

## Advanced Usage

### Custom Tenant Identification

You can customize how tenants are identified by modifying the middleware:

```php
// In stubs/EnsureValidTenantSession.php.stub
// Choose your preferred method:

// Domain-based
return app(InitializeTenancyByDomain::class)->handle($request, $next);

// Subdomain-based
return app(InitializeTenancyBySubdomain::class)->handle($request, $next);

// Path-based
return app(InitializeTenancyByPath::class)->handle($request, $next);
```

### Error Handling

The plugin is designed to be robust and handle cases where dependencies aren't available:

- **Tenancy not available**: Plugin works without tenant-specific features
- **Shield not available**: Plugin works without permission features
- **Graceful degradation**: Features are disabled rather than causing errors
- **Test environment support**: Plugin works in testing contexts with proper null checks
- **Asset registration**: Removed problematic asset registration that caused composer require errors

### Custom Tenant Data

Store additional tenant data in the `data` column:

```php
$tenant = Tenant::create([
    'id' => 'tenant-1',
    'name' => 'My Tenant',
    'domain' => 'tenant1.example.com',
    'data' => [
        'plan' => 'premium',
        'settings' => [
            'theme' => 'dark',
            'features' => ['feature1', 'feature2'],
        ],
    ],
]);
```

### Tenant-Specific Permissions

The plugin automatically creates tenant-specific permissions. For example:

```php
// Global permission
'view_any_post'

// Tenant-specific permission
'view_any_post_tenant-1'
'view_any_post_tenant-2'
```

This allows you to control access per tenant:

```php
// Check if user can view posts in current tenant
if (auth()->user()->can('view_any_post_' . $tenant->getTenantKey())) {
    // User has permission for this specific tenant
}
```

### Using the Permission Service

```php
use MuhammadNawlo\MultitenantPlugin\Services\TenantPermissionService;

$permissionService = app('tenant-permission-service');

// Check permission for current tenant
if ($permissionService->hasPermission('view_any_post')) {
    // User has permission
}

// Get tenant-specific permission name
$permissionName = $permissionService->getTenantPermission('view_any_post');
// Returns: 'view_any_post_tenant-1' (if in tenant context)
```

## Testing

```bash
composer test
```

## Troubleshooting

### Common Issues

1. **"Target class [Stancl\Tenancy\TenancyManager] does not exist"**
   - Make sure you have installed `stancl/tenancy`
   - Run `composer require stancl/tenancy`
   - Follow the tenancy package setup instructions
   - The plugin will work without tenancy, but tenant-specific features will be disabled

2. **Setup command fails**
   - Ensure you have write permissions to your app directory
   - Check that the User model exists and is writable
   - Run `php artisan multitenant:setup --force` to overwrite existing files
   - The command will create missing directories automatically

3. **Permissions not working**
   - Ensure `filament-shield` is properly installed and configured
   - Run `php artisan shield:generate` to generate base permissions
   - Then run `php artisan multitenant:generate-permissions --all`
   - Check that your User model has the `HasRoles` trait

4. **"Class not found" errors in TenantResource**
   - This usually happens when page namespaces are incorrect
   - The plugin now uses fully qualified namespaces to avoid this issue
   - If you encounter this, try running the setup command again

5. **Syntax errors in User.php after setup**
   - The setup command now uses regex to properly insert the trait
   - If you encounter syntax errors, check the User model file
   - Run `php artisan multitenant:setup --force` to fix the User model

6. **Plugin not working in test environment**
   - This is expected behavior as the plugin requires a proper Laravel application
   - The plugin includes null checks and try-catch blocks for test environments
   - Features will be gracefully disabled in test contexts

### Debugging

To debug issues with the plugin:

```bash
# Check if tenancy is properly installed
php artisan tinker
>>> app('Stancl\Tenancy\TenancyManager')

# Check if shield is properly installed
php artisan tinker
>>> app('BezhanSalleh\FilamentShield\FilamentShield')

# Verify plugin registration
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Getting Help

If you're still experiencing issues:

1. Check the [stancl/tenancy documentation](https://tenancyforlaravel.com/)
2. Check the [filament-shield documentation](https://github.com/bezhanSalleh/filament-shield)
3. Open an issue on the GitHub repository with detailed error messages

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Muhammad Nawlo](https://github.com/Muhammad-Nawlo)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## TenantManagementPanel

This plugin provides a dedicated Filament panel for tenant management, accessible at `/tenant-management`.

### Features
- Isolated panel for managing tenants
- Only users with the `super_admin` role can access this panel
- Integrates with [stancl/tenancy](https://github.com/stancl/tenancy) and [Shield](https://github.com/filamentphp/shield)
- All tenant management resources and pages are registered only in this panel

### Dependencies
- `stancl/tenancy`
- `filamentphp/shield`

### Access Control
- The panel uses middleware to restrict access to users with a configurable super admin role:
  - By default, only users with the `super_admin` role can access this panel.
  - You can change the role name by setting `MULTITENANT_PLUGIN_SUPER_ADMIN_ROLE` in your `.env` file.
    - Example: `MULTITENANT_PLUGIN_SUPER_ADMIN_ROLE=your_custom_role`
  - If a user is not authenticated or does not have the configured role, they will receive a 403 error.

### Testing
1. Install this package in a Laravel app with Filament, stancl/tenancy, and Shield installed.
2. Ensure your user has the `super_admin` role (using Spatie/Permission or Shield).
3. Visit `/tenant-management` in your browser.
4. Only super admins should be able to access and manage tenants.
