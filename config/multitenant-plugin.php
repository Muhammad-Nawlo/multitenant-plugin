<?php

// config for MuhammadNawlo/MultitenantPlugin
return [
    /*
    |--------------------------------------------------------------------------
    | Multitenant Plugin Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the multitenant plugin.
    | You can customize these settings according to your needs.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Tenant Model
    |--------------------------------------------------------------------------
    |
    | The model class that represents a tenant in your application.
    |
    */
    'tenant_model' => env('MULTITENANT_PLUGIN_TENANT_MODEL', \Stancl\Tenancy\Database\Models\Tenant::class),

    /*
    |--------------------------------------------------------------------------
    | Navigation Group
    |--------------------------------------------------------------------------
    |
    | The navigation group where tenant-related resources will be displayed.
    |
    */
    'navigation_group' => env('MULTITENANT_PLUGIN_NAVIGATION_GROUP', 'Tenant Management'),

    /*
    |--------------------------------------------------------------------------
    | Enable Tenant Dashboard
    |--------------------------------------------------------------------------
    |
    | Whether to enable the tenant dashboard page.
    |
    */
    'enable_dashboard' => env('MULTITENANT_PLUGIN_ENABLE_DASHBOARD', true),

    /*
    |--------------------------------------------------------------------------
    | Enable Tenant Resource
    |--------------------------------------------------------------------------
    |
    | Whether to enable the tenant management resource.
    |
    */
    'enable_tenant_resource' => env('MULTITENANT_PLUGIN_ENABLE_TENANT_RESOURCE', true),

    /*
    |--------------------------------------------------------------------------
    | Tenant Resource Columns
    |--------------------------------------------------------------------------
    |
    | The columns to display in the tenant resource table.
    |
    */
    'tenant_resource_columns' => [
        'id' => [
            'label' => 'Tenant ID',
            'searchable' => true,
            'sortable' => true,
        ],
        'name' => [
            'label' => 'Tenant Name',
            'searchable' => true,
            'sortable' => true,
        ],
        'domain' => [
            'label' => 'Domain',
            'searchable' => true,
            'sortable' => true,
        ],
        'created_at' => [
            'label' => 'Created At',
            'sortable' => true,
            'toggleable' => true,
            'hidden_by_default' => true,
        ],
        'updated_at' => [
            'label' => 'Updated At',
            'sortable' => true,
            'toggleable' => true,
            'hidden_by_default' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Form Fields
    |--------------------------------------------------------------------------
    |
    | The form fields for creating and editing tenants.
    |
    */
    'tenant_form_fields' => [
        'id' => [
            'type' => 'text',
            'label' => 'Tenant ID',
            'required' => true,
            'max_length' => 255,
        ],
        'name' => [
            'type' => 'text',
            'label' => 'Tenant Name',
            'required' => true,
            'max_length' => 255,
        ],
        'domain' => [
            'type' => 'text',
            'label' => 'Domain',
            'required' => false,
            'max_length' => 255,
        ],
        'data' => [
            'type' => 'textarea',
            'label' => 'Additional Data',
            'required' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-scope Resources
    |--------------------------------------------------------------------------
    |
    | Whether to automatically scope resources to the current tenant.
    | Set to false if you want to manually handle tenant scoping.
    |
    */
    'auto_scope_resources' => env('MULTITENANT_PLUGIN_AUTO_SCOPE_RESOURCES', true),

    /*
    |--------------------------------------------------------------------------
    | Tenant Middleware
    |--------------------------------------------------------------------------
    |
    | The middleware to apply for tenant-specific routes.
    |
    */
    'tenant_middleware' => [
        'web',
        'auth',
        'tenant',
    ],

    /*
    |--------------------------------------------------------------------------
    | Central Middleware
    |--------------------------------------------------------------------------
    |
    | The middleware to apply for central administration routes.
    |
    */
    'central_middleware' => [
        'web',
        'auth',
    ],

    /*
    |--------------------------------------------------------------------------
    | Shield Integration
    |--------------------------------------------------------------------------
    |
    | Configuration for Filament Shield integration.
    |
    */
    'shield_integration' => [
        'enabled' => env('MULTITENANT_PLUGIN_SHIELD_ENABLED', true),
        'auto_generate_permissions' => env('MULTITENANT_PLUGIN_SHIELD_AUTO_GENERATE_PERMISSIONS', true),
        'tenant_permission_suffix' => env('MULTITENANT_PLUGIN_SHIELD_TENANT_PERMISSION_SUFFIX', true),
        'default_roles' => [
            'tenant_admin' => 'Tenant Administrator',
            'tenant_manager' => 'Tenant Manager',
            'tenant_user' => 'Tenant User',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Patterns
    |--------------------------------------------------------------------------
    |
    | Patterns for generating tenant-specific permissions.
    |
    */
    'permission_patterns' => [
        'resource' => '{action}_{resource}_{tenant}',
        'page' => 'view_{page}_{tenant}',
        'custom' => '{permission}_{tenant}',
    ],

    'super_admin_role' => env('MULTITENANT_PLUGIN_SUPER_ADMIN_ROLE', 'super_admin'), // .env: MULTITENANT_PLUGIN_SUPER_ADMIN_ROLE
];
