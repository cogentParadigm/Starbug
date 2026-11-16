<?php
namespace Starbug\Users\Admin\Tests;

use Starbug\Users\Admin\Tests\Adapter\UserManagementAdapterInterface;
use Starbug\Users\Admin\Tests\Adapter\UserManagementWebAdapter;
use function DI\autowire;

return [
  UserManagementAdapterInterface::class => autowire(UserManagementWebAdapter::class)
];
