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
    DI\get("Starbug\Core\AuthenticationMiddleware"),
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
    $handler = new StreamHandler('var/log/'.$name.".log");
    return $handler;
  },
  "error_handler" => DI\get("Whoops\Run")
];
