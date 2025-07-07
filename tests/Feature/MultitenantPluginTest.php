<?php

namespace MuhammadNawlo\MultitenantPlugin\Tests\Feature;

use MuhammadNawlo\MultitenantPlugin\MultitenantPlugin;
use MuhammadNawlo\MultitenantPlugin\Tests\TestCase;
use Stancl\Tenancy\TenancyManager;

class MultitenantPluginTest extends TestCase
{
    public function test_plugin_can_be_instantiated()
    {
        $tenancyManager = app(TenancyManager::class);
        $plugin = new MultitenantPlugin($tenancyManager);
        
        $this->assertInstanceOf(MultitenantPlugin::class, $plugin);
    }

    public function test_plugin_can_get_tenancy_manager()
    {
        $tenancyManager = app(TenancyManager::class);
        $plugin = new MultitenantPlugin($tenancyManager);
        
        $this->assertInstanceOf(TenancyManager::class, $plugin->getTenancyManager());
    }

    public function test_plugin_can_check_tenant_context()
    {
        $tenancyManager = app(TenancyManager::class);
        $plugin = new MultitenantPlugin($tenancyManager);
        
        // Should be false when no tenant is initialized
        $this->assertFalse($plugin->isTenantContext());
    }

    public function test_plugin_can_get_tenant_data()
    {
        $tenancyManager = app(TenancyManager::class);
        $plugin = new MultitenantPlugin($tenancyManager);
        
        // Should return empty array when no tenant is initialized
        $this->assertEquals([], $plugin->getTenantData());
    }

    public function test_plugin_can_get_tenant_id()
    {
        $tenancyManager = app(TenancyManager::class);
        $plugin = new MultitenantPlugin($tenancyManager);
        
        // Should return null when no tenant is initialized
        $this->assertNull($plugin->getTenantId());
    }
} 