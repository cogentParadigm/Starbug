<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ControllerMiddleware implements MiddlewareInterface {

  /**
   * The factory which creates controllers
   *
   * @var ControllerFactoryInterface
   */
  protected $controllers;

  /**
   * Controller factory.
   *
   * @param ControllerFactoryInterface $controllers Factory to create controllers.
   */
  public function __construct(ContainerInterface $container, ControllerFactoryInterface $controllers) {
    $this->container = $container;
    $this->controllers = $controllers;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $this->container->set("Psr\Http\Message\ServerRequestInterface", $request);
    $controller = $this->controllers->get($request->getAttribute("controller"));
    return $controller->handle($request);
  }
}
