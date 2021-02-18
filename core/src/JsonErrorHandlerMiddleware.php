<?php
namespace Starbug\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Run;

class JsonErrorHandlerMiddleware implements MiddlewareInterface {

  protected $whoops;

  public function __construct(Run $whoops) {
    $this->whoops = $whoops;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $route = $request->getAttribute("route");
    if ($route->getOption("format") == "json") {
      $this->configureJsonHandler();
    }
    return $handler->handle($request);
  }
  protected function configureJsonHandler() {
    $this->whoops->popHandler();
    $jsonHandler = new JsonResponseHandler();
    $jsonHandler->addTraceToOutput(true);
    $this->whoops->pushHandler($jsonHandler);
  }
}
