<?php
require __DIR__ . '/../../vendor/autoload.php'; // From composer
spl_autoload_register(function ($class) {
    echo $class. "<br />";
        if (file_exists(PATH_LIB .  str_replace('\\', '/', $class)  . '.php')) { 
            include_once PATH_LIB. str_replace('\\', '/', $class) . ".php";
        }
        elseif (file_exists(PATH_CONTROLLER .  str_replace('\\', '/', $class)  . '.php')) { 
            include_once PATH_CONTROLLER . str_replace('\\', '/', $class) . ".php";
        }
        elseif (file_exists(__DIR__ ."/../../lib/" .  str_replace('\\', '/', $class)  . '.php')) { 
            include_once __DIR__ ."/../../lib/" . str_replace('\\', '/', $class) . ".php";
        }
        elseif (file_exists(__DIR__ ."/../../" .  str_replace('\\', '/', $class)  . '.php')) { 
            include_once __DIR__ ."/../../" . str_replace('\\', '/', $class) . ".php";
        }    
});