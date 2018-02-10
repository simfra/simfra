<?php

/*
 * /App/app_name
 * /App/lib/
 * /lib/
 */

require __DIR__ . '/../../vendor/autoload.php'; // From composer
spl_autoload_register(function ($class) {
    //echo (defined('APP_DIR')) ? APP_DIR : "";//. __DIR__ ."<br />";
    $class_file = str_replace('\\', '/', $class) .".php";
    $dir = realpath(__DIR__ . "/../../") . "/";
    //echo "Dir: $dir   Class_file: $class_file <br/>";
    if (file_exists($dir . "App/" . $class_file)) {
        include_once($dir . "App/" . $class_file);
    } elseif (file_exists($dir . "lib/" . $class_file)) {
        include_once($dir . "lib/" . $class_file);
    } elseif (file_exists($dir . $class_file)) {
        include_once($dir . $class_file);
    } elseif (defined('APP_DIR') && file_exists(APP_DIR . $class_file)) {
        include_once(APP_DIR . $class_file);
    } elseif (defined('APP_DIR') && file_exists(APP_DIR . "lib/" . $class_file)) {
        include_once(APP_DIR . "lib/" . $class_file);
    }
    /*
    //echo "Klasa: $class ". __DIR__ .str_replace('/', '\\', $class)."\n<br/>";
    echo "Klasa: $class   |  after: " . str_replace('\\', '/', $class) ."<br/>";
    //echo "". __DIR__ ."/../" .  str_replace('\\', '/', $class) ."<br />";
    if (file_exists(__DIR__ ."/../../" .  str_replace('\\', '/', $class)  . '.php')) {
        //echo "A";
        include_once __DIR__ ."/../../". str_replace('\\', '/', $class) . ".php";
    }
    if (file_exists(__DIR__ ."/../../App/" .  str_replace('\\', '/', $class)  . '.php')) {
        //echo "A";
        include_once __DIR__ ."/../../App/". str_replace('\\', '/', $class) . ".php";
    }

    elseif (file_exists(__DIR__ ."/../" .  str_replace('\\', '/', $class)  . '.php')) {
        //echo "B". $class ;
            include_once __DIR__ ."/../"  . str_replace('\\', '/', $class) . ".php";
          }
    elseif (file_exists(__DIR__ ."/../../lib/" .  str_replace('\\', '/', $class)  . '.php')) {
        include_once __DIR__ ."/../../lib/" . str_replace('\\', '/', $class) . ".php";
    }
    elseif (file_exists(__DIR__ ."/../../" .  str_replace('\\', '/', $class)  . '.php')) {
        include_once __DIR__ ."/../../" . str_replace('\\', '/', $class) . ".php";
    }

    elseif (file_exists(__DIR__ ."/../../" .  str_replace('\\', '/', $class)  . '.php')) {
        include_once __DIR__ ."/../../" . str_replace('\\', '/', $class) . ".php";
    }*/
});
