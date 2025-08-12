<?php
/**
 * Simple test script for S3ConnectorService
 * Run this to verify the service is working correctly
 */

require_once 'S3ConnectorService.php';

// Test configuration
$config = [
    'base_url' => 'http://localhost:8000/api',
    'api_key' => 'sk_test_key_here', // Replace with your actual API key
    'timeout' => 30,
    'enable_logging' => true
];

echo "🧪 Testing S3ConnectorService\n";
echo "=============================\n\n";

try {
    // Create service instance
    $service = new S3ConnectorService(
        $config['base_url'],
        $config['api_key'],
        $config['timeout'],
        $config['enable_logging']
    );

    echo "✅ Service created successfully\n";
    echo "Base URL: {$config['base_url']}\n";
    echo "API Key: " . substr($config['api_key'], 0, 10) . "...\n";
    echo "Timeout: {$config['timeout']}s\n";
    echo "Logging: " . ($config['enable_logging'] ? 'Enabled' : 'Disabled') . "\n\n";

    // Test service info
    echo "📋 Service Information:\n";
    $info = $service->getServiceInfo();
    foreach ($info as $key => $value) {
        if (is_array($value)) {
            echo "  {$key}: " . implode(', ', $value) . "\n";
        } else {
            echo "  {$key}: {$value}\n";
        }
    }
    echo "\n";

    // Test API key validation
    echo "🔑 API Key Validation:\n";
    $isValid = $service->validateApiKey();
    echo "  Valid: " . ($isValid ? 'Yes' : 'No') . "\n\n";

    // Test connection (this will fail if API key is invalid or service is not running)
    echo "🔌 Testing Connection:\n";
    $connection = $service->testConnection();
    
    if ($connection['success']) {
        echo "  ✅ Connection successful!\n";
        echo "  Message: {$connection['message']}\n";
        echo "  Health Status: {$connection['health_status']}\n";
    } else {
        echo "  ❌ Connection failed\n";
        echo "  Error: {$connection['error']}\n";
        echo "  Note: This is expected if the S3 Connector is not running\n";
    }
    echo "\n";

    // Test health check
    echo "🏥 Health Check:\n";
    $health = $service->health();
    
    if ($health['success']) {
        echo "  ✅ Health check passed\n";
        echo "  Message: {$health['message']}\n";
        echo "  Bucket Status: {$health['data']['bucket_status']}\n";
    } else {
        echo "  ❌ Health check failed\n";
        echo "  Error: {$health['error']}\n";
    }
    echo "\n";

    echo "🎉 Service test completed!\n";
    echo "\n";
    echo "📝 Next steps:\n";
    echo "1. Make sure your S3 Connector is running\n";
    echo "2. Update the API key in this test script\n";
    echo "3. Run the test again to verify connectivity\n";
    echo "4. Copy the service files to your Laravel project\n";
    echo "5. Use the service in your controllers\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";
echo "📖 For more information, see README.md\n";
echo "🔗 For examples, see examples/ExampleUsage.php\n";
