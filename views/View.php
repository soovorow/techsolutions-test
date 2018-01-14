<?php

namespace views;

use Exception;
use models\Model;

class View extends Model
{
    public $controller;
    public $title;

    public function render($view_name, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = $this->getViewFile($view_name);

        if (!is_readable($file)) {
            throw new Exception($file . ' not found');
        }

        return require($file);
    }

    public function renderTemplate($view_name, $args = [])
    {
        extract($args, EXTR_SKIP);

        $content = $this->getViewFile($view_name);

        if (!is_readable($content)) {
            throw new Exception($content . ' not found');
        }

        return require(dirname(__DIR__) . '/views/layout/main.php');
    }

    public function getViewFile($view_name)
    {
        $name = explode('\\', get_class($this->controller))[1];
        $name = explode('Controller', $name)[0];
        $name = strtolower($name);

        return dirname(__DIR__) . '/views/' . $name . '/' . $view_name . '.php';
    }
}