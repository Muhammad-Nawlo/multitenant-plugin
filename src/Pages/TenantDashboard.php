<?php

namespace MuhammadNawlo\MultitenantPlugin\Pages;

use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use MuhammadNawlo\MultitenantPlugin\Traits\TenantAwareShieldPage;
use Stancl\Tenancy\Database\Models\Tenant;

class TenantDashboard extends Page
{
    use TenantAwareShieldPage;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'multitenant-plugin::pages.tenant-dashboard';

    protected static ?int $navigationSort = 0;

    public static function getNavigationGroup(): ?string
    {
        return 'Tenant Management';
    }

    public function getTitle(): string
    {
        return 'Tenant Dashboard';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TenantStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TenantListWidget::class,
        ];
    }
}

class TenantStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::whereNotNull('domain')->count();
        $currentTenant = tenancy()->tenant;

        return [
            Stat::make('Total Tenants', $totalTenants)
                ->description('All registered tenants')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),
            Stat::make('Active Tenants', $activeTenants)
                ->description('Tenants with domains')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info'),
            Stat::make('Current Tenant', $currentTenant ? $currentTenant->name ?? $currentTenant->getTenantKey() : 'None')
                ->description('Currently active tenant')
                ->descriptionIcon('heroicon-m-user-group')
                ->color($currentTenant ? 'success' : 'gray'),
        ];
    }
}

class TenantListWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $recentTenants = Tenant::latest()->take(5)->get();

        return [
            Stat::make('Recent Tenants', $recentTenants->count())
                ->description('Latest tenant registrations')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
