<?php
declare(strict_types=0);
ob_start();
use \Core\Exception\FatalException;

require __DIR__ . '/../lib/Resources/autoload.php';
$app = new \Core\Bootstrap("my_app", "dev");
$request = \Core\Http\Request\Request::create();
$response = $app->handleRequest($request);
if ($response instanceof \Core\Http\Response\Response) {
    $response->sendResponse();
} else {
    throw new FatalException("Init", "Incorrect response from BootStrap (not a \Core\Http\Response\Response object).");
}
$app->close($request, $response);
