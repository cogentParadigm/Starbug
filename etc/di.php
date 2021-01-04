<?php

use Psr\Container\ContainerInterface;
use Monolog\Handler\StreamHandler;

return [
  'modules' => [
    "Starbug\Core" => "core/app",
    "Starbug\Db" => "modules/db",
    "Starbug\Doctrine" => "modules/doctrine",
    "Starbug\Users" => "modules/users",
    "Starbug\Files" => "modules/files",
    "Starbug\Emails" => "modules/emails",
    "Starbug\Comments" => "modules/comments",
    "Starbug\Css" => "modules/css",
    "Starbug\Js" => "modules/js",
    "Starbug\Tachyons" => "modules/tachyons",
    "Starbug\Content" => "modules/content",
    "Starbug\Theme" => "app/themes/tachyons",
    "Starbug\App" => "app",
    "Starbug\Var" => "var"

  ],
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
  }
];
