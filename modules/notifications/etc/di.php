<?php
namespace Starbug\Notifications;

use DI;
use Psr\Container\ContainerInterface;
use Starbug\Notifications\Script\ProcessNotifications;

return [
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\Notifications\Migration")
  ]),
  "notification.handlers" => ["email"],
  "notification.channels" => ["system"],
  "notification.handlers.default" => ["email"],
  "notification.channels.default" => ["system"],
  "notification.handler.email" => DI\autowire("Starbug\Notifications\Handler\Email"),
  "notification.channel.system" => DI\autowire("Starbug\Notifications\Channel\System"),
  "Starbug\Notifications\Recipient\*Interface" => DI\autowire("Starbug\Notifications\Recipient\*"),
  "Starbug\Notifications\Migration" => DI\autowire()
    ->constructorParameter("handlers", DI\get("notification.handlers"))
    ->constructorParameter("channels", DI\get("notification.channels"))
    ->constructorParameter("defaultHandlers", DI\get("notification.handlers.default"))
    ->constructorParameter("defaultChannels", DI\get("notification.channels.default")),
  "Starbug\Notifications\NotificationManagerInterface" => function (ContainerInterface $c) {
    $manager = $c->get("Starbug\Notifications\NotificationManager");
    $handlers = $c->get("notification.handlers");
    foreach ($handlers as $name) {
      $handler = $c->get("notification.handler.".$name);
      $manager->addHandler($name, $handler);
    }
    $channels = $c->get("notification.channels");
    foreach ($channels as $name) {
      $channel = $c->get("notification.channel.".$name);
      $manager->addChannel($name, $channel);
    }
    $loggerFactory = $c->get("Starbug\Log\LoggerFactory");
    $manager->setLogger($loggerFactory->create("notifications"));
    return $manager;
  },
  "scripts.process-notifications" => ProcessNotifications::class
];
