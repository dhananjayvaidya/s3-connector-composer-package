# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Complete authentication header update across all methods for consistent X-API-Key usage

## [1.1.1] - 2024-12-19

### Changed
- **Authentication**: Updated all authentication headers from `Authorization: Bearer` to `X-API-Key` for complete API key compatibility
- **Consistency**: All HTTP methods now use the same `X-API-Key` header format
- **Methods Updated**: `makeRequest()`, `makeFileRequest()`, and `downloadFileContent()` all use consistent authentication

## [1.1.0] - 2024-12-19

### Changed
- **Authentication**: Updated authentication header from `Authorization: Bearer` to `X-API-Key` for improved API key compatibility
- **Header Format**: Changed from `Authorization: Bearer {api_key}` to `X-API-Key: {api_key}`

### Added
- Initial package structure
- S3ConnectorService class with complete S3 operations
- Laravel service provider for easy integration
- Facade for simplified access
- Comprehensive configuration system
- PHPUnit test suite setup
- MIT license

## [1.0.0] - 2024-12-19

### Added
- **S3ConnectorService**: Main service class for S3 operations
  - File upload with metadata and visibility control
  - File download (streaming and local save)
  - File deletion
  - File listing with pagination
  - File metadata retrieval
  - File existence checking
  - File copying within S3
  - Presigned URL generation
  - Health monitoring
  - Configuration validation
  - Bucket information
  - Temporary file cleanup
  - Connection testing
  - Service information
  - API key validation

- **S3ConnectorServiceProvider**: Laravel service provider
  - Automatic service registration
  - Configuration publishing
  - Service alias registration

- **S3Connector Facade**: Easy access facade
  - Static method access to all service methods
  - IDE autocomplete support
  - Method documentation

- **Configuration System**:
  - Environment variable support
  - Sensible defaults
  - Flexible overrides
  - Comprehensive documentation

- **Documentation**:
  - Comprehensive README with examples
  - Architecture overview
  - Installation instructions
  - Usage patterns
  - Troubleshooting guide

### Features
- **Authentication**: API key-based authentication
- **Logging**: Built-in request/response logging
- **Error Handling**: Consistent error response format
- **HTTP Client**: Laravel HTTP client integration
- **File Operations**: Complete S3 file management
- **Health Monitoring**: API status and configuration checks
- **Flexible Configuration**: Multiple configuration options
- **Laravel Integration**: Native Laravel service integration

### Technical Details
- **PHP Version**: 8.0+
- **Laravel Version**: 8.0+
- **Dependencies**: Laravel Framework, Guzzle HTTP Client
- **License**: MIT
- **Namespace**: `DhananjayVaidya\S3Connector`
