<?php

use Interop\Container\ContainerInterface;

return [
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Devices\Migration')
  ]),
  'Starbug\Devices\NotificationHandlerInterface' => function (ContainerInterface $c) {
    $manager = $c->get("Starbug\Devices\NotificationManager");
    $handlers = $c->get("notification.handlers");
    foreach ($handlers as $handler) {
      $manager->addHandler($handler);
    }
    return $manager;
  },
  'notification.handlers' => [
    DI\get('Starbug\Devices\EmailNotificationHandler'),
    DI\get('Starbug\Devices\ApplePushNotificationHandler')
  ]
];
