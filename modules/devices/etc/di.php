<?php
use Interop\Container\ContainerInterface;

return [
  "notification.handlers" => ["email"],
  "notification.channels" => ["system"],
  "notification.handlers.default" => ["email"],
  "notification.channels.default" => ["system"],
  "notification.handler.email" => DI\object('Starbug\Devices\Notification\Handler\Email'),
  "notification.channel.system" => DI\object('Starbug\Devices\Notification\Channel\System'),
  "notification.handler.web" => DI\object("Starbug\Devices\Notification\Handler\WebPush"),
  "notification.handler.web.registration.enabled" => false,
  "notification.handler.web.publicKey" => false,
  "notification.handler.web.privateKey" => false,
  "notification.handler.android" => DI\object("Starbug\Devices\Notification\Handler\AndroidPush"),
  "notification.handler.android.apiKey" => false,
  "notification.handler.apple" => DI\object("Starbug\Devices\Notification\Handler\ApplePush"),
  "notification.handler.apple.certificateDirectory" => false,
  "notification.handler.apple.passphrase" => false,
  "notification.handler.push.handlers" => [],
  "notification.handler.push" => function (ContainerInterface $container) {
    $handlers = $container->get("notification.handler.push.handlers");
    $push = $container->make("Starbug\Devices\Notification\Handler\Aggregate");
    foreach ($handlers as $name) {
      $handler = $container->get("notification.handler.".$name);
      $push->addHandler($name, $handler);
    }
    return $push;
  },
  "Minishlink\WebPush\WebPush" => function (ContainerInterface $container) {
    $publicKey = $container->get("notification.handler.web.publicKey");
    $privateKey = $container->get("notification.handler.web.privateKey");
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
  "Starbug\Devices\Notification\Handler\AndroidPush" => DI\object()
    ->constructorParameter("apiKey", DI\get("notification.handler.android.apiKey")),
  "Starbug\Devices\Notification\Handler\ApplePush" => DI\object()
    ->constructorParameter("certificateDirectory", "notification.handler.apple.certificateDirectory")
    ->constructorParameter("passphrase", "notification.handler.apple.passphrase"),
  "Starbug\Core\ConfigInterface" => DI\decorate(function ($config, ContainerInterface $container) {
    $config->set("notification.handler.web.registration.enabled", $container->get("notification.handler.web.registration.enabled"));
    $config->set("notification.handler.web.publicKey", $container->get("notification.handler.web.publicKey"));
    return $config;
  }),
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
  }
];
