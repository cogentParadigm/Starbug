<?php
namespace Starbug\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Starbug\Core\Routing\RouterInterface;

class ControllerMiddleware implements MiddlewareInterface {

  /**
   * The factory which creates controllers
   *
   * @var ControllerFactoryInterface
   */
  protected $controllers;
  /**
   * The router which produces a route for the request.
   *
   * @var RouterInterface
   */
  protected $router;
  /**
   * The logger instance.
   *
   * @var LoggerInterface
   */
  protected $logger;

  /**
   * All the dependencies needed to co-ordinate the application.
   *
   * @param ControllerFactoryInterface $controllers Factory to create controllers.
   * @param RouterInterface $router Router translates paths to controllers.
   * @param LoggerInterface $logger To write log messages.
   */
  public function __construct(
    ControllerFactoryInterface $controllers,
    RouterInterface $router,
    LoggerInterface $logger
  ) {
    $this->controllers = $controllers;
    $this->router = $router;
    $this->logger = $logger;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $route = $this->router->route($request);
    $this->logger->addInfo("Loading ".$route['controller'].' -> '.$route['action']);
    $controller = $this->controllers->get($route['controller']);
    return $controller->handle($request, $route);
  }
}
