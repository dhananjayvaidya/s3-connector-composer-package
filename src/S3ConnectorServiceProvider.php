<?php

namespace DhananjayVaidya\S3Connector;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

/**
 * S3ConnectorServiceProvider - Laravel service provider for S3ConnectorService
 * 
 * This service provider makes it easy to integrate the S3ConnectorService
 * into any Laravel project.
 * 
 * @package DhananjayVaidya\S3Connector
 * @author Dhananjay Vaidya
 */
class S3ConnectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/s3-connector.php', 's3-connector'
        );

        // Register the main service
        $this->app->singleton(S3ConnectorService::class, function ($app) {
            return new S3ConnectorService(
                config('s3-connector.base_url'),
                config('s3-connector.api_key'),
                config('s3-connector.timeout'),
                config('s3-connector.enable_logging')
            );
        });

        // Register with alias for easier access
        $this->app->alias(S3ConnectorService::class, 's3-connector');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/s3-connector.php' => config_path('s3-connector.php'),
            ], 's3-connector-config');

            // Publish the service class
            $this->publishes([
                __DIR__ . '/S3ConnectorService.php' => app_path('Services/S3ConnectorService.php'),
            ], 's3-connector-service');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [S3ConnectorService::class, 's3-connector'];
    }
}
