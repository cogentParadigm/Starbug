<?php
namespace Starbug\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whoops\Handler\Handler;
use Whoops\Run;

class SecureJsonErrorHandlerMiddleware implements MiddlewareInterface {

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
    $this->whoops->pushHandler(function () {
      echo json_encode(["error" => "An error has occurred."]);
      return Handler::QUIT;
    });
  }
}
