<?php

use components\Router;

require(dirname(__DIR__).'/components/Autoloader.php');

Autoloader::register();

$router = new Router([
    '' => ['controller' => 'Home', 'action' => 'Index'],
    'home/index' => ['controller' => 'Home', 'action' => 'Index']
]);

$router->dispatch($_SERVER['REQUEST_URI']);