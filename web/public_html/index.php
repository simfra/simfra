<?php
echo 1;
ob_start();
use \Core\Exception\FatalException;
require __DIR__ . '/../App/Resources/autoload.php';
$time_start = microtime(true);
$app = new \App\Bootstrap("dev");
$request = \Core\Http\Request\Request::Create();
$response = $app->HandleRequest($request);
if($response instanceof \Core\Http\Response\Response) {
    $response->sendResponse();
}
else{
    throw new FatalException("Init","Incorrect response from BootStrap (not a \Core\Http\Response\Response object).");
}
$app->Close($request, $response);