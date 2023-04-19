<?php
use Minishlink\WebPush\WebPush;
use function DI\add;
use function DI\get;
use function DI\autowire;
use function DI\decorate;
use Psr\Container\ContainerInterface;

return [
  "route.providers" => add([
    get("Starbug\Devices\RouteProvider")
  ]),
  "notification.handlers" => ["email"],
  "notification.channels" => ["system"],
  "notification.handlers.default" => ["email"],
  "notification.channels.default" => ["system"],
  "notification.handler.email" => autowire('Starbug\Devices\Notification\Handler\Email'),
  "notification.channel.system" => autowire('Starbug\Devices\Notification\Channel\System'),
  "notification.handler.web" => autowire("Starbug\Devices\Notification\Handler\WebPush"),
  "notification.handler.web.registration.enabled" => false,
  "notification.handler.web.publicKey" => false,
  "notification.handler.web.privateKey" => false,
  "notification.handler.android" => autowire("Starbug\Devices\Notification\Handler\AndroidPush"),
  "notification.handler.android.apiKey" => false,
  "notification.handler.apple" => autowire("Starbug\Devices\Notification\Handler\ApplePush"),
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
      return new WebPush($auth);
    } else {
      return new WebPush();
    }
  },
  "Starbug\Devices\Notification\Handler\AndroidPush" => autowire()
    ->constructorParameter("apiKey", get("notification.handler.android.apiKey")),
  "Starbug\Devices\Notification\Handler\ApplePush" => autowire()
    ->constructorParameter("certificateDirectory", "notification.handler.apple.certificateDirectory")
    ->constructorParameter("passphrase", "notification.handler.apple.passphrase"),
  "Starbug\Config\ConfigInterface" => decorate(function ($config, ContainerInterface $container) {
    $config->set("notification.handler.web.registration.enabled", $container->get("notification.handler.web.registration.enabled"));
    $config->set("notification.handler.web.publicKey", $container->get("notification.handler.web.publicKey"));
    return $config;
  }),
  'db.schema.migrations' => add([
    get('Starbug\Devices\Migration')
  ]),
  'Starbug\Devices\Migration' => autowire()
    ->constructorParameter("handlers", get("notification.handlers"))
    ->constructorParameter("channels", get("notification.channels"))
    ->constructorParameter("defaultHandlers", get("notification.handlers.default"))
    ->constructorParameter("defaultChannels", get("notification.channels.default")),
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
