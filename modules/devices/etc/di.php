<?php

use Interop\Container\ContainerInterface;

return [
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Devices\Migration')
  ]),
  'Starbug\Devices\Migration' => DI\object()
    ->constructorParameter("handlers", DI\get("notification.handlers"))
    ->constructorParameter("channels", DI\get("notification.channels"))
    ->constructorParameter("defaultHandlers", DI\get("notification.handlers.default"))
    ->constructorParameter("defaultChannels", DI\get("notification.channels.default")),
  'Starbug\Devices\NotificationManagerInterface' => function (ContainerInterface $c) {
    $manager = $c->get("Starbug\Devices\NotificationManager");
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
  "notification.handlers" => ["email", "push"],
  "notification.channels" => ["system"],
  "notification.handlers.default" => ["email", "push"],
  "notification.channels.default" => ["system"],
  "notification.handler.email" => DI\object('Starbug\Devices\Notification\Handler\Email'),
  "notification.handler.push" => DI\object('Starbug\Devices\Notification\Handler\Push'),
  "notification.channel.system" => DI\object('Starbug\Devices\Notification\Channel\System')
];
