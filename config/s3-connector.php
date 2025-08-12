<?php

return [
    /*
    |--------------------------------------------------------------------------
    | S3 Connector Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the S3 Connector service.
    | You can override these values by setting them in your .env file.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL of your S3 Connector API. This should point to your
    | S3 Connector installation.
    |
    */
    'base_url' => env('S3_CONNECTOR_BASE_URL', 'http://localhost:8000/api'),

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Your S3 Connector API key. This is required for authentication.
    | You can get this from your S3 Connector super admin dashboard.
    |
    */
    'api_key' => env('S3_CONNECTOR_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | HTTP request timeout in seconds. Increase this for large file uploads.
    |
    */
    'timeout' => env('S3_CONNECTOR_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Enable Logging
    |--------------------------------------------------------------------------
    |
    | Whether to enable logging of API requests and responses.
    |
    */
    'enable_logging' => env('S3_CONNECTOR_ENABLE_LOGGING', true),

    /*
    |--------------------------------------------------------------------------
    | Default File Visibility
    |--------------------------------------------------------------------------
    |
    | Default visibility for uploaded files. Options: private, public-read,
    | public-read-write, authenticated-read
    |
    */
    'default_visibility' => env('S3_CONNECTOR_DEFAULT_VISIBILITY', 'private'),

    /*
    |--------------------------------------------------------------------------
    | Default Presigned URL Expiration
    |--------------------------------------------------------------------------
    |
    | Default expiration time for presigned URLs in seconds.
    |
    */
    'default_presigned_expiration' => env('S3_CONNECTOR_PRESIGNED_EXPIRATION', 3600),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for retrying failed requests.
    |
    */
    'retry' => [
        'enabled' => env('S3_CONNECTOR_RETRY_ENABLED', true),
        'max_attempts' => env('S3_CONNECTOR_MAX_RETRY_ATTEMPTS', 3),
        'delay' => env('S3_CONNECTOR_RETRY_DELAY', 1000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for caching API responses.
    |
    */
    'cache' => [
        'enabled' => env('S3_CONNECTOR_CACHE_ENABLED', false),
        'ttl' => env('S3_CONNECTOR_CACHE_TTL', 300), // seconds
    ],
];
