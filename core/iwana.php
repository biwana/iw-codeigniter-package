<?php

spl_autoload_register('Iwana::autoload');

class Iwana {

    public static function autoload($class) {
        $found = false;
        $paths = array(
            'core' => strtolower(dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . EXT),
        );
        foreach ($paths as $k => $path) {
            if (is_readable($path)) {
                require_once($path);
                $found = true;
                break;
            }
        }

        return $found;
    }

}