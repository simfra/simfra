<?php
require __DIR__ . '/../../vendor/autoload.php'; // From composer
spl_autoload_register(function ($class) {
    //echo "Klasa: $class ". __DIR__ .str_replace('/', '\\', $class)."\n<br/>";
    //echo "". __DIR__ ."/../" .  str_replace('\\', '/', $class) ."<br />";
    if (file_exists(__DIR__ ."/../../" .  str_replace('\\', '/', $class)  . '.php')) {
        //echo "A";
        include_once __DIR__ ."/../../". str_replace('\\', '/', $class) . ".php";
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
});