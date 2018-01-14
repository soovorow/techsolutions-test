<?php

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(
            function ($class) {

                $file = dirname(__DIR__) . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

                if (!file_exists($file)) {
                    throw new ErrorException('File ' . $file . ' not found');
                }

                require($file);
            }
        );
    }
}