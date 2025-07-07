<?php

namespace MuhammadNawlo\MultitenantPlugin\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\TenancyManager;

class TenantPermissionMiddleware
{
    protected TenancyManager $tenancyManager;

    public function __construct(TenancyManager $tenancyManager)
    {
        $this->tenancyManager = $tenancyManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $tenant = $this->tenancyManager->tenant;

        if (! $tenant) {
            // In central context, check global permission
            if (! auth()->user()->can($permission)) {
                abort(403, 'Unauthorized action.');
            }
        } else {
            // In tenant context, check tenant-specific permission
            $tenantPermission = $permission . '_' . $tenant->getTenantKey();

            if (! auth()->user()->can($tenantPermission)) {
                abort(403, 'Unauthorized action for this tenant.');
            }
        }

        return $next($request);
    }
}
