<?php

$baseDir = _PS_MODULE_DIR_ . 'x13eucookies/';

if (!defined('X13_EUCOOKIES_DIR')) {
    define('X13_EUCOOKIES_DIR', $baseDir);
    define('X13_EUCOOKIES_RESOURCES_DIR', X13_EUCOOKIES_DIR . 'src/Resources/');
    define('X13_EUCOOKIES_CACHE_DIR', X13_EUCOOKIES_RESOURCES_DIR . 'cache/');
    define('X13_EUCOOKIES_LOG_DIR', X13_EUCOOKIES_RESOURCES_DIR . 'log/');
    define('X13_EUCOOKIES_TOOLS_DIR', X13_EUCOOKIES_DIR . 'tools/');
}

if (!defined('X13_EUCOOKIES_ION')) {
    if (PHP_VERSION_ID >= 80100) {
        $x13IonVer = '-81';
        $x13IonFolder = 'php81';
    } elseif (PHP_VERSION_ID >= 70100) {
        $x13IonVer = '-71';
        $x13IonFolder = 'php71';
    } elseif (PHP_VERSION_ID >= 70000) {
        $x13IonVer = '-7';
        $x13IonFolder = 'php70';
    } else {
        $x13IonVer = '';
        $x13IonFolder = 'php5';
    }

    $phpVersions = 'php5;php70;php71;php81';

    if (file_exists(X13_EUCOOKIES_DIR . 'dev')) {
        $x13IonVer = '';
        $x13IonFolder = 'php5';
        $phpVersions = 'php5';
    }

    define('X13_EUCOOKIES_ION_VERSIONS', $phpVersions);
    define('X13_EUCOOKIES_ION', $x13IonFolder);
}

// Autoload classes
include_once X13_EUCOOKIES_TOOLS_DIR . 'Psr/Autoloader/EuCookiesPsr4Autoloader.php';

$loader = new EuCookiesPsr4Autoloader();
$loader->register();
$loader->addNamespace('x13eucookies', $baseDir . 'src/' . X13_EUCOOKIES_ION . '/');

// To prevent some errors with legacy classes we load them manually
$legacyClasses = [
    'classes/XEuCookiesCookie',
    'classes/XEuCookiesCookieCategory',
];

foreach ($legacyClasses as $legacyClass) {
    if (file_exists(X13_EUCOOKIES_DIR . $legacyClass . '.php')) {
        include_once X13_EUCOOKIES_DIR . $legacyClass . '.php';
    }
}
