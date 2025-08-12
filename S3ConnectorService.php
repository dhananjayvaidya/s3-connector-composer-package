<?php

namespace S3ConnectorService;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * S3ConnectorService - Easy integration service for S3 Connector API
 * 
 * This service provides a simple interface to interact with the S3 Connector API
 * from any Laravel project. Just add S3_CONNECTOR_API_KEY to your .env file.
 * 
 * @package S3ConnectorService
 * @version 1.0.0
 */
class S3ConnectorService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;
    protected bool $enableLogging;

    public function __construct(
        ?string $baseUrl = null,
        ?string $apiKey = null,
        int $timeout = 30,
        bool $enableLogging = true
    ) {
        $this->baseUrl = $baseUrl ?? config('s3-connector.base_url', 'http://localhost:8000/api');
        $this->apiKey = $apiKey ?? config('s3-connector.api_key', env('S3_CONNECTOR_API_KEY'));
        $this->timeout = $timeout;
        $this->enableLogging = $enableLogging;

        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException(
                'S3 Connector API key is required. Please set S3_CONNECTOR_API_KEY in your .env file.'
            );
        }
    }

    public function getBaseUrl(): string { return $this->baseUrl; }
    public function setBaseUrl(string $baseUrl): self { $this->baseUrl = $baseUrl; return $this; }
    public function setApiKey(string $apiKey): self { $this->apiKey = $apiKey; return $this; }
    public function setTimeout(int $timeout): self { $this->timeout = $timeout; return $this; }
    public function setLogging(bool $enable): self { $this->enableLogging = $enable; return $this; }

    protected function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        $defaultHeaders = [
            'X-API-Key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = array_merge($defaultHeaders, $headers);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->$method($url, $data);

            if ($this->enableLogging) {
                Log::info('S3 Connector API Request', [
                    'method' => $method, 'url' => $url, 'status' => $response->status()
                ]);
            }

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json('data'),
                    'message' => $response->json('message'),
                    'status_code' => $response->status(),
                    'response' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message') ?? 'Request failed',
                'status_code' => $response->status(),
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            if ($this->enableLogging) {
                Log::error('S3 Connector API Error', [
                    'method' => $method, 'url' => $url, 'error' => $e->getMessage()
                ]);
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 0
            ];
        }
    }

    protected function makeFileRequest(string $method, string $endpoint, array $data = [], array $files = []): array
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
        
        $headers = [
            'X-API-Key' => $this->apiKey,
            'Accept' => 'application/json',
        ];

        try {
            $request = Http::timeout($this->timeout)->withHeaders($headers);

            if (!empty($files)) {
                foreach ($files as $key => $file) {
                    if ($file instanceof UploadedFile) {
                        $request->attach($key, $file->get(), $file->getClientOriginalName());
                    } else {
                        $request->attach($key, $file);
                    }
                }
            }

            $response = $request->$method($url, $data);

            if ($this->enableLogging) {
                Log::info('S3 Connector File API Request', [
                    'method' => $method, 'url' => $url, 'status' => $response->status()
                ]);
            }

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json('data'),
                    'message' => $response->json('message'),
                    'status_code' => $response->status(),
                    'response' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message') ?? 'Request failed',
                'status_code' => $response->status(),
                'response' => $response->json()
            ];

        } catch (\Exception $e) {
            if ($this->enableLogging) {
                Log::error('S3 Connector File API Error', [
                    'method' => $method, 'url' => $url, 'error' => $e->getMessage()
                ]);
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 0
            ];
        }
    }

    // S3 OPERATIONS
    public function upload($file, string $path, array $metadata = [], string $visibility = 'private'): array
    {
        $data = ['path' => $path, 'visibility' => $visibility];
        if (!empty($metadata)) $data['metadata'] = $metadata;
        $files = ['file' => $file];
        return $this->makeFileRequest('post', 's3/upload', $data, $files);
    }

    public function download(string $key, string $localPath = null): array
    {
        $data = ['key' => $key];
        $response = $this->makeRequest('post', 's3/download', $data);
        
        if ($response['success'] && $localPath) {
            $fileContent = $this->downloadFileContent($key);
            if ($fileContent['success']) {
                Storage::put($localPath, $fileContent['data']);
                return [
                    'success' => true,
                    'message' => 'File downloaded and saved successfully',
                    'local_path' => $localPath,
                    'data' => $response['data']
                ];
            }
        }
        return $response;
    }

    public function downloadFileContent(string $key): array
    {
        $url = rtrim($this->baseUrl, '/') . '/s3/download';
        
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'X-API-Key' => $this->apiKey,
                    'Accept' => '*/*',
                ])
                ->post($url, ['key' => $key]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->body(),
                    'content_type' => $response->header('Content-Type'),
                    'content_length' => $response->header('Content-Length')
                ];
            }

            return [
                'success' => false,
                'error' => 'Download failed',
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 0
            ];
        }
    }

    public function delete(string $key): array
    {
        return $this->makeRequest('delete', 's3/delete', ['key' => $key]);
    }

    public function list(string $prefix = '', int $maxKeys = 1000): array
    {
        $params = [];
        if ($prefix) $params['prefix'] = $prefix;
        if ($maxKeys !== 1000) $params['max_keys'] = $maxKeys;
        return $this->makeRequest('get', 's3/list', $params);
    }

    public function metadata(string $key): array
    {
        return $this->makeRequest('get', 's3/metadata', ['key' => $key]);
    }

    public function exists(string $key): array
    {
        return $this->makeRequest('get', 's3/exists', ['key' => $key]);
    }

    public function copy(string $sourceKey, string $destinationKey, array $metadata = []): array
    {
        $data = ['source_key' => $sourceKey, 'destination_key' => $destinationKey];
        if (!empty($metadata)) $data['metadata'] = $metadata;
        return $this->makeRequest('post', 's3/copy', $data);
    }

    public function presignedUrl(string $key, int $expiresIn = 3600, string $operation = 'getObject'): array
    {
        $data = ['key' => $key, 'expires_in' => $expiresIn, 'operation' => $operation];
        return $this->makeRequest('get', 's3/presigned-url', $data);
    }

    // SYSTEM OPERATIONS
    public function health(): array
    {
        return $this->makeRequest('get', 's3/health');
    }

    public function configCheck(): array
    {
        return $this->makeRequest('get', 's3/config-check');
    }

    public function bucketInfo(): array
    {
        return $this->makeRequest('get', 's3/bucket-info');
    }

    public function cleanupTemp(): array
    {
        return $this->makeRequest('post', 's3/cleanup-temp');
    }

    // UTILITY METHODS
    public function testConnection(): array
    {
        $health = $this->health();
        
        if ($health['success']) {
            return [
                'success' => true,
                'message' => 'Successfully connected to S3 Connector',
                'base_url' => $this->baseUrl,
                'api_key' => substr($this->apiKey, 0, 10) . '...',
                'health_status' => $health['data']['bucket_status'] ?? 'unknown'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to connect to S3 Connector',
            'base_url' => $this->baseUrl,
            'error' => $health['error'] ?? 'Unknown error'
        ];
    }

    public function getServiceInfo(): array
    {
        return [
            'service_name' => 'S3 Connector Service',
            'version' => '1.0.0',
            'base_url' => $this->baseUrl,
            'api_key_prefix' => substr($this->apiKey, 0, 10) . '...',
            'timeout' => $this->timeout,
            'logging_enabled' => $this->enableLogging,
            'supported_operations' => [
                'upload', 'download', 'delete', 'list', 'metadata',
                'exists', 'copy', 'presigned_url', 'health', 'config_check',
                'bucket_info', 'cleanup_temp'
            ]
        ];
    }

    public function validateApiKey(): bool
    {
        return !empty($this->apiKey) && strlen($this->apiKey) >= 10;
    }
}
