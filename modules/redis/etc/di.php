<?php
namespace Starbug\Redis;

use function DI\autowire;
use DI;
use Predis\Client;
use Psr\Container\ContainerInterface;
use Starbug\Queue\QueueFactory;

return [
  "redis.scheme" => "tcp",
  "redis.host" => "127.0.0.1",
  "redis.port" => "6379",
  "redis.password" => "",
  "redis.prefix" => "starbug:",
  "Predis\Client" => function (ContainerInterface $container) {
    return new Client(
      [
        "scheme" => $container->get("redis.scheme"),
        "host" => $container->get("redis.host"),
        "port" => $container->get("redis.port"),
        "password" => $container->get("redis.password")
      ],
      [
        "prefix" => $container->get("redis.prefix")
      ]
    );
  },
  "Starbug\Queue\*Interface" => autowire("Starbug\Queue\*"),
  "Starbug\Queue\QueueFactoryInterface" => function (ContainerInterface $container) {
    $factory = new QueueFactory();
    $factory->addQueue("default", function () use ($container) {
      return $container->make("Starbug\Queue\Driver\Predis", [
        "name" => "default",
        "redis" => $container->get("Predis\Client")
      ]);
    });
    return $factory;
  }
];
