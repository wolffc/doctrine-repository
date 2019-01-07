<?php
/**
 * Created by PhpStorm.
 * User: cwolff
 * Date: 10.10.2018
 * Time: 17:59
 */

namespace Wolffc\DocrineRepository\Persistence;

class DatabaseConfigurationService
{
    public static function getDatabaseConfiguration($identifier)
    {
        if (is_array(
            $GLOBALS['TYPO3_CONF_VARS']['DB']['Connections'][$identifier]
        )) {
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['user_extranet_forms']['databases'][$identifier];
        }
        throw new \Exception('Could not Retrieve Database configuration', 1449716415895);
    }

    /**
     * Returns a Low Level MySQLi Connection Object
     * @param $identifier
     * @return \mysqli
     */
    public static function getMySQLiConnection($identifier)
    {
        $config = self::getDatabaseConfiguration($identifier);
        return new \mysqli(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['dbname']
        );
    }
}
