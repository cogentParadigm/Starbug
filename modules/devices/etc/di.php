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
  "notification.handlers" => ["email"],
  "notification.channels" => ["system"],
  "notification.handlers.default" => ["email"],
  "notification.channels.default" => ["system"],
  "notification.handler.email" => DI\object('Starbug\Devices\Notification\Handler\Email'),
  "notification.handler.push" => function (ContainerInterface $container) {
    $push = $container->make("Starbug\Devices\Notification\Handler\Aggregate");
    $push->addHandler("webPush", $container->get("Starbug\Devices\Notification\Handler\WebPush"));
    return $push;
  },
  "notification.handler.push.web.registration.enabled" => false,
  "notification.handler.push.web.publicKey" => false,
  "notification.handler.push.web.privateKey" => false,
  "notification.channel.system" => DI\object('Starbug\Devices\Notification\Channel\System'),
  "Minishlink\WebPush\WebPush" => function (ContainerInterface $container) {
    $publicKey = $container->get("notification.handler.push.web.publicKey");
    $privateKey = $container->get("notification.handler.push.web.privateKey");
    $auth = [
      "VAPID" => [
        "subject" => "",
        "publicKey" => $publicKey,
        "privateKey" => $privateKey,
      ]
    ];
    if ($publicKey && $privateKey) {
      return new Minishlink\WebPush\WebPush($auth);
    } else {
      return new Minishlink\WebPush\WebPush();
    }
  },
  "Starbug\Core\ConfigInterface" => DI\decorate(function ($config, ContainerInterface $container) {
    $config->set("notification.handler.push.web.registration.enabled", $container->get("notification.handler.push.web.registration.enabled"));
    $config->set("notification.handler.push.web.publicKey", $container->get("notification.handler.push.web.publicKey"));
    return $config;
  })
];
