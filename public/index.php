<?php

$router = require_once dirname(__DIR__, 1) . '/Routes/Api.php';

$match = $router->match();

if ($match !== false) {
    Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1))->load();
    require_once dirname(__DIR__, 1) . '/Database/Database.php';
    require_once dirname(__DIR__, 1) . '/Bases/@Type.php';
    require_once dirname(__DIR__, 1) . '/Models/@Type.php';
    require_once dirname(__DIR__, 1) . '/Controllers/@Type.php';
    if (is_callable($match['target'])) {
        $match['target']();
    } else {
        $params = explode('@', $match['target']);
        if (class_exists($params[0])) {
            $action = new $params[0]();
            call_user_func_array(array($action, $params[1]), $match['params']);
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
        }
    }
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
