<?php
namespace Starbug\Users\Admin\Tests\Acceptance\Developer;

use DI\Attribute\Inject;
use Starbug\Db\Testing\DatabaseTestCase;
use Starbug\Testing\Attribute\Bind;
use Starbug\Testing\Attribute\Fixture;
use Starbug\Users\Admin\Tests\Acceptance\Traits\UserManagementCrudTestCases;
use Starbug\Users\Admin\Tests\Adapter\UserManagementAdapterInterface;
use Starbug\Users\Admin\Tests\Adapter\UserManagementShellAdapter;
use Starbug\Users\Admin\Tests\Domain\UserManagement;

/**
 * Shell-based acceptance tests for user management.
 *
 * Covers user CRUD operations through the CLI.
 */
#[Bind(UserManagementAdapterInterface::class, UserManagementShellAdapter::class)]
#[Fixture('app/fixtures/db/reset.yml')]
class UserManagementTest extends DatabaseTestCase {
  use UserManagementCrudTestCases;

  #[Inject]
  protected UserManagement $users;
}
