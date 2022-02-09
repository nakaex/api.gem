<?php

require_once __DIR__ . '/../vendor/autoload.php';

$router = new AltoRouter();

$router->map('GET|POST|PATCH|DELETE', '/user', function () {
    echo "aaaa";
});

$router->map('GET', '/', 'Api\Controllers\WelcomeController@get', 'welcome');

return $router;
