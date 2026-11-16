<?php
namespace Starbug\Content\Admin\Tests;

use function DI\autowire;
use Starbug\Content\Admin\Tests\Adapter\PageManagementWebAdapter;
use Starbug\Testing\Adapter\CrudAdapterInterface;

return [
  CrudAdapterInterface::class => autowire(PageManagementWebAdapter::class)
];
