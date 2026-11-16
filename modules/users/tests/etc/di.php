<?php
namespace Starbug\Users\Tests;

use Starbug\Users\Tests\Adapter\AuthAdapterInterface;
use Starbug\Users\Tests\Adapter\AuthWebAdapter;
use function DI\autowire;

return [
  AuthAdapterInterface::class => autowire(AuthWebAdapter::class)
];
