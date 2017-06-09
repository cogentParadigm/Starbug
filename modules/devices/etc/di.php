<?php

use Interop\Container\ContainerInterface;

return [
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Devices\Migration')
  ]),
  'Starbug\Devices\NotificationHandlerInterface' => function (ContainerInterface $c) {
    $manager = $c->get("Starbug\Devices\NotificationManager");
    $handlers = $c->get("notification.handlers");
    foreach ($handlers as $name) {
      $handler = $c->get("notification.handler.".$name);
      $manager->addHandler($name, $handler);
    }
    return $manager;
  },
  'notification.handlers' => ["email", "push"],
  "notification.handler.email" => DI\object('Starbug\Devices\EmailNotificationHandler'),
  "notification.handler.push" => DI\object('Starbug\Devices\PushNotificationHandler')
];
