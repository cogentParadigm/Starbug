<?php

use GuzzleHttp\Psr7\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

// include init file
include("core/init.php");

$request = ServerRequest::fromGlobals();
$uri = $container->make("Psr\Http\Message\UriInterface", ["request" => $request]);
$request = $request->withUri($uri);

$container->set("Psr\Http\Message\UriInterface", $uri);
$container->set("Psr\Http\Message\ServerRequestInterface", $request);
$application = $container->get("Starbug\Core\ApplicationInterface");
$response = $application->handle($request);

$emitter = new SapiEmitter();
$emitter->emit($response);
