<?php

namespace MuhammadNawlo\MultitenantPlugin\Traits;

use Illuminate\Database\Eloquent\Builder;

trait TenantAwareResource
{
    use HasTenancy;

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        if ($this->isTenantContext()) {
            $query = $this->applyTenantScope($query);
        }

        return $query;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Tenant Management';
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 3;
    }

    protected function getTableActionsFormColumns(): int
    {
        return 3;
    }
}
