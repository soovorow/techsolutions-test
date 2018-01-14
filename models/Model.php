<?php

namespace models;

abstract class Model
{
    public function __construct($params = [])
    {
        foreach ($params as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    public function getClassName()
    {
        return explode('\\', get_class($this))[1];
    }
}