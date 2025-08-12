<?php

namespace DhananjayVaidya\S3Connector\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * S3Connector Facade
 * 
 * This facade provides easy access to the S3ConnectorService.
 * 
 * @package DhananjayVaidya\S3Connector\Facades
 * @author Dhananjay Vaidya
 * 
 * @method static string getBaseUrl()
 * @method static self setBaseUrl(string $baseUrl)
 * @method static self setApiKey(string $apiKey)
 * @method static self setTimeout(int $timeout)
 * @method static self setLogging(bool $enable)
 * @method static array upload($file, string $path, array $metadata = [], string $visibility = 'private')
 * @method static array download(string $key, string $localPath = null)
 * @method static array downloadFileContent(string $key)
 * @method static array delete(string $key)
 * @method static array list(string $prefix = '', int $maxKeys = 1000)
 * @method static array metadata(string $key)
 * @method static array exists(string $key)
 * @method static array copy(string $sourceKey, string $destinationKey, array $metadata = [])
 * @method static array presignedUrl(string $key, int $expiresIn = 3600, string $operation = 'getObject')
 * @method static array health()
 * @method static array configCheck()
 * @method static array bucketInfo()
 * @method static array cleanupTemp()
 * @method static array testConnection()
 * @method static array getServiceInfo()
 * @method static bool validateApiKey()
 */
class S3Connector extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 's3-connector';
    }
}
