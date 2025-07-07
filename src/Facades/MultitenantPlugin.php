<?php

namespace MuhammadNawlo\MultitenantPlugin\Facades;

use Illuminate\Support\Facades\Facade;
use Stancl\Tenancy\TenancyManager;

/**
 * @method static TenancyManager getTenancyManager()
 * @method static mixed getCurrentTenant()
 * @method static string|null getTenantId()
 * @method static bool isTenantContext()
 * @method static \Illuminate\Database\Eloquent\Builder scopeToTenant(\Illuminate\Database\Eloquent\Builder $query)
 * @method static array getTenantData()
 * 
 * @see \MuhammadNawlo\MultitenantPlugin\MultitenantPlugin
 */
class MultitenantPlugin extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'multitenant-plugin';
    }
}
