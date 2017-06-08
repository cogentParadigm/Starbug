<?php

use Interop\Container\ContainerInterface;

return [
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Devices\Migration')
  ]),
  'Starbug\Devices\NotificationHandlerInterface' => function (ContainerInterface $c) {
    $manager = $c->get("Starbug\Devices\NotificationManager");
    $handlers = $c->get("notification.handlers");
    foreach ($handlers as $name => $handler) {
      $manager->addHandler($name, $handler);
    }
    return $manager;
  },
  'notification.handlers' => [
    "email" => DI\get('Starbug\Devices\EmailNotificationHandler'),
    "push" => DI\get('Starbug\Devices\PushNotificationHandler')
  ]
];
