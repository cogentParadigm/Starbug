<?php

use Psr\Container\ContainerInterface;
use Monolog\Handler\StreamHandler;

return [
  'modules' => [
    "core" => [
      "type" => "module",
      "path" => "core/app",
      "namespace" => "Starbug\Core"
    ]
  ] +
  (include("vendor/modules.php")) +
  [
    "app" => [
      "type" => "module",
      "path" => "app",
      "namespace" => "Starbug\App"
    ],
    "var" => [
      "type" => "module",
      "path" => "var",
      "namespace" => "Starbug\Var"
    ]
  ],
  "Starbug\Modules\Configuration" => DI\autowire("Starbug\Modules\Configuration")
    ->constructorParameter("modules", DI\get("modules"))
    ->method("enableAll", ["type" => "module"])
    ->method("enable", DI\get("theme")),
  "application.middleware" => [
    DI\get("Middlewares\Https"),
    DI\get("Starbug\Http\BaseUrlMiddleware"),
    DI\get("Middlewares\CachePrevention"),
    DI\get("Middlewares\UrlEncodePayload"),
    DI\get("Starbug\Auth\Http\AuthenticationMiddleware"),
    DI\get("Starbug\Auth\Http\CsrfMiddleware"),
    DI\get("Starbug\Core\RoutingMiddleware"),
    DI\get("Starbug\Http\RequestInjectionMiddleware"),
    DI\get("Starbug\Http\TemplateRenderingMiddleware"),
    DI\get("Starbug\Core\Routing\ResolutionMiddleware"),
    DI\get("Starbug\Operation\Http\OperationMiddleware"),
    DI\get("Starbug\Core\FormHandlerMiddleware"),
    DI\get("Starbug\Core\ControllerMiddleware")
  ],
  "Psr\Http\Server\RequestHandlerInterface" => DI\autowire("Middleland\Dispatcher")
    ->constructorParameter("middleware", DI\get("application.middleware")),
  'log.handlers.development' => [
    DI\get('Monolog\Handler\StreamHandler')
  ],
  'log.handlers.production' => [
    DI\get('Monolog\Handler\StreamHandler')
  ],
  'Monolog\Handler\StreamHandler' => function (ContainerInterface $c) {
    $name = $c->get("environment");
    $handler = new StreamHandler("php://stdout");
    return $handler;
  },
  "error_handler" => DI\get("Whoops\Run")
];
