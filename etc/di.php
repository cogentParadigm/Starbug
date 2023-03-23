<?php

use Psr\Container\ContainerInterface;

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
      "Starbug\Core\RoutingMiddleware",
      [$env !== "development", "Starbug\Core\SecureJsonErrorHandlerMiddleware"],
      [$env === "development", "Starbug\Core\JsonErrorHandlerMiddleware"],
      "Starbug\Http\RequestInjectionMiddleware",
      "Starbug\Http\TemplateRenderingMiddleware",
      "Starbug\Core\Routing\ResolutionMiddleware",
      "Starbug\Operation\Http\OperationMiddleware",
      "Starbug\Core\ControllerMiddleware"
    ];
  },
  "Psr\Http\Server\RequestHandlerInterface" => DI\autowire("Middleland\Dispatcher")
    ->constructorParameter("middleware", DI\get("application.middleware"))
    ->constructorParameter("container", DI\get(ContainerInterface::class))
];
