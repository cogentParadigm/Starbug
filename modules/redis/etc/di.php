<?php
namespace Starbug\Redis;

use DI\FactoryInterface;
use Predis\Client;
use Psr\Container\ContainerInterface;
use Starbug\Queue\QueueFactory;

use function DI\autowire;

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
  "Starbug\Queue\QueueFactoryInterface" => function (
    ContainerInterface $container,
    FactoryInterface $factory
  ) {
    $queues = new QueueFactory();
    $queues->addQueue("default", function () use ($container, $factory) {
      return $factory->make("Starbug\Queue\Driver\Predis", [
        "name" => "default",
        "redis" => $container->get("Predis\Client")
      ]);
    });
    return $queues;
  }
];
