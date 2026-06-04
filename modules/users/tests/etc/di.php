<?php
namespace Starbug\Users\Tests;

use Starbug\Users\Tests\Adapter\AuthAdapterInterface;
use Starbug\Users\Tests\Adapter\AuthWebAdapter;
use Starbug\Users\Tests\Adapter\UserManagementAdapterInterface;
use Starbug\Users\Tests\Adapter\UserManagementWebAdapter;
use function DI\autowire;

return [
  AuthAdapterInterface::class => autowire(AuthWebAdapter::class),
  UserManagementAdapterInterface::class => autowire(UserManagementWebAdapter::class)
];
