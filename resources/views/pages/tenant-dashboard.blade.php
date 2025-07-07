<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Current Tenant Info -->
        @if($this->getCurrentTenant())
            <x-filament::section>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                            Current Tenant: {{ $this->getCurrentTenant()->name ?? $this->getCurrentTenant()->getTenantKey() }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Tenant ID: {{ $this->getCurrentTenant()->getTenantKey() }}
                        </p>
                        @if($this->getCurrentTenant()->domain)
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Domain: {{ $this->getCurrentTenant()->domain }}
                            </p>
                        @endif
                    </div>
                    <x-filament::button
                        color="gray"
                        icon="heroicon-m-arrow-left"
                        href="{{ route('filament.admin.resources.tenants.index') }}"
                    >
                        Back to Tenants
                    </x-filament::button>
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <div class="text-center">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        No Active Tenant
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        You are currently in the central administration context.
                    </p>
                    <x-filament::button
                        class="mt-4"
                        href="{{ route('filament.admin.resources.tenants.index') }}"
                    >
                        Manage Tenants
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif

        <!-- Tenant Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-filament::card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-m-building-office class="h-8 w-8 text-blue-500" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Total Tenants
                        </h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ \Stancl\Tenancy\Database\Models\Tenant::count() }}
                        </p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-m-globe-alt class="h-8 w-8 text-green-500" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Active Tenants
                        </h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ \Stancl\Tenancy\Database\Models\Tenant::whereNotNull('domain')->count() }}
                        </p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-m-clock class="h-8 w-8 text-yellow-500" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            Recent Tenants
                        </h3>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ \Stancl\Tenancy\Database\Models\Tenant::latest()->take(5)->count() }}
                        </p>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <!-- Quick Actions -->
        <x-filament::section>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-filament::button
                    href="{{ route('filament.admin.resources.tenants.create') }}"
                    icon="heroicon-m-plus"
                    class="w-full justify-start"
                >
                    Create Tenant
                </x-filament::button>

                <x-filament::button
                    href="{{ route('filament.admin.resources.tenants.index') }}"
                    icon="heroicon-m-list-bullet"
                    color="gray"
                    class="w-full justify-start"
                >
                    View All Tenants
                </x-filament::button>

                @if($this->getCurrentTenant())
                    <x-filament::button
                        icon="heroicon-m-arrow-left"
                        color="warning"
                        class="w-full justify-start"
                        wire:click="exitTenant"
                    >
                        Exit Tenant
                    </x-filament::button>
                @endif

                <x-filament::button
                    icon="heroicon-m-cog-6-tooth"
                    color="gray"
                    class="w-full justify-start"
                    href="{{ route('filament.admin.pages.settings') }}"
                >
                    Settings
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page> 