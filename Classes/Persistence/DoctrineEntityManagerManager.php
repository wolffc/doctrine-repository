<?php
/**
 * Created by PhpStorm.
 * User: cwolff
 * Date: 10.12.2015
 * Time: 08:28
 */

namespace Wolffc\DocrineRepository\Persistence;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ChainCache;
use Doctrine\Common\Proxy\AbstractProxyFactory;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Wolffc\DocrineRepository\Cache\DoctrineTypo3Cache;

/**
 * Class DoctrineEntityManagerManager
 *
 * This Provides an EntityManger Manager which Makes shure there is Only one EntityManager for a Given Database
 *
 * @package Wolffc\DocrineRepository\Persistence
 */
class DoctrineEntityManagerManager implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * @var array
     */
    protected $entityManagers = [];

    /**
     * @var array
     */
    protected $connectionConfiguration = [];


    /**
     * @param string $databaseIdentifier
     *
     * @return EntityManager
     * @throws \Exception
     */
    public function getEntityManagerForDatabase($databaseIdentifier)
    {
        if (!array_key_exists($databaseIdentifier, $this->entityManagers)) {
            $this->createEntityManger($databaseIdentifier);
        }
        return $this->entityManagers[$databaseIdentifier];
    }

    /**
     * Set the EntityManager Database Configuration
     *
     * @param string $databaseIdentifier
     * @param array  $config
     */
    public function setConnectionConfiguration($databaseIdentifier, array $config)
    {
        $this->connectionConfiguration[$databaseIdentifier] = $config;
    }

    /**
     * Gets The Database Parameters for a Given Database
     *
     * @param string $databaseIdentifier
     *
     * @return mixed
     */
    public function getConnectionParameters($databaseIdentifier)
    {
        if (array_key_exists($databaseIdentifier, $this->connectionConfiguration)
            && is_array($this->connectionConfiguration[$databaseIdentifier])
        ) {
            return $this->connectionConfiguration[$databaseIdentifier];
        }
        throw new \Exception('No database connection parameters set.', 1449708970484);
    }

    /**
     * @param $databaseIdentifier
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    protected function createEntityManger($databaseIdentifier)
    {
        $connection = $this->getConnectionParameters($databaseIdentifier);
        $config = $this->getDoctrineConfiguration();
        $this->entityManagers[$databaseIdentifier] = EntityManager::create($connection, $config);
    }


    /**
     * Creates The Doctrine Cache Directory and Returns its Final Name
     *
     * @return string
     */
    protected function createDoctrineProxyDirectory()
    {
        $cacheDirectory = PATH_site . 'typo3temp/Doctrine/';
        if (!is_dir($cacheDirectory)) {
            try {
                GeneralUtility::mkdir_deep($cacheDirectory);
            } catch (\RuntimeException $e) {
                throw new \Exception('The directory "' . $cacheDirectory . '" can not be created.', 1450436252001);
            }
            if (!is_writable($cacheDirectory)) {
                throw new \Exception('The directory "' . $cacheDirectory . '" is not writable.', 1450436333678);
            }
        }
        return $cacheDirectory;
    }

    /**
     * @param $databaseIdentifier
     *
     * @return \Doctrine\ORM\Configuration
     * @throws \Exception
     */
    protected function getDoctrineConfiguration()
    {
        // Create Doctrine Configuration Object
        $config = new Configuration();
        // Set Metadata Driver
        $metaDataDriver = $config->newDefaultAnnotationDriver(array());
        $config->setMetadataDriverImpl($metaDataDriver);
        // Set The Proxy Directory
        $proxyDirectory = $this->createDoctrineProxyDirectory();
        $config->setProxyDir($proxyDirectory);
        $config->setProxyNamespace('DoctrineProxies');
        // Enable Caching in Production
        if (GeneralUtility::getApplicationContext()->isProduction()) {
            $cache = $this->getMetaDataCache();
            $config->setMetadataCacheImpl($cache);
            $config->setQueryCacheImpl($cache);
            $config->setAutoGenerateProxyClasses(AbstractProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS);
        } else {
            // Regenerate Proxy Classes on Every Hit
            $config->setAutoGenerateProxyClasses(AbstractProxyFactory::AUTOGENERATE_ALWAYS);
        }

        return $config;
    }

    /**
     * Returns Cache Handler for Doctrine
     *
     *
     * @return ChainCache
     */
    protected function getMetaDataCache()
    {
        $arrayCache = new ArrayCache();
        $typo3cache = GeneralUtility::makeInstance(DoctrineTypo3Cache::class);
        $typo3cache->initializeObject();
        return new ChainCache([$arrayCache, $typo3cache]);
    }
}
