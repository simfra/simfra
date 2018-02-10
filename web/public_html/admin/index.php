<?php
declare(strict_types=0);
//namespace admin;

//ob_start();
use \Core\Exception\FatalException;
//echo "<pre>";
//print_r($_SERVER);
//echo "</pre><br/>";
//echo "@".__DIR__."<br>";
error_reporting(E_ALL);
//session_start(["session_name" => SESSION_NAME]);

ini_set('display_errors', 1);
//echo $_SERVER['DOCUMENT_ROOT'] . '/../../lib/Resources/autoload.php<br />';
require __DIR__ . '/../../lib/Resources/autoload.php';
$app = new \admin\AppKernel("admin", "dev");
$request = \Core\Http\Request\Request::create();
$response = $app->handleRequest($request);
if ($response instanceof \Core\Http\Response\Response) {
    $response->sendResponse();
} else {
    throw new FatalException("Init", "Incorrect response from BootStrap (not a \Core\Http\Response\Response object).");
}
$app->close($request, $response);

/*
$app = new \Core\Bootstrap("admin", "dev");
$request = \Core\Http\Request\Request::create();
$response = $app->handleRequest($request);
if ($response instanceof \Core\Http\Response\Response) {
    $response->sendResponse();
} else {
    throw new FatalException("Init", "Incorrect response from BootStrap (not a \Core\Http\Response\Response object).");
}
$app->close($request, $response);
*/