<?php

namespace DhananjayVaidya\S3Connector\Tests;

use Orchestra\Testbench\TestCase;
use DhananjayVaidya\S3Connector\S3ConnectorService;
use DhananjayVaidya\S3Connector\S3ConnectorServiceProvider;

class S3ConnectorServiceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            S3ConnectorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('s3-connector.api_key', 'test_api_key_12345');
        $app['config']->set('s3-connector.base_url', 'http://test.local/api');
        $app['config']->set('s3-connector.timeout', 30);
        $app['config']->set('s3-connector.enable_logging', false);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new S3ConnectorService();
        $this->assertInstanceOf(S3ConnectorService::class, $service);
    }

    public function test_service_can_be_resolved_from_container()
    {
        $service = $this->app->make(S3ConnectorService::class);
        $this->assertInstanceOf(S3ConnectorService::class, $service);
    }

    public function test_service_can_be_resolved_via_alias()
    {
        $service = $this->app->make('s3-connector');
        $this->assertInstanceOf(S3ConnectorService::class, $service);
    }

    public function test_api_key_validation()
    {
        $service = new S3ConnectorService();
        $this->assertTrue($service->validateApiKey());
    }

    public function test_service_info()
    {
        $service = new S3ConnectorService();
        $info = $service->getServiceInfo();
        
        $this->assertArrayHasKey('service_name', $info);
        $this->assertArrayHasKey('version', $info);
        $this->assertArrayHasKey('supported_operations', $info);
        $this->assertEquals('S3 Connector Service', $info['service_name']);
        $this->assertEquals('1.0.0', $info['version']);
    }

    public function test_setter_methods()
    {
        $service = new S3ConnectorService();
        
        $service->setBaseUrl('http://new-url.com/api');
        $this->assertEquals('http://new-url.com/api', $service->getBaseUrl());
        
        $service->setTimeout(60);
        $service->setLogging(false);
        
        $info = $service->getServiceInfo();
        $this->assertEquals(60, $info['timeout']);
        $this->assertFalse($info['logging_enabled']);
    }
}
