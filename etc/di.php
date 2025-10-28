<?php

use Psr\Container\ContainerInterface;

use function DI\autowire;
use function DI\get;

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
  "Starbug\Modules\Configuration" => autowire("Starbug\Modules\Configuration")
    ->constructorParameter("modules", get("modules"))
    ->method("enableAll", ["type" => "module"])
    ->method("enable", get("theme")),
  "application.middleware" => function (ContainerInterface $container) {
    $env = $container->get("environment");
    return [
      "Middlewares\Https",
      "Starbug\Http\BaseUrlMiddleware",
      "Middlewares\CachePrevention",
      "Middlewares\UrlEncodePayload",
      "Starbug\Auth\Http\AuthenticationMiddleware",
      "Starbug\Auth\Http\CsrfMiddleware",
      "Starbug\Http\RequestInjectionMiddleware",
      "Starbug\Routing\RoutingMiddleware",
      [$env !== "development", "Starbug\Core\SecureJsonErrorHandlerMiddleware"],
      [$env === "development", "Starbug\Core\JsonErrorHandlerMiddleware"],
      "Starbug\Http\RequestInjectionMiddleware",
      "Starbug\Templates\Http\TemplateRenderingMiddleware",
      "Starbug\Routing\ResolutionMiddleware",
      "Starbug\Operation\Http\OperationMiddleware",
      "Starbug\Routing\ControllerMiddleware"
    ];
  },
  "Psr\Http\Server\RequestHandlerInterface" => autowire("Middleland\Dispatcher")
    ->constructorParameter("middleware", get("application.middleware"))
    ->constructorParameter("container", get(ContainerInterface::class))
];
