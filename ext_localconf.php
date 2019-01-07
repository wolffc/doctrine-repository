<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(
    \Wolffc\DocrineRepository\Property\TypeConverter\DoctrineObjectConverter::class
);

// Register Caching Framework for Current Extension
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['docrine_repository'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['docrine_repository'] = [];
}