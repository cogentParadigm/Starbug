<?php
namespace Starbug\Log;

use function DI\get;
use function DI\add;
use function DI\autowire;
use function DI\decorate;
use DI;
use Doctrine\DBAL\DriverManager;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Starbug\Http\ResponseBuilderInterface;
use Whoops\Handler\Handler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

return [
  "log.environment" => "default",
  "debug" => false,
  "error_handler" => get(Run::class),
  "log.handlers.default" => [
    get(StreamHandler::class),
    get(DatabaseLogHandler::class)
  ],
  "log.handlers.bootstrap" => [
    get(StreamHandler::class)
  ],
  "log.handlers.active" => function (ContainerInterface $container) {
    $env = $container->has("databases.default") ? $container->get("log.environment") : "bootstrap";
    return $container->get("log.handlers.".$env);
  },
  "route.providers" => add([
    get("Starbug\Log\RouteProvider")
  ]),
  "db.schema.migrations" => add([
    get("Starbug\Log\Migration")
  ]),
  DatabaseLogHandler::class => function (ContainerInterface $container) {
    $db = $container->get("databases.active");
    $tz = $container->get("time_zone");
    $params = [
      "url" => $db["type"]."://".
        urlencode($db["username"]).":".urlencode($db["password"]).'@'.
        $db["host"].'/'.$db["db"].'?charset=utf8'
    ];
    if (!empty($db["driverOptions"])) {
      $params["driverOptions"] = $db["driverOptions"];
    }
    $conn = DriverManager::getConnection($params);
    if (!empty($tz)) {
      $conn->executeQuery("SET time_zone='".$tz."'");
    }
    return new DatabaseLogHandler($conn, $db["prefix"]."error_log");
  },
  StreamHandler::class => autowire()
    ->constructorParameter("stream", "php://stdout"),
  LoggerInterface::class => autowire(Logger::class)
    ->constructorParameter("name", "main")
    ->constructorParameter("handlers", get("log.handlers.active")),
  LoggerFactory::class => autowire()
    ->constructorParameter("handlers", get("log.handlers.active")),
  Run::class => decorate(function ($whoops, $container) {
    $textHandler = new PlainTextHandler($container->get(LoggerInterface::class));
    $whoops->appendHandler($textHandler);
    $cli = $container->get("cli");
    if (!Misc::isCommandLine() && !$cli) {
      $textHandler->loggerOnly(true);
      if ($container->get("debug")) {
        $whoops->appendHandler(new PrettyPageHandler());
      } else {
        $whoops->appendHandler(function ($e) use ($container) {
          $response = $container->get(ResponseBuilderInterface::class);
          $response = $response->create(500)
          ->render("exception.html")
          ->getResponse();
          $emitter = new SapiEmitter();
          $emitter->emit($response);
          return Handler::QUIT;
        });
      }
    }
    return $whoops;
  })
];
