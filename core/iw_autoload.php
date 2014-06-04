<?php

spl_autoload_register('IW_Autoload::autoload');

class IW_Autoload {

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