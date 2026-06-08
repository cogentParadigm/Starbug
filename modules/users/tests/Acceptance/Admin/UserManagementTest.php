<?php
namespace Starbug\Users\Tests\Acceptance\Admin;

use DI\Attribute\Inject;
use Starbug\Testing\Attribute\Fixture;
use Starbug\Testing\WebTestCase;
use Starbug\Users\Tests\Acceptance\Traits\UserManagementCrudTestCases;
use Starbug\Users\Tests\Domain\Authentication;
use Starbug\Users\Tests\Domain\UserManagement;

/**
 * Web-based acceptance tests for user management.
 *
 * Covers user CRUD operations through the admin web UI.
 */
#[Fixture('app/fixtures/db/reset.yml')]
class UserManagementTest extends WebTestCase {
  use UserManagementCrudTestCases;

  #[Inject]
  protected Authentication $auth;

  #[Inject]
  protected UserManagement $users;

  protected function setUp(): void {
    parent::setUp();
    $this->auth->login('root', 'Mz2&cssMy@9tclan');
  }
}
