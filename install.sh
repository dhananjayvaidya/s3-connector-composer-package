#!/bin/bash

# S3ConnectorService Installation Script
# This script helps you install the S3ConnectorService into your Laravel project

echo "🚀 S3ConnectorService Installation Script"
echo "========================================"
echo ""

# Check if we're in a Laravel project
if [ ! -f "artisan" ]; then
    echo "❌ Error: This doesn't appear to be a Laravel project."
    echo "   Please run this script from your Laravel project root directory."
    exit 1
fi

echo "✅ Laravel project detected"
echo ""

# Create necessary directories
echo "📁 Creating directories..."
mkdir -p app/Services
mkdir -p config
echo "✅ Directories created"
echo ""

# Copy service files
echo "📋 Copying service files..."
cp S3ConnectorService.php app/Services/
cp config/s3-connector.php config/
echo "✅ Service files copied"
echo ""

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "⚠️  Warning: .env file not found. Please create one and add:"
    echo "   S3_CONNECTOR_API_KEY=your_api_key_here"
    echo "   S3_CONNECTOR_BASE_URL=http://your-s3-connector-domain.com/api"
    echo ""
else
    echo "📝 Adding environment variables to .env..."
    
    # Check if S3_CONNECTOR_API_KEY already exists
    if grep -q "S3_CONNECTOR_API_KEY" .env; then
        echo "   S3_CONNECTOR_API_KEY already exists in .env"
    else
        echo "S3_CONNECTOR_API_KEY=your_api_key_here" >> .env
        echo "   S3_CONNECTOR_API_KEY added to .env"
    fi
    
    # Check if S3_CONNECTOR_BASE_URL already exists
    if grep -q "S3_CONNECTOR_BASE_URL" .env; then
        echo "   S3_CONNECTOR_BASE_URL already exists in .env"
    else
        echo "S3_CONNECTOR_BASE_URL=http://localhost:8000/api" >> .env
        echo "   S3_CONNECTOR_BASE_URL added to .env"
    fi
    
    echo "✅ Environment variables added"
    echo ""
fi

echo "🔧 Next steps:"
echo "1. Edit your .env file and set your actual S3_CONNECTOR_API_KEY"
echo "2. Update S3_CONNECTOR_BASE_URL to point to your S3 Connector instance"
echo "3. Add the service binding to your AppServiceProvider:"
echo ""
echo "   public function register(): void"
echo "   {"
echo "       \$this->app->singleton(\\App\\Services\\S3ConnectorService::class, function (\$app) {"
echo "           return new \\App\\Services\\S3ConnectorService("
echo "               config('s3-connector.base_url'),"
echo "               config('s3-connector.api_key'),"
echo "               config('s3-connector.timeout'),"
echo "               config('s3-connector.enable_logging')"
echo "           );"
echo "       });"
echo "   }"
echo ""
echo "4. Clear your configuration cache:"
echo "   php artisan config:clear"
echo ""
echo "🎉 Installation complete! You can now use S3ConnectorService in your project."
echo ""
echo "📖 For usage examples, see the README.md file"
echo "🔗 For more information, visit the S3 Connector documentation"
