<?php
namespace Starbug\Queues;

use Psr\Container\ContainerInterface;
use Starbug\Queue\Driver\Sql;
use Starbug\Queue\QueueFactory;
use Starbug\Queues\Script\ProcessQueue;
use Starbug\Queues\Script\Queue;

use function DI\add;
use function DI\get;

return [
  "db.schema.migrations" => add([
    get(Migration::class)
  ]),
  "scripts.process-queue" => ProcessQueue::class,
  "scripts.queue" => Queue::class,
  "Starbug\Queue\*Interface" => autowire("Starbug\Queue\*"),
  "Starbug\Queue\QueueFactoryInterface" => function (ContainerInterface $container) {
    $factory = new QueueFactory();
    $factory->addQueue("default", function () use ($container) {
      return $container->make(Sql::class, ["name" => "default"]);
    });
    return $factory;
  }
];
