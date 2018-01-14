<?php

namespace controllers;

use views\View;

abstract class Controller
{
    protected $view;

    public function __construct()
    {
        $this->view = new View(['controller' => $this]);
    }

    public function __call($name, $args)
    {
        $method = 'action' . $name;

        if (!method_exists($this, $method)) {
            throw new \Exception('Action ' . $name . ' not found at ' . __CLASS__);
        }

        call_user_func_array([$this, $method], $args);
    }

    public function render($view_name, $args = [])
    {
        return $this->view->renderTemplate($view_name, $args);
    }

    public function renderPartial($view_name, $args = [])
    {
        return $this->view->render($view_name, $args);
    }

    public function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }


}