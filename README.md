# Filament Multitenant Plugin

A comprehensive multitenant plugin for Filament that integrates seamlessly with `stancl/tenancy` to make multitenant applications easier to build and manage.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/muhammad-nawlo/multitenant-plugin.svg?style=flat-square)](https://packagist.org/packages/muhammad-nawlo/multitenant-plugin)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/muhammad-nawlo/multitenant-plugin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/muhammad-nawlo/multitenant-plugin/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/muhammad-nawlo/multitenant-plugin/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/muhammad-nawlo/multitenant-plugin/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/muhammad-nawlo/multitenant-plugin.svg?style=flat-square)](https://packagist.org/packages/muhammad-nawlo/multitenant-plugin)

## Features

- 🏢 **Tenant Management**: Complete CRUD operations for tenants through Filament
- 📊 **Tenant Dashboard**: Beautiful dashboard with tenant statistics and quick actions
- 🔧 **Easy Integration**: Simple traits to make your resources tenant-aware
- ⚙️ **Flexible Configuration**: Extensive configuration options
- 🚀 **Quick Setup**: Automated setup command for fast deployment
- 🎨 **Modern UI**: Beautiful Filament interface for tenant management

## Installation

1. **Install the package via Composer:**

```bash
composer require muhammad-nawlo/multitenant-plugin
```

2. **Publish the configuration:**

```bash
php artisan vendor:publish --tag="multitenant-plugin-config"
```

3. **Run the setup command:**

```bash
php artisan multitenant:setup
```

4. **Add the plugin to your Filament panel:**

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

## Testing

```bash
composer test
```

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
