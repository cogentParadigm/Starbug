<?php
use GuzzleHttp\Psr7\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

include("vendor/starbug/di/bootstrap/default.php");

$request = ServerRequest::fromGlobals();

$dispatcher = $container->get("Psr\Http\Server\RequestHandlerInterface");
$response = $dispatcher->handle($request);

$emitter = new SapiEmitter();
$emitter->emit($response);
