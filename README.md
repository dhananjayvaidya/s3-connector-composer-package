# S3 Connector

A lightweight Laravel package for easy integration with the S3 Connector API. This package provides a simple interface to interact with S3 storage through the S3 Connector API without the need for direct AWS SDK integration.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dhananjay-vaidya/s3-connector.svg)](https://packagist.org/packages/dhananjay-vaidya/s3-connector)
[![Total Downloads on Packagist](https://img.shields.io/packagist/dt/dhananjay-vaidya/s3-connector.svg)](https://packagist.org/packages/dhananjay-vaidya/s3-connector)
[![License](https://img.shields.io/packagist/l/dhananjay-vaidya/s3-connector.svg)](https://packagist.org/packages/dhananjay-vaidya/s3-connector)

## Features

- 🚀 **Simple Integration** - Easy to add to any Laravel project via Composer
- 🔐 **API Key Authentication** - Secure access using API keys
- 📁 **Complete S3 Operations** - Upload, download, delete, list, copy, and more
- 🔗 **Presigned URLs** - Generate temporary access URLs for files
- 📊 **Health Monitoring** - Check API status and configuration
- 🧹 **Cleanup Tools** - Manage temporary files and storage
- 📝 **Comprehensive Logging** - Track all API requests and responses
- ⚡ **Configurable** - Customize timeouts, logging, and other settings

## Installation

### Via Composer (Recommended)

```bash
composer require dhananjay-vaidya/s3-connector
```

### Manual Installation

If you prefer manual installation:

```bash
# Download the package
git clone https://github.com/dhananjay-vaidya/s3-connector.git

# Copy files to your project
cp -r s3-connector/src/* app/Services/
cp s3-connector/config/s3-connector.php config/
```

## Configuration

### 1. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=s3-connector-config
```

### 2. Add Environment Variables

Add these to your `.env` file:

```env
# Required: Your S3 Connector API key
S3_CONNECTOR_API_KEY=sk_your_api_key_here

# Optional: Base URL of your S3 Connector API
S3_CONNECTOR_BASE_URL=http://localhost:8000/api

# Optional: Request timeout in seconds
S3_CONNECTOR_TIMEOUT=30

# Optional: Enable/disable logging
S3_CONNECTOR_ENABLE_LOGGING=true

# Optional: Default file visibility
S3_CONNECTOR_DEFAULT_VISIBILITY=private

# Optional: Default presigned URL expiration
S3_CONNECTOR_PRESIGNED_EXPIRATION=3600
```

### 3. Service Provider (Auto-registered)

The package automatically registers the service provider. If you need manual registration, add this to `config/app.php`:

```php
'providers' => [
    // ... other providers
    DhananjayVaidya\S3Connector\S3ConnectorServiceProvider::class,
],
```

## Usage

### Using Dependency Injection

```php
use DhananjayVaidya\S3Connector\S3ConnectorService;

class FileController extends Controller
{
    protected $s3Service;

    public function __construct(S3ConnectorService $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    public function upload(Request $request)
    {
        $file = $request->file('file');
        $result = $this->s3Service->upload($file, 'uploads/documents/');

        if ($result['success']) {
            return response()->json([
                'message' => 'File uploaded successfully',
                'data' => $result['data']
            ]);
        }

        return response()->json([
            'error' => $result['error']
        ], 400);
    }
}
```

### Using the Facade

```php
use DhananjayVaidya\S3Connector\Facades\S3Connector;

// Upload a file
$result = S3Connector::upload($file, 'uploads/');

// Download a file
$result = S3Connector::download('uploads/file.pdf');

// List files
$files = S3Connector::list('uploads/', 100);

// Get file metadata
$metadata = S3Connector::metadata('uploads/file.pdf');
```

### Using the Service Container

```php
$s3Service = app('s3-connector');
$result = $s3Service->upload($file, 'uploads/');
```

## Available Methods

### File Operations

| Method | Description | Parameters |
|--------|-------------|------------|
| `upload()` | Upload a file to S3 | `$file`, `$path`, `$metadata`, `$visibility` |
| `download()` | Download a file from S3 | `$key`, `$localPath` |
| `delete()` | Delete a file from S3 | `$key` |
| `list()` | List files in S3 bucket | `$prefix`, `$maxKeys` |
| `metadata()` | Get file metadata | `$key` |
| `exists()` | Check if file exists | `$key` |
| `copy()` | Copy a file in S3 | `$sourceKey`, `$destinationKey`, `$metadata` |
| `presignedUrl()` | Generate presigned URL | `$key`, `$expiresIn`, `$operation` |

### System Operations

| Method | Description | Parameters |
|--------|-------------|------------|
| `health()` | Check API health | None |
| `configCheck()` | Check S3 configuration | None |
| `bucketInfo()` | Get bucket information | None |
| `cleanupTemp()` | Clean up temporary files | None |

### Utility Methods

| Method | Description | Parameters |
|--------|-------------|------------|
| `testConnection()` | Test API connection | None |
| `getServiceInfo()` | Get service information | None |
| `validateApiKey()` | Validate API key format | None |

## Examples

### Upload with Metadata

```php
$result = $s3Service->upload(
    $request->file('document'),
    'documents/invoices/',
    [
        'invoice_number' => 'INV-001',
        'customer_id' => '12345',
        'uploaded_by' => auth()->id()
    ],
    'private'
);
```

### Download and Save Locally

```php
$result = $s3Service->download(
    'documents/invoices/invoice.pdf',
    'local/invoices/invoice.pdf'
);

if ($result['success']) {
    echo "File saved to: " . $result['local_path'];
}
```

### Generate Presigned URL

```php
$result = $s3Service->presignedUrl(
    'documents/report.pdf',
    7200, // 2 hours
    'getObject'
);

if ($result['success']) {
    $downloadUrl = $result['data']['presigned_url'];
    // Use $downloadUrl for temporary file access
}
```

### List Files with Pagination

```php
$result = $s3Service->list('uploads/images/', 50);

if ($result['success']) {
    foreach ($result['data']['files'] as $file) {
        echo "File: " . $file['key'] . " (Size: " . $file['size'] . ")\n";
    }
}
```

### Health Check

```php
$health = $s3Service->health();

if ($health['success']) {
    echo "S3 Connector is healthy!\n";
    echo "Bucket status: " . $health['data']['bucket_status'] . "\n";
} else {
    echo "Health check failed: " . $health['error'] . "\n";
}
```

## Response Format

All methods return a consistent response format:

```php
// Success response
[
    'success' => true,
    'data' => [...],
    'message' => 'Operation completed successfully',
    'status_code' => 200
]

// Error response
[
    'success' => false,
    'error' => 'Error message',
    'status_code' => 400
]
```

### Example Error Handling

```php
$result = $s3Service->upload($file, 'uploads/');

if (!$result['success']) {
    Log::error('S3 upload failed', [
        'error' => $result['error'],
        'status_code' => $result['status_code']
    ]);

    return response()->json([
        'error' => 'Upload failed: ' . $result['error']
    ], 400);
}

// Handle success
return response()->json([
    'message' => 'File uploaded successfully',
    'file_url' => $result['data']['url']
]);
```

## Testing

### Run Tests

```bash
composer test
```

### Test Configuration

The package includes PHPUnit configuration and test suite setup. Tests use Orchestra Testbench for Laravel package testing.

## Logging

The service automatically logs all API requests and responses when logging is enabled:

```php
// In your .env file
S3_CONNECTOR_ENABLE_LOGGING=true
```

Log entries include:
- Request method and URL
- Response status and size
- Error details (if any)
- File operation details

## Troubleshooting

### Common Issues

1. **API Key Error**
   ```
   S3 Connector API key is required. Please set S3_CONNECTOR_API_KEY in your .env file.
   ```
   **Solution**: Add your API key to the `.env` file

2. **Connection Timeout**
   ```
   Request failed: cURL error 28: Operation timed out
   ```
   **Solution**: Increase timeout in configuration or check network connectivity

3. **Authentication Failed**
   ```
   Unauthorized: Invalid API key
   ```
   **Solution**: Verify your API key is correct and has proper permissions

4. **File Not Found**
   ```
   Download failed: File not found
   ```
   **Solution**: Check the file key/path exists in your S3 bucket

### Debug Mode

Enable detailed logging for debugging:

```php
$s3Service->setLogging(true);
$s3Service->setTimeout(60); // Increase timeout for large files
```

## Security Considerations

- **API Key Security**: Keep your API key secure and never commit it to version control
- **File Permissions**: Use appropriate file visibility settings for your use case
- **Input Validation**: Always validate file inputs before uploading
- **Rate Limiting**: Be aware of API rate limits for your S3 Connector instance

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

```bash
# Clone the repository
git clone https://github.com/dhananjay-vaidya/s3-connector.git

# Install dependencies
composer install

# Run tests
composer test

# Run PHPStan analysis
composer analyse
```

## Support

For issues and questions:

1. Check the [documentation](https://github.com/dhananjay-vaidya/s3-connector/blob/main/README.md)
2. Review the error logs
3. Verify your configuration settings
4. Test the connection using the `testConnection()` method
5. [Create an issue](https://github.com/dhananjay-vaidya/s3-connector/issues) on GitHub

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for more information on what has changed recently.

## Credits

- **Dhananjay Vaidya** - *Initial work* - [dhananjay-vaidya](https://github.com/dhananjay-vaidya)

---

**S3 Connector** makes S3 storage integration simple, secure, and scalable for any Laravel project. 🚀
