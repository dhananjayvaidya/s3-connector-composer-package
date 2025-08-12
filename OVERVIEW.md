# S3ConnectorService - Overview

## What is S3ConnectorService?

S3ConnectorService is a lightweight, easy-to-integrate Laravel service that provides a simple interface to interact with the S3 Connector API. It allows other Laravel projects to use S3 storage functionality without directly integrating with AWS SDK or managing complex S3 configurations.

## Why Use S3ConnectorService?

### 🚀 **Easy Integration**
- Copy and paste installation - no Composer dependencies
- Simple configuration with environment variables
- Works with any Laravel project (Laravel 8+)

### 🔐 **Secure & Simple**
- API key-based authentication
- No need to manage AWS credentials in multiple projects
- Centralized S3 management through S3 Connector

### 📁 **Complete S3 Operations**
- File upload, download, delete, list, copy
- Metadata management
- Presigned URL generation
- Health monitoring and configuration checks

### 🛠️ **Developer Friendly**
- Consistent response format
- Comprehensive error handling
- Built-in logging and debugging
- Extensive examples and documentation

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Other Laravel Projects                  │
├─────────────────────────────────────────────────────────────┤
│  S3ConnectorService                                        │
│  ├── HTTP Client (Guzzle)                                  │
│  ├── Authentication (API Key)                              │
│  ├── Error Handling                                        │
│  └── Response Processing                                   │
├─────────────────────────────────────────────────────────────┤
│                    S3 Connector API                        │
│  ├── S3 Operations                                         │
│  ├── File Management                                       │
│  ├── Security & Rate Limiting                              │
│  └── Monitoring & Analytics                                │
├─────────────────────────────────────────────────────────────┤
│                    AWS S3 Storage                          │
│  ├── File Storage                                          │
│  ├── Access Control                                        │
│  └── Scalability                                           │
└─────────────────────────────────────────────────────────────┘
```

## File Structure

```
third-party/S3ConnectorService/
├── S3ConnectorService.php           # Main service class
├── S3ConnectorServiceProvider.php   # Laravel service provider
├── config/
│   └── s3-connector.php            # Configuration file
├── examples/
│   └── ExampleUsage.php            # Usage examples
├── install.sh                      # Installation script
├── README.md                       # Comprehensive documentation
└── OVERVIEW.md                     # This file
```

## Key Components

### 1. **S3ConnectorService.php** - Main Service Class
- **HTTP Communication**: Uses Laravel's HTTP client for API calls
- **Authentication**: Handles API key authentication automatically
- **Error Handling**: Consistent error response format
- **Logging**: Built-in request/response logging
- **Configuration**: Flexible configuration options

### 2. **S3ConnectorServiceProvider.php** - Laravel Integration
- **Service Registration**: Registers the service in Laravel's container
- **Configuration Publishing**: Makes config files available for customization
- **Alias Registration**: Provides easy access via service alias

### 3. **Configuration System**
- **Environment Variables**: Easy configuration via .env file
- **Default Values**: Sensible defaults for all settings
- **Flexible Overrides**: Override any setting as needed

## Installation Options

### Option 1: Manual Installation (Simple)
```bash
# Copy files manually
cp S3ConnectorService.php app/Services/
cp config/s3-connector.php config/

# Add to .env
S3_CONNECTOR_API_KEY=your_key_here
S3_CONNECTOR_BASE_URL=http://your-domain.com/api
```

### Option 2: Automated Installation
```bash
# Run the installation script
./install.sh
```

### Option 3: Service Provider (Advanced)
```bash
# Copy service provider
cp S3ConnectorServiceProvider.php app/Providers/

# Register in config/app.php
'providers' => [
    App\Providers\S3ConnectorServiceProvider::class,
]
```

## Usage Patterns

### 1. **Dependency Injection**
```php
public function upload(Request $request, S3ConnectorService $s3Service)
{
    $result = $s3Service->upload($request->file('file'), 'uploads/');
    // Handle result...
}
```

### 2. **Service Container**
```php
$s3Service = app(S3ConnectorService::class);
$result = $s3Service->upload($file, 'uploads/');
```

### 3. **Facade (if registered)**
```php
use S3ConnectorService;

$result = S3ConnectorService::upload($file, 'uploads/');
```

## Response Format

All methods return a consistent response format:

```php
// Success
[
    'success' => true,
    'data' => [...],
    'message' => 'Operation completed',
    'status_code' => 200
]

// Error
[
    'success' => false,
    'error' => 'Error message',
    'status_code' => 400
]
```

## Supported Operations

### File Operations
- ✅ **Upload**: File upload with metadata and visibility
- ✅ **Download**: Stream download or save locally
- ✅ **Delete**: Remove files from S3
- ✅ **List**: Browse files with pagination
- ✅ **Metadata**: Get file information
- ✅ **Exists**: Check file existence
- ✅ **Copy**: Duplicate files within S3
- ✅ **Presigned URL**: Generate temporary access URLs

### System Operations
- ✅ **Health Check**: API status monitoring
- ✅ **Configuration Check**: S3 setup validation
- ✅ **Bucket Info**: Storage information
- ✅ **Cleanup**: Temporary file management

### Utility Operations
- ✅ **Connection Test**: Verify API connectivity
- ✅ **Service Info**: Get service details
- ✅ **API Key Validation**: Verify key format

## Configuration Options

| Setting | Environment Variable | Default | Description |
|---------|---------------------|---------|-------------|
| Base URL | `S3_CONNECTOR_BASE_URL` | `http://localhost:8000/api` | API endpoint |
| API Key | `S3_CONNECTOR_API_KEY` | Required | Authentication key |
| Timeout | `S3_CONNECTOR_TIMEOUT` | `30` | Request timeout (seconds) |
| Logging | `S3_CONNECTOR_ENABLE_LOGGING` | `true` | Enable/disable logging |
| Visibility | `S3_CONNECTOR_DEFAULT_VISIBILITY` | `private` | Default file visibility |
| Presigned Expiration | `S3_CONNECTOR_PRESIGNED_EXPIRATION` | `3600` | URL expiration (seconds) |

## Security Features

- **API Key Authentication**: Secure access control
- **Environment Variables**: No hardcoded credentials
- **Input Validation**: Built-in request validation
- **Error Logging**: Comprehensive security logging
- **Rate Limiting**: Respects API rate limits

## Performance Features

- **HTTP Keep-Alive**: Efficient connection reuse
- **Configurable Timeouts**: Adjustable for different use cases
- **Chunked Downloads**: Memory-efficient file handling
- **Response Caching**: Optional response caching
- **Retry Logic**: Automatic retry for failed requests

## Monitoring & Debugging

- **Health Checks**: Regular API status monitoring
- **Request Logging**: Track all API interactions
- **Error Tracking**: Detailed error information
- **Performance Metrics**: Response time monitoring
- **Connection Testing**: Verify API connectivity

## Use Cases

### 1. **Content Management Systems**
- File uploads and downloads
- Image and document storage
- Media file management

### 2. **E-commerce Applications**
- Product image storage
- Document management
- Backup and archival

### 3. **Document Management**
- File storage and retrieval
- Version control
- Access management

### 4. **Media Applications**
- Video and audio storage
- Image processing
- Content delivery

### 5. **Backup Systems**
- Data archival
- Disaster recovery
- Long-term storage

## Benefits

### For Developers
- **Faster Development**: No need to learn AWS SDK
- **Simpler Code**: Clean, intuitive API
- **Better Testing**: Easy to mock and test
- **Consistent Interface**: Same pattern across operations

### For Projects
- **Reduced Complexity**: Centralized S3 management
- **Better Security**: Centralized access control
- **Easier Maintenance**: Single point of configuration
- **Cost Control**: Centralized usage monitoring

### For Organizations
- **Standardization**: Consistent S3 usage patterns
- **Security**: Centralized credential management
- **Compliance**: Centralized audit trails
- **Scalability**: Easy to scale across projects

## Getting Started

1. **Download the service files**
2. **Run the installation script**: `./install.sh`
3. **Configure your API key** in `.env`
4. **Add service binding** to your provider
5. **Start using** the service in your controllers

## Support & Documentation

- **README.md**: Comprehensive usage guide
- **Examples**: Real-world usage examples
- **Configuration**: Detailed configuration options
- **Troubleshooting**: Common issues and solutions

## Future Enhancements

- **Composer Package**: Official package distribution
- **Artisan Commands**: CLI tools for management
- **Queue Integration**: Background job processing
- **Cache Integration**: Response caching system
- **Metrics Collection**: Usage analytics and monitoring

---

**S3ConnectorService** makes S3 storage integration simple, secure, and scalable for any Laravel project. 🚀
