<?php
declare(strict_types=0);
ob_start();
use \Core\Exception\FatalException;

require __DIR__ . '/../App/Resources/autoload.php';
$app = new \Core\Bootstrap("dev");
$request = \Core\Http\Request\Request::create();
$response = $app->handleRequest($request);
if ($response instanceof \Core\Http\Response\Response) {
    $response->sendResponse();
} else {
    throw new FatalException("Init", "Incorrect response from BootStrap (not a \Core\Http\Response\Response object).");
}
$app->close($request, $response);
