<?php
namespace Starbug\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

use function DI\autowire;

return [
  SymfonyEventDispatcherInterface::class => autowire("Symfony\Component\EventDispatcher\EventDispatcher"),
  EventDispatcherInterface::class => autowire("Symfony\Component\EventDispatcher\EventDispatcher")
];
