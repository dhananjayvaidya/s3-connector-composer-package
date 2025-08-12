<?php

/**
 * Example Usage of S3ConnectorService
 * 
 * This file demonstrates how to use the S3ConnectorService in your Laravel project.
 * Copy the relevant examples to your controllers or services.
 */

use App\Services\S3ConnectorService;

class ExampleUsage
{
    /**
     * Example 1: Basic file upload
     */
    public function uploadExample(Request $request, S3ConnectorService $s3Service)
    {
        $file = $request->file('document');
        
        $result = $s3Service->upload(
            $file,
            'documents/invoices/',
            [
                'invoice_number' => 'INV-001',
                'customer_id' => '12345',
                'uploaded_by' => auth()->id()
            ],
            'private'
        );

        if ($result['success']) {
            return response()->json([
                'message' => 'File uploaded successfully',
                'file_url' => $result['data']['url'],
                'file_key' => $result['data']['key']
            ]);
        }

        return response()->json([
            'error' => 'Upload failed: ' . $result['error']
        ], 400);
    }

    /**
     * Example 2: File download
     */
    public function downloadExample(Request $request, S3ConnectorService $s3Service)
    {
        $key = $request->input('key');
        
        // Stream download (no local storage)
        $result = $s3Service->download($key);
        
        if ($result['success']) {
            return response()->json([
                'message' => 'Download started',
                'file_info' => $result['data']
            ]);
        }

        return response()->json([
            'error' => 'Download failed: ' . $result['error']
        ], 404);
    }

    /**
     * Example 3: Download and save locally
     */
    public function downloadAndSaveExample(Request $request, S3ConnectorService $s3Service)
    {
        $key = $request->input('key');
        $localPath = 'local/downloads/' . basename($key);
        
        $result = $s3Service->download($key, $localPath);
        
        if ($result['success']) {
            return response()->json([
                'message' => 'File downloaded and saved',
                'local_path' => $result['local_path']
            ]);
        }

        return response()->json([
            'error' => 'Download failed: ' . $result['error']
        ], 400);
    }

    /**
     * Example 4: List files
     */
    public function listFilesExample(Request $request, S3ConnectorService $s3Service)
    {
        $prefix = $request->input('prefix', 'uploads/');
        $maxKeys = $request->input('max_keys', 50);
        
        $result = $s3Service->list($prefix, $maxKeys);
        
        if ($result['success']) {
            return response()->json([
                'files' => $result['data']['files'],
                'total_count' => count($result['data']['files'])
            ]);
        }

        return response()->json([
            'error' => 'Failed to list files: ' . $result['error']
        ], 400);
    }

    /**
     * Example 5: Generate presigned URL
     */
    public function presignedUrlExample(Request $request, S3ConnectorService $s3Service)
    {
        $key = $request->input('key');
        $expiresIn = $request->input('expires_in', 3600); // 1 hour
        
        $result = $s3Service->presignedUrl($key, $expiresIn);
        
        if ($result['success']) {
            return response()->json([
                'presigned_url' => $result['data']['presigned_url'],
                'expires_at' => now()->addSeconds($expiresIn)->toISOString()
            ]);
        }

        return response()->json([
            'error' => 'Failed to generate presigned URL: ' . $result['error']
        ], 400);
    }

    /**
     * Example 6: File operations with error handling
     */
    public function fileOperationsExample(Request $request, S3ConnectorService $s3Service)
    {
        $key = $request->input('key');
        
        // Check if file exists
        $exists = $s3Service->exists($key);
        if (!$exists['success']) {
            return response()->json(['error' => 'File not found'], 404);
        }
        
        // Get file metadata
        $metadata = $s3Service->metadata($key);
        if (!$metadata['success']) {
            return response()->json(['error' => 'Failed to get metadata'], 400);
        }
        
        // Copy file to backup location
        $backupKey = 'backup/' . $key;
        $copyResult = $s3Service->copy($key, $backupKey, [
            'backup_date' => now()->toISOString(),
            'original_key' => $key
        ]);
        
        if (!$copyResult['success']) {
            return response()->json(['error' => 'Backup failed'], 400);
        }
        
        return response()->json([
            'message' => 'File operations completed successfully',
            'original_file' => $metadata['data'],
            'backup_created' => $copyResult['data']
        ]);
    }

    /**
     * Example 7: Health monitoring
     */
    public function healthCheckExample(S3ConnectorService $s3Service)
    {
        // Test connection
        $connection = $s3Service->testConnection();
        if (!$connection['success']) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => $connection['error']
            ], 503);
        }
        
        // Check API health
        $health = $s3Service->health();
        if (!$health['success']) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => $health['error']
            ], 503);
        }
        
        // Check S3 configuration
        $config = $s3Service->configCheck();
        if (!$config['success']) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => $config['error']
            ], 503);
        }
        
        return response()->json([
            'status' => 'healthy',
            'connection' => $connection['data'],
            'health' => $health['data'],
            'configuration' => $config['data']
        ]);
    }

    /**
     * Example 8: Batch operations
     */
    public function batchOperationsExample(Request $request, S3ConnectorService $s3Service)
    {
        $files = $request->input('files', []);
        $results = [];
        
        foreach ($files as $file) {
            $key = $file['key'];
            $operation = $file['operation'];
            
            switch ($operation) {
                case 'upload':
                    // Handle upload
                    $uploadResult = $s3Service->upload(
                        $file['file'],
                        $key,
                        $file['metadata'] ?? [],
                        $file['visibility'] ?? 'private'
                    );
                    $results[] = [
                        'key' => $key,
                        'operation' => $operation,
                        'success' => $uploadResult['success'],
                        'result' => $uploadResult
                    ];
                    break;
                    
                case 'delete':
                    // Handle delete
                    $deleteResult = $s3Service->delete($key);
                    $results[] = [
                        'key' => $key,
                        'operation' => $operation,
                        'success' => $deleteResult['success'],
                        'result' => $deleteResult
                    ];
                    break;
                    
                case 'copy':
                    // Handle copy
                    $copyResult = $s3Service->copy(
                        $key,
                        $file['destination_key'],
                        $file['metadata'] ?? []
                    );
                    $results[] = [
                        'key' => $key,
                        'operation' => $operation,
                        'success' => $copyResult['success'],
                        'result' => $copyResult
                    ];
                    break;
                    
                default:
                    $results[] = [
                        'key' => $key,
                        'operation' => $operation,
                        'success' => false,
                        'error' => 'Unknown operation'
                    ];
            }
        }
        
        $successCount = count(array_filter($results, fn($r) => $r['success']));
        $totalCount = count($results);
        
        return response()->json([
            'message' => "Batch operations completed: {$successCount}/{$totalCount} successful",
            'results' => $results,
            'summary' => [
                'total' => $totalCount,
                'successful' => $successCount,
                'failed' => $totalCount - $successCount
            ]
        ]);
    }

    /**
     * Example 9: Service information
     */
    public function serviceInfoExample(S3ConnectorService $s3Service)
    {
        $info = $s3Service->getServiceInfo();
        
        return response()->json([
            'service_info' => $info,
            'api_key_valid' => $s3Service->validateApiKey(),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Example 10: Cleanup operations
     */
    public function cleanupExample(S3ConnectorService $s3Service)
    {
        // Clean up temporary files
        $cleanupResult = $s3Service->cleanupTemp();
        
        if ($cleanupResult['success']) {
            return response()->json([
                'message' => 'Cleanup completed successfully',
                'files_removed' => $cleanupResult['data']['files_removed']
            ]);
        }
        
        return response()->json([
            'error' => 'Cleanup failed: ' . $cleanupResult['error']
        ], 400);
    }
}
