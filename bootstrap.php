<?php
// Loads helpers, classes, config and the session. Included once by public/index.php.
require __DIR__ . '/helpers.php';

// Simple autoloader: looks for ClassName.php in Core, Controllers and Models.
spl_autoload_register(function (string $class): void {
    foreach (['Core', 'Controllers', 'Models'] as $folder) {
        $file = __DIR__ . "/$folder/$class.php";
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

$configFile = __DIR__ . '/config/config.php';
$GLOBALS['config'] = is_file($configFile)
    ? require $configFile
    : require __DIR__ . '/config/config.example.php';

ini_set('display_errors', config('debug') ? '1' : '0');
error_reporting(E_ALL);

Session::start();
