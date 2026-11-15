<?php
namespace Starbug\Content\Tests;

use function DI\autowire;
use Starbug\Content\Tests\Adapter\PageManagementWebAdapter;
use Starbug\Testing\Adapter\CrudAdapterInterface;

return [
  CrudAdapterInterface::class => autowire(PageManagementWebAdapter::class)
];
