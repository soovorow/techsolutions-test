<?php

namespace components;

class Router
{
    public $routes;

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function dispatch($url)
    {
        $path = trim(explode('?', $url)[0], '/');

        if (!isset($this->routes[$path])) {
            throw new \Exception('Page not found');
        }

        $controller_name = 'controllers\\' . $this->routes[$path]['controller'] . 'Controller';
        $controller = new $controller_name();

        $action = 'action' . $this->routes[$path]['action'];

        return $controller->$action();
    }
}