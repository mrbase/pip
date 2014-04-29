<?php
// no need to handle favicon calls
if ($_SERVER['REQUEST_URI'] == '/favicon.ico') {
    header('HTTP/1.0 404 Not Found');
    exit;
}

require_once __DIR__.'/../vendor/autoload.php';

$loader = new \Composer\Autoload\ClassLoader();
$loader->add('Pip', __DIR__.'/../src');
$loader->register();

$app = require __DIR__.'/../src/app.php';

// Don't load the cache if we're on localhost.
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.2') {
    $app['debug'] = true;
    $app->run();
} else {
    $app['http_cache']->run();
}
