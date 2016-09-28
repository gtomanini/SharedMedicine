<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration,
    Doctrine\ORM,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\OXM;
 
define('DEBUGGING', false);
/**
 * Doctrine
 *
 * @category Libraries
 * @package  CodeIgniter
 * @author   Tariqul Islam <tareq@webkutir.net>
 * @license  http://directory.fsf.org/wiki/License:ReciprocalPLv1.3 Reciprocal Public License v1.3
 * @link     http://webkutir.net
 */
class Doctrine
{
    public $em = null;
    /**
     * Class Constructor
     */
    public function __construct()
    {
        // load database configuration and custom config from CodeIgniter
        include APPPATH . 'config/database.php';
        // Set up class loading.
        include_once APPPATH . 'third_party/Doctrine/Common/ClassLoader.php';
        $doctrineClassLoader = new \Doctrine\Common\ClassLoader('Doctrine', APPPATH . 'third_party');
        $doctrineClassLoader->register();
        $entitiesClassLoader = new \Doctrine\Common\ClassLoader('entities', APPPATH . 'models/doctrine');
        $entitiesClassLoader->register();
        $proxiesClassLoader = new \Doctrine\Common\ClassLoader('proxies', APPPATH . 'cache/doctrine');
        $proxiesClassLoader->register();
        $symfonyClassLoader = new \Doctrine\Common\ClassLoader('Symfony', APPPATH . 'third_party/Doctrine');
        $symfonyClassLoader->register();
        // Choose caching method based on application mode (ENVIRONMENT is defined in /index.php)
        if (ENVIRONMENT == 'development' || ENVIRONMENT == 'testing') {
            $cache = new \Doctrine\Common\Cache\ArrayCache;
        } else {
            $cache = new \Doctrine\Common\Cache\ApcCache;
        }
        // Set some configuration options
        $config = new Configuration;
        // Metadata driver
        $driverImpl = $config->newDefaultAnnotationDriver(APPPATH . 'models/doctrine/entities');
        $config->setMetadataDriverImpl($driverImpl);
        // Caching
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        // Proxies
        $config->setProxyDir(APPPATH . 'cache/doctrine/proxies');
        $config->setProxyNamespace('Proxies');
        if (ENVIRONMENT == 'development') {
            $config->setAutoGenerateProxyClasses(true);
        } else {
            $config->setAutoGenerateProxyClasses(false);
        }
        // SQL query logger
        if (DEBUGGING) {
            $logger = new \Doctrine\DBAL\Logging\EchoSQLLogger;
            $config->setSQLLogger($logger);
        }
        // Database connection information
        if (ENVIRONMENT == 'testing') {
            $active_group = 'default_test';
        }
        $connectionOptions = array(
            'driver'   => 'pdo_mysql',
            'user'     => $db[$active_group]['username'],
            'password' => $db[$active_group]['password'],
            'host'     => $db[$active_group]['hostname'],
            'dbname'   => $db[$active_group]['database']
        );
        // Create EntityManager
        $this->em = EntityManager::create($connectionOptions, $config);
    }
}