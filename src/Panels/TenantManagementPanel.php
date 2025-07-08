<?php

namespace MuhammadNawlo\MultitenantPlugin\Panels;

use Filament\Panel;
use MuhammadNawlo\MultitenantPlugin\Resources\TenantResource;

class TenantManagementPanel extends Panel
{
    public function boot()
    {
        $this->id('tenant-management');
        $this->path('tenant-management'); // URL prefix: /tenant-management
        $this->resources([
            TenantResource::class,
        ]);
        $this->middleware([
            'auth', // Ensure user is authenticated
            function ($request, $next) {
                $user = auth()->user();
                $superAdminRole = config('multitenant-plugin.super_admin_role', 'super_admin');
                if (! $user || ! $user->hasRole($superAdminRole)) {
                    abort(403, 'Unauthorized. Only super admins can access this panel.');
                }
                return $next($request);
            },
        ]);
        // Optionally, add custom pages or middleware here
    }
} 